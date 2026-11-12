<?php

namespace Tests\Feature;

use App\Services\SchoolDeduplicationService;
use App\Services\SchoolIdentityService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class SchoolDeduplicationTest extends TestCase
{
    private array $priorBackups;

    protected function setUp(): void
    {
        parent::setUp();
        $this->priorBackups = glob(storage_path('app/import-backups/school-dedup-*.json')) ?: [];
        config(['school_identity.merges' => ['old' => ['target' => 'current', 'code' => 'OLD-CODE']]]);
        Schema::create('schools', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('campus_type')->default('MAIN');
            $table->string('parent_school_id')->nullable()->references('id')->on('schools');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
        });
        foreach (['school_training_major', 'school_education_levels'] as $name) {
            Schema::create($name, function (Blueprint $table): void {
                $table->id();
                $table->string('school_id')->references('id')->on('schools')->cascadeOnDelete();
                $table->unsignedInteger('catalog_id');
                $table->unsignedInteger('annual_quota')->default(0);
                $table->unique(['school_id', 'catalog_id']);
            });
        }
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('school_id')->nullable()->unique()->references('id')->on('schools')->nullOnDelete();
            $table->string('name');
        });
        Schema::create('training_capacities', function (Blueprint $table): void {
            $table->id();
            $table->string('school_id')->references('id')->on('schools')->cascadeOnDelete();
            $table->unsignedInteger('quantity');
        });
        Schema::create('capacity_proposals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('capacity_id')->constrained('training_capacities')->cascadeOnDelete();
        });
        Schema::create('legacy_reports', function (Blueprint $table): void {
            $table->id();
            // Deliberately no FK: older tables must also be handled.
            $table->string('school_id');
            $table->string('payload');
        });
        DB::table('schools')->insert([
            ['id' => 'old', 'code' => 'OLD-CODE', 'name' => 'Trung tâm GDNN - GDTX Huyện Kim Bảng', 'address' => 'Địa chỉ seed', 'phone' => 'OLD'],
            ['id' => 'current', 'code' => 'CURRENT', 'name' => 'Trung tâm GDNN-GDTX Kim Bảng', 'address' => 'Địa chỉ đã đối chiếu', 'phone' => null],
            ['id' => 'separate', 'code' => 'SEPARATE', 'name' => 'Trung tâm GDNN-GDTX Kim Bảng – Cơ sở 2', 'address' => 'Cơ sở riêng', 'phone' => null],
        ]);
    }

    protected function tearDown(): void
    {
        foreach (array_diff(glob(storage_path('app/import-backups/school-dedup-*.json')) ?: [], $this->priorBackups) as $path) {
            File::delete($path);
        }
        parent::tearDown();
    }

    public function test_merge_keeps_new_data_archives_old_catalog_and_preserves_business_links(): void
    {
        $current = (array) DB::table('schools')->find('current');
        foreach (['school_training_major', 'school_education_levels'] as $table) {
            DB::table($table)->insert([
                ['school_id' => 'old', 'catalog_id' => 1, 'annual_quota' => 999],
                ['school_id' => 'old', 'catalog_id' => 2, 'annual_quota' => 999],
                ['school_id' => 'current', 'catalog_id' => 1, 'annual_quota' => 120],
            ]);
        }
        DB::table('users')->insert(['school_id' => 'old', 'name' => 'Tài khoản giữ lại']);
        $capacity = DB::table('training_capacities')->insertGetId(['school_id' => 'old', 'quantity' => 45]);
        DB::table('capacity_proposals')->insert(['capacity_id' => $capacity]);
        DB::table('legacy_reports')->insert(['school_id' => 'old', 'payload' => 'Hồ sơ giữ lại']);
        DB::table('schools')->where('id', 'separate')->update(['parent_school_id' => 'old', 'campus_type' => 'CAMPUS']);
        $service = app(SchoolDeduplicationService::class);
        $this->assertCount(1, $service->plan());
        $this->assertDatabaseCount('schools', 3);
        $result = $service->apply();
        $this->assertSame(1, $result['merged']);
        $backup = json_decode(File::get($result['backup']), true);
        $this->assertCount(3, $backup['tables']['schools']);
        $this->assertCount(3, $backup['tables']['school_training_major']);
        $this->assertSame($current, (array) DB::table('schools')->find('current'));
        $this->assertDatabaseMissing('schools', ['id' => 'old']);
        $this->assertDatabaseHas('schools', ['id' => 'separate', 'parent_school_id' => 'current', 'campus_type' => 'CAMPUS']);
        $this->assertDatabaseHas('users', ['school_id' => 'current', 'name' => 'Tài khoản giữ lại']);
        $this->assertDatabaseHas('training_capacities', ['id' => $capacity, 'school_id' => 'current', 'quantity' => 45]);
        $this->assertDatabaseHas('capacity_proposals', ['capacity_id' => $capacity]);
        $this->assertDatabaseHas('legacy_reports', ['school_id' => 'current', 'payload' => 'Hồ sơ giữ lại']);
        foreach (['school_training_major', 'school_education_levels'] as $table) {
            $this->assertDatabaseCount($table, 1);
            $this->assertDatabaseHas($table, ['school_id' => 'current', 'catalog_id' => 1, 'annual_quota' => 120]);
        }
        $this->assertSame(['merged' => 0, 'backup' => null, 'changes' => []], $service->apply());
    }

    public function test_unique_account_conflict_rolls_back_all_changes(): void
    {
        DB::table('users')->insert([
            ['school_id' => 'old', 'name' => 'Cũ'],
            ['school_id' => 'current', 'name' => 'Mới'],
        ]);
        DB::table('legacy_reports')->insert(['school_id' => 'old', 'payload' => 'Không thay đổi']);
        try {
            app(SchoolDeduplicationService::class)->apply();
            $this->fail('Must reject two accounts rather than silently remove one.');
        } catch (QueryException) {
            $this->assertDatabaseCount('schools', 3);
            $this->assertDatabaseCount('users', 2);
            $this->assertDatabaseHas('legacy_reports', ['school_id' => 'old']);
        }
    }

    public function test_mapping_refuses_to_merge_an_unrelated_name(): void
    {
        DB::table('schools')->where('id', 'current')->update(['name' => 'Trường khác']);
        $this->expectException(RuntimeException::class);
        app(SchoolDeduplicationService::class)->apply();
    }

    public function test_mapping_refuses_to_merge_a_branch(): void
    {
        DB::table('schools')->where('id', 'current')->update(['parent_school_id' => 'separate', 'campus_type' => 'CAMPUS']);
        $this->expectException(RuntimeException::class);
        app(SchoolDeduplicationService::class)->apply();
    }

    public function test_identity_uses_reviewed_alias_and_rejects_ambiguous_names(): void
    {
        $identity = app(SchoolIdentityService::class);
        $schools = DB::table('schools')->get();
        $this->assertSame('current', $identity->findIn($schools, 'Tên trong file cũ', 'OLD-CODE')->id);
        $this->assertSame('current', $identity->canonicalId('old'));
        $this->assertSame('separate', $identity->canonicalId('separate'));
        $this->expectException(RuntimeException::class);
        $identity->findIn($schools, 'Trung tâm GDNN - GDTX huyện Kim Bảng');
    }

    public function test_normalized_excel_name_matches_after_merge_without_matching_another_campus(): void
    {
        app(SchoolDeduplicationService::class)->apply();
        $schools = DB::table('schools')->get();
        $identity = app(SchoolIdentityService::class);
        $this->assertSame('current', $identity->findIn($schools, '  TRUNG TÂM GDNN - GDTX Huyện Kim Bảng  ')->id);
        $this->assertSame('separate', $identity->findIn($schools, 'Trung tâm GDNN-GDTX Kim Bảng - Cơ sở 2')->id);
    }
}
