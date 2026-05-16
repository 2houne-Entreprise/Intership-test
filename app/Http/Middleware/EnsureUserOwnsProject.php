<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Project;

class EnsureUserOwnsProject
{
    public function handle(Request $request, Closure $next)
    {
        $project = $request->route('project');

        // if project not found OR not owned by user
        if (!$project || $project->user_id !== auth()->id()) {
            abort(403);
        }

        return $next($request);
    }
}