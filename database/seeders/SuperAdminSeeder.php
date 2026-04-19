<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ── Super Admin (Non-deletable, Full Access) ───────────────────────
        User::updateOrCreate(
            ['email' => 'kushaldoshi26@gmail.com'],
            [
                'name'         => 'Kushal Doshi',
                'email'        => 'kushaldoshi26@gmail.com',
                'password'     => Hash::make('Dabhik26@'),
                'role'         => 'super_admin',
                'is_active'    => true,
                'is_deletable' => false,
                'admin_level'  => 'super', // Full access
            ]
        );

        // 2. ── Manager Admin (Mid-level) ─────────────────────────────────────
        //    Can: Products, Orders, Categories, Coupons, Reviews, Subscriptions
        //    Cannot: User management, Admin management
        User::updateOrCreate(
            ['email' => 'manager@aushvera.com'],
            [
                'name'        => 'Store Manager',
                'email'       => 'manager@aushvera.com',
                'password'    => Hash::make('Manager@123'),
                'role'        => 'admin',
                'admin_level' => 'manager',
                'is_active'   => true,
            ]
        );

        // 3. ── Staff Admin (Low-level) ────────────────────────────────────────
        //    Can: View Orders, View Products (read-only)
        //    Cannot: Edit/Delete anything, Users, Coupons, Categories, Admins
        User::updateOrCreate(
            ['email' => 'staff@aushvera.com'],
            [
                'name'        => 'Support Staff',
                'email'       => 'staff@aushvera.com',
                'password'    => Hash::make('Staff@123'),
                'role'        => 'admin',
                'admin_level' => 'staff',
                'is_active'   => true,
            ]
        );

        // 4. ── Secondary Admin (kept for compatibility, full admin) ───────────
        User::updateOrCreate(
            ['email' => 'admin@aushvera.com'],
            [
                'name'        => 'Admin User',
                'email'       => 'admin@aushvera.com',
                'password'    => Hash::make('admin123'),
                'role'        => 'admin',
                'admin_level' => 'manager',
                'is_active'   => true,
            ]
        );
    }
}
