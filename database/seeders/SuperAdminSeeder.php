<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'nikunj@superadmin.com'],
            [
                'name'      => 'Nikunj',
                'email'     => 'nikunj@superadmin.com',
                'password'  => Hash::make('Nikunj@2025!'),
                'role'      => 'super_admin',
                'is_active' => true,
            ]
        );
    }
}
