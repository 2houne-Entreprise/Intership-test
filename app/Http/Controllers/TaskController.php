<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request, Project $project)
    {
        $data = $request->validated();
        $data['project_id'] = $project->id;

        // Gérer l'upload du fichier
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $data['attachment_path'] = $path;
        }

        Task::create($data);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Tâche créée avec succès.');
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $data = $request->validated();
        $task->update($data);

        // Si la requête vient du formulaire de changement de statut rapide
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'status' => $task->status]);
        }

        return redirect()->back()
            ->with('success', 'Tâche mise à jour.');
    }

    public function destroy(Task $task)
    {
        // Supprimer le fichier attaché s'il existe
        if ($task->attachment_path) {
            Storage::disk('public')->delete($task->attachment_path);
        }

        $task->delete();

        return redirect()->back()
            ->with('success', 'Tâche supprimée.');
    }
}