<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        return view('projects.index', [
            'projects' => $request->user()->projects()->with('tasks')->latest('id')->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('projects.form', ['project' => new Project]);
    }

    public function show(Request $request, Project $project): View
    {
        Gate::authorize('view', $project);

        $tasks = $project->tasks();

        if ($request->boolean('overdue')) {
            $tasks->overdue();
        }

        return view('projects.show', [
            'project' => $project,
            'tasks' => $tasks->latest('id')->paginate(10)->withQueryString(),
            'overdueOnly' => $request->boolean('overdue'),
        ]);
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $request->user()->projects()->create($request->validated());

        return to_route('projects.index')->with('status', 'Projet créé.');
    }

    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        return view('projects.form', compact('project'));
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return to_route('projects.index')->with('status', 'Projet modifié.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        Gate::authorize('delete', $project);
        $project->delete();

        return to_route('projects.index')->with('status', 'Projet supprimé.');
    }
}
