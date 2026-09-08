<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Tên cột password trong DB là password_hash (theo schema.sql)
     */
    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    /**
     * Cho phép tài khoản đăng nhập vào Filament Admin trong môi trường Production
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Bảng users không có cột updated_at (schema.sql chỉ có created_at)
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'username',
        'email',
        'password_hash',
        'role',
        'district_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Filament cần thuộc tính 'name' để hiển thị tên người dùng
     */
    public function getNameAttribute(): string
    {
        return $this->full_name ?? $this->username;
    }

    protected static function booted(): void
    {
        static::saving(function ($user) {
            if (empty($user->username)) {
                $user->username = explode('@', (string) $user->email)[0] ?: 'admin';
            }
        });
    }
}
