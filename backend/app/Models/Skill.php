<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Skill extends Model
{
    public const CAREER_FIELDS = [
        'co_khi' => 'Cơ khí - Chế tạo - Ô tô',
        'dien_dien_tu' => 'Điện - Điện tử - Tự động hóa',
        'cntt' => 'Công nghệ thông tin - Truyền thông',
        'xay_dung' => 'Xây dựng - Kiến trúc - Vật liệu',
        'giao_thong_logistics' => 'Giao thông - Vận tải - Logistics',
        'det_may' => 'Dệt may - Da giày - Thời trang',
        'du_lich' => 'Du lịch - Nhà hàng - Khách sạn',
        'nong_nghiep' => 'Nông nghiệp - Thủy sản - Thực phẩm',
        'kinh_doanh' => 'Kinh doanh - Tài chính - Hành chính',
        'y_te' => 'Y tế - Dược - Chăm sóc sức khỏe',
        'hoa_chat_nang_luong' => 'Hóa chất - Năng lượng - Môi trường',
        'van_hoa_nghe_thuat' => 'Văn hóa - Nghệ thuật - Thiết kế',
        'giao_duc_dich_vu' => 'Giáo dục - Xã hội - Dịch vụ cộng đồng',
    ];

    protected $fillable = ['name', 'normalized_name', 'career_field', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function careerField(): BelongsTo
    {
        return $this->belongsTo(CareerField::class, 'career_field', 'id');
    }

    public function careerFieldLabel(): ?string
    {
        return $this->career_field ? (CareerField::options()[$this->career_field] ?? $this->career_field) : null;
    }

    public static function normalize(string $value): string
    {
        return Str::of($value)->trim()->squish()->ascii()->lower()->toString();
    }

    protected static function booted(): void
    {
        static::saving(function (Skill $skill): void {
            $skill->name = trim($skill->name);
            $skill->normalized_name = static::normalize($skill->name);
        });
    }
}
