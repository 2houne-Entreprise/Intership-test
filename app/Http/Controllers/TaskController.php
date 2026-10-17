<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function create(Project $project): View
    {
        Gate::authorize('update', $project);

        return view('tasks.form', ['project' => $project, 'task' => new Task]);
    }

    public function store(StoreTaskRequest $request, Project $project): RedirectResponse
    {
        $project->tasks()->create($request->validated());

        return to_route('projects.show', $project)->with('status', 'Tâche créée.');
    }

    public function edit(Project $project, Task $task): View
    {
        Gate::authorize('update', $project);

        return view('tasks.form', compact('project', 'task'));
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return to_route('projects.show', $project)->with('status', 'Tâche modifiée.');
    }

    public function destroy(Project $project, Task $task): RedirectResponse
    {
        Gate::authorize('update', $project);
        $task->delete();

        return to_route('projects.show', $project)->with('status', 'Tâche supprimée.');
    }
}
