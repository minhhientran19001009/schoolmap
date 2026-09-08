<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'username'      => 'admin',
                'full_name'     => 'Quản trị viên',
                'password_hash' => Hash::make('12345678'),
                'role'          => 'SUPER_ADMIN',
                'is_active'     => 1,
            ]
        );

        $this->command->info("🎉 Tài khoản Quản trị viên Admin đã tạo thành công!");
        $this->command->info("👉 Email:    admin@gmail.com");
        $this->command->info("👉 Mật khẩu: 12345678");
    }
}
