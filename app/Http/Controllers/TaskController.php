<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;



class TaskController extends Controller
{

    private function authorizeTask(Task $task)
    {
        if ($task->project->user_id !== auth()->id()) {
            abort(403);
        }
    }
    public function store(StoreTaskRequest $request, Project $project)
    {
            if ($project->user_id !== auth()->id()) {
        abort(403);
}
        $project->tasks()->create([
            'title' => $request->title,
            'status' => $request->status,
            'deadline' => $request->deadline,
        ]);

        return back();
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
            $this->authorizeTask($task);

        $task->update([
            'title' => $request->title,
            'status' => $request->status,
            'deadline' => $request->deadline,
        ]);

        return back();
    }

    public function destroy(Task $task)
    {
          $this->authorizeTask($task);
        $task->delete();

        return back();
    }
}
