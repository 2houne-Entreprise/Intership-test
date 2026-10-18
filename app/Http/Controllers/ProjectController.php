<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Project::class);
        $projects = Project::accessibleTo($request->user())
            ->withCount(['tasks', 'tasks as completed_tasks_count' => fn (Builder $query) => $query->where('status', 'done')])
            ->with('user', 'members')
            ->latest()
            ->get();

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        Gate::authorize('create', Project::class);

        return view('projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Project::class);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $project = $request->user()->projects()->create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet créé avec succès.');
    }

    public function show(Request $request, string $project): View
    {
        $project = Project::accessibleTo($request->user())->with('tasks')->findOrFail($project);
        Gate::authorize('view', $project);
        $overdueTasks = $project->tasks()->overdue()->get();

        return view('projects.show', compact('project', 'overdueTasks'));
    }

    public function edit(Request $request, string $project): View
    {
        $project = Project::accessibleTo($request->user())->findOrFail($project);
        Gate::authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, string $project): RedirectResponse
    {
        $project = Project::accessibleTo($request->user())->findOrFail($project);
        Gate::authorize('update', $project);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Projet modifié avec succès.');
    }

    public function destroy(Request $request, string $project): RedirectResponse
    {
        $project = Project::accessibleTo($request->user())->with('tasks')->findOrFail($project);
        Gate::authorize('delete', $project);
        $attachmentPaths = $project->tasks->pluck('attachment_path')->filter()->all();
        $project->delete();
        Storage::disk('local')->delete($attachmentPaths);
        Storage::disk('public')->delete($attachmentPaths);

        return redirect()->route('projects.index')
            ->with('success', 'Projet supprimé avec succès.');
    }
}
