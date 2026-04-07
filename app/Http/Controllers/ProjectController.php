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

    

    public function show(Project $project)
    {
        Gate::authorize('view', $project);
        $project->load('tasks');
        return view('projects.show', compact('project'));
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