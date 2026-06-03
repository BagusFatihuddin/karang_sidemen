<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed super admin user.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            [
                'email' => env(
                    'SUPER_ADMIN_EMAIL',
                    'admin@pokdarwis.local'
                ),
            ],
            [
                'name' => env(
                    'SUPER_ADMIN_NAME',
                    'Super Admin'
                ),
                'password' => env(
                    'SUPER_ADMIN_PASSWORD',
                    'password'
                ),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );
    }
}