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
        // Xoá trùng nếu có tài khoản khác dùng email này
        User::where('email', 'admin@gmail.com')->where('username', '!=', 'admin')->delete();

        // Tìm tài khoản username 'admin' đã có hoặc tạo mới
        $user = User::where('username', 'admin')->first() ?? new User();
        $user->username      = 'admin';
        $user->email         = 'admin@gmail.com';
        $user->full_name     = 'Quản trị viên';
        $user->password_hash = Hash::make('12345678');
        $user->role          = 'SUPER_ADMIN';
        $user->is_active     = 1;
        $user->save();

        $this->command->info("🎉 Tài khoản Quản trị viên Admin đã tạo/cập nhật thành công!");
        $this->command->info("👉 Email:    admin@gmail.com");
        $this->command->info("👉 Username: admin");
        $this->command->info("👉 Mật khẩu: 12345678");
    }
}
