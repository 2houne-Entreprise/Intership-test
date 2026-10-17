<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class TaskController extends Controller
{
    public function create(Project $project): View
    {
        Gate::authorize('update', $project);

        return view('tasks.form', ['project' => $project, 'task' => new Task]);
    }

    public function store(StoreTaskRequest $request, Project $project): RedirectResponse
    {
        $this->saveTask($request, $project->tasks()->make());

        return to_route('projects.show', $project)->with('status', 'Tâche créée.');
    }

    public function edit(Project $project, Task $task): View
    {
        Gate::authorize('update', $project);

        return view('tasks.form', compact('project', 'task'));
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task): RedirectResponse
    {
        $this->saveTask($request, $task);

        return to_route('projects.show', $project)->with('status', 'Tâche modifiée.');
    }

    public function destroy(Project $project, Task $task): RedirectResponse
    {
        Gate::authorize('update', $project);
        $task->delete();

        if ($task->attachment_path) {
            Storage::disk('public')->delete($task->attachment_path);
        }

        return to_route('projects.show', $project)->with('status', 'Tâche supprimée.');
    }

    private function saveTask(StoreTaskRequest $request, Task $task): void
    {
        $oldPath = $task->attachment_path;
        $newPath = null;
        $task->fill($request->safe()->except('attachment'));

        if ($request->hasFile('attachment')) {
            $newPath = Storage::disk('public')->putFile('attachments', $request->file('attachment'));
            abort_if($newPath === false, 500, 'Impossible de stocker la pièce jointe.');
            $task->attachment_path = $newPath;
        }

        try {
            $task->save();
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            throw $exception;
        }

        if ($newPath && $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }
    }
}
