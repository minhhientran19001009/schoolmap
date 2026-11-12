<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employer extends Model
{
    protected $fillable = [
        'name', 'aliases', 'tax_code', 'industry', 'region', 'employee_count',
        'source_code', 'ward_id', 'address', 'contact_person', 'phone', 'email',
        'website', 'scope', 'data_note', 'source_details', 'reviewed_at', 'status',
        'is_verified', 'source', 'external_id', 'public_id', 'public_profile',
    ];

    protected function casts(): array
    {
        return [
            'aliases' => 'array',
            'employee_count' => 'integer',
            'source_details' => 'array',
            'reviewed_at' => 'date',
            'is_verified' => 'boolean',
            'public_profile' => 'array',
        ];
    }

    public function account(): HasOne
    {
        return $this->hasOne(User::class, 'employer_id');
    }

    public function demands(): HasMany
    {
        return $this->hasMany(WorkforceDemand::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class);
    }
}
