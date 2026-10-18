<?php

namespace App\Http\Middleware;

use App\Models\Project;
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
        $project = $request->route('project');
        $projectId = $project instanceof Project ? $project->getKey() : $project;

        $user = $request->user();
        $hasAccess = $projectId && Project::query()
            ->whereKey($projectId)
            ->where(function ($query) use ($user): void {
                $query->where('user_id', $user->id)
                    ->orWhereHas('members', fn ($members) => $members->whereKey($user->id));
            })
            ->exists();

        abort_unless($hasAccess, Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}
