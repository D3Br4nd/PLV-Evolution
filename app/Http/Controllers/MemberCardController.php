<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Inertia\Inertia;

class MemberCardController extends Controller
{
    public function show()
    {
        $user = request()->user();

        $year = now()->year;

        $membership = Membership::query()
            ->where('user_id', $user->id)
            ->where('year', $year)
            ->first();

        return Inertia::render('Member/Card', [
            'year' => $year,
            'membership' => $membership,
        ]);
    }
}


