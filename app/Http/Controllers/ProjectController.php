<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    /**
     * Afficher la liste des projets avec le nombre de tâches
     * Utilisation de Eager Loading pour éviter N+1
     */
    public function index()
    {
        // Eager loading: charger les tâches et compter uniquement
        $projects = auth()->user()
                        ->projects()
                        ->withCount(['tasks', 'tasks as overdue_count' => function($query) {
                            $query->where('deadline', '<', now())
                                  ->where('status', '!=', 'done');
                        }])
                        ->latest()
                        ->get();
        
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    

     /**
     * Afficher un projet avec ses tâches
     * Utilisation de Eager Loading pour éviter N+1
     */
    public function show(Project $project)
    {
        Gate::authorize('view', $project);
        
        // Eager loading: charger les tâches avec leurs relations
        $project->load([
            'tasks' => function($query) {
                $query->orderBy('deadline', 'asc')
                      ->orderBy('created_at', 'desc');
            }
        ]);
        
        // Récupérer les tâches en retard séparément
        $overdueTasks = $project->tasks()->overdue()->get();
        
        // Statistiques des tâches
        $stats = [
            'total' => $project->tasks->count(),
            'pending' => $project->tasks->where('status', 'pending')->count(),
            'in_progress' => $project->tasks->where('status', 'in_progress')->count(),
            'done' => $project->tasks->where('status', 'done')->count(),
            'overdue' => $overdueTasks->count(),
        ];
        
        return view('projects.show', compact('project', 'stats', 'overdueTasks'));
    }

    public function edit(Project $project)
    {
        Gate::authorize('update', $project);
        return view('projects.edit', compact('project'));
    }

    

    
    public function store(Request $request)
    {
    try {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        auth()->user()->projects()->create($request->only('name', 'description'));

        return redirect()->route('projects.index')
            ->with('success', ' Projet créé avec succès.');
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', ' Erreur lors de la création du projet.');
    }
    }

    public function update(Request $request, Project $project)
    {
        try {
            Gate::authorize('update', $project);

            $request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $project->update($request->only('name', 'description'));

            return redirect()->route('projects.index')
                ->with('success', ' Projet mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', ' Erreur lors de la mise à jour.');
        }
    }

    public function destroy(Project $project)
    {
        try {
            Gate::authorize('delete', $project);
            $project->delete();

            return redirect()->route('projects.index')
                ->with('success', ' Projet supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression.');
        }
    }
    }