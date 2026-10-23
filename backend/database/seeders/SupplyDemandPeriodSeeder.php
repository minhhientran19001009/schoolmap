<?php

namespace Database\Seeders;

use App\Models\ReportingPeriod;
use Illuminate\Database\Seeder;

class SupplyDemandPeriodSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['code' => '2026-Q4', 'name' => 'Quý IV/2026', 'starts_at' => '2026-10-01', 'ends_at' => '2026-12-31'],
            ['code' => '2027-Q1', 'name' => 'Quý I/2027', 'starts_at' => '2027-01-01', 'ends_at' => '2027-03-31'],
        ] as $period) {
            ReportingPeriod::query()->updateOrCreate(
                ['code' => $period['code']],
                [...$period, 'is_active' => true],
            );
        }
    }
}
