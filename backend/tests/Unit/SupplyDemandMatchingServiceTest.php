<?php

namespace Tests\Unit;

use App\Models\Skill;
use App\Models\TrainingCapacity;
use App\Models\WorkforceDemand;
use App\Services\SupplyDemandMatchingService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class SupplyDemandMatchingServiceTest extends TestCase
{
    public function test_skill_gap_is_case_and_accent_insensitive(): void
    {
        $demand = new WorkforceDemand();
        $demand->setRelation('skills', new Collection([
            new Skill(['name' => 'Kỹ năng PLC']),
            new Skill(['name' => 'Đọc bản vẽ']),
        ]));

        $capacity = new TrainingCapacity();
        $capacity->setRelation('skills', new Collection([
            new Skill(['name' => 'KY NANG PLC']),
        ]));

        $gap = app(SupplyDemandMatchingService::class)->skillGap($demand, $capacity);

        $this->assertSame(['Đọc bản vẽ'], $gap);
    }
}
