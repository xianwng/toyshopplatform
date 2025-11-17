<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'username' => 'sysadmin',
            'email' => 'sysadmin@toyshop.com',
            'password' => bcrypt('Admin123!'), // Change this!
            'role' => 'admin',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->command->info('Admin account created successfully!');
        $this->command->info('Username: sysadmin');
        $this->command->info('Email: sysadmin@toyshop.com');
        $this->command->info('Password: Admin123!');
    }
}