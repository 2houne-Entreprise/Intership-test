<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    /**
     * Store a newly created task inside a project.
     */
    public function store(StoreTaskRequest $request, Project $project)
    {
        // security: ensure user owns the project
        

        // create task linked to project
        $project->tasks()->create($request->validated());

        return redirect()->back()->with('success', 'Task created successfully');
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        // security: ensure user owns the project of this task
        if ($task->project->user_id !== auth()->id()) {
            abort(403);
        }

        // update task
        $task->update($request->validated());

        return redirect()->back()->with('success', 'Task updated successfully');
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task)
    {
        // security check
        if ($task->project->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully');
    }

    /**
     * Optional: list tasks of a project
     */
    public function index(Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        $tasks = $project->tasks;

        return view('tasks.index', compact('tasks', 'project'));
    }
}