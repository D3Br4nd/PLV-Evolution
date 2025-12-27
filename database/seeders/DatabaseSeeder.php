<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\Membership;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        $admin = User::create([
            'name' => 'Massimiliano Admin',
            'email' => 'admin@prolocoventicanese.it',
            'password' => 'password', // Will be hashed by model cast
            'role' => 'admin',
            'membership_status' => 'active',
        ]);

        // Member User
        $member = User::create([
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@example.com',
            'password' => 'password',
            'role' => 'member',
            'membership_status' => 'active',
        ]);
        
        // Memberships
        Membership::create([
            'user_id' => $member->id,
            'year' => 2025,
            'qr_token' => Str::random(32),
            'status' => 'active',
        ]);

        // Events
        Event::create([
            'title' => '46ª Fiera Campionaria Venticano',
            'start_date' => '2025-04-24 09:00:00',
            'end_date' => '2025-04-27 22:00:00',
            'type' => 'fair',
            'metadata' => ['location' => 'Quartiere Fieristico', 'organizer' => 'Pro Loco Venticanese'],
        ]);

        Event::create([
            'title' => 'Sagra del Prosciutto',
            'start_date' => '2025-08-30 18:00:00',
            'end_date' => '2025-08-31 23:59:00',
            'type' => 'festival',
            'metadata' => ['location' => 'Piazza Monumento', 'theme' => 'Gastronomy'],
        ]);
    }
}
