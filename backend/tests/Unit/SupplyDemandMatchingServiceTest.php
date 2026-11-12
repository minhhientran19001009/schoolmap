<?php

namespace Tests\Unit;

use App\Models\TrainingCapacity;
use App\Models\WorkforceDemand;
use App\Services\SupplyDemandMatchingService;
use Tests\TestCase;

class SupplyDemandMatchingServiceTest extends TestCase
{
    public function test_skill_gap_is_case_and_accent_insensitive(): void
    {
        $demand = new WorkforceDemand(['skills' => ['Kỹ năng PLC', 'Đọc bản vẽ']]);
        $capacity = new TrainingCapacity(['skills' => ['KY NANG PLC']]);

        $gap = app(SupplyDemandMatchingService::class)->skillGap($demand, $capacity);

        $this->assertSame(['Đọc bản vẽ'], $gap);
    }
}
