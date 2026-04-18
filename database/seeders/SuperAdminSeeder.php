<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Main Super Admin (NON-DELETABLE)
        User::updateOrCreate(
            ['email' => 'kushaldoshi26@gmail.com'],
            [
                'name'         => 'Kushal Doshi',
                'email'        => 'kushaldoshi26@gmail.com',
                'password'     => Hash::make('Dabhik26@'),
                'role'         => 'super_admin',
                'is_active'    => true,
                'is_deletable' => false,
            ]
        );

        // 2. Secondary Admin (for testing)
        User::updateOrCreate(
            ['email' => 'admin@aushvera.com'],
            [
                'name'      => 'Admin User',
                'email'     => 'admin@aushvera.com',
                'password'  => Hash::make('admin123'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );
    }
}
