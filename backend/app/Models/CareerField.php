<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CareerField extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'icon',
        'description',
        'display_order',
        'is_active',
    ];

    public const DEFAULTS = [
        [
            'id' => 'co_khi',
            'name' => Skill::CAREER_FIELDS['co_khi'],
            'icon' => 'fa-solid fa-gears',
            'description' => 'Gia công, lắp ráp, bảo trì máy móc và công nghệ ô tô.',
            'display_order' => 1,
        ],
        [
            'id' => 'dien_dien_tu',
            'name' => Skill::CAREER_FIELDS['dien_dien_tu'],
            'icon' => 'fa-solid fa-bolt',
            'description' => 'Điện công nghiệp, điện tử, điều khiển và dây chuyền tự động.',
            'display_order' => 2,
        ],
        [
            'id' => 'cntt',
            'name' => Skill::CAREER_FIELDS['cntt'],
            'icon' => 'fa-solid fa-laptop-code',
            'description' => 'Phần mềm, mạng máy tính, dữ liệu và nội dung số.',
            'display_order' => 3,
        ],
        [
            'id' => 'xay_dung',
            'name' => Skill::CAREER_FIELDS['xay_dung'],
            'icon' => 'fa-solid fa-trowel-bricks',
            'description' => 'Thi công, vận hành thiết bị và sản xuất vật liệu xây dựng.',
            'display_order' => 4,
        ],
        [
            'id' => 'giao_thong_logistics',
            'name' => Skill::CAREER_FIELDS['giao_thong_logistics'],
            'icon' => 'fa-solid fa-truck-fast',
            'description' => 'Vận tải, kho vận, chuỗi cung ứng và dịch vụ logistics.',
            'display_order' => 5,
        ],
        [
            'id' => 'det_may',
            'name' => Skill::CAREER_FIELDS['det_may'],
            'icon' => 'fa-solid fa-shirt',
            'description' => 'May công nghiệp, thiết kế, kiểm soát chất lượng và da giày.',
            'display_order' => 6,
        ],
        [
            'id' => 'du_lich',
            'name' => Skill::CAREER_FIELDS['du_lich'],
            'icon' => 'fa-solid fa-hotel',
            'description' => 'Lưu trú, ẩm thực, lữ hành và dịch vụ khách hàng.',
            'display_order' => 7,
        ],
        [
            'id' => 'nong_nghiep',
            'name' => Skill::CAREER_FIELDS['nong_nghiep'],
            'icon' => 'fa-solid fa-seedling',
            'description' => 'Nông nghiệp công nghệ cao, thủy sản và chế biến thực phẩm.',
            'display_order' => 8,
        ],
        [
            'id' => 'kinh_doanh',
            'name' => Skill::CAREER_FIELDS['kinh_doanh'],
            'icon' => 'fa-solid fa-chart-line',
            'description' => 'Kinh doanh, bán hàng, kế toán, quản trị và hành chính.',
            'display_order' => 9,
        ],
        [
            'id' => 'y_te',
            'name' => Skill::CAREER_FIELDS['y_te'],
            'icon' => 'fa-solid fa-heart-pulse',
            'description' => 'Điều dưỡng, dược, chăm sóc và hỗ trợ sức khỏe.',
            'display_order' => 10,
        ],
        [
            'id' => 'hoa_chat_nang_luong',
            'name' => Skill::CAREER_FIELDS['hoa_chat_nang_luong'],
            'icon' => 'fa-solid fa-flask',
            'description' => 'Hóa chất, năng lượng, xử lý môi trường và công nghệ xanh.',
            'display_order' => 11,
        ],
        [
            'id' => 'van_hoa_nghe_thuat',
            'name' => Skill::CAREER_FIELDS['van_hoa_nghe_thuat'],
            'icon' => 'fa-solid fa-palette',
            'description' => 'Thiết kế, mỹ thuật, biểu diễn, báo chí và nghề thủ công.',
            'display_order' => 12,
        ],
        [
            'id' => 'giao_duc_dich_vu',
            'name' => Skill::CAREER_FIELDS['giao_duc_dich_vu'],
            'icon' => 'fa-solid fa-people-group',
            'description' => 'Giáo dục, công tác xã hội và các dịch vụ phục vụ cộng đồng.',
            'display_order' => 13,
        ],
    ];

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class, 'career_field', 'id');
    }

    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class, 'industry', 'id');
    }

    public static function options(bool $onlyActive = false): array
    {
        try {
            if (Schema::hasTable('career_fields') && static::query()->exists()) {
                return static::query()
                    ->when($onlyActive, fn ($query) => $query->where('is_active', true))
                    ->orderBy('display_order')
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all();
            }
        } catch (\Throwable) {
            // Fall back cleanly when running before migrations or inside minimal schema tests.
        }

        return Skill::CAREER_FIELDS;
    }

    public static function publicRows(bool $onlyActive = true): Collection
    {
        try {
            if (Schema::hasTable('career_fields') && static::query()->exists()) {
                return static::query()
                    ->when($onlyActive, fn ($query) => $query->where('is_active', true))
                    ->orderBy('display_order')
                    ->orderBy('name')
                    ->get(['id', 'name', 'icon', 'description', 'display_order']);
            }
        } catch (\Throwable) {
            // Fall back cleanly when running before migrations or inside minimal schema tests.
        }

        return collect(self::DEFAULTS);
    }

    protected static function booted(): void
    {
        static::saving(function (CareerField $field): void {
            $field->name = trim((string) $field->name);
            $field->icon = filled($field->icon) ? trim((string) $field->icon) : null;
            $field->description = filled($field->description) ? trim((string) $field->description) : null;

            if (blank($field->id)) {
                $base = Str::slug($field->name, '_') ?: 'linh_vuc';
                $base = Str::limit($base, 50, '');
                $candidate = $base;
                $suffix = 2;
                while (static::query()->whereKey($candidate)->exists()) {
                    $candidate = "{$base}_{$suffix}";
                    $suffix++;
                }
                $field->id = $candidate;
            } else {
                $field->id = Str::of((string) $field->id)->trim()->lower()->replace('-', '_')->toString();
            }

            if (blank($field->display_order)) {
                $field->display_order = ((int) static::max('display_order')) + 1;
            }
        });
    }
}
