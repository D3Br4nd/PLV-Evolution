<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@prolocoventicanese.it'],
            [
                'name' => 'Massimiliano Admin',
                'password' => 'password', // hashed by model cast
                'role' => UserRole::SuperAdmin->value,
                'membership_status' => 'active',
            ]
        );

        $member = User::updateOrCreate(
            ['email' => 'mario.rossi@example.com'],
            [
                'name' => 'Mario Rossi',
                'password' => 'password',
                'role' => UserRole::Member->value,
                'membership_status' => 'active',
            ]
        );

        $year = now()->year;

        $membership = Membership::firstOrCreate(
            [
                'user_id' => $member->id,
                'year' => $year,
            ],
            [
                'status' => 'active',
                // IMPORTANT: do not rotate qr_token on reseed (it would invalidate issued QR codes)
                'qr_token' => Str::random(32),
            ]
        );

        if (! $membership->wasRecentlyCreated && $membership->status !== 'active') {
            $membership->update(['status' => 'active']);
        }

        Event::firstOrCreate(
            [
                'title' => '46ª Fiera Campionaria Venticano',
                'start_date' => now()->addMonths(2)->startOfDay()->setTime(9, 0, 0)->toDateTimeString(),
            ],
            [
                'end_date' => now()->addMonths(2)->addDays(3)->setTime(22, 0, 0)->toDateTimeString(),
                'type' => 'fair',
                'metadata' => ['location' => 'Quartiere Fieristico', 'organizer' => 'Pro Loco Venticanese'],
            ]
        );

        Event::firstOrCreate(
            [
                'title' => 'Sagra del Prosciutto',
                'start_date' => now()->addMonths(6)->startOfDay()->setTime(18, 0, 0)->toDateTimeString(),
            ],
            [
                'end_date' => now()->addMonths(6)->addDay()->setTime(23, 59, 0)->toDateTimeString(),
                'type' => 'festival',
                'metadata' => ['location' => 'Piazza Monumento', 'theme' => 'Gastronomy'],
            ]
        );
    }
}


