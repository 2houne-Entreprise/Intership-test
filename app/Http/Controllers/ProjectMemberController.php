<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProjectMemberRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectMemberController extends Controller
{
    public function index(Request $request, string $project): View
    {
        $project = Project::query()->findOrFail($project);
        Gate::authorize('manageMembers', $project);
        $project->load(['user', 'members']);
        $invitations = $project->invitations()->with('invitedUser')->latest()->get();

        return view('projects.members.index', compact('project', 'invitations'));
    }

    public function update(UpdateProjectMemberRequest $request, string $project, string $member): RedirectResponse
    {
        $project = Project::query()->findOrFail($project);
        Gate::authorize('manageMembers', $project);
        $member = $project->members()->whereKey($member)->firstOrFail();
        $project->members()->updateExistingPivot($member->id, $request->validated());

        return redirect()->route('projects.members.index', $project)
            ->with('success', 'Rôle du membre modifié.');
    }

    public function destroy(Request $request, string $project, string $member): RedirectResponse
    {
        $project = Project::query()->findOrFail($project);
        Gate::authorize('manageMembers', $project);
        $member = $project->members()->whereKey($member)->firstOrFail();
        $project->members()->detach($member);

        return redirect()->route('projects.members.index', $project)
            ->with('success', 'Membre retiré du projet.');
    }
}
