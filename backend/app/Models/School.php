<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class School extends Model
{
    protected $table = 'schools';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'code', 'name', 'campus_note', 'campus_type', 'campus_name', 'parent_school_id',
        'education_level_id', 'school_type_id', 'special_type', 'district_id',
        'ward', 'legacy_province', 'address', 'lat', 'lng', 'phone', 'email', 'website',
        'principal', 'leaders', 'training_majors', 'is_national_standard', 'national_standard_level', 'founded_year',
        'student_count', 'annual_enrollment', 'annual_graduates', 'employment_rate',
        'teacher_count', 'teacher_quota', 'teachers_shortage', 'teachers_surplus',
        'faculty_rank_1', 'faculty_rank_2', 'faculty_rank_3',
        'faculty_doctors', 'faculty_masters', 'faculty_professors',
        'partner_enterprises',
        'class_count', 'classroom_count', 'computer_room_count', 'library', 'lab_count', 'workshops_count',
        'campus_area_m2', 'gallery', 'status', 'last_verified_at', 'geom'
    ];

    protected $hidden = [
        'geom',
    ];

    protected $casts = [
        'is_national_standard' => 'boolean',
        'library' => 'boolean',
        'lat' => 'float',
        'lng' => 'float',
        'student_count' => 'integer',
        'annual_enrollment' => 'integer',
        'annual_graduates' => 'integer',
        'employment_rate' => 'float',
        'teacher_count' => 'integer',
        'teacher_quota' => 'integer',
        'teachers_shortage' => 'integer',
        'teachers_surplus' => 'integer',
        'faculty_rank_1' => 'integer',
        'faculty_rank_2' => 'integer',
        'faculty_rank_3' => 'integer',
        'faculty_doctors' => 'integer',
        'faculty_masters' => 'integer',
        'faculty_professors' => 'integer',
        'workshops_count' => 'integer',
        'class_count' => 'integer',
        'classroom_count' => 'integer',
        'computer_room_count' => 'integer',
        'lab_count' => 'integer',
        'campus_area_m2' => 'float',
        'last_verified_at' => 'date',
    ];

    protected static function booted()
    {
        static::saving(function ($school) {
            if (empty($school->id)) {
                $slug = Str::slug($school->name) ?: 'truong';
                $baseId = substr($slug, 0, 40);
                $candidateId = $baseId . '-' . rand(100, 999);
                while (static::where('id', $candidateId)->exists()) {
                    $candidateId = $baseId . '-' . rand(1000, 9999);
                }
                $school->id = $candidateId;
            }

            if (empty($school->code)) {
                $candidateCode = 'NB-' . str_pad((string) rand(1000, 99999), 5, '0', STR_PAD_LEFT);
                while (static::where('code', $candidateCode)->where('id', '!=', $school->id)->exists()) {
                    $candidateCode = 'NB-' . str_pad((string) rand(1000, 99999), 5, '0', STR_PAD_LEFT);
                }
                $school->code = $candidateCode;
            }

            if ($school->is_national_standard === null) {
                $school->is_national_standard = false;
            }

            if (empty($school->education_level_id) || !EducationLevel::where('id', $school->education_level_id)->exists()) {
                $school->education_level_id = EducationLevel::first()?->id ?? 'cao_dang';
            }

            if (empty($school->school_type_id)) {
                $school->school_type_id = 'cong_lap';
            }

            $school->normalizeCampusRelationship();

            if (empty($school->lat) || empty($school->lng)) {
                $school->lat = 20.2506;
                $school->lng = 105.9745;
            }

            $school->geom = DB::raw("Point({$school->lng}, {$school->lat})");
        });
    }

    /** @return array<string, string> */
    public static function campusTypeLabels(): array
    {
        return [
            'MAIN' => 'Cơ sở chính',
            'CAMPUS' => 'Cơ sở trực thuộc',
            'BRANCH' => 'Phân hiệu',
        ];
    }

    /** Keep the hierarchy to one level and block invalid/cyclic relations. */
    protected function normalizeCampusRelationship(): void
    {
        $types = static::campusTypeLabels();
        $type = strtoupper((string) ($this->campus_type ?: 'MAIN'));

        if (! array_key_exists($type, $types)) {
            throw ValidationException::withMessages(['campus_type' => 'Loại cơ sở không hợp lệ.']);
        }

        $this->campus_type = $type;
        $this->campus_name = filled($this->campus_name) ? trim((string) $this->campus_name) : null;

        if ($type === 'MAIN') {
            $this->parent_school_id = null;

            return;
        }

        $parentId = filled($this->parent_school_id) ? (string) $this->parent_school_id : null;
        if ($parentId === null) {
            throw ValidationException::withMessages([
                'parent_school_id' => 'Cơ sở trực thuộc phải chọn một cơ sở chính.',
            ]);
        }

        if ($parentId === (string) $this->getKey()) {
            throw ValidationException::withMessages([
                'parent_school_id' => 'Một cơ sở không thể là cơ sở chính của chính nó.',
            ]);
        }

        $parent = static::query()->select(['id', 'campus_type', 'parent_school_id'])->find($parentId);
        if ($parent === null || $parent->campus_type !== 'MAIN' || $parent->parent_school_id !== null) {
            throw ValidationException::withMessages([
                'parent_school_id' => 'Chỉ có thể liên kết với một cơ sở chính hợp lệ.',
            ]);
        }

        if (blank($this->campus_note)) {
            $this->campus_note = $this->campus_name ?: $types[$type];
        }
    }

    public function wardRelation()
    {
        return $this->belongsTo(Ward::class, 'ward', 'name');
    }

    public function educationLevels()
    {
        return $this->belongsToMany(EducationLevel::class, 'school_education_levels', 'school_id', 'education_level_id');
    }

    public function parentCampus()
    {
        return $this->belongsTo(School::class, 'parent_school_id');
    }

    public function childCampuses()
    {
        return $this->hasMany(School::class, 'parent_school_id')
            ->orderBy('name');
    }

    /**
     * Some historical imports stored a JSON string instead of a JSON array.
     * Filament Repeaters require an array, so normalize the legacy values at
     * the model boundary and persist the correct JSON shape on later saves.
     *
     * @return array<int, mixed>
     */
    private function decodeListAttribute(mixed $value): array
    {
        if (is_array($value)) {
            return array_is_list($value) ? $value : [$value];
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (is_array($decoded)) {
                return array_is_list($decoded) ? $decoded : [$decoded];
            }

            if (is_string($decoded) && trim($decoded) !== '') {
                return [$decoded];
            }
        }

        return [$value];
    }

    /** @return array<int, string> */
    private function splitLegacyList(string $value): array
    {
        return array_values(array_filter(
            preg_split('/\s*;\s*|\r?\n/', trim($value)) ?: [],
            fn (string $item): bool => $item !== '',
        ));
    }

    /** @return array<int, array{name: string, position: string}> */
    public function getLeadersAttribute(mixed $value): array
    {
        $leaders = [];
        foreach ($this->decodeListAttribute($value) as $item) {
            if (is_array($item) && filled($item['name'] ?? null)) {
                $leaders[] = [
                    'name' => trim((string) $item['name']),
                    'position' => trim((string) ($item['position'] ?? 'Lãnh đạo')),
                ];
                continue;
            }

            if (is_string($item)) {
                foreach ($this->splitLegacyList($item) as $entry) {
                    [$position, $name] = array_pad(explode(':', $entry, 2), 2, '');
                    $name = trim($name ?: $position);
                    if ($name !== '') {
                        $leaders[] = [
                            'name' => $name,
                            'position' => trim($name === $position ? 'Lãnh đạo' : $position),
                        ];
                    }
                }
            }
        }

        return $leaders;
    }

    public function setLeadersAttribute(mixed $value): void
    {
        $this->attributes['leaders'] = empty($value)
            ? null
            : json_encode($this->getLeadersAttribute($value), JSON_UNESCAPED_UNICODE);
    }

    /** @return array<int, array{name: string, degree_level: string, major_code: ?string, annual_quota: int}> */
    public function getTrainingMajorsAttribute(mixed $value): array
    {
        $majors = [];
        foreach ($this->decodeListAttribute($value) as $item) {
            if (is_array($item) && filled($item['name'] ?? null)) {
                $majors[] = [
                    'name' => trim((string) $item['name']),
                    'degree_level' => (string) ($item['degree_level'] ?? 'cao_dang'),
                    'major_code' => filled($item['major_code'] ?? null) ? trim((string) $item['major_code']) : null,
                    'annual_quota' => max(0, (int) ($item['annual_quota'] ?? 0)),
                ];
                continue;
            }

            if (is_string($item)) {
                foreach ($this->splitLegacyList($item) as $entry) {
                    [$name, $quota] = array_pad(explode(':', $entry, 2), 2, '');
                    $name = trim($name);
                    if ($name !== '') {
                        $majors[] = [
                            'name' => $name,
                            'degree_level' => 'cao_dang',
                            'major_code' => null,
                            'annual_quota' => max(0, (int) preg_replace('/\D+/', '', $quota)),
                        ];
                    }
                }
            }
        }

        return $majors;
    }

    public function setTrainingMajorsAttribute(mixed $value): void
    {
        $this->attributes['training_majors'] = empty($value)
            ? null
            : json_encode($this->getTrainingMajorsAttribute($value), JSON_UNESCAPED_UNICODE);
    }

    /** @return array<int, array{name: string, cooperation: string, logo: ?string, is_featured: bool}> */
    public function getPartnerEnterprisesAttribute(mixed $value): array
    {
        $partners = [];
        foreach ($this->decodeListAttribute($value) as $item) {
            if (is_array($item) && filled($item['name'] ?? null)) {
                $partners[] = [
                    'name' => trim((string) $item['name']),
                    'cooperation' => trim((string) ($item['cooperation'] ?? '')),
                    'logo' => filled($item['logo'] ?? null) ? (string) $item['logo'] : null,
                    'is_featured' => (bool) ($item['is_featured'] ?? true),
                ];
                continue;
            }

            if (is_string($item)) {
                foreach ($this->splitLegacyList($item) as $entry) {
                    [$name, $cooperation] = array_pad(explode(':', $entry, 2), 2, '');
                    $name = trim($name);
                    if ($name !== '') {
                        $partners[] = [
                            'name' => $name,
                            'cooperation' => trim($cooperation),
                            'logo' => null,
                            'is_featured' => true,
                        ];
                    }
                }
            }
        }

        return $partners;
    }

    public function setPartnerEnterprisesAttribute(mixed $value): void
    {
        $this->attributes['partner_enterprises'] = empty($value)
            ? null
            : json_encode($this->getPartnerEnterprisesAttribute($value), JSON_UNESCAPED_UNICODE);
    }

    /** @return array<int, string> */
    public function getGalleryAttribute(mixed $value): array
    {
        return array_values(array_filter(
            $this->decodeListAttribute($value),
            fn (mixed $item): bool => is_string($item) && trim($item) !== '',
        ));
    }

    public function setGalleryAttribute(mixed $value): void
    {
        $this->attributes['gallery'] = empty($value)
            ? null
            : json_encode($this->getGalleryAttribute($value), JSON_UNESCAPED_UNICODE);
    }
}
