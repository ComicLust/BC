<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Eğer kullanıcı zaten varsa oluşturma
        if (User::where('email', 'info@tads.online')->exists()) {
            return;
        }

        User::create([
            'name' => 'Admin',
            'email' => 'info@tads.online',
            'password' => bcrypt('admin123'),
            'is_admin' => true,
            'email_notifications' => true,
            'backlink_status_notifications' => true,
            'metric_change_notifications' => true,
        ]);
    }
}
