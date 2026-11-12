<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class JobPosting extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (JobPosting $job): void {
            if (blank($job->id)) {
                $job->id = 'job-'.Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return ['details' => 'array', 'deadline' => 'date', 'recruitment_count' => 'integer'];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function careerField(): BelongsTo
    {
        return $this->belongsTo(CareerField::class, 'industry', 'id');
    }

    public function publicData(): array
    {
        return array_replace([
            'address' => '', 'ward_name' => '', 'ward_code' => null,
            'salary' => '', 'salary_level' => 'all', 'education' => '', 'experience' => '',
            'profession' => $this->title, 'industry_name' => '', 'is_hot' => false,
            'description' => [], 'requirements' => [], 'benefits' => [],
            'skills' => [], 'training_schools' => [], 'demands' => [], 'contact' => [],
        ], $this->details ?? [], [
            'id' => $this->id,
            'employer_id' => $this->employer_id,
            'title' => $this->title,
            'company' => $this->employer->name,
            'company_logo' => $this->employer->public_profile['logo'] ?? ($this->details['company_logo'] ?? 'DN'),
            'company_color' => $this->employer->public_profile['color'] ?? ($this->details['company_color'] ?? 'bg-blue-700'),
            'industry' => $this->industry,
            'recruitment_count' => $this->recruitment_count,
            'deadline' => $this->deadline?->format('d/m/Y'),
        ]);
    }
}
