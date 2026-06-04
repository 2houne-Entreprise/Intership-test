<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    private function authorizeTask(Task $task): void
    {
        if ($task->project->user_id !== auth()->id()) {
            abort(403);
        }
    }

    public function create(Project $project): View
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        return view('tasks.create', compact('project'));
    }

    public function store(StoreTaskRequest $request, Project $project): RedirectResponse
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request
                ->file('attachment')
                ->store('attachments', 'public');
        }

        $project->tasks()->create([
            'title' => $request->title,
            'status' => $request->status,
            'deadline' => $request->deadline,
            'attachment_path' => $attachmentPath,
        ]);

        return redirect()->route('projects.show', $project);
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($task);
        $attachmentPath = $task->attachment_path;

        if ($request->hasFile('attachment')) {

            if ($task->attachment_path) {
                Storage::disk('public')->delete($task->attachment_path);
            }

            $attachmentPath = $request
                ->file('attachment')
                ->store('attachments', 'public');
        }

        $task->update([
            'title' => $request->title,
            'status' => $request->status,
            'deadline' => $request->deadline,
            'attachment_path' => $attachmentPath,
        ]);

        return back();
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        if ($task->attachment_path) {
            Storage::disk('public')->delete($task->attachment_path);
        }
        $task->delete();

        return back();
    }
}
