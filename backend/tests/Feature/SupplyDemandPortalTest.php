<?php

namespace Tests\Feature;

use App\Services\SupplyDemandStatisticsService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SupplyDemandPortalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('reporting_periods', function (Blueprint $table): void {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('training_majors', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('training_levels', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('name');
            $table->unsignedInteger('display_order')->default(1);
            $table->boolean('is_active')->default(true);
        });
        Schema::create('employers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->json('aliases')->nullable();
            $table->string('industry')->nullable();
            $table->string('region')->nullable();
            $table->unsignedInteger('employee_count')->nullable();
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->text('data_note')->nullable();
            $table->date('reviewed_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
        });
        Schema::create('schools', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('ward')->nullable();
            $table->timestamps();
        });
        Schema::create('skills', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('normalized_name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('workforce_demands', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('employer_id');
            $table->unsignedBigInteger('training_major_id');
            $table->string('education_level_id');
            $table->unsignedBigInteger('reporting_period_id');
            $table->string('purpose')->default('RECRUITMENT');
            $table->unsignedInteger('quantity');
            $table->string('location')->nullable();
            $table->string('salary')->nullable();
            $table->string('status')->default('PUBLISHED');
            $table->boolean('is_sample')->default(false);
            $table->timestamps();
        });
        Schema::create('training_capacities', function (Blueprint $table): void {
            $table->id();
            $table->string('school_id');
            $table->unsignedBigInteger('training_major_id');
            $table->string('education_level_id');
            $table->unsignedBigInteger('reporting_period_id');
            $table->unsignedInteger('quantity');
            $table->string('location')->nullable();
            $table->string('duration')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->boolean('is_sample')->default(false);
            $table->timestamps();
        });
        Schema::create('demand_skill', function (Blueprint $table): void {
            $table->unsignedBigInteger('workforce_demand_id');
            $table->unsignedBigInteger('skill_id');
        });
        Schema::create('capacity_skill', function (Blueprint $table): void {
            $table->unsignedBigInteger('training_capacity_id');
            $table->unsignedBigInteger('skill_id');
        });
        Schema::create('demand_proposals', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('workforce_demand_id');
            $table->unsignedBigInteger('training_capacity_id');
            $table->unsignedInteger('quantity');
            $table->string('status')->default('SUBMITTED');
            $table->boolean('is_sample')->default(false);
            $table->timestamps();
        });

        DB::table('reporting_periods')->insert([
            'id' => 1, 'code' => '2026-Q4', 'name' => 'Quý IV/2026',
            'starts_at' => '2026-10-01', 'ends_at' => '2026-12-31', 'is_active' => true,
        ]);
        DB::table('training_majors')->insert(['id' => 1, 'name' => 'Cơ điện tử', 'slug' => 'co-dien-tu', 'is_active' => true]);
        DB::table('training_levels')->insert([
            ['id' => 'cao_dang', 'name' => 'Cao đẳng', 'display_order' => 2, 'is_active' => true],
            ['id' => 'trung_cap', 'name' => 'Trung cấp', 'display_order' => 1, 'is_active' => true],
        ]);
        DB::table('employers')->insert([
            ['id' => 1, 'name' => 'Doanh nghiệp Điện tử A', 'industry' => 'Điện tử', 'status' => 'ACTIVE'],
            ['id' => 2, 'name' => 'Doanh nghiệp Cơ khí B', 'industry' => 'Cơ khí', 'status' => 'ACTIVE'],
        ]);
        DB::table('schools')->insert([
            ['id' => 'school-a', 'name' => 'Cơ sở GDNN A', 'address' => 'Ninh Bình'],
            ['id' => 'school-b', 'name' => 'Cơ sở GDNN B', 'address' => 'Tam Điệp'],
        ]);
        DB::table('skills')->insert([
            ['id' => 1, 'name' => 'PLC', 'normalized_name' => 'plc', 'is_active' => true],
            ['id' => 2, 'name' => 'Lắp ráp', 'normalized_name' => 'lap rap', 'is_active' => true],
        ]);
        DB::table('workforce_demands')->insert([
            ['id' => 1, 'employer_id' => 1, 'training_major_id' => 1, 'education_level_id' => 'cao_dang', 'reporting_period_id' => 1, 'quantity' => 100, 'location' => 'Gia Viễn', 'status' => 'PUBLISHED', 'is_sample' => false],
            ['id' => 2, 'employer_id' => 2, 'training_major_id' => 1, 'education_level_id' => 'trung_cap', 'reporting_period_id' => 1, 'quantity' => 80, 'location' => 'Tam Điệp', 'status' => 'PUBLISHED', 'is_sample' => false],
        ]);
        DB::table('training_capacities')->insert([
            ['id' => 1, 'school_id' => 'school-a', 'training_major_id' => 1, 'education_level_id' => 'cao_dang', 'reporting_period_id' => 1, 'quantity' => 60, 'location' => 'Ninh Bình', 'status' => 'ACTIVE', 'is_sample' => false],
            ['id' => 2, 'school_id' => 'school-b', 'training_major_id' => 1, 'education_level_id' => 'trung_cap', 'reporting_period_id' => 1, 'quantity' => 50, 'location' => 'Tam Điệp', 'status' => 'ACTIVE', 'is_sample' => false],
        ]);
        DB::table('demand_skill')->insert([
            ['workforce_demand_id' => 1, 'skill_id' => 1],
            ['workforce_demand_id' => 1, 'skill_id' => 2],
            ['workforce_demand_id' => 2, 'skill_id' => 2],
        ]);
        DB::table('capacity_skill')->insert([
            ['training_capacity_id' => 1, 'skill_id' => 1],
            ['training_capacity_id' => 2, 'skill_id' => 2],
        ]);
    }

    public function test_portal_aggregates_demands_and_schools_by_profession(): void
    {
        $this->getJson('/api/supply-demand/portal?reporting_period_id=1')
            ->assertOk()
            ->assertJsonPath('professions.0.major', 'Cơ điện tử')
            ->assertJsonPath('professions.0.demand', 180)
            ->assertJsonPath('professions.0.capacity', 110)
            ->assertJsonPath('professions.0.gap', 70)
            ->assertJsonPath('professions.0.companies', 2)
            ->assertJsonPath('professions.0.school_count', 2)
            ->assertJsonCount(2, 'professions.0.levels')
            ->assertJsonCount(2, 'professions.0.schools')
            ->assertJsonCount(2, 'professions.0.demands')
            ->assertJsonFragment(['skills' => ['PLC', 'Lắp ráp']]);
    }

    public function test_admin_statistics_sum_shortages_per_major_and_level(): void
    {
        DB::table('training_capacities')->insert([
            'id' => 3,
            'school_id' => 'school-a',
            'training_major_id' => 1,
            'education_level_id' => 'trung_cap',
            'reporting_period_id' => 1,
            'quantity' => 100,
            'location' => 'Ninh Bình',
            'status' => 'ACTIVE',
            'is_sample' => false,
        ]);
        DB::table('demand_proposals')->insert([
            'id' => 1,
            'workforce_demand_id' => 1,
            'training_capacity_id' => 1,
            'quantity' => 25,
            'status' => 'SUBMITTED',
            'is_sample' => false,
        ]);

        $statistics = app(SupplyDemandStatisticsService::class)->build(1);

        $this->assertSame(180, $statistics['totals']['demand']);
        $this->assertSame(210, $statistics['totals']['capacity']);
        $this->assertSame(40, $statistics['totals']['gap']);
        $this->assertSame(25, $statistics['totals']['proposed']);
        $this->assertSame('Cao đẳng', $statistics['priorities'][0]['level']);
        $this->assertSame(40, $statistics['priorities'][0]['gap']);
    }
}
