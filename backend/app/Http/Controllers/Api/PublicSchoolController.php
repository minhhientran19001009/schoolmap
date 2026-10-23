<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EducationLevel;
use App\Models\School;
use App\Models\TrainingMajor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicSchoolController extends Controller
{
    /**
     * Map of district keywords and wards for resolving and filtering districts.
     */
    private const DISTRICT_KEYWORDS = [
        'tp-ninh-binh' => [
            'name' => 'Thành phố Ninh Bình',
            'keywords' => ['thành phố ninh bình', 'tp ninh bình', 'tp. ninh bình'],
            'wards' => [
                'Phường Bích Đào', 'Phường Đông Thành', 'Phường Nam Bình', 'Phường Nam Thành',
                'Phường Ninh Khánh', 'Phường Ninh Phong', 'Phường Ninh Sơn', 'Phường Phúc Thành',
                'Phường Tân Thành', 'Phường Thanh Bình', 'Phường Vân Giang', 'Xã Ninh Nhất',
                'Xã Ninh Phúc', 'Xã Ninh Tiến',
            ],
        ],
        'tp-tam-diep' => [
            'name' => 'Thành phố Tam Điệp',
            'keywords' => ['tam điệp'],
            'wards' => [
                'Phường Bắc Sơn', 'Phường Nam Sơn', 'Phường Tân Bình', 'Phường Tây Sơn',
                'Phường Trung Sơn', 'Phường Yên Bình', 'Xã Đông Sơn', 'Xã Quang Sơn', 'Xã Yên Sơn',
            ],
        ],
        'h-hoa-lu' => [
            'name' => 'Huyện Hoa Lư',
            'keywords' => ['hoa lư'],
            'wards' => [
                'Thị trấn Thiên Tôn', 'Xã Ninh An', 'Xã Ninh Giang', 'Xã Ninh Hải',
                'Xã Ninh Hòa', 'Xã Ninh Khang', 'Xã Ninh Mỹ', 'Xã Ninh Thắng',
                'Xã Ninh Vân', 'Xã Ninh Xuân', 'Xã Trường Yên', 'Phường Hoa Lư',
            ],
        ],
        'h-gia-vien' => [
            'name' => 'Huyện Gia Viễn',
            'keywords' => ['gia viễn'],
            'wards' => [
                'Thị trấn Me', 'Xã Gia Hòa', 'Xã Gia Hưng', 'Xã Gia Lập',
                'Xã Gia Minh', 'Xã Gia Phong', 'Xã Gia Phú', 'Xã Gia Sinh',
                'Xã Gia Tân', 'Xã Gia Thắng', 'Xã Gia Thanh', 'Xã Gia Thịnh',
                'Xã Gia Trấn', 'Xã Gia Trung', 'Xã Gia Vượng', 'Xã Gia Xuân', 'Xã Gia Viễn',
            ],
        ],
        'h-nho-quan' => [
            'name' => 'Huyện Nho Quan',
            'keywords' => ['nho quan'],
            'wards' => [
                'Thị trấn Nho Quan', 'Xã Cúc Phương', 'Xã Đồng Phong', 'Xã Đức Long',
                'Xã Gia Lâm', 'Xã Gia Sơn', 'Xã Gia Thủy', 'Xã Gia Tường',
                'Xã Kỳ Phú', 'Xã Lạc Vân', 'Xã Lạng Phong', 'Xã Phú Long',
                'Xã Phú Lộc', 'Xã Phú Sơn', 'Xã Quảng Lạc', 'Xã Quỳnh Lưu',
                'Xã Sơn Hà', 'Xã Sơn Lai', 'Xã Sơn Thành', 'Xã Thạch Bình',
                'Xã Thanh Lạc', 'Xã Thượng Hòa', 'Xã Văn Phong', 'Xã Văn Phú',
                'Xã Văn Phương', 'Xã Xích Thổ', 'Xã Yên Quang', 'Xã Nho Quan',
            ],
        ],
        'h-kim-son' => [
            'name' => 'Huyện Kim Sơn',
            'keywords' => ['kim sơn', 'phát diệm'],
            'wards' => [
                'Thị trấn Phát Diệm', 'Xã Phát Diệm', 'Thị trấn Bình Minh', 'Xã Ân Hòa',
                'Xã Chất Bình', 'Xã Cồn Thoi', 'Xã Định Hóa', 'Xã Đồng Hướng',
                'Xã Hồi Ninh', 'Xã Hùng Tiến', 'Xã Kim Chính', 'Xã Kim Định',
                'Xã Kim Đông', 'Xã Kim Hải', 'Xã Kim Mỹ', 'Xã Kim Tân',
                'Xã Kim Trung', 'Xã Lai Thành', 'Xã Lưu Phương', 'Xã Như Hòa',
                'Xã Quang Thiện', 'Xã Rạng Đông', 'Xã Tân Thành', 'Xã Thượng Kiệm',
                'Xã Tiền Hải', 'Xã Văn Hải', 'Xã Xuân Chính', 'Xã Xuân Thiện', 'Xã Yên Lộc', 'Xã Yên Mật',
            ],
        ],
        'h-yen-khanh' => [
            'name' => 'Huyện Yên Khánh',
            'keywords' => ['yên khánh'],
            'wards' => [
                'Thị trấn Yên Ninh', 'Xã Khánh An', 'Xã Khánh Cư', 'Xã Khánh Cường',
                'Xã Khánh Hải', 'Xã Khánh Hòa', 'Xã Khánh Hội', 'Xã Khánh Hồng',
                'Xã Khánh Lợi', 'Xã Khánh Mậu', 'Xã Khánh Nhạc', 'Xã Khánh Ninh',
                'Xã Khánh Phú', 'Xã Khánh Tiên', 'Xã Khánh Thành', 'Xã Khánh Thiện',
                'Xã Khánh Thủy', 'Xã Khánh Trung', 'Xã Khánh Vân',
            ],
        ],
        'h-yen-mo' => [
            'name' => 'Huyện Yên Mô',
            'keywords' => ['yên mô'],
            'wards' => [
                'Thị trấn Yên Thịnh', 'Xã Khánh Dương', 'Xã Khánh Thịnh', 'Xã Khánh Thượng',
                'Xã Mai Sơn', 'Xã Yên Đồng', 'Xã Yên Hòa', 'Xã Yên Lâm',
                'Xã Yên Mạc', 'Xã Yên Mỹ', 'Xã Yên Nhân', 'Xã Yên Phong',
                'Xã Yên Thái', 'Xã Yên Thắng', 'Xã Yên Từ', 'Xã Yên Thành', 'Phường Yên Thắng',
            ],
        ],
    ];

    /**
     * Get paginated public schools list with summary and filters.
     * GET /api/public/schools
     */
    public function index(Request $request): JsonResponse
    {
        $query = School::query()
            ->where('status', 'VERIFIED')
            ->select([
                'id', 'code', 'name', 'education_level_id', 'school_type_id', 'district_id',
                'ward', 'legacy_province', 'address', 'lat', 'lng', 'phone', 'email', 'website',
                'status', 'updated_at',
            ]);

        // 1. Text search (unaccented / accent-insensitive on school name/code or major name/code)
        if ($search = trim((string) $request->input('q', ''))) {
            $isSqlite = DB::connection()->getDriverName() === 'sqlite';
            if ($isSqlite) {
                try {
                    $pdo = DB::connection()->getPdo();
                    if (method_exists($pdo, 'sqliteCreateFunction')) {
                        $pdo->sqliteCreateFunction('unaccent', fn ($str) => $str === null ? '' : Str::ascii($str));
                    }
                } catch (\Throwable) {
                }

                $asciiSearch = Str::ascii($search);
                $query->where(function ($sq) use ($search, $asciiSearch): void {
                    $sq->whereRaw("unaccent(name) LIKE ?", ["%{$asciiSearch}%"])
                        ->orWhere('code', 'LIKE', "%{$search}%")
                        ->orWhereHas('majors', function ($mq) use ($search, $asciiSearch): void {
                            $mq->whereRaw("unaccent(training_majors.name) LIKE ?", ["%{$asciiSearch}%"])
                                ->orWhere('training_majors.code', 'LIKE', "%{$search}%");
                        });
                });
            } else {
                $query->where(function ($sq) use ($search): void {
                    $sq->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%")
                        ->orWhereHas('majors', function ($mq) use ($search): void {
                            $mq->where('training_majors.name', 'LIKE', "%{$search}%")
                                ->orWhere('training_majors.code', 'LIKE', "%{$search}%");
                        });
                });
            }
        }

        // 2. Education level filter
        $level = trim((string) $request->input('level', ''));
        if ($level !== '' && $level !== 'all') {
            $normalizedLevel = str_replace('-', '_', $level);
            $query->whereHas('educationLevels', function ($lq) use ($normalizedLevel): void {
                $lq->where('education_levels.id', $normalizedLevel);
            });
        }

        // 3. Training major filter
        $majorId = $request->integer('major_id');
        if ($majorId > 0) {
            $query->whereHas('majors', function ($mq) use ($majorId): void {
                $mq->where('training_majors.id', $majorId);
            });
        }

        // 4. School type filter
        $schoolType = trim((string) $request->input('school_type', ''));
        if ($schoolType !== '' && $schoolType !== 'all') {
            $query->where('school_type_id', $schoolType);
        }

        // 5. Ward filter
        $ward = trim((string) $request->input('ward', ''));
        if ($ward !== '' && $ward !== 'all') {
            $cleanWard = trim((string) preg_replace('/^(phường|xã|thị trấn)\s+/iu', '', $ward));
            $query->where(function ($wq) use ($ward, $cleanWard): void {
                $wq->where('ward', 'LIKE', "%{$ward}%")
                    ->orWhere('ward', 'LIKE', "%{$cleanWard}%")
                    ->orWhere('address', 'LIKE', "%{$cleanWard}%");
            });
        }

        // 6. District filter
        $districtId = trim((string) $request->input('district_id', ''));
        if ($districtId !== '' && $districtId !== 'all') {
            $this->applyDistrictFilter($query, $districtId);
        }

        // Summary statistics on matching schools BEFORE pagination
        $filteredIds = (clone $query)->pluck('schools.id');
        $schoolCount = $filteredIds->count();

        $levelCount = $schoolCount > 0
            ? DB::table('school_education_levels')
                ->whereIn('school_id', $filteredIds)
                ->distinct('education_level_id')
                ->count('education_level_id')
            : 0;

        $majorCount = $schoolCount > 0
            ? DB::table('school_training_major')
                ->whereIn('school_id', $filteredIds)
                ->distinct('training_major_id')
                ->count('training_major_id')
            : 0;

        $annualQuotaTotal = $schoolCount > 0
            ? (int) DB::table('school_training_major')
                ->whereIn('school_id', $filteredIds)
                ->sum('annual_quota')
            : 0;

        // Sorting
        $sort = trim((string) $request->input('sort', 'name'));
        match ($sort) {
            'name_desc' => $query->orderBy('name', 'desc'),
            'quota_desc' => $query->orderByDesc(
                DB::raw('(SELECT COALESCE(SUM(annual_quota), 0) FROM school_training_major WHERE school_training_major.school_id = schools.id)')
            )->orderBy('name', 'asc'),
            'newest' => $query->orderByDesc('updated_at'),
            default => $query->orderBy('name', 'asc'),
        };

        // Pagination
        $perPage = max(6, min(36, $request->integer('per_page', 12)));
        $page = max(1, $request->integer('page', 1));

        $paginator = $query
            ->with([
                'educationLevels:id,name,color,display_order',
                'majors' => function ($mq): void {
                    $mq->select(['training_majors.id', 'training_majors.name', 'training_majors.code'])
                        ->withPivot(['degree_level', 'annual_quota'])
                        ->orderBy('training_majors.name');
                },
            ])
            ->paginate($perPage, ['*'], 'page', $page);

        // Map school items
        $data = $paginator->getCollection()->map(function (School $school): array {
            $educationLevels = $school->educationLevels
                ->sortBy('display_order')
                ->map(fn (EducationLevel $lvl): array => [
                    'id' => $lvl->id,
                    'name' => $lvl->name,
                    'color' => $lvl->color ?: '#16a34a',
                ])
                ->values()
                ->all();

            $allMajors = $school->majors;
            $annualQuotaTotal = (int) $allMajors->sum('pivot.annual_quota');
            $majorCount = $allMajors->count();

            // Preview top 3 majors
            $previewMajors = $allMajors
                ->take(3)
                ->map(fn (TrainingMajor $major): array => [
                    'id' => $major->id,
                    'name' => $major->name,
                    'code' => $major->code,
                    'degree_level' => $major->pivot?->degree_level,
                    'annual_quota' => (int) ($major->pivot?->annual_quota ?? 0),
                ])
                ->values()
                ->all();

            $districtInfo = $this->resolveDistrict($school);

            return [
                'id' => $school->id,
                'code' => $school->code,
                'name' => $school->name,
                'education_levels' => $educationLevels,
                'school_type' => $school->school_type_id,
                'school_type_name' => $school->school_type_id === 'tu_thuc' ? 'Ngoài công lập / Tư thục' : 'Công lập',
                'district_id' => $districtInfo['id'],
                'district_name' => $districtInfo['name'],
                'ward' => $school->ward,
                'address' => $school->address ?: ($school->ward ? $school->ward . ', Tỉnh Ninh Bình' : 'Tỉnh Ninh Bình'),
                'phone' => $school->phone,
                'email' => $school->email,
                'website' => $school->website,
                'training_major_count' => $majorCount,
                'training_major_preview' => $previewMajors,
                'annual_quota_total' => $annualQuotaTotal,
                'lat' => (float) $school->lat,
                'lng' => (float) $school->lng,
                'status' => $school->status,
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'summary' => [
                    'school_count' => $schoolCount,
                    'level_count' => $levelCount,
                    'major_count' => $majorCount,
                    'annual_quota_total' => $annualQuotaTotal,
                ],
            ],
        ])
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
    }

    /**
     * Get filter catalogs for the public school directory.
     * GET /api/public/schools/catalogs
     */
    public function catalogs(): JsonResponse
    {
        $levels = EducationLevel::query()
            ->orderBy('display_order')
            ->orderBy('name')
            ->get(['id', 'name', 'color', 'icon', 'display_order']);

        $districts = collect(self::DISTRICT_KEYWORDS)->map(function ($val, $key) {
            return [
                'id' => $key,
                'name' => $val['name'],
                'wards' => $val['wards'],
            ];
        })->values();

        // Full list of wards from DB wards table (129 wards in Ninh Binh) or fallback to schools
        $wards = \Illuminate\Support\Facades\Schema::hasTable('wards')
            ? DB::table('wards')
                ->select(['id', 'code', 'name', 'full_name', 'unit_type'])
                ->orderBy('name')
                ->get()
                ->map(fn ($w) => [
                    'id' => (string) $w->id,
                    'code' => (string) ($w->code ?? ''),
                    'name' => (string) $w->name,
                    'full_name' => (string) ($w->full_name ?: (($w->unit_type ?? 'Xã') . ' ' . $w->name)),
                    'unit_type' => (string) ($w->unit_type ?: 'Xã'),
                ])
                ->values()
            : School::query()
                ->where('status', 'VERIFIED')
                ->whereNotNull('ward')
                ->select('ward')
                ->distinct()
                ->orderBy('ward')
                ->get()
                ->map(fn ($s) => [
                    'id' => (string) $s->ward,
                    'code' => '',
                    'name' => (string) preg_replace('/^(phường|xã|thị trấn)\s+/iu', '', (string) $s->ward),
                    'full_name' => (string) $s->ward,
                    'unit_type' => str_starts_with((string) $s->ward, 'Phường') ? 'Phường' : (str_starts_with((string) $s->ward, 'Thị trấn') ? 'Thị trấn' : 'Xã'),
                ])
                ->values();

        $majors = TrainingMajor::query()
            ->where('is_active', true)
            ->has('schools')
            ->withCount(['schools' => fn ($q) => $q->where('status', 'VERIFIED')])
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'slug']);

        $schoolTypes = [
            ['id' => 'cong_lap', 'name' => 'Công lập'],
            ['id' => 'tu_thuc', 'name' => 'Ngoài công lập / Tư thục'],
        ];

        return response()->json([
            'education_levels' => $levels,
            'districts' => $districts,
            'wards' => $wards,
            'school_types' => $schoolTypes,
            'training_majors' => $majors,
        ])
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
    }

    /**
     * Filter query by district id.
     */
    private function applyDistrictFilter($query, string $districtId): void
    {
        $info = self::DISTRICT_KEYWORDS[$districtId] ?? null;

        $query->where(function ($dq) use ($districtId, $info): void {
            $dq->where('district_id', $districtId);

            if ($info) {
                // Match by wards in this district
                if (!empty($info['wards'])) {
                    $dq->orWhereIn('ward', $info['wards']);
                }

                // Match by keyword in address
                foreach ($info['keywords'] as $kw) {
                    $dq->orWhere('address', 'LIKE', "%{$kw}%");
                }
            }
        });
    }

    /**
     * Resolve district ID and name for a school.
     */
    private function resolveDistrict(School $school): array
    {
        if ($school->district_id && isset(self::DISTRICT_KEYWORDS[$school->district_id])) {
            return [
                'id' => $school->district_id,
                'name' => self::DISTRICT_KEYWORDS[$school->district_id]['name'],
            ];
        }

        // Try matching by ward
        if ($school->ward) {
            foreach (self::DISTRICT_KEYWORDS as $dId => $dInfo) {
                if (in_array($school->ward, $dInfo['wards'], true)) {
                    return [
                        'id' => $dId,
                        'name' => $dInfo['name'],
                    ];
                }
            }
        }

        // Try matching by address
        if ($school->address) {
            $addr = mb_strtolower($school->address, 'UTF-8');
            foreach (self::DISTRICT_KEYWORDS as $dId => $dInfo) {
                foreach ($dInfo['keywords'] as $kw) {
                    if (str_contains($addr, $kw)) {
                        return [
                            'id' => $dId,
                            'name' => $dInfo['name'],
                        ];
                    }
                }
            }
        }

        return [
            'id' => $school->district_id,
            'name' => $school->district_id ?: 'Ninh Bình',
        ];
    }
}
