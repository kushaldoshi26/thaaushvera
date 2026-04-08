<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Super Admin
        User::updateOrCreate(
            ['email' => 'nikunj@superadmin.com'],
            [
                'name'      => 'Nikunj (Super)',
                'email'     => 'nikunj@superadmin.com',
                'password'  => Hash::make('Nikunj@2025!'),
                'role'      => 'super_admin',
                'is_active' => true,
            ]
        );

        // 2. Create standard Admin (for testing/general use)
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
