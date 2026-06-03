<?php

namespace App\Http\Middleware;

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
    
        public function handle(Request $request, Closure $next)
    {
        if ($request->route('project')) {

            $project = $request->route('project');

        } elseif ($request->route('task')) {

            $project = $request->route('task')->project;

        } else {

            abort(403);
        }

        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        return $next($request);
    }
        
}
