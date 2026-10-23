<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DemoSchoolPortalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('demo_portal.enabled', true);

        if (! Schema::hasTable('schools')) {
            Schema::create('schools', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('code')->nullable();
                $table->string('name');
                $table->string('status')->nullable();
                $table->string('address')->nullable();
                $table->string('ward')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('training_majors')) {
            Schema::create('training_majors', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('slug')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('training_levels')) {
            Schema::create('training_levels', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('name');
            });
        }

        if (! Schema::hasTable('reporting_periods')) {
            Schema::create('reporting_periods', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('demo_training_capacities')) {
            Schema::create('demo_training_capacities', function (Blueprint $table): void {
                $table->id();
                $table->string('school_id');
                $table->unsignedBigInteger('training_major_id');
                $table->string('education_level_id');
                $table->unsignedBigInteger('reporting_period_id');
                $table->unsignedInteger('quantity');
                $table->string('location');
                $table->string('duration')->nullable();
                $table->json('skills');
                $table->text('equipment')->nullable();
                $table->text('note')->nullable();
                $table->string('status')->default('ACTIVE');
                $table->timestamps();
                $table->unique(['school_id', 'training_major_id', 'education_level_id', 'reporting_period_id']);
            });
        }

        if (! Schema::hasTable('employers')) {
            Schema::create('employers', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('industry')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('demo_workforce_demands')) {
            Schema::create('demo_workforce_demands', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('employer_id');
                $table->unsignedBigInteger('training_major_id');
                $table->string('education_level_id');
                $table->unsignedBigInteger('reporting_period_id');
                $table->string('purpose');
                $table->unsignedInteger('quantity');
                $table->string('location');
                $table->json('skills');
                $table->text('note')->nullable();
                $table->string('status')->default('ACTIVE');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('demo_demand_proposals')) {
            Schema::create('demo_demand_proposals', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('demo_workforce_demand_id');
                $table->unsignedBigInteger('demo_training_capacity_id');
                $table->string('school_id');
                $table->unsignedInteger('quantity');
                $table->text('plan');
                $table->string('status')->default('SUBMITTED');
                $table->timestamps();
                $table->unique(['demo_workforce_demand_id', 'demo_training_capacity_id']);
            });
        }

        if (! Schema::hasTable('demo_career_field_proposals')) {
            Schema::create('demo_career_field_proposals', function (Blueprint $table): void {
                $table->id();
                $table->string('school_id');
                $table->string('career_field_id');
                $table->unsignedInteger('quantity');
                $table->text('plan');
                $table->string('status')->default('SUBMITTED');
                $table->timestamps();
                $table->unique(['school_id', 'career_field_id']);
            });
        }

        DB::table('schools')->insertOrIgnore([
            ['id' => 'school-a', 'code' => 'A01', 'name' => 'Cơ sở A', 'status' => 'ACTIVE', 'address' => 'Ninh Bình', 'ward' => 'Phường Hoa Lư'],
            ['id' => 'school-b', 'code' => 'B01', 'name' => 'Cơ sở B', 'status' => 'ACTIVE', 'address' => 'Tam Điệp', 'ward' => 'Phường Tam Điệp'],
        ]);
        DB::table('training_majors')->insertOrIgnore(['id' => 901, 'name' => 'Công nghệ ô tô', 'slug' => 'cong-nghe-o-to', 'is_active' => true]);
        DB::table('training_levels')->insertOrIgnore(['id' => 'cao_dang', 'name' => 'Cao đẳng']);
        DB::table('reporting_periods')->insertOrIgnore(['id' => 901, 'name' => 'Quý IV/2026']);
        DB::table('employers')->insertOrIgnore(['id' => 901, 'name' => 'Doanh nghiệp A', 'industry' => 'Cơ khí', 'status' => 'ACTIVE']);
    }

    public function test_capacity_is_saved_and_scoped_to_the_selected_school_session(): void
    {
        $this->postJson('/api/demo/school-portal/select-school', ['school_id' => 'school-a'])
            ->assertOk()
            ->assertJsonPath('school.id', 'school-a');

        $this->postJson('/api/demo/school-portal/capacities', [
            'school_id' => 'school-b',
            'training_major_id' => 901,
            'training_level_id' => 'cao_dang',
            'reporting_period_id' => 901,
            'quantity' => 120,
            'location' => 'Ninh Bình',
            'skills' => ['PLC', 'Đọc bản vẽ'],
        ])->assertCreated()
            ->assertJsonPath('capacity.school_id', 'school-a')
            ->assertJsonPath('capacity.quantity', 120);

        $this->postJson('/api/demo/school-portal/select-school', ['school_id' => 'school-b'])
            ->assertOk();

        $this->getJson('/api/demo/school-portal/capacities')
            ->assertOk()
            ->assertJsonCount(0, 'capacities');

        $this->assertDatabaseHas('demo_training_capacities', [
            'school_id' => 'school-a',
            'quantity' => 120,
        ]);
        $this->assertDatabaseMissing('demo_training_capacities', [
            'school_id' => 'school-b',
        ]);
    }

    public function test_school_can_only_propose_with_its_matching_capacity_and_within_limits(): void
    {
        $this->postJson('/api/demo/school-portal/select-school', ['school_id' => 'school-a'])->assertOk();
        $capacityId = $this->postJson('/api/demo/school-portal/capacities', [
            'training_major_id' => 901,
            'training_level_id' => 'cao_dang',
            'reporting_period_id' => 901,
            'quantity' => 100,
            'location' => 'Ninh Bình',
            'skills' => ['PLC', 'Lắp ráp'],
        ])->assertCreated()->json('capacity.id');

        $demandId = DB::table('demo_workforce_demands')->insertGetId([
            'employer_id' => 901,
            'training_major_id' => 901,
            'education_level_id' => 'cao_dang',
            'reporting_period_id' => 901,
            'purpose' => 'RECRUITMENT',
            'quantity' => 180,
            'location' => 'KCN Gián Khẩu',
            'skills' => json_encode(['PLC', 'Lắp ráp', 'Tiếng Trung cơ bản']),
            'status' => 'ACTIVE',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->getJson('/api/demo/school-portal/opportunities')
            ->assertOk()
            ->assertJsonPath('opportunities.0.id', $demandId)
            ->assertJsonPath('opportunities.0.candidates.0.id', $capacityId)
            ->assertJsonPath('opportunities.0.candidates.0.maximum', 100)
            ->assertJsonPath('opportunities.0.candidates.0.missing_skills.0', 'Tiếng Trung cơ bản');

        $this->postJson('/api/demo/school-portal/proposals', [
            'demo_workforce_demand_id' => $demandId,
            'demo_training_capacity_id' => $capacityId,
            'quantity' => 80,
            'plan' => 'Bổ sung tiếng Trung cơ bản trước khi bàn giao nhân lực.',
        ])->assertCreated()
            ->assertJsonPath('proposal.school_id', 'school-a')
            ->assertJsonPath('proposal.quantity', 80);

        $this->postJson('/api/demo/school-portal/proposals', [
            'demo_workforce_demand_id' => $demandId,
            'demo_training_capacity_id' => $capacityId,
            'quantity' => 101,
            'plan' => 'Phương án vượt quá năng lực hiện có của cơ sở.',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('quantity');

        $this->postJson('/api/demo/school-portal/select-school', ['school_id' => 'school-b'])->assertOk();
        $this->postJson('/api/demo/school-portal/proposals', [
            'demo_workforce_demand_id' => $demandId,
            'demo_training_capacity_id' => $capacityId,
            'quantity' => 20,
            'plan' => 'Không được dùng năng lực thuộc cơ sở khác.',
        ])->assertNotFound();

        $this->assertDatabaseCount('demo_demand_proposals', 1);
        $this->assertDatabaseHas('demo_demand_proposals', [
            'school_id' => 'school-a',
            'quantity' => 80,
        ]);
    }

    public function test_school_can_create_and_update_one_proposal_per_career_field(): void
    {
        $this->postJson('/api/demo/school-portal/select-school', ['school_id' => 'school-a'])->assertOk();

        $this->postJson('/api/demo/school-portal/field-proposals', [
            'career_field_id' => 'co_khi',
            'quantity' => 80,
            'plan' => 'Đào tạo bổ sung kỹ năng lắp ráp và an toàn sản xuất.',
        ])->assertCreated()
            ->assertJsonPath('proposal.school_id', 'school-a')
            ->assertJsonPath('proposal.career_field_id', 'co_khi')
            ->assertJsonPath('proposal.quantity', 80);

        $this->postJson('/api/demo/school-portal/field-proposals', [
            'career_field_id' => 'co_khi',
            'quantity' => 95,
            'plan' => 'Cập nhật phương án đào tạo và thời gian bàn giao nhân lực.',
        ])->assertOk()
            ->assertJsonPath('proposal.quantity', 95);

        $this->getJson('/api/demo/school-portal/field-proposals')
            ->assertOk()
            ->assertJsonCount(1, 'proposals')
            ->assertJsonPath('proposals.0.career_field_id', 'co_khi');

        $this->assertDatabaseCount('demo_career_field_proposals', 1);
        $this->assertDatabaseHas('demo_career_field_proposals', [
            'school_id' => 'school-a',
            'career_field_id' => 'co_khi',
            'quantity' => 95,
        ]);
    }
}
