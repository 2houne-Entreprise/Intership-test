<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects()->latest()->get();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        // ✅ Validation avec messages personnalisés
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Le nom du projet est obligatoire.',
            'name.max' => 'Le nom du projet ne doit pas dépasser 255 caractères.',
        ]);

        auth()->user()->projects()->create($validated);

        return redirect()->route('projects.index')
            ->with('success', '✅ Projet créé avec succès.');
    }

    public function show(Project $project)
    {
        Gate::authorize('view', $project);
        $project->load('tasks');
        
        $stats = [
            'total' => $project->tasks->count(),
            'pending' => $project->tasks->where('status', 'pending')->count(),
            'in_progress' => $project->tasks->where('status', 'in_progress')->count(),
            'done' => $project->tasks->where('status', 'done')->count(),
            'overdue' => $project->tasks()->overdue()->count(),
        ];
        
        return view('projects.show', compact('project', 'stats'));
    }

    public function edit(Project $project)
    {
        Gate::authorize('update', $project);
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Le nom du projet est obligatoire.',
            'name.max' => 'Le nom du projet ne doit pas dépasser 255 caractères.',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', '✅ Projet mis à jour avec succès.');
    }

    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', '✅ Projet supprimé avec succès.');
    }
}