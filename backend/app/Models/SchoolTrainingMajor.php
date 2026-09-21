<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolTrainingMajor extends Model
{
    protected $table = 'school_training_major';

    protected $fillable = [
        'school_id',
        'training_major_id',
        'degree_level',
        'annual_quota',
    ];

    protected $casts = [
        'annual_quota' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function trainingMajor()
    {
        return $this->belongsTo(TrainingMajor::class);
    }
}
