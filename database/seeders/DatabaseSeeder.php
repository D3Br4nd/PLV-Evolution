<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create super admin from environment variables
        User::updateOrCreate(
            ['email' => env('SUPER_ADMIN_EMAIL', 'admin@prolocoventicanese.it')],
            [
                'name' => env('SUPER_ADMIN_NAME', 'Admin'),
                'password' => env('SUPER_ADMIN_PASSWORD', 'password'),
                'role' => UserRole::SuperAdmin->value,
                'membership_status' => 'active',
            ]
        );
    }
}