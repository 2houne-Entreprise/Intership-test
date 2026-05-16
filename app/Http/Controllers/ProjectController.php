<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
{
    $projects = Project::with('tasks')->where('user_id', auth()->id())->get();

    return view('projects.index', compact('projects'));
}

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Project::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('projects.index');
    }

    public function show(Project $project)
{
    if ($project->user_id !== auth()->id()) {
        abort(403);
    }

   
    $project->load('tasks');

    return view('projects.show', compact('project'));
}

    public function edit(Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('projects.index');
    }

    public function destroy(Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        $project->delete();

        return redirect()->route('projects.index');
    }
}