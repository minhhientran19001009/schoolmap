<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrainingMajor extends Model
{
    protected $fillable = [
        'name',
        'code',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (TrainingMajor $major): void {
            $major->name = trim((string) $major->name);
            $major->code = filled($major->code) ? trim((string) $major->code) : null;
            $major->slug = Str::slug($major->name);
        });
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_training_major')
            ->withPivot('degree_level', 'annual_quota')
            ->withTimestamps();
    }

    public function assignments()
    {
        return $this->hasMany(SchoolTrainingMajor::class);
    }

    public function workforceDemands()
    {
        return $this->hasMany(WorkforceDemand::class);
    }

    public function trainingCapacities()
    {
        return $this->hasMany(TrainingCapacity::class);
    }
}
