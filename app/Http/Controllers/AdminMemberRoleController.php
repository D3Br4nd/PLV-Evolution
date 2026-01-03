<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminMemberRoleController extends Controller
{
    public function update(Request $request, User $member)
    {
        $this->authorize('manage-roles');

        $validated = $request->validate([
            'role' => 'required|string|in:super_admin,direzione,segreteria,member',
        ]);

        $member->update([
            'role' => $validated['role'],
        ]);

        return redirect()->back()->with('success', 'Ruolo aggiornato con successo.');
    }
}


