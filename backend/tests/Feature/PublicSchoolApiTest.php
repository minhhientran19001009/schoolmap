<?php

namespace Tests\Feature;

use App\Models\EducationLevel;
use App\Models\School;
use App\Models\TrainingMajor;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PublicSchoolApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('education_levels')) {
            Schema::create('education_levels', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('name');
                $table->string('color')->nullable();
                $table->string('icon')->nullable();
                $table->integer('display_order')->default(1);
            });
        }

        if (! Schema::hasTable('schools')) {
            Schema::create('schools', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('code')->nullable();
                $table->string('name');
                $table->string('status')->default('VERIFIED');
                $table->string('education_level_id')->nullable();
                $table->string('school_type_id')->nullable();
                $table->string('district_id')->nullable();
                $table->string('ward')->nullable();
                $table->text('address')->nullable();
                $table->string('campus_type')->default('MAIN');
                $table->string('campus_name')->nullable();
                $table->string('parent_school_id')->nullable();
                $table->string('campus_note')->nullable();
                $table->decimal('lat', 10, 7)->nullable();
                $table->decimal('lng', 10, 7)->nullable();
                $table->boolean('is_national_standard')->default(false);
                $table->text('training_majors')->nullable();
                $table->text('partner_enterprises')->nullable();
                $table->text('gallery')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('school_education_levels')) {
            Schema::create('school_education_levels', function (Blueprint $table): void {
                $table->id();
                $table->string('school_id');
                $table->string('education_level_id');
            });
        }

        if (! Schema::hasTable('training_majors')) {
            Schema::create('training_majors', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('code', 50)->nullable();
                $table->string('slug')->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('school_training_major')) {
            Schema::create('school_training_major', function (Blueprint $table): void {
                $table->id();
                $table->string('school_id');
                $table->unsignedBigInteger('training_major_id');
                $table->string('degree_level')->nullable();
                $table->integer('annual_quota')->default(0);
                $table->timestamps();
            });
        }

        // Seed basic fixtures if empty
        if (EducationLevel::count() === 0) {
            DB::table('education_levels')->insert([
                ['id' => 'cao_dang', 'name' => 'Cao đẳng', 'color' => '#1d4ed8', 'icon' => 'academic-cap', 'display_order' => 1],
                ['id' => 'trung_cap', 'name' => 'Trung cấp', 'color' => '#059669', 'icon' => 'building-library', 'display_order' => 2],
            ]);
        }

        if (TrainingMajor::count() === 0) {
            DB::table('training_majors')->insert([
                ['id' => 1, 'name' => 'Kỹ thuật Hàn', 'code' => '6520123', 'slug' => 'ky-thuat-han', 'is_active' => 1],
                ['id' => 2, 'name' => 'Công nghệ Thông tin', 'code' => '6480201', 'slug' => 'cong-nghe-thong-tin', 'is_active' => 1],
            ]);
        }

        if (School::count() === 0) {
            DB::table('schools')->insert([
                [
                    'id' => 'truong-cd-co-dien-xay-dung-ninh-binh',
                    'code' => 'NB001',
                    'name' => 'Trường Cao đẳng Cơ điện và Xây dựng Việt Xô',
                    'status' => 'VERIFIED',
                    'education_level_id' => 'cao_dang',
                    'school_type_id' => 'cong_lap',
                    'district_id' => 'tp-tam-diep',
                    'ward' => 'Phường Trung Sơn',
                    'address' => 'Số 123 Đường 30/6, Phường Trung Sơn, TP Tam Điệp, Ninh Bình',
                    'lat' => 20.15,
                    'lng' => 105.9,
                ],
            ]);

            DB::table('school_education_levels')->insert([
                ['school_id' => 'truong-cd-co-dien-xay-dung-ninh-binh', 'education_level_id' => 'cao_dang'],
            ]);

            DB::table('school_training_major')->insert([
                ['school_id' => 'truong-cd-co-dien-xay-dung-ninh-binh', 'training_major_id' => 1, 'degree_level' => 'cao_dang', 'annual_quota' => 150],
            ]);
        }
    }

    public function test_can_fetch_public_schools_with_meta_and_summary(): void
    {
        $response = $this->getJson('/api/public/schools');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'code',
                    'name',
                    'education_levels',
                    'school_type',
                    'school_type_name',
                    'district_id',
                    'district_name',
                    'ward',
                    'address',
                    'training_major_count',
                    'training_major_preview',
                    'annual_quota_total',
                    'lat',
                    'lng',
                    'status',
                ],
            ],
            'meta' => [
                'current_page',
                'per_page',
                'total',
                'last_page',
                'summary' => [
                    'school_count',
                    'level_count',
                    'major_count',
                    'annual_quota_total',
                ],
            ],
        ]);
    }

    public function test_accent_insensitive_search_matches_same_results(): void
    {
        $resUnaccented = $this->getJson('/api/public/schools?q=han');
        $resAccented = $this->getJson('/api/public/schools?q=hàn');

        $resUnaccented->assertStatus(200);
        $resAccented->assertStatus(200);

        $this->assertEquals(
            $resUnaccented->json('meta.summary.school_count'),
            $resAccented->json('meta.summary.school_count')
        );
    }

    public function test_can_filter_by_education_level(): void
    {
        $response = $this->getJson('/api/public/schools?level=cao_dang');

        $response->assertStatus(200);
        $data = $response->json('data');
        foreach ($data as $school) {
            $levels = collect($school['education_levels'])->pluck('id')->all();
            $this->assertContains('cao_dang', $levels);
        }
    }

    public function test_catalogs_returns_levels_districts_wards_majors(): void
    {
        $response = $this->getJson('/api/public/schools/catalogs');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'education_levels',
            'districts',
            'wards',
            'school_types',
            'training_majors',
        ]);
    }
}
