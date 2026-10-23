<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reporting_periods', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 100);
            $table->date('starts_at');
            $table->date('ends_at');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('employers', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 200)->index();
            $table->string('tax_code', 30)->nullable()->unique();
            $table->string('industry', 150)->nullable();
            $table->string('ward_id', 50)->nullable()->index();
            $table->string('address', 255)->nullable();
            $table->string('contact_person', 120)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 120)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('status', 20)->default('ACTIVE')->index();
            $table->boolean('is_verified')->default(false)->index();
            $table->string('source', 50)->default('SYSTEM');
            $table->string('external_id', 100)->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('normalized_name', 160)->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('workforce_demands', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employer_id')->constrained('employers')->cascadeOnDelete();
            $table->foreignId('training_major_id')->constrained('training_majors')->restrictOnDelete();
            $table->string('education_level_id', 20);
            $table->foreignId('reporting_period_id')->constrained('reporting_periods')->restrictOnDelete();
            $table->string('purpose', 30)->default('RECRUITMENT');
            $table->unsignedInteger('quantity');
            $table->string('location', 200)->nullable();
            $table->string('age_range', 80)->nullable();
            $table->string('salary', 100)->nullable();
            $table->text('note')->nullable();
            $table->string('status', 20)->default('DRAFT')->index();
            $table->string('source', 50)->default('SYSTEM');
            $table->string('external_id', 100)->nullable()->unique();
            $table->boolean('is_sample')->default(false)->index();
            $table->timestamps();

            $table->index(
                ['reporting_period_id', 'training_major_id', 'education_level_id', 'status'],
                'workforce_demands_matching_idx',
            );
        });

        Schema::create('training_capacities', function (Blueprint $table): void {
            $table->id();
            $table->string('school_id', 50);
            $table->foreignId('training_major_id')->constrained('training_majors')->restrictOnDelete();
            $table->string('education_level_id', 20);
            $table->foreignId('reporting_period_id')->constrained('reporting_periods')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('duration', 120)->nullable();
            $table->text('equipment')->nullable();
            $table->text('note')->nullable();
            $table->string('status', 20)->default('ACTIVE')->index();
            $table->string('source', 50)->default('SYSTEM');
            $table->string('external_id', 100)->nullable()->unique();
            $table->boolean('is_sample')->default(false)->index();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
            $table->unique(
                ['school_id', 'training_major_id', 'education_level_id', 'reporting_period_id'],
                'training_capacities_scope_unique',
            );
            $table->index(
                ['reporting_period_id', 'training_major_id', 'education_level_id', 'status'],
                'training_capacities_matching_idx',
            );
        });

        Schema::create('demand_skill', function (Blueprint $table): void {
            $table->foreignId('workforce_demand_id')->constrained('workforce_demands')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
            $table->primary(['workforce_demand_id', 'skill_id']);
        });

        Schema::create('capacity_skill', function (Blueprint $table): void {
            $table->foreignId('training_capacity_id')->constrained('training_capacities')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
            $table->primary(['training_capacity_id', 'skill_id']);
        });

        Schema::create('demand_proposals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workforce_demand_id')->constrained('workforce_demands')->cascadeOnDelete();
            $table->foreignId('training_capacity_id')->constrained('training_capacities')->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->text('plan');
            $table->string('status', 20)->default('SUBMITTED')->index();
            // The production users table predates Laravel and uses a signed BIGINT id.
            // Keep this column signed as well so the migration works on both the legacy
            // MySQL schema and fresh environments.
            $table->bigInteger('created_by')->nullable()->index();
            $table->string('source', 50)->default('SYSTEM');
            $table->string('external_id', 100)->nullable()->unique();
            $table->boolean('is_sample')->default(false)->index();
            $table->timestamps();

            $table->index(['workforce_demand_id', 'status'], 'demand_proposals_demand_status_idx');
            $table->index(['training_capacity_id', 'status'], 'demand_proposals_capacity_status_idx');
        });

        if (! Schema::hasColumn('users', 'employer_id')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->foreignId('employer_id')
                    ->nullable()
                    ->unique()
                    ->after('school_id')
                    ->constrained('employers')
                    ->nullOnDelete();
            });
        }

        $this->addBusinessAdminRole();
    }

    private function addBusinessAdminRole(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $column = collect(DB::select("SHOW COLUMNS FROM users WHERE Field = 'role'"))->first();
        $type = (string) ($column->Type ?? '');

        if ($type !== '' && ! str_contains($type, "'BUSINESS_ADMIN'")) {
            DB::statement(
                "ALTER TABLE users MODIFY role ENUM('SUPER_ADMIN','DISTRICT_ADMIN','VIEWER','SCHOOL_ADMIN','BUSINESS_ADMIN') NOT NULL DEFAULT 'VIEWER'"
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'employer_id')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('employer_id');
            });
        }

        Schema::dropIfExists('demand_proposals');
        Schema::dropIfExists('capacity_skill');
        Schema::dropIfExists('demand_skill');
        Schema::dropIfExists('training_capacities');
        Schema::dropIfExists('workforce_demands');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('employers');
        Schema::dropIfExists('reporting_periods');
    }
};
