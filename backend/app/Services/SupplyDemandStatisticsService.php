<?php

namespace App\Services;

use App\Models\ReportingPeriod;
use App\Models\TrainingCapacity;
use App\Models\WorkforceDemand;
use Illuminate\Support\Facades\Schema;

class SupplyDemandStatisticsService
{
    /** @return array<int, string> */
    public function periodOptions(): array
    {
        if (! Schema::hasTable('reporting_periods')) {
            return [];
        }

        return ReportingPeriod::query()
            ->orderByDesc('starts_at')
            ->pluck('name', 'id')
            ->all();
    }

    public function defaultPeriod(): ?ReportingPeriod
    {
        if (! Schema::hasTable('reporting_periods')) {
            return null;
        }

        return ReportingPeriod::query()
            ->where('is_active', true)
            ->whereDate('starts_at', '<=', today())
            ->whereDate('ends_at', '>=', today())
            ->orderByDesc('starts_at')
            ->first()
            ?? ReportingPeriod::query()
                ->where('is_active', true)
                ->orderByDesc('starts_at')
                ->first()
            ?? ReportingPeriod::query()->orderByDesc('starts_at')->first();
    }

    /**
     * @return array{
     *     period: ?ReportingPeriod,
     *     totals: array{demand: int, capacity: int, gap: int, proposed: int},
     *     rows: array<int, array<string, mixed>>,
     *     priorities: array<int, array<string, mixed>>,
     *     max_value: int
     * }
     */
    public function build(int|string|null $periodId = null): array
    {
        if (! $this->hasRequiredTables()) {
            return $this->emptyResult();
        }

        $period = filled($periodId)
            ? ReportingPeriod::query()->find($periodId)
            : $this->defaultPeriod();

        if ($period === null) {
            return $this->emptyResult();
        }

        $demands = WorkforceDemand::query()
            ->where('reporting_period_id', $period->getKey())
            ->where('status', 'PUBLISHED')
            ->where('is_sample', false)
            ->with([
                'major:id,name',
                'trainingLevel:id,name',
                'proposals' => fn ($query) => $query
                    ->where('status', '!=', 'WITHDRAWN')
                    ->where('is_sample', false),
            ])
            ->get();

        $capacities = TrainingCapacity::query()
            ->where('reporting_period_id', $period->getKey())
            ->where('status', 'ACTIVE')
            ->where('is_sample', false)
            ->with(['major:id,name', 'trainingLevel:id,name'])
            ->get();

        $rows = [];

        foreach ($demands as $demand) {
            $key = $demand->training_major_id.'|'.$demand->education_level_id;
            $rows[$key] ??= $this->emptyRow(
                (int) $demand->training_major_id,
                (string) ($demand->major?->name ?? 'Chưa xác định'),
                (string) $demand->education_level_id,
                (string) ($demand->trainingLevel?->name ?? $demand->education_level_id),
            );
            $rows[$key]['demand'] += (int) $demand->quantity;
            $rows[$key]['proposed'] += (int) $demand->proposals->sum('quantity');
        }

        foreach ($capacities as $capacity) {
            $key = $capacity->training_major_id.'|'.$capacity->education_level_id;
            $rows[$key] ??= $this->emptyRow(
                (int) $capacity->training_major_id,
                (string) ($capacity->major?->name ?? 'Chưa xác định'),
                (string) $capacity->education_level_id,
                (string) ($capacity->trainingLevel?->name ?? $capacity->education_level_id),
            );
            $rows[$key]['capacity'] += (int) $capacity->quantity;
        }

        foreach ($rows as &$row) {
            $row['gap'] = max(0, $row['demand'] - $row['capacity']);
            $row['surplus'] = max(0, $row['capacity'] - $row['demand']);
        }
        unset($row);

        $rows = array_values($rows);
        usort($rows, fn (array $left, array $right): int => [$right['gap'], $right['demand'], $right['capacity']]
            <=> [$left['gap'], $left['demand'], $left['capacity']]
        );

        $priorities = array_values(array_slice(array_filter(
            $rows,
            fn (array $row): bool => $row['gap'] > 0,
        ), 0, 5));

        $maxValue = 1;
        foreach ($rows as $row) {
            $maxValue = max($maxValue, $row['demand'], $row['capacity']);
        }

        return [
            'period' => $period,
            'totals' => [
                'demand' => (int) $demands->sum('quantity'),
                'capacity' => (int) $capacities->sum('quantity'),
                'gap' => (int) collect($rows)->sum('gap'),
                'proposed' => (int) collect($rows)->sum('proposed'),
            ],
            'rows' => $rows,
            'priorities' => $priorities,
            'max_value' => $maxValue,
        ];
    }

    /** @return array<string, mixed> */
    private function emptyRow(int $majorId, string $major, string $levelId, string $level): array
    {
        return [
            'training_major_id' => $majorId,
            'major' => $major,
            'training_level_id' => $levelId,
            'level' => $level,
            'demand' => 0,
            'capacity' => 0,
            'gap' => 0,
            'surplus' => 0,
            'proposed' => 0,
        ];
    }

    private function hasRequiredTables(): bool
    {
        return Schema::hasTable('reporting_periods')
            && Schema::hasTable('workforce_demands')
            && Schema::hasTable('training_capacities')
            && Schema::hasTable('demand_proposals');
    }

    /** @return array<string, mixed> */
    private function emptyResult(): array
    {
        return [
            'period' => null,
            'totals' => ['demand' => 0, 'capacity' => 0, 'gap' => 0, 'proposed' => 0],
            'rows' => [],
            'priorities' => [],
            'max_value' => 1,
        ];
    }
}
