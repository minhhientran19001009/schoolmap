<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DemoWorkforceDemand;
use App\Models\Employer;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DemoBusinessPortalController extends Controller
{
    public function employers(): JsonResponse
    {
        $this->ensureEnabled();

        $employers = Employer::query()
            ->where(fn ($query) => $query->whereNull('status')->orWhere('status', 'ACTIVE'))
            ->orderByDesc('is_verified')
            ->orderBy('name')
            ->get()
            ->map(fn (Employer $employer): array => $this->employerData($employer))
            ->values();

        return response()->json(['employers' => $employers]);
    }

    public function selectEmployer(Request $request): JsonResponse
    {
        $this->ensureEnabled();

        $data = $request->validate([
            'employer_id' => ['required', 'integer', 'exists:employers,id'],
        ]);

        $employer = Employer::query()
            ->whereKey($data['employer_id'])
            ->where(fn ($query) => $query->whereNull('status')->orWhere('status', 'ACTIVE'))
            ->firstOrFail();

        $request->session()->put('demo_employer_id', (int) $employer->getKey());
        $request->session()->regenerate();

        return response()->json([
            'employer' => $this->employerData($employer),
        ]);
    }

    public function demands(Request $request): JsonResponse
    {
        $employer = $this->selectedEmployer($request);

        $demands = DemoWorkforceDemand::query()
            ->where('employer_id', $employer->getKey())
            ->with(['major:id,name', 'trainingLevel:id,name', 'period:id,name'])
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (DemoWorkforceDemand $demand): array => $this->demandData($demand))
            ->values();

        return response()->json([
            'employer' => $this->employerData($employer),
            'demands' => $demands,
        ]);
    }

    public function storeDemand(Request $request): JsonResponse
    {
        $employer = $this->selectedEmployer($request);
        $data = $request->validate([
            'training_major_id' => ['required', 'integer', 'exists:training_majors,id'],
            'training_level_id' => ['required', 'string', 'max:20', 'exists:training_levels,id'],
            'reporting_period_id' => ['required', 'integer', 'exists:reporting_periods,id'],
            'purpose' => ['required', Rule::in(['RECRUITMENT', 'NEW_TRAINING', 'RETRAINING'])],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'location' => ['required', 'string', 'max:200'],
            'age_range' => ['nullable', 'string', 'max:80'],
            'salary' => ['nullable', 'string', 'max:100'],
            'skills' => ['required', 'array', 'min:1', 'max:20'],
            'skills.*' => ['required', 'string', 'max:120'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $skills = collect($data['skills'])
            ->map(fn (string $name): string => trim($name))
            ->filter()
            ->unique(fn (string $name): string => Skill::normalize($name))
            ->values()
            ->all();

        $demand = DemoWorkforceDemand::query()->updateOrCreate(
            [
                'employer_id' => $employer->getKey(),
                'training_major_id' => $data['training_major_id'],
                'education_level_id' => $data['training_level_id'],
                'reporting_period_id' => $data['reporting_period_id'],
                'purpose' => $data['purpose'],
            ],
            [
                'quantity' => $data['quantity'],
                'location' => trim($data['location']),
                'age_range' => $this->nullableText($data['age_range'] ?? null),
                'salary' => $this->nullableText($data['salary'] ?? null),
                'skills' => $skills,
                'note' => $this->nullableText($data['note'] ?? null),
                'status' => 'ACTIVE',
            ],
        );

        $demand->load(['major:id,name', 'trainingLevel:id,name', 'period:id,name']);

        return response()->json([
            'message' => 'Đã lưu nhu cầu nhân lực.',
            'demand' => $this->demandData($demand),
        ], $demand->wasRecentlyCreated ? 201 : 200);
    }

    private function selectedEmployer(Request $request): Employer
    {
        $this->ensureEnabled();

        $employerId = (int) $request->session()->get('demo_employer_id', 0);
        abort_if($employerId < 1, 401, 'Chưa chọn doanh nghiệp.');

        return Employer::query()
            ->whereKey($employerId)
            ->where(fn ($query) => $query->whereNull('status')->orWhere('status', 'ACTIVE'))
            ->firstOrFail();
    }

    private function ensureEnabled(): void
    {
        abort_unless(config('demo_portal.enabled'), 404);
    }

    /** @return array<string, mixed> */
    private function employerData(Employer $employer): array
    {
        return [
            'id' => (int) $employer->getKey(),
            'name' => $employer->name,
            'tax_code' => $employer->tax_code,
            'industry' => $employer->industry,
            'region' => $employer->region,
            'employee_count' => $employer->employee_count,
            'address' => $employer->address,
            'is_verified' => (bool) $employer->is_verified,
        ];
    }

    /** @return array<string, mixed> */
    private function demandData(DemoWorkforceDemand $demand): array
    {
        return [
            'id' => $demand->getKey(),
            'employer_id' => (int) $demand->employer_id,
            'major' => $demand->major?->name,
            'level' => $demand->trainingLevel?->name,
            'period' => $demand->period?->name,
            'purpose' => $demand->purpose,
            'quantity' => (int) $demand->quantity,
            'location' => $demand->location,
            'age_range' => $demand->age_range,
            'salary' => $demand->salary,
            'skills' => $demand->skills ?? [],
            'note' => $demand->note,
            'status' => $demand->status,
            'updated_at' => $demand->updated_at?->toIso8601String(),
        ];
    }

    private function nullableText(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
