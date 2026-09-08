<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SampleExtendedSeeder extends Seeder
{
    public function run(): void
    {
        $cd = School::where('id', 'NB-CD-001')->orWhere('code', '37010')->first();
        if ($cd) {
            $cd->special_type = 'cao_dang';
            $cd->leaders = [
                ['name' => 'TS. Phạm Ngọc Vũ', 'position' => 'Hiệu trưởng'],
                ['name' => 'ThS. Nguyễn Thị Lan', 'position' => 'Phó Hiệu trưởng phụ trách Đào tạo'],
                ['name' => 'ThS. Trần Mạnh Hùng', 'position' => 'Phó Hiệu trưởng phụ trách NCKH'],
                ['name' => 'KS. Lê Văn Thắng', 'position' => 'Trưởng khoa Cơ khí Ô tô'],
                ['name' => 'ThS. Vũ Hoàng Điệp', 'position' => 'Trưởng khoa Điện - Điện tử']
            ];
            $cd->training_majors = [
                ['name' => 'Công nghệ Kỹ thuật Ô tô', 'degree_level' => 'cao_dang', 'major_code' => '6510216', 'annual_quota' => 350],
                ['name' => 'Điện công nghiệp & Dân dụng', 'degree_level' => 'cao_dang', 'major_code' => '6520227', 'annual_quota' => 280],
                ['name' => 'Hàn công nghệ cao', 'degree_level' => 'trung_cap', 'major_code' => '5520123', 'annual_quota' => 200],
                ['name' => 'Vận hành Máy thi công xây dựng', 'degree_level' => 'trung_cap', 'major_code' => '5580201', 'annual_quota' => 180],
                ['name' => 'Kỹ thuật Lắp đặt Điện & Điều hòa', 'degree_level' => 'so_cap', 'major_code' => '4520202', 'annual_quota' => 150]
            ];
            $cd->annual_enrollment = 1150;
            $cd->annual_graduates = 980;
            $cd->employment_rate = 94.5;
            $cd->teacher_count = 145;
            $cd->teacher_quota = 150;
            $cd->teachers_shortage = 5;
            $cd->teachers_surplus = 0;
            $cd->faculty_rank_1 = 10;
            $cd->faculty_rank_2 = 20;
            $cd->faculty_rank_3 = 85;
            $cd->faculty_doctors = 11;
            $cd->faculty_masters = 11;
            $cd->faculty_professors = 2;
            $cd->campus_area_m2 = 48000;
            $cd->classroom_count = 55;
            $cd->workshops_count = 16;
            $cd->partner_enterprises = [
                ['name' => 'Tập đoàn Hyundai Thành Công Ninh Bình', 'cooperation' => 'Hợp tác đào tạo thực tập & tuyển dụng kỹ sư công nghệ ô tô', 'logo' => 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=200&auto=format&fit=crop&q=80', 'is_featured' => true],
                ['name' => 'Tổng công ty LILAMA Ninh Bình', 'cooperation' => 'Đào tạo kỹ sư cơ khí lắp máy & hàn công nghệ cao', 'logo' => 'https://images.unsplash.com/photo-1504917599217-d4dc5ebe6122?w=200&auto=format&fit=crop&q=80', 'is_featured' => true],
                ['name' => 'Công ty TNHH Mcnex Vina Ninh Bình', 'cooperation' => 'Cung ứng nhân lực kỹ thuật điện tử công nghệ cao', 'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&auto=format&fit=crop&q=80', 'is_featured' => true],
                ['name' => 'Tập đoàn Xi măng The Vissai', 'cooperation' => 'Hợp tác vận hành hệ thống điện và máy công nghiệp', 'logo' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=200&auto=format&fit=crop&q=80', 'is_featured' => true],
                ['name' => 'Công ty Doosan Vina Hải Phòng', 'cooperation' => 'Tài trợ xưởng thực hành và tuyển dụng học viên tốt nghiệp', 'logo' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=200&auto=format&fit=crop&q=80', 'is_featured' => true]
            ];
            $cd->gallery = [
                'https://images.unsplash.com/photo-1562774053-701939374585?w=800&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=800&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1581092335397-9583fe92d232?w=800&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=800&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=800&auto=format&fit=crop&q=80'
            ];
            $cd->save();
        }

        // Also update GDTX sample
        $gdtx = School::where('id', 'NB-GDTX-001')->orWhere('code', '37023')->first();
        if ($gdtx) {
            $gdtx->special_type = 'gdtx';
            $gdtx->leaders = [
                ['name' => 'Thầy Nguyễn Đăng Khánh', 'position' => 'Giám đốc Trung tâm'],
                ['name' => 'Cô Trần Thị Mai', 'position' => 'Phó Giám đốc'],
                ['name' => 'ThS. Đỗ Văn Nam', 'position' => 'Trưởng phòng Đào tạo & Bồi dưỡng']
            ];
            $gdtx->training_majors = [
                ['name' => 'Chương trình GDTX cấp THPT', 'degree_level' => 'trung_cap', 'major_code' => 'GDTX-THPT', 'annual_quota' => 250],
                ['name' => 'Tin học Văn phòng Ứng dụng', 'degree_level' => 'so_cap', 'major_code' => 'TH-VP', 'annual_quota' => 200],
                ['name' => 'Tiếng Anh Giao tiếp Khung chuẩn', 'degree_level' => 'so_cap', 'major_code' => 'NN-TA', 'annual_quota' => 180]
            ];
            $gdtx->annual_enrollment = 420;
            $gdtx->annual_graduates = 390;
            $gdtx->employment_rate = 88.0;
            $gdtx->teacher_count = 42;
            $gdtx->teacher_quota = 45;
            $gdtx->teachers_shortage = 3;
            $gdtx->teachers_surplus = 0;
            $gdtx->faculty_rank_1 = 4;
            $gdtx->faculty_rank_2 = 12;
            $gdtx->faculty_rank_3 = 26;
            $gdtx->faculty_doctors = 2;
            $gdtx->faculty_masters = 18;
            $gdtx->faculty_professors = 0;
            $gdtx->campus_area_m2 = 12500;
            $gdtx->classroom_count = 18;
            $gdtx->workshops_count = 4;
            $gdtx->partner_enterprises = [
                ['name' => 'Viettel Telecom Ninh Bình', 'cooperation' => 'Hợp tác đào tạo tin học ứng dụng và thực tập sinh', 'logo' => 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=200&auto=format&fit=crop&q=80', 'is_featured' => true],
                ['name' => 'VNPT Ninh Bình', 'cooperation' => 'Tài trợ phòng máy tính và cấp chứng chỉ CNTT', 'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&auto=format&fit=crop&q=80', 'is_featured' => true]
            ];
            $gdtx->gallery = [
                'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&auto=format&fit=crop&q=80'
            ];
            $gdtx->save();
        }
    }
}
