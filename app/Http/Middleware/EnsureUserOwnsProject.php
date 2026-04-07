<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserOwnsProject
{
    /**
     * Vérifier que l'utilisateur est propriétaire du projet
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Récupérer l'ID du projet depuis les paramètres de route
        $project = $request->route('project');
        
        // Vérifier si l'utilisateur est connecté et propriétaire du projet
        if (!$project || $project->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder à ce projet.');
        }
        
        return $next($request);
    }
}