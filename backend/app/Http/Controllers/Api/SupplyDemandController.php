<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\ReportingPeriod;
use App\Models\TrainingCapacity;
use App\Models\TrainingLevel;
use App\Models\TrainingMajor;
use App\Models\WorkforceDemand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SupplyDemandController extends Controller
{
    public function catalogs(): JsonResponse
    {
        return response()->json([
            'training_majors' => TrainingMajor::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
            'training_levels' => TrainingLevel::query()
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get(['id', 'name']),
            'reporting_periods' => ReportingPeriod::query()
                ->where('is_active', true)
                ->orderByDesc('starts_at')
                ->get(['id', 'code', 'name', 'starts_at', 'ends_at']),
        ]);
    }

    public function portal(Request $request): JsonResponse
    {
        $period = $this->resolvePeriod($request);
        $search = $this->normalize((string) $request->input('q', ''));
        if ($period === null) {
            return response()->json([
                'period' => null,
                'totals' => ['demand' => 0, 'capacity' => 0, 'gap' => 0, 'proposed' => 0],
                'summary' => [],
                'professions' => [],
                'demands' => [],
                'capacities' => [],
                'employers' => $this->publicEmployers($search),
            ]);
        }

        $majorId = $request->integer('training_major_id') ?: null;
        $levelId = trim((string) $request->input('training_level_id', '')) ?: null;

        $demands = WorkforceDemand::query()
            ->where('reporting_period_id', $period->id)
            ->where('status', 'PUBLISHED')
            ->where('is_sample', false)
            ->when($majorId, fn ($query) => $query->where('training_major_id', $majorId))
            ->when($levelId, fn ($query) => $query->where('education_level_id', $levelId))
            ->with([
                'employer:id,name,industry,address',
                'major:id,name,slug',
                'trainingLevel:id,name',
                'skills:id,name,normalized_name',
                'proposals' => fn ($query) => $query->where('status', '!=', 'WITHDRAWN'),
            ])
            ->orderByDesc('created_at')
            ->get();

        $capacities = TrainingCapacity::query()
            ->where('reporting_period_id', $period->id)
            ->where('status', 'ACTIVE')
            ->where('is_sample', false)
            ->when($majorId, fn ($query) => $query->where('training_major_id', $majorId))
            ->when($levelId, fn ($query) => $query->where('education_level_id', $levelId))
            ->with([
                'school:id,name,address,ward',
                'major:id,name,slug',
                'trainingLevel:id,name',
                'skills:id,name,normalized_name',
                'proposals' => fn ($query) => $query->where('status', '!=', 'WITHDRAWN'),
            ])
            ->orderByDesc('created_at')
            ->get();

        if ($search !== '') {
            $demands = $demands->filter(fn (WorkforceDemand $demand): bool => $this->containsSearch([
                $demand->employer?->name,
                $demand->major?->name,
                $demand->location,
                ...$demand->skills->pluck('name')->all(),
            ], $search))->values();

            $capacities = $capacities->filter(fn (TrainingCapacity $capacity): bool => $this->containsSearch([
                $capacity->school?->name,
                $capacity->major?->name,
                $capacity->location,
                $capacity->school?->address,
                ...$capacity->skills->pluck('name')->all(),
            ], $search))->values();
        }

        $summary = $this->buildSummary($demands, $capacities);
        $professions = $this->buildProfessions($demands, $capacities);
        $proposed = $demands->sum(fn (WorkforceDemand $demand): int => (int) $demand->proposals->sum('quantity'));
        $totalDemand = (int) $demands->sum('quantity');
        $totalCapacity = (int) $capacities->sum('quantity');

        return response()->json([
            'period' => $period->only(['id', 'code', 'name', 'starts_at', 'ends_at']),
            'totals' => [
                'demand' => $totalDemand,
                'capacity' => $totalCapacity,
                // Shortages are calculated for each major and education level.
                // Capacity from another group must not hide a real shortage.
                'gap' => (int) collect($summary)->sum('gap'),
                'proposed' => (int) $proposed,
            ],
            'summary' => $summary,
            'professions' => $professions,
            'demands' => $demands->map(fn (WorkforceDemand $demand): array => [
                'id' => $demand->id,
                'employer' => $demand->employer?->name,
                'industry' => $demand->employer?->industry,
                'training_major_id' => $demand->training_major_id,
                'major' => $demand->major?->name,
                'training_level_id' => $demand->education_level_id,
                'level' => $demand->trainingLevel?->name,
                'purpose' => $demand->purpose,
                'quantity' => $demand->quantity,
                'proposed' => (int) $demand->proposals->sum('quantity'),
                'remaining' => max(0, $demand->quantity - (int) $demand->proposals->sum('quantity')),
                'location' => $demand->location,
                'salary' => $demand->salary,
                'skills' => $demand->skills->pluck('name')->values(),
            ])->values(),
            'capacities' => $capacities->map(fn (TrainingCapacity $capacity): array => [
                'id' => $capacity->id,
                'school_id' => $capacity->school_id,
                'school' => $capacity->school?->name,
                'address' => $capacity->school?->address,
                'training_major_id' => $capacity->training_major_id,
                'major' => $capacity->major?->name,
                'training_level_id' => $capacity->education_level_id,
                'level' => $capacity->trainingLevel?->name,
                'quantity' => $capacity->quantity,
                'proposed' => (int) $capacity->proposals->sum('quantity'),
                'remaining' => max(0, $capacity->quantity - (int) $capacity->proposals->sum('quantity')),
                'location' => $capacity->location ?: $capacity->school?->address,
                'duration' => $capacity->duration,
                'skills' => $capacity->skills->pluck('name')->values(),
            ])->values(),
            'employers' => $this->publicEmployers($search),
        ]);
    }

    private function resolvePeriod(Request $request): ?ReportingPeriod
    {
        if ($request->filled('reporting_period_id')) {
            return ReportingPeriod::query()->where('is_active', true)->find($request->integer('reporting_period_id'));
        }

        $current = ReportingPeriod::query()
            ->where('is_active', true)
            ->whereDate('starts_at', '<=', today())
            ->whereDate('ends_at', '>=', today())
            ->orderBy('starts_at')
            ->first();

        if ($current !== null) {
            return $current;
        }

        return ReportingPeriod::query()
            ->where('is_active', true)
            ->orderByRaw('CASE WHEN starts_at >= ? THEN 0 ELSE 1 END', [today()->toDateString()])
            ->orderBy('starts_at')
            ->first();
    }

    /** @return array<int, array<string, mixed>> */
    private function buildSummary(Collection $demands, Collection $capacities): array
    {
        $rows = [];

        foreach ($demands as $demand) {
            $key = $demand->training_major_id.'|'.$demand->education_level_id;
            $rows[$key] ??= [
                'training_major_id' => $demand->training_major_id,
                'major' => $demand->major?->name,
                'training_level_id' => $demand->education_level_id,
                'level' => $demand->trainingLevel?->name,
                'demand' => 0,
                'capacity' => 0,
                'gap' => 0,
                'surplus' => 0,
                'proposed' => 0,
                '_employer_ids' => [],
                '_school_ids' => [],
            ];
            $rows[$key]['demand'] += (int) $demand->quantity;
            $rows[$key]['proposed'] += (int) $demand->proposals->sum('quantity');
            $rows[$key]['_employer_ids'][(int) $demand->employer_id] = true;
        }

        foreach ($capacities as $capacity) {
            $key = $capacity->training_major_id.'|'.$capacity->education_level_id;
            $rows[$key] ??= [
                'training_major_id' => $capacity->training_major_id,
                'major' => $capacity->major?->name,
                'training_level_id' => $capacity->education_level_id,
                'level' => $capacity->trainingLevel?->name,
                'demand' => 0,
                'capacity' => 0,
                'gap' => 0,
                'surplus' => 0,
                'proposed' => 0,
                '_employer_ids' => [],
                '_school_ids' => [],
            ];
            $rows[$key]['capacity'] += (int) $capacity->quantity;
            $rows[$key]['_school_ids'][(string) $capacity->school_id] = true;
        }

        foreach ($rows as &$row) {
            $row['gap'] = max(0, $row['demand'] - $row['capacity']);
            $row['surplus'] = max(0, $row['capacity'] - $row['demand']);
            $row['companies'] = count($row['_employer_ids']);
            $row['schools'] = count($row['_school_ids']);
            unset($row['_employer_ids'], $row['_school_ids']);
        }

        return collect($rows)
            ->sortByDesc('gap')
            ->values()
            ->all();
    }

    /** @return array<int, array<string, mixed>> */
    private function buildProfessions(Collection $demands, Collection $capacities): array
    {
        $rows = [];

        foreach ($demands as $demand) {
            $majorId = (int) $demand->training_major_id;
            $rows[$majorId] ??= $this->emptyProfession($majorId, $demand->major?->name);
            $row = &$rows[$majorId];
            $quantity = (int) $demand->quantity;
            $proposed = (int) $demand->proposals->sum('quantity');
            $levelId = (string) $demand->education_level_id;

            $row['demand'] += $quantity;
            $row['proposed'] += $proposed;
            $row['_employer_ids'][(int) $demand->employer_id] = true;
            $row['_levels'][$levelId] ??= [
                'id' => $levelId,
                'name' => $demand->trainingLevel?->name,
                'demand' => 0,
                'capacity' => 0,
            ];
            $row['_levels'][$levelId]['demand'] += $quantity;

            foreach ($demand->skills as $skill) {
                $row['_skills'][$skill->normalized_name ?: $this->normalize($skill->name)] = $skill->name;
            }

            $row['demands'][] = [
                'id' => (int) $demand->getKey(),
                'employer' => $demand->employer?->name,
                'industry' => $demand->employer?->industry,
                'level_id' => $levelId,
                'level' => $demand->trainingLevel?->name,
                'quantity' => $quantity,
                'remaining' => max(0, $quantity - $proposed),
                'location' => $demand->location,
                'salary' => $demand->salary,
                'skills' => $demand->skills->pluck('name')->values()->all(),
            ];
            unset($row);
        }

        foreach ($capacities as $capacity) {
            $majorId = (int) $capacity->training_major_id;
            $rows[$majorId] ??= $this->emptyProfession($majorId, $capacity->major?->name);
            $row = &$rows[$majorId];
            $quantity = (int) $capacity->quantity;
            $levelId = (string) $capacity->education_level_id;
            $schoolId = (string) $capacity->school_id;
            $schoolKey = $schoolId.'|'.$levelId;

            $row['capacity'] += $quantity;
            $row['_school_ids'][$schoolId] = true;
            $row['_levels'][$levelId] ??= [
                'id' => $levelId,
                'name' => $capacity->trainingLevel?->name,
                'demand' => 0,
                'capacity' => 0,
            ];
            $row['_levels'][$levelId]['capacity'] += $quantity;

            foreach ($capacity->skills as $skill) {
                $row['_capacity_skills'][$skill->normalized_name ?: $this->normalize($skill->name)] = $skill->name;
            }

            $row['_schools'][$schoolKey] ??= [
                'id' => $schoolId,
                'name' => $capacity->school?->name,
                'level_id' => $levelId,
                'level' => $capacity->trainingLevel?->name,
                'location' => $capacity->location ?: $capacity->school?->address,
                'address' => $capacity->school?->address,
                'duration' => $capacity->duration,
                'capacity' => 0,
                'skills' => [],
            ];
            $row['_schools'][$schoolKey]['capacity'] += $quantity;
            $row['_schools'][$schoolKey]['skills'] = collect([
                ...$row['_schools'][$schoolKey]['skills'],
                ...$capacity->skills->pluck('name')->all(),
            ])->unique(fn (string $skill): string => $this->normalize($skill))->values()->all();
            unset($row);
        }

        foreach ($rows as &$row) {
            $row['gap'] = max(0, $row['demand'] - $row['capacity']);
            $row['surplus'] = max(0, $row['capacity'] - $row['demand']);
            $row['companies'] = count($row['_employer_ids']);
            $row['school_count'] = count($row['_school_ids']);
            $row['levels'] = collect($row['_levels'])->sortBy('name')->values()->all();
            $row['skills'] = array_values($row['_skills']);
            $row['capacity_skills'] = array_values($row['_capacity_skills']);
            $row['schools'] = collect($row['_schools'])->sortBy('name')->values()->all();
            $row['demands'] = collect($row['demands'])->sortByDesc('quantity')->values()->all();
            unset(
                $row['_employer_ids'],
                $row['_school_ids'],
                $row['_levels'],
                $row['_skills'],
                $row['_capacity_skills'],
                $row['_schools'],
            );
        }

        return collect($rows)
            ->sortBy([
                ['demand', 'desc'],
                ['major', 'asc'],
            ])
            ->values()
            ->all();
    }

    /** @return array<string, mixed> */
    private function emptyProfession(int $majorId, ?string $major): array
    {
        return [
            'training_major_id' => $majorId,
            'major' => $major,
            'demand' => 0,
            'capacity' => 0,
            'gap' => 0,
            'surplus' => 0,
            'proposed' => 0,
            'companies' => 0,
            'school_count' => 0,
            'levels' => [],
            'skills' => [],
            'capacity_skills' => [],
            'schools' => [],
            'demands' => [],
            '_employer_ids' => [],
            '_school_ids' => [],
            '_levels' => [],
            '_skills' => [],
            '_capacity_skills' => [],
            '_schools' => [],
        ];
    }

    /** @param array<int, mixed> $values */
    private function containsSearch(array $values, string $search): bool
    {
        return str_contains($this->normalize(implode(' ', array_filter($values))), $search);
    }

    private function normalize(string $value): string
    {
        return Str::of($value)->ascii()->lower()->squish()->toString();
    }

    /** @return array<int, array<string, mixed>> */
    private function publicEmployers(string $search): array
    {
        return Employer::query()
            ->where('status', 'ACTIVE')
            ->orderByDesc('is_verified')
            ->orderByDesc('employee_count')
            ->orderBy('name')
            ->get([
                'id', 'name', 'aliases', 'industry', 'region', 'employee_count',
                'address', 'website', 'data_note', 'reviewed_at', 'is_verified',
            ])
            ->when($search !== '', fn ($items) => $items->filter(fn (Employer $employer): bool => $this->containsSearch([
                $employer->name,
                $employer->industry,
                $employer->region,
                $employer->address,
                ...($employer->aliases ?? []),
            ], $search)))
            ->map(fn (Employer $employer): array => [
                'id' => $employer->id,
                'name' => $employer->name,
                'aliases' => $employer->aliases ?? [],
                'industry' => $employer->industry,
                'region' => $employer->region,
                'employee_count' => $employer->employee_count,
                'address' => $employer->address,
                'website' => $employer->website,
                'note' => $employer->data_note,
                'reviewed_at' => $employer->reviewed_at?->format('Y-m-d'),
                'is_verified' => $employer->is_verified,
            ])
            ->values()
            ->all();
    }
}
