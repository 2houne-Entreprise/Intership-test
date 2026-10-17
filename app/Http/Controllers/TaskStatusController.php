<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;

class TaskStatusController extends Controller
{
    public function __invoke(UpdateTaskStatusRequest $request, Project $project, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return to_route('projects.show', $project)->with('status', 'Statut de la tâche mis à jour.');
    }
}
