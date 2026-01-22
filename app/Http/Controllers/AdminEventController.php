<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Committee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
        $filterType = $request->input('type', null);

        $date = Carbon::createFromDate($year, $month, 1);
        $start = $date->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $end = $date->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $query = Event::where('start_date', '<=', $end)
            ->where('end_date', '>=', $start);

        if ($filterType) {
            $query->where('type', $filterType);
        }

        $events = $query->orderBy('start_date')->get();
        $committees = Committee::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Admin/Events/Calendar', [
            'events' => $events,
            'committees' => $committees,
            'currentDate' => $date->format('Y-m-d'),
            'filterType' => $filterType,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string|in:meeting,event,fair,other',
            'description' => 'nullable|string',
            'committee_id' => 'nullable|uuid|exists:committees,id',
            'metadata' => 'nullable|array',
        ]);

        // Fix Timezone: Input is assumed to be in App Timezone (e.g. Europe/Rome)
        $appTimezone = config('app.timezone');
        
        if (isset($validated['start_date'])) {
            $validated['start_date'] = Carbon::parse($validated['start_date'], $appTimezone);
        }
        if (isset($validated['end_date'])) {
            $validated['end_date'] = Carbon::parse($validated['end_date'], $appTimezone);
        }

        $event = Event::create($validated);

        // Send notification to all active members
        $activeMembers = \App\Models\User::where('membership_status', 'active')->get();
        \Illuminate\Support\Facades\Notification::send($activeMembers, new \App\Notifications\NewEventNotification($event));


        return redirect()->back()->with('success', 'Evento creato con successo.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string|in:meeting,event,fair,other',
            'description' => 'nullable|string',
            'committee_id' => 'nullable|uuid|exists:committees,id',
            'metadata' => 'nullable|array',
        ]);

        // Fix Timezone for Update
        $appTimezone = config('app.timezone');

        if (isset($validated['start_date'])) {
            $validated['start_date'] = Carbon::parse($validated['start_date'], $appTimezone);
        }
        if (isset($validated['end_date'])) {
            $validated['end_date'] = Carbon::parse($validated['end_date'], $appTimezone);
        }

        $event->update($validated);


        return redirect()->back()->with('success', 'Evento aggiornato con successo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load('committee:id,name');

        return Inertia::render('Admin/Events/Show', [
            'event' => $event,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $summary = 'Eliminato evento: '.$event->title;
        $event->delete();


        return redirect()->back()->with('success', 'Evento eliminato con successo.');
    }
}
