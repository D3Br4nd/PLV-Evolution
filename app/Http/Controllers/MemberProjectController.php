<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = $request->user()->projects()
            ->with(['committee'])
            ->orderBy('deadline', 'asc')
            ->get();

        return Inertia::render('Member/Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function show(Project $project)
    {
        // Ensure user is member of the project
        if (!$project->members()->where('users.id', auth()->id())->exists()) {
            abort(403);
        }

        $project->load(['committee', 'members']);

        return Inertia::render('Member/Projects/Show', [
            'project' => $project,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        // Ensure user is member of the project
        if (!$project->members()->where('users.id', auth()->id())->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,done',
        ]);

        $project->update([
            'status' => $validated['status'],
        ]);

        // Note: AdminProjectController usually handles notifications on status change.
        // If we want members to notify each other or admins, we'd add it here.
        // For now, keeping it simple as requested: "solo quello però" (status change only).

        return back()->with('success', 'Stato aggiornato.');
    }
}
