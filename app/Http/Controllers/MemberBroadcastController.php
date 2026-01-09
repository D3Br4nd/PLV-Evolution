<?php

namespace App\Http\Controllers;

use App\Models\BroadcastNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberBroadcastController extends Controller
{
    /**
     * Display the specified broadcast notification.
     */
    public function show(Request $request, string $id)
    {
        $broadcast = BroadcastNotification::findOrFail($id);

        // Mark related notification as read (PostgreSQL compatible JSON query)
        $user = $request->user();
        $user->unreadNotifications()
            ->whereRaw("data::jsonb->>'broadcast_id' = ?", [$id])
            ->update(['read_at' => now()]);

        return Inertia::render('Member/Broadcast', [
            'broadcast' => $broadcast,
        ]);
    }
}
