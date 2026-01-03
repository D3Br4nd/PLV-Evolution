<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Keep production clean by default.
        // Seed demo data only when explicitly enabled:
        //   PLV_DEMO_SEED=true php artisan db:seed
        if (env('PLV_DEMO_SEED', false)) {
            $this->call(DemoSeeder::class);
        }
    }
}