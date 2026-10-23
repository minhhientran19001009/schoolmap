<?php

namespace App\Services;

use App\Models\DemandProposal;
use App\Models\Skill;
use App\Models\TrainingCapacity;
use App\Models\WorkforceDemand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplyDemandMatchingService
{
    public function matchingCapacitiesQuery(WorkforceDemand $demand): Builder
    {
        return TrainingCapacity::query()
            ->where('training_major_id', $demand->training_major_id)
            ->where('education_level_id', $demand->education_level_id)
            ->where('reporting_period_id', $demand->reporting_period_id)
            ->where('status', 'ACTIVE');
    }

    public function remainingDemand(WorkforceDemand $demand): int
    {
        $proposed = $demand->proposals()
            ->where('status', '!=', 'WITHDRAWN')
            ->sum('quantity');

        return max(0, (int) $demand->quantity - (int) $proposed);
    }

    public function remainingCapacity(TrainingCapacity $capacity): int
    {
        $proposed = $capacity->proposals()
            ->where('status', '!=', 'WITHDRAWN')
            ->sum('quantity');

        return max(0, (int) $capacity->quantity - (int) $proposed);
    }

    /** @return array<int, string> */
    public function skillGap(WorkforceDemand $demand, TrainingCapacity $capacity): array
    {
        $capacitySkills = $capacity->skills
            ->map(fn (Skill $skill): string => $skill->normalized_name ?: Skill::normalize($skill->name))
            ->all();

        return $demand->skills
            ->reject(fn (Skill $skill): bool => in_array(
                $skill->normalized_name ?: Skill::normalize($skill->name),
                $capacitySkills,
                true,
            ))
            ->pluck('name')
            ->values()
            ->all();
    }

    public function createProposal(
        WorkforceDemand $demand,
        TrainingCapacity $capacity,
        int $quantity,
        string $plan,
        ?int $createdBy = null,
    ): DemandProposal {
        if ($quantity < 1 || mb_strlen(trim($plan)) < 10) {
            throw ValidationException::withMessages([
                'quantity' => 'Số lượng phải lớn hơn 0.',
                'plan' => 'Phương án đáp ứng phải có ít nhất 10 ký tự.',
            ]);
        }

        return DB::transaction(function () use ($demand, $capacity, $quantity, $plan, $createdBy): DemandProposal {
            $lockedDemand = WorkforceDemand::query()->lockForUpdate()->findOrFail($demand->getKey());
            $lockedCapacity = TrainingCapacity::query()->lockForUpdate()->findOrFail($capacity->getKey());

            $matches = $lockedDemand->status === 'PUBLISHED'
                && $lockedCapacity->status === 'ACTIVE'
                && (int) $lockedDemand->training_major_id === (int) $lockedCapacity->training_major_id
                && (string) $lockedDemand->education_level_id === (string) $lockedCapacity->education_level_id
                && (int) $lockedDemand->reporting_period_id === (int) $lockedCapacity->reporting_period_id;

            $maximum = min(
                $this->remainingDemand($lockedDemand),
                $this->remainingCapacity($lockedCapacity),
            );

            if (! $matches || $quantity > $maximum) {
                throw ValidationException::withMessages([
                    'quantity' => 'Nhu cầu, năng lực hoặc số lượng còn lại đã thay đổi. Vui lòng tải lại dữ liệu.',
                ]);
            }

            return DemandProposal::create([
                'workforce_demand_id' => $lockedDemand->getKey(),
                'training_capacity_id' => $lockedCapacity->getKey(),
                'quantity' => $quantity,
                'plan' => trim($plan),
                'status' => 'SUBMITTED',
                'created_by' => $createdBy,
                'source' => 'SYSTEM',
            ]);
        }, 3);
    }
}
