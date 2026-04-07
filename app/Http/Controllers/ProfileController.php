<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index() {
    $projects = auth()->user()->projects()->with('tasks')->get();
    return view('projects.index', compact('projects'));
}

public function store(Request $request) {
    $request->validate(['name'=>'required|max:255']);
    auth()->user()->projects()->create($request->all());
    return back();
}

public function update(Request $request, Project $project) {
    $this->authorize('update', $project);
    $project->update($request->all());
    return back();
}

public function destroy(Project $project) {
    $this->authorize('delete', $project);
    $project->delete();
    return back();
}
}
