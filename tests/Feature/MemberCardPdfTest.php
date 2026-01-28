<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MemberCardPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_member_with_membership_can_download_pdf_card()
    {
        $user = User::create([
            'name' => 'Mario Rossi',
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario@example.com',
            'password' => Hash::make('password'),
            'role' => 'member',
        ]);
        
        Membership::create([
            'user_id' => $user->id,
            'year' => now()->year,
            'paid_at' => now(),
            'amount' => 10,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)
            ->get(route('member.card.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename=tessera-plv-' . now()->year . '.pdf');
    }

    public function test_member_without_membership_cannot_download_pdf_card()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'member',
        ]);

        $response = $this->actingAs($user)
            ->get(route('member.card.pdf'));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    public function test_admin_can_download_any_member_card_pdf()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $member = User::create([
            'name' => 'Luigi Bianchi',
            'first_name' => 'Luigi',
            'last_name' => 'Bianchi',
            'email' => 'luigi@example.com',
            'password' => Hash::make('password'),
            'role' => 'member',
        ]);
        
        Membership::create([
            'user_id' => $member->id,
            'year' => now()->year,
            'paid_at' => now(),
            'amount' => 10,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('members.card.pdf', $member));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
