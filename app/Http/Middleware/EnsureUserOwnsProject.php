<?php

namespace App\Http\Middleware;

use App\Models\Project;
use App\Models\Task;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserOwnsProject
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $project = null;

        if ($request->route('project')) {
            $project = Project::findOrFail($request->route('project'));
        } elseif ($request->route('task')) {
            $task = Task::findOrFail($request->route('task'));
            $project = $task->project;
        }

        if ($project && $project->user_id !== auth()->id()) {
            abort(403);
        }

        return $next($request);
    }
}
