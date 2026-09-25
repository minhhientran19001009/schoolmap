<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Tên cột password mặc định
     */
    public function getAuthPasswordName(): string
    {
        return !empty($this->password_hash) ? 'password_hash' : 'password';
    }

    /**
     * Lấy chuỗi hash mật khẩu để xác thực: tự động kiểm tra cả password_hash và password
     */
    public function getAuthPassword()
    {
        if (!empty($this->password_hash) && str_starts_with($this->password_hash, '$2')) {
            return $this->password_hash;
        }

        if (!empty($this->password) && str_starts_with($this->password, '$2')) {
            return $this->password;
        }

        return $this->password_hash ?? $this->password ?? '';
    }

    /**
     * Restrict each Filament panel to the account type it serves.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if (! (bool) $this->is_active || filled($this->locked_at)) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => in_array($this->role, ['SUPER_ADMIN', 'DISTRICT_ADMIN', 'VIEWER'], true),
            'school' => $this->isSchoolAdmin() && filled($this->school_id),
            default => false,
        };
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
        'name',
        'username',
        'email',
        'password',
        'password_hash',
        'role',
        'district_id',
        'school_id',
        'is_active',
        'must_change_password',
        'password_changed_at',
        'locked_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'password_hash' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'locked_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function isSchoolAdmin(): bool
    {
        return $this->role === 'SCHOOL_ADMIN';
    }

    public function isSystemAdmin(): bool
    {
        return in_array($this->role, ['SUPER_ADMIN', 'DISTRICT_ADMIN'], true);
    }

    public function canManageSchool(School $school): bool
    {
        if (! $this->isSchoolAdmin() || blank($this->school_id)) {
            return false;
        }

        return (string) $school->getKey() === (string) $this->school_id
            || (string) $school->parent_school_id === (string) $this->school_id;
    }

    /**
     * Filament cần thuộc tính 'name' để hiển thị tên người dùng
     */
    public function getNameAttribute(): string
    {
        return $this->full_name ?? $this->attributes['name'] ?? $this->username;
    }

    protected static function booted(): void
    {
        static::saving(function ($user) {
            if (empty($user->username)) {
                $user->username = explode('@', (string) $user->email)[0] ?: 'admin';
            }

            // Đồng bộ 2 cột password và password_hash nếu một trong hai chưa có
            if (!empty($user->password) && empty($user->password_hash)) {
                $user->password_hash = $user->password;
            } elseif (!empty($user->password_hash) && empty($user->password)) {
                $user->password = $user->password_hash;
            }
        });
    }
}
