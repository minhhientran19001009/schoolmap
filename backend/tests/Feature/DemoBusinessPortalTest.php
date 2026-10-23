<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DemoBusinessPortalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('demo_portal.enabled', true);

        Schema::create('employers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('tax_code')->nullable();
            $table->string('industry')->nullable();
            $table->string('region')->nullable();
            $table->unsignedInteger('employee_count')->nullable();
            $table->string('address')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
        Schema::create('training_majors', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('training_levels', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('name');
        });
        Schema::create('reporting_periods', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('demo_workforce_demands', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('employer_id');
            $table->unsignedBigInteger('training_major_id');
            $table->string('education_level_id');
            $table->unsignedBigInteger('reporting_period_id');
            $table->string('purpose');
            $table->unsignedInteger('quantity');
            $table->string('location');
            $table->string('age_range')->nullable();
            $table->string('salary')->nullable();
            $table->json('skills');
            $table->text('note')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
            $table->unique(['employer_id', 'training_major_id', 'education_level_id', 'reporting_period_id', 'purpose']);
        });

        DB::table('employers')->insert([
            ['id' => 701, 'name' => 'Doanh nghiệp A', 'tax_code' => '2700000001', 'industry' => 'Cơ khí', 'region' => 'Ninh Bình', 'address' => 'KCN A', 'status' => 'ACTIVE', 'is_verified' => true],
            ['id' => 702, 'name' => 'Doanh nghiệp B', 'tax_code' => '2700000002', 'industry' => 'Điện tử', 'region' => 'Ninh Bình', 'address' => 'KCN B', 'status' => 'ACTIVE', 'is_verified' => true],
        ]);
        DB::table('training_majors')->insert(['id' => 701, 'name' => 'Công nghệ ô tô']);
        DB::table('training_levels')->insert(['id' => 'cao_dang', 'name' => 'Cao đẳng']);
        DB::table('reporting_periods')->insert(['id' => 701, 'name' => 'Quý IV/2026']);
    }

    public function test_demand_is_saved_and_scoped_to_the_selected_employer_session(): void
    {
        $this->getJson('/api/demo/business-portal/employers')
            ->assertOk()
            ->assertJsonCount(2, 'employers');

        $this->postJson('/api/demo/business-portal/select-employer', ['employer_id' => 701])
            ->assertOk()
            ->assertJsonPath('employer.id', 701);

        $this->postJson('/api/demo/business-portal/demands', [
            'employer_id' => 702,
            'training_major_id' => 701,
            'training_level_id' => 'cao_dang',
            'reporting_period_id' => 701,
            'purpose' => 'RECRUITMENT',
            'quantity' => 80,
            'location' => 'KCN A',
            'skills' => ['Lắp ráp', 'Đọc bản vẽ'],
        ])->assertCreated()
            ->assertJsonPath('demand.employer_id', 701)
            ->assertJsonPath('demand.quantity', 80);

        $this->postJson('/api/demo/business-portal/select-employer', ['employer_id' => 702])
            ->assertOk();

        $this->getJson('/api/demo/business-portal/demands')
            ->assertOk()
            ->assertJsonCount(0, 'demands');

        $this->assertDatabaseHas('demo_workforce_demands', ['employer_id' => 701, 'quantity' => 80]);
        $this->assertDatabaseMissing('demo_workforce_demands', ['employer_id' => 702]);
    }
}
