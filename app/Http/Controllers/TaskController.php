<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,done',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $data = ['status' => $request->status];

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $data['attachment_path'] = $path;
        }

        $task->update($data);

        return back()->with('success', 'Tâche mise à jour.');
    }
}
