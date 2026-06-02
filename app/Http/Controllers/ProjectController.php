<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private function authorizeProject(Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }
    }

    public function index()
    {
        $projects = auth()->user()->projects()->latest()->get();

        return view('projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        auth()->user()->projects()->create($request->all());

        return redirect()->route('projects.index');
    }

    public function update(Request $request, Project $project)
    {
        $this->authorizeProject($project);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($request->only('name', 'description'));

        return redirect()->route('projects.index');
    }

    public function destroy(Project $project)
    {
        $this->authorizeProject($project);

        $project->delete();

        return redirect()->route('projects.index');
    }

    public function create()
    {
        return view('projects.create');
    }

    public function edit(Project $project)
    {
        $this->authorizeProject($project);

        return view('projects.edit', compact('project'));
    }
}
