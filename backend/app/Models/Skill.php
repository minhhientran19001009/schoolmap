<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Skill extends Model
{
    protected $fillable = ['name', 'normalized_name', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public static function normalize(string $value): string
    {
        return Str::of($value)->trim()->squish()->ascii()->lower()->toString();
    }

    protected static function booted(): void
    {
        static::saving(function (Skill $skill): void {
            $skill->name = trim($skill->name);
            $skill->normalized_name = static::normalize($skill->name);
        });
    }
}
