<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use App\Models\Task;
use App\Models\Project;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Store a newly created task inside a project.
     */
   

   public function store(StoreTaskRequest $request, Project $project)
{
    // security: ensure user owns the project
    if ($project->user_id !== auth()->id()) {
        abort(403);
    }

    // Prepare data
    $data = $request->validated();

    // Check if a file was uploaded
    if ($request->hasFile('attachment')) {
        $data['attachment_path'] = 'You uploaded a file'; // simple message
    } else {
        $data['attachment_path'] = null;
    }

    // Create task
    $project->tasks()->create($data);

    return redirect()->back()->with('success', 'Task added successfully');
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

    if ($request->hasFile('attachment')) {
        
        if ($task->attachment_path) {
            Storage::disk('public')->delete($task->attachment_path);
        }
        $task->attachment_path = $request->file('attachment')->store('attachments', 'public');
    }
    
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