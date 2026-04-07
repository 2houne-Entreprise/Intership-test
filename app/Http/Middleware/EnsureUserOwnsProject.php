<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserOwnsProject
{
    /**
     * Vérifier que l'utilisateur est propriétaire du projet ou de la tâche
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Récupérer le projet depuis les paramètres de route
        $project = $request->route('project');
        
        // Si c'est une route de tâche, récupérer le projet via la tâche
        $task = $request->route('task');
        
        if ($task) {
            // Pour les routes de tâches, vérifier via la relation
            $project = $task->project;
        }
        
        // Vérifier si l'utilisateur est propriétaire
        if (!$project || $project->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder à cette ressource.');
        }
        
        return $next($request);
    }
}