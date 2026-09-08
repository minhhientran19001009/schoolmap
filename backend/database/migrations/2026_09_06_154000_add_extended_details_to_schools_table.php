<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // 1. Phân loại cơ sở đào tạo đặc thù
            $table->string('special_type', 50)->nullable()->after('school_type_id');

            // 2. Ban Giám hiệu / Lãnh đạo (Tên + Chức vụ)
            $table->json('leaders')->nullable()->after('principal');

            // 3. Ngành đào tạo
            $table->json('training_majors')->nullable()->after('leaders');

            // 4. Số liệu sinh viên / học viên
            $table->integer('annual_enrollment')->default(0)->after('student_count');
            $table->integer('annual_graduates')->default(0)->after('annual_enrollment');
            $table->decimal('employment_rate', 5, 2)->nullable()->after('annual_graduates');

            // 5. Giáo viên định biên & thừa / thiếu
            $table->integer('teacher_quota')->default(0)->after('teacher_count');
            $table->integer('teachers_shortage')->default(0)->after('teacher_quota');
            $table->integer('teachers_surplus')->default(0)->after('teachers_shortage');

            // 6. Phân loại giảng viên (Ngạch / Hạng & Học vị)
            $table->integer('faculty_rank_1')->default(0)->after('teachers_surplus'); // Loại 1
            $table->integer('faculty_rank_2')->default(0)->after('faculty_rank_1'); // Loại 2
            $table->integer('faculty_rank_3')->default(0)->after('faculty_rank_2'); // Hạng III
            $table->integer('faculty_doctors')->default(0)->after('faculty_rank_3'); // Tiến sĩ (TS)
            $table->integer('faculty_masters')->default(0)->after('faculty_doctors'); // Thạc sĩ (ThS)
            $table->integer('faculty_professors')->default(0)->after('faculty_masters'); // GS/PGS

            // 7. Doanh nghiệp liên kết
            $table->json('partner_enterprises')->nullable()->after('faculty_professors');

            // 8. Cơ sở vật chất & Thư viện ảnh
            $table->integer('workshops_count')->default(0)->after('lab_count');
            $table->json('gallery')->nullable()->after('campus_area_m2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'special_type',
                'leaders',
                'training_majors',
                'annual_enrollment',
                'annual_graduates',
                'employment_rate',
                'teacher_quota',
                'teachers_shortage',
                'teachers_surplus',
                'faculty_rank_1',
                'faculty_rank_2',
                'faculty_rank_3',
                'faculty_doctors',
                'faculty_masters',
                'faculty_professors',
                'partner_enterprises',
                'workshops_count',
                'gallery',
            ]);
        });
    }
};
