<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DemoTrainingCapacity extends Model
{
    protected $fillable = [
        'school_id',
        'training_major_id',
        'education_level_id',
        'reporting_period_id',
        'quantity',
        'location',
        'duration',
        'skills',
        'equipment',
        'note',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'skills' => 'array',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(TrainingMajor::class, 'training_major_id');
    }

    public function trainingLevel(): BelongsTo
    {
        return $this->belongsTo(TrainingLevel::class, 'education_level_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(ReportingPeriod::class, 'reporting_period_id');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(DemoDemandProposal::class, 'demo_training_capacity_id');
    }
}
