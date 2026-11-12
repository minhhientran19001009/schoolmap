<?php

namespace Tests\Feature;

use App\Models\SchoolSourceRecord;
use App\Models\SchoolTrainingMajor;
use App\Models\TrainingMajor;
use App\Services\CleanGdnnImportService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CleanGdnnImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('schools', function (Blueprint $table): void {
            $table->string('id', 50)->primary();
            $table->string('code');
            $table->string('name');
            $table->string('campus_type')->default('MAIN');
            $table->string('parent_school_id')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
        Schema::create('training_majors', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('school_training_major', function (Blueprint $table): void {
            $table->id();
            $table->string('school_id');
            $table->unsignedBigInteger('training_major_id');
            $table->string('degree_level');
            $table->unsignedInteger('annual_quota')->default(0);
            $table->timestamps();
            $table->unique(['school_id', 'training_major_id', 'degree_level']);
        });
        (require database_path('migrations/2026_11_12_000001_create_school_source_records_table.php'))->up();
    }

    public function test_import_skips_uncertain_records_omits_director_phones_and_preserves_annual_quotas(): void
    {
        DB::table('schools')->insert(['id' => 'known', 'code' => 'KNOWN', 'name' => 'Trường kiểm thử', 'address' => 'Địa chỉ cũ', 'phone' => 'PHONE-UNCHANGED']);
        $major = TrainingMajor::create(['name' => 'Cơ khí', 'code' => '6520123']);
        SchoolTrainingMajor::create(['school_id' => 'known', 'training_major_id' => $major->id, 'degree_level' => 'cao_dang', 'annual_quota' => 99]);
        $clean = [
            'institutions' => [
                ['source_ref' => 'Sheet!1', 'name' => 'Trường kiểm thử', 'match_status' => 'matched', 'project_school_id' => 'known', 'programs' => [
                    ['name' => 'Cơ khí', 'source_codes' => ['6520123'], 'codes_by_level' => ['cao_dang' => ['6520123']], 'current_scale' => ['clean_total' => 30, 'cao_dang' => 30], 'planned_2026' => ['clean_total' => 20, 'cao_dang' => 20]],
                ]],
                ['source_ref' => 'Sheet!2', 'name' => 'Trường đổi tên cần xác minh', 'match_status' => 'needs_review', 'candidate_project_school_id' => 'known', 'programs' => []],
                ['source_ref' => 'Sheet!3', 'name' => 'Trường chưa tồn tại', 'match_status' => 'unmatched', 'programs' => []],
            ],
            'centers' => [['source_ref' => 'Word!1', 'name' => 'Trường kiểm thử', 'address' => 'Địa chỉ sạch', 'director_phone' => 'DIRECTOR-PRIVATE', 'source_director_phone' => 'SOURCE-PRIVATE']],
        ];
        $service = app(CleanGdnnImportService::class);
        $plan = $service->plan($clean, []);
        $this->assertSame(2, $plan['counts']['skipped_uncertain']);
        $this->assertSame(2, $plan['counts']['records']);
        $this->assertDatabaseCount('school_source_records', 0);
        $this->assertDatabaseHas('schools', ['id' => 'known', 'address' => 'Địa chỉ cũ']);
        $backup = $service->apply($plan);
        try {
            $this->assertDatabaseCount('school_source_records', 2);
            $this->assertDatabaseHas('schools', ['id' => 'known', 'address' => 'Địa chỉ sạch', 'phone' => 'PHONE-UNCHANGED']);
            $this->assertDatabaseHas('school_training_major', ['school_id' => 'known', 'annual_quota' => 99]);
            $stored = SchoolSourceRecord::where('record_type', 'center_contact')->first()->payload;
            $this->assertArrayNotHasKey('director_phone', $stored);
            $this->assertArrayNotHasKey('source_director_phone', $stored);
            $this->assertSame('Địa chỉ cũ', json_decode(File::get($backup), true)['schools'][0]['address']);
            $again = $service->plan($clean, []);
            $this->assertSame(0, $again['counts']['reports_to_write']);
            $this->assertSame(0, $again['counts']['addresses_to_update']);
            $this->assertSame(0, $again['counts']['new_assignments']);
        } finally {
            File::delete($backup);
        }
    }

    public function test_exact_center_name_wins_over_administrative_alias_and_ambiguous_names_are_skipped(): void
    {
        DB::table('schools')->insert([
            ['id' => 'exact', 'code' => 'E', 'name' => 'Trung tâm GDNN-GDTX Duy Tiên', 'campus_type' => 'MAIN'],
            ['id' => 'alias', 'code' => 'A', 'name' => 'Trung tâm GDNN-GDTX Thị xã Duy Tiên', 'campus_type' => 'MAIN'],
            ['id' => 'dup1', 'code' => 'D1', 'name' => 'Trung tâm trùng tên', 'campus_type' => 'MAIN'],
            ['id' => 'dup2', 'code' => 'D2', 'name' => 'Trung tâm trùng tên', 'campus_type' => 'MAIN'],
        ]);
        $plan = app(CleanGdnnImportService::class)->plan(['institutions' => [], 'centers' => [
            ['source_ref' => '1', 'name' => 'Trung tâm GDNN-GDTX Duy Tiên', 'address' => 'Địa chỉ mới'],
            ['source_ref' => '2', 'name' => 'Trung tâm trùng tên', 'address' => 'Không nhập'],
        ]], []);
        $this->assertSame('exact', $plan['records'][0]['school_id']);
        $this->assertCount(1, $plan['records']);
        $this->assertCount(1, $plan['skipped']);
    }
}
