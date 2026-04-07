<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,in_progress,done',
            'deadline' => 'nullable|date',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $data = [
            'title' => $validated['title'],
            'status' => $validated['status'],
            'deadline' => $validated['deadline'] ?? null,
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
        }

        $project->tasks()->create($data);

        return back()->with('success', 'Tâche créée.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,done',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $data = ['status' => $request->status];

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
        }

        $task->update($data);

        return back()->with('success', 'Tâche mise à jour.');
    }
}
