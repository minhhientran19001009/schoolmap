<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class School extends Model
{
    protected $table = 'schools';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'code', 'name', 'campus_note', 'education_level_id', 'school_type_id', 'special_type', 'district_id',
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
        'leaders' => 'array',
        'training_majors' => 'array',
        'partner_enterprises' => 'array',
        'gallery' => 'array',
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

            if (empty($school->lat) || empty($school->lng)) {
                $school->lat = 20.2506;
                $school->lng = 105.9745;
            }

            $school->geom = DB::raw("Point({$school->lng}, {$school->lat})");
        });
    }

    public function wardRelation()
    {
        return $this->belongsTo(Ward::class, 'ward', 'name');
    }

    public function educationLevels()
    {
        return $this->belongsToMany(EducationLevel::class, 'school_education_levels', 'school_id', 'education_level_id');
    }
}
