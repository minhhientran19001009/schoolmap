<?php

namespace Database\Seeders;

use App\Models\CareerField;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillCatalogSeeder extends Seeder
{
    /**
     * Canonical skills grouped by the 13 career field IDs used across job postings and portals.
     */
    public const CATALOG = [
        'co_khi' => [
            'Đọc bản vẽ',
            'Vận hành máy CNC',
            'Hàn MIG/MAG',
            'Hàn TIG',
            'Thiết kế khuôn mẫu',
            'Thiết kế AutoCAD',
            'Sửa chữa và bảo dưỡng ô tô',
            'Bảo trì máy công nghiệp',
            'Kỹ thuật đúc kim loại',
            'An toàn 5S',
        ],
        'dien_dien_tu' => [
            'PLC',
            'An toàn điện',
            'Lắp ráp linh kiện điện tử',
            'Vận hành dây chuyền SMT',
            'Kiểm tra chất lượng (QC)',
            'Vận hành rô bốt công nghiệp',
            'Điện công nghiệp và dân dụng',
            'Sửa chữa thiết bị điện lạnh',
        ],
        'cntt' => [
            'Tin học văn phòng / Excel',
            'Kỹ năng số cơ bản',
            'Lập trình phần mềm',
            'Quản trị mạng và hệ thống',
            'Phát triển ứng dụng web',
            'Thiết kế cơ sở dữ liệu',
            'Vận hành thiết bị viễn thông',
            'An toàn thông tin cơ bản',
        ],
        'xay_dung' => [
            'Thi công xây dựng dân dụng',
            'Vận hành máy thi công xây dựng',
            'Kỹ thuật sơn, gò, hoàn thiện công trình',
            'Gia công và lắp đặt kết cấu thép',
            'Sản xuất vật liệu xây dựng',
            'Kỹ thuật mộc và lắp đặt nội thất',
            'Dự toán và giám sát công trình',
            'An toàn lao động công trường',
        ],
        'giao_thong_logistics' => [
            'Lái xe ô tô hạng B2/C',
            'Vận hành xe nâng hàng',
            'Quản lý kho bãi và tồn kho',
            'Giao nhận và điều phối vận tải',
            'Đóng gói và kiểm đếm hàng hóa',
            'Nghiệp vụ xuất nhập khẩu',
            'Vận hành phương tiện thủy nội địa',
        ],
        'det_may' => [
            'May công nghiệp',
            'Vận hành chuyền may giày da',
            'Bảo trì máy may công nghiệp',
            'Cắt rập và giác sơ đồ',
            'Thiết kế mẫu thời trang',
            'Kiểm tra sản phẩm may mặc (KCS)',
            'Kỹ thuật thêu và hoàn thiện sản phẩm',
        ],
        'du_lich' => [
            'Nghiệp vụ buồng phòng',
            'Phục vụ bàn và nhà hàng',
            'Nghiệp vụ lễ tân khách sạn',
            'Kỹ thuật chế biến món ăn',
            'Vệ sinh an toàn thực phẩm (HACCP)',
            'Hướng dẫn du lịch',
            'Chăm sóc khách hàng du lịch',
        ],
        'nong_nghiep' => [
            'Kỹ thuật chăn nuôi và thú y',
            'Kỹ thuật trồng trọt công nghệ cao',
            'Nuôi trồng thủy sản',
            'Chế biến và bảo quản thực phẩm',
            'Vận hành dây chuyền thức ăn chăn nuôi',
            'Kiểm soát chất lượng nông sản',
            'Vận hành máy nông nghiệp',
        ],
        'kinh_doanh' => [
            'Kế toán tổng hợp',
            'Tư vấn bán hàng và chăm sóc khách hàng',
            'Quản lý cửa hàng bán lẻ',
            'Tư vấn tài chính - tín dụng',
            'Nghiệp vụ hành chính - nhân sự',
            'Kỹ năng đàm phán và chốt đơn',
            'Marketing và thương mại điện tử',
            'Quản lý chuỗi cung ứng và mua hàng',
        ],
        'y_te' => [
            'Kỹ thuật bào chế và dập viên dược phẩm',
            'Thực hành tốt sản xuất thuốc (GMP)',
            'Chăm sóc người bệnh và điều dưỡng',
            'Tư vấn sử dụng thuốc và dược lâm sàng',
            'Sơ cấp cứu ban đầu',
            'Vận hành thiết bị y tế cơ bản',
            'Kiểm soát nhiễm khuẩn y tế',
        ],
        'hoa_chat_nang_luong' => [
            'Kỹ thuật ép nhựa công nghiệp',
            'Vận hành dây chuyền sản xuất hóa chất',
            'Kỹ thuật xi mạ và xử lý bề mặt',
            'Vận hành hệ thống xử lý nước thải',
            'Kiểm soát an toàn hóa chất và PCCC',
            'Vận hành hệ thống năng lượng công nghiệp',
            'Kỹ thuật pha chế và kiểm nghiệm vật liệu',
        ],
        'van_hoa_nghe_thuat' => [
            'Thiết kế đồ họa 2D/3D',
            'Chế tác thủ công mỹ nghệ truyền thống',
            'Nhiếp ảnh và quay dựng truyền thông',
            'Trình diễn và hướng dẫn nghệ thuật',
            'Kỹ thuật lắp ráp đồ chơi và sản phẩm mỹ thuật',
            'Thiết kế bao bì và nhận diện thương hiệu',
            'Biên tập nội dung văn hóa - truyền thông',
        ],
        'giao_duc_dich_vu' => [
            'Tiếng Anh giao tiếp',
            'Tiếng Trung cơ bản',
            'Tiếng Hàn giao tiếp',
            'Tiếng Nhật cơ bản',
            'Nghiệp vụ bảo vệ và an ninh cơ sở',
            'Kỹ năng sư phạm và đào tạo nghề',
            'Chăm sóc trẻ em và hỗ trợ cộng đồng',
            'Kỹ năng giao tiếp và làm việc nhóm',
            'Kỹ thuật pha chế và dịch vụ đồ uống',
        ],
    ];

    public function run(): void
    {
        $validFields = CareerField::options();

        foreach (self::CATALOG as $careerField => $skills) {
            if (! array_key_exists($careerField, $validFields)) {
                continue;
            }

            foreach ($skills as $name) {
                $normalized = Skill::normalize($name);
                $skill = Skill::query()->firstOrNew(['normalized_name' => $normalized]);

                if (! $skill->exists) {
                    $skill->fill([
                        'name' => $name,
                        'career_field' => $careerField,
                        'is_active' => true,
                    ])->save();
                    continue;
                }

                // Preserve existing admin edits; only backfill missing career field categorization.
                if (blank($skill->career_field)) {
                    $skill->career_field = $careerField;
                    $skill->save();
                }
            }
        }
    }
}
