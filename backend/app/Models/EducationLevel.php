<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EducationLevel extends Model
{
    protected $table = 'education_levels';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id', 'name', 'color', 'icon', 'display_order'
    ];

    protected static function booted()
    {
        static::saving(function ($level) {
            if (empty($level->id)) {
                $level->id = Str::slug($level->name);
            }
            if (empty($level->color)) {
                $level->color = '#3b82f6';
            }
            if (empty($level->display_order)) {
                $level->display_order = (static::max('display_order') ?: 0) + 1;
            }
        });
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_education_levels', 'education_level_id', 'school_id');
    }
}
