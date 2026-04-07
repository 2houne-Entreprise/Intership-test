<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request, Project $project)
{
    if (Gate::denies('update', $project)) {
        abort(403);
    }
    
    $data = $request->validated();
    $data['project_id'] = $project->id;

  
    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('attachments', $filename, 'public');
        $data['attachment_path'] = $path;
    }

    Task::create($data);

    return redirect()->route('projects.show', $project)
        ->with('success', '✅ Tâche créée avec succès.');
}
    public function update(UpdateTaskRequest $request, Task $task)
    {
        // Vérifier que l'utilisateur est propriétaire du projet de la tâche
        Gate::authorize('update', $task->project);
        
        $data = $request->validated();
        $task->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'status' => $task->status]);
        }

        return redirect()->back()
            ->with('success', 'Tâche mise à jour.');
    }

    public function destroy(Task $task)
    {
        // Vérifier que l'utilisateur est propriétaire du projet de la tâche
        Gate::authorize('delete', $task->project);
        
        // Supprimer le fichier attaché s'il existe
        if ($task->attachment_path) {
            Storage::disk('public')->delete($task->attachment_path);
        }

        $task->delete();

        return redirect()->back()
            ->with('success', ' Tâche supprimée.');
    }
}