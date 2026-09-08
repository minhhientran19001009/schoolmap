<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ward extends Model
{
    protected $table = 'wards';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id', 'code', 'name', 'full_name', 'unit_type', 'postal_code', 'area_km2'
    ];

    protected static function booted()
    {
        static::saving(function ($ward) {
            if (!empty($ward->full_name)) {
                $ward->name = trim(str_ireplace(['Phường', 'Xã', 'Thị trấn'], '', $ward->full_name));
            }
            if (empty($ward->id)) {
                $ward->id = Str::slug($ward->full_name ?: $ward->name);
            }
            if (empty($ward->code)) {
                $ward->code = $ward->postal_code ?: (string) rand(10000, 99999);
            }
        });
    }
}
