<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DemoCareerFieldProposal;
use App\Models\DemoDemandProposal;
use App\Models\DemoTrainingCapacity;
use App\Models\DemoWorkforceDemand;
use App\Models\School;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DemoSchoolPortalController extends Controller
{
    private const CAREER_FIELD_IDS = [
        'co_khi',
        'dien_dien_tu',
        'cntt',
        'xay_dung',
        'giao_thong_logistics',
        'det_may',
        'du_lich',
        'nong_nghiep',
        'kinh_doanh',
        'y_te',
        'hoa_chat_nang_luong',
        'van_hoa_nghe_thuat',
        'giao_duc_dich_vu',
    ];

    public function selectSchool(Request $request): JsonResponse
    {
        $this->ensureEnabled();

        $data = $request->validate([
            'school_id' => ['required', 'string', 'max:50', 'exists:schools,id'],
        ]);

        $school = School::query()
            ->whereKey($data['school_id'])
            ->where(fn ($query) => $query->whereNull('status')->orWhere('status', '!=', 'INACTIVE'))
            ->firstOrFail();

        $request->session()->put('demo_school_id', (string) $school->getKey());
        $request->session()->regenerate();

        return response()->json([
            'school' => $this->schoolData($school),
        ]);
    }

    public function capacities(Request $request): JsonResponse
    {
        $school = $this->selectedSchool($request);

        $capacities = DemoTrainingCapacity::query()
            ->where('school_id', $school->getKey())
            ->with(['major:id,name', 'trainingLevel:id,name', 'period:id,name'])
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (DemoTrainingCapacity $capacity): array => $this->capacityData($capacity))
            ->values();

        return response()->json([
            'school' => $this->schoolData($school),
            'capacities' => $capacities,
        ]);
    }

    public function storeCapacity(Request $request): JsonResponse
    {
        $school = $this->selectedSchool($request);
        $data = $request->validate([
            'training_major_id' => ['required', 'integer', 'exists:training_majors,id'],
            'training_level_id' => ['required', 'string', 'max:20', 'exists:training_levels,id'],
            'reporting_period_id' => ['required', 'integer', 'exists:reporting_periods,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'location' => ['required', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:120'],
            'skills' => ['required', 'array', 'min:1', 'max:20'],
            'skills.*' => ['required', 'string', 'max:120'],
            'equipment' => ['nullable', 'string', 'max:2000'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $skills = collect($data['skills'])
            ->map(fn (string $name): string => trim($name))
            ->filter()
            ->unique(fn (string $name): string => Skill::normalize($name))
            ->values()
            ->all();

        $capacity = DemoTrainingCapacity::query()->updateOrCreate(
            [
                'school_id' => $school->getKey(),
                'training_major_id' => $data['training_major_id'],
                'education_level_id' => $data['training_level_id'],
                'reporting_period_id' => $data['reporting_period_id'],
            ],
            [
                'quantity' => $data['quantity'],
                'location' => trim($data['location']),
                'duration' => $this->nullableText($data['duration'] ?? null),
                'skills' => $skills,
                'equipment' => $this->nullableText($data['equipment'] ?? null),
                'note' => $this->nullableText($data['note'] ?? null),
                'status' => 'ACTIVE',
            ],
        );

        $capacity->load(['major:id,name', 'trainingLevel:id,name', 'period:id,name']);

        return response()->json([
            'message' => 'Đã lưu khai báo năng lực.',
            'capacity' => $this->capacityData($capacity),
        ], $capacity->wasRecentlyCreated ? 201 : 200);
    }

    public function opportunities(Request $request): JsonResponse
    {
        $school = $this->selectedSchool($request);

        $capacities = DemoTrainingCapacity::query()
            ->where('school_id', $school->getKey())
            ->where('status', 'ACTIVE')
            ->with(['major:id,name', 'trainingLevel:id,name', 'period:id,name'])
            ->get();

        $proposals = DemoDemandProposal::query()
            ->where('school_id', $school->getKey())
            ->where('status', '!=', 'WITHDRAWN')
            ->get();
        $proposalByPair = $proposals->keyBy(
            fn (DemoDemandProposal $proposal): string => $proposal->demo_workforce_demand_id.'|'.$proposal->demo_training_capacity_id,
        );

        $demandTotals = DemoDemandProposal::query()
            ->where('status', '!=', 'WITHDRAWN')
            ->selectRaw('demo_workforce_demand_id, SUM(quantity) as quantity')
            ->groupBy('demo_workforce_demand_id')
            ->pluck('quantity', 'demo_workforce_demand_id');
        $capacityTotals = DemoDemandProposal::query()
            ->where('status', '!=', 'WITHDRAWN')
            ->selectRaw('demo_training_capacity_id, SUM(quantity) as quantity')
            ->groupBy('demo_training_capacity_id')
            ->pluck('quantity', 'demo_training_capacity_id');

        $opportunities = DemoWorkforceDemand::query()
            ->where('status', 'ACTIVE')
            ->with(['employer:id,name,industry', 'major:id,name', 'trainingLevel:id,name', 'period:id,name'])
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (DemoWorkforceDemand $demand) use ($capacities, $proposalByPair, $demandTotals, $capacityTotals): array {
                $proposed = (int) ($demandTotals[$demand->getKey()] ?? 0);
                $remaining = max(0, (int) $demand->quantity - $proposed);
                $candidates = $capacities
                    ->filter(fn (DemoTrainingCapacity $capacity): bool => $this->capacityMatchesDemand($capacity, $demand))
                    ->map(function (DemoTrainingCapacity $capacity) use ($demand, $proposalByPair, $proposed, $capacityTotals): array {
                        $proposal = $proposalByPair->get($demand->getKey().'|'.$capacity->getKey());
                        $currentQuantity = (int) ($proposal?->quantity ?? 0);
                        $otherDemandQuantity = max(0, $proposed - $currentQuantity);
                        $otherCapacityQuantity = max(
                            0,
                            (int) ($capacityTotals[$capacity->getKey()] ?? 0) - $currentQuantity,
                        );
                        $maximum = min(
                            max(0, (int) $demand->quantity - $otherDemandQuantity),
                            max(0, (int) $capacity->quantity - $otherCapacityQuantity),
                        );

                        return [
                            'id' => $capacity->getKey(),
                            'label' => trim(($capacity->major?->name ?? 'Năng lực đào tạo').' · '.($capacity->trainingLevel?->name ?? '')),
                            'major' => $capacity->major?->name,
                            'level' => $capacity->trainingLevel?->name,
                            'period' => $capacity->period?->name,
                            'quantity' => (int) $capacity->quantity,
                            'maximum' => $maximum,
                            'skills' => $capacity->skills ?? [],
                            'missing_skills' => $this->skillGap($demand->skills ?? [], $capacity->skills ?? []),
                            'proposal' => $proposal ? $this->proposalData($proposal) : null,
                        ];
                    })
                    ->values();

                return [
                    'id' => $demand->getKey(),
                    'employer' => $demand->employer?->name,
                    'employer_industry' => $demand->employer?->industry,
                    'major' => $demand->major?->name,
                    'level' => $demand->trainingLevel?->name,
                    'period' => $demand->period?->name,
                    'purpose' => $demand->purpose,
                    'quantity' => (int) $demand->quantity,
                    'proposed_quantity' => $proposed,
                    'remaining_quantity' => $remaining,
                    'school_proposed_quantity' => (int) $candidates->sum(fn (array $candidate): int => (int) ($candidate['proposal']['quantity'] ?? 0)),
                    'location' => $demand->location,
                    'skills' => $demand->skills ?? [],
                    'note' => $demand->note,
                    'candidates' => $candidates,
                ];
            })
            ->values();

        return response()->json([
            'school' => $this->schoolData($school),
            'opportunities' => $opportunities,
        ]);
    }

    public function storeProposal(Request $request): JsonResponse
    {
        $school = $this->selectedSchool($request);
        $data = $request->validate([
            'demo_workforce_demand_id' => ['required', 'integer', 'exists:demo_workforce_demands,id'],
            'demo_training_capacity_id' => ['required', 'integer', 'exists:demo_training_capacities,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'plan' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        [$proposal, $created] = DB::transaction(function () use ($data, $school): array {
            $demand = DemoWorkforceDemand::query()
                ->whereKey($data['demo_workforce_demand_id'])
                ->where('status', 'ACTIVE')
                ->lockForUpdate()
                ->firstOrFail();
            $capacity = DemoTrainingCapacity::query()
                ->whereKey($data['demo_training_capacity_id'])
                ->where('school_id', $school->getKey())
                ->where('status', 'ACTIVE')
                ->lockForUpdate()
                ->firstOrFail();

            if (! $this->capacityMatchesDemand($capacity, $demand)) {
                throw ValidationException::withMessages([
                    'demo_training_capacity_id' => 'Năng lực được chọn không cùng ngành/nghề, trình độ và kỳ với nhu cầu.',
                ]);
            }

            $existing = DemoDemandProposal::query()
                ->where('demo_workforce_demand_id', $demand->getKey())
                ->where('demo_training_capacity_id', $capacity->getKey())
                ->lockForUpdate()
                ->first();

            $demandUsed = (int) DemoDemandProposal::query()
                ->where('demo_workforce_demand_id', $demand->getKey())
                ->where('status', '!=', 'WITHDRAWN')
                ->when($existing, fn ($query) => $query->whereKeyNot($existing->getKey()))
                ->sum('quantity');
            $capacityUsed = (int) DemoDemandProposal::query()
                ->where('demo_training_capacity_id', $capacity->getKey())
                ->where('status', '!=', 'WITHDRAWN')
                ->when($existing, fn ($query) => $query->whereKeyNot($existing->getKey()))
                ->sum('quantity');
            $maximum = min(
                max(0, (int) $demand->quantity - $demandUsed),
                max(0, (int) $capacity->quantity - $capacityUsed),
            );

            if ((int) $data['quantity'] > $maximum) {
                throw ValidationException::withMessages([
                    'quantity' => "Số người đề xuất không được vượt quá {$maximum} người.",
                ]);
            }

            $proposal = DemoDemandProposal::query()->updateOrCreate(
                [
                    'demo_workforce_demand_id' => $demand->getKey(),
                    'demo_training_capacity_id' => $capacity->getKey(),
                ],
                [
                    'school_id' => $school->getKey(),
                    'quantity' => (int) $data['quantity'],
                    'plan' => trim($data['plan']),
                    'status' => 'SUBMITTED',
                ],
            );

            return [$proposal, $proposal->wasRecentlyCreated];
        }, 3);

        return response()->json([
            'message' => $created ? 'Đã gửi đề xuất đáp ứng.' : 'Đã cập nhật đề xuất đáp ứng.',
            'proposal' => $this->proposalData($proposal),
        ], $created ? 201 : 200);
    }

    public function fieldProposals(Request $request): JsonResponse
    {
        $school = $this->selectedSchool($request);

        return response()->json([
            'school' => $this->schoolData($school),
            'proposals' => DemoCareerFieldProposal::query()
                ->where('school_id', $school->getKey())
                ->where('status', '!=', 'WITHDRAWN')
                ->orderByDesc('updated_at')
                ->get()
                ->map(fn (DemoCareerFieldProposal $proposal): array => $this->fieldProposalData($proposal))
                ->values(),
        ]);
    }

    public function storeFieldProposal(Request $request): JsonResponse
    {
        $school = $this->selectedSchool($request);
        $data = $request->validate([
            'career_field_id' => ['required', 'string', Rule::in(self::CAREER_FIELD_IDS)],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'plan' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        $proposal = DemoCareerFieldProposal::query()->updateOrCreate(
            [
                'school_id' => $school->getKey(),
                'career_field_id' => $data['career_field_id'],
            ],
            [
                'quantity' => (int) $data['quantity'],
                'plan' => trim($data['plan']),
                'status' => 'SUBMITTED',
            ],
        );

        return response()->json([
            'message' => $proposal->wasRecentlyCreated
                ? 'Đã gửi đề xuất theo lĩnh vực.'
                : 'Đã cập nhật đề xuất theo lĩnh vực.',
            'proposal' => $this->fieldProposalData($proposal),
        ], $proposal->wasRecentlyCreated ? 201 : 200);
    }

    private function selectedSchool(Request $request): School
    {
        $this->ensureEnabled();

        $schoolId = (string) $request->session()->get('demo_school_id', '');
        abort_if($schoolId === '', 401, 'Chưa chọn cơ sở GDNN.');

        return School::query()
            ->whereKey($schoolId)
            ->where(fn ($query) => $query->whereNull('status')->orWhere('status', '!=', 'INACTIVE'))
            ->firstOrFail();
    }

    private function ensureEnabled(): void
    {
        abort_unless(config('demo_portal.enabled'), 404);
    }

    /** @return array<string, mixed> */
    private function schoolData(School $school): array
    {
        return [
            'id' => (string) $school->getKey(),
            'code' => $school->code,
            'name' => $school->name,
            'address' => $school->address,
            'ward' => $school->ward,
        ];
    }

    /** @return array<string, mixed> */
    private function capacityData(DemoTrainingCapacity $capacity): array
    {
        return [
            'id' => $capacity->getKey(),
            'school_id' => (string) $capacity->school_id,
            'major' => $capacity->major?->name,
            'level' => $capacity->trainingLevel?->name,
            'period' => $capacity->period?->name,
            'quantity' => (int) $capacity->quantity,
            'location' => $capacity->location,
            'duration' => $capacity->duration,
            'skills' => $capacity->skills ?? [],
            'equipment' => $capacity->equipment,
            'note' => $capacity->note,
            'status' => $capacity->status,
            'updated_at' => $capacity->updated_at?->toIso8601String(),
        ];
    }

    /** @return array<string, mixed> */
    private function proposalData(DemoDemandProposal $proposal): array
    {
        return [
            'id' => $proposal->getKey(),
            'demand_id' => (int) $proposal->demo_workforce_demand_id,
            'capacity_id' => (int) $proposal->demo_training_capacity_id,
            'school_id' => (string) $proposal->school_id,
            'quantity' => (int) $proposal->quantity,
            'plan' => $proposal->plan,
            'status' => $proposal->status,
            'updated_at' => $proposal->updated_at?->toIso8601String(),
        ];
    }

    private function fieldProposalData(DemoCareerFieldProposal $proposal): array
    {
        return [
            'id' => $proposal->getKey(),
            'school_id' => (string) $proposal->school_id,
            'career_field_id' => $proposal->career_field_id,
            'quantity' => (int) $proposal->quantity,
            'plan' => $proposal->plan,
            'status' => $proposal->status,
            'updated_at' => $proposal->updated_at?->toIso8601String(),
        ];
    }

    private function capacityMatchesDemand(DemoTrainingCapacity $capacity, DemoWorkforceDemand $demand): bool
    {
        return (int) $capacity->training_major_id === (int) $demand->training_major_id
            && (string) $capacity->education_level_id === (string) $demand->education_level_id
            && (int) $capacity->reporting_period_id === (int) $demand->reporting_period_id;
    }

    /** @param array<int, string> $demandSkills @param array<int, string> $capacitySkills @return array<int, string> */
    private function skillGap(array $demandSkills, array $capacitySkills): array
    {
        $available = collect($capacitySkills)
            ->map(fn (string $skill): string => Skill::normalize($skill))
            ->all();

        return collect($demandSkills)
            ->filter(fn (string $skill): bool => ! in_array(Skill::normalize($skill), $available, true))
            ->values()
            ->all();
    }

    private function nullableText(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
