<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectInvitationRequest;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\User;
use App\Notifications\ProjectInvitationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ProjectInvitationController extends Controller
{
    public function store(StoreProjectInvitationRequest $request, string $project): RedirectResponse
    {
        $project = Project::query()->findOrFail($project);
        Gate::authorize('manageMembers', $project);
        $validated = $request->validated();
        $invitee = User::query()->where('email', $validated['email'])->firstOrFail();

        DB::transaction(function () use ($project, $invitee, $validated, $request): void {
            $lockedProject = Project::query()->lockForUpdate()->findOrFail($project->id);

            if ($lockedProject->isOwnedBy($invitee) || $lockedProject->members()->whereKey($invitee->id)->exists()) {
                throw ValidationException::withMessages(['email' => 'Cet utilisateur est déjà membre du projet.']);
            }

            if ($lockedProject->invitations()->where('invited_user_id', $invitee->id)->where('status', 'pending')->exists()) {
                throw ValidationException::withMessages(['email' => 'Une invitation est déjà en attente pour cet utilisateur.']);
            }

            $invitation = $lockedProject->invitations()->create([
                'inviter_id' => $request->user()->id,
                'invited_user_id' => $invitee->id,
                'role' => $validated['role'],
                'status' => 'pending',
            ]);

            $invitee->notify(new ProjectInvitationNotification($invitation));
        });

        return redirect()->route('projects.members.index', $project)
            ->with('success', 'Invitation envoyée dans TaskFlow.');
    }

    public function cancel(Request $request, string $project, string $invitation): RedirectResponse
    {
        $project = Project::query()->findOrFail($project);
        Gate::authorize('manageMembers', $project);
        $invitation = $project->invitations()->findOrFail($invitation);
        Gate::authorize('cancel', $invitation);
        $invitation->update(['status' => 'cancelled', 'responded_at' => now()]);
        $this->markInvitationNotificationAsRead($invitation);

        return redirect()->route('projects.members.index', $project)
            ->with('success', 'Invitation annulée.');
    }

    public function accept(Request $request, ProjectInvitation $invitation): RedirectResponse
    {
        $project = DB::transaction(function () use ($request, $invitation): Project {
            $invitation = ProjectInvitation::query()->lockForUpdate()->findOrFail($invitation->id);
            Gate::authorize('respond', $invitation);
            $project = Project::query()->lockForUpdate()->findOrFail($invitation->project_id);

            if (! $project->isOwnedBy($request->user()) && ! $project->members()->whereKey($request->user()->id)->exists()) {
                $project->members()->attach($request->user(), ['role' => $invitation->role]);
            }

            $invitation->update(['status' => 'accepted', 'responded_at' => now()]);
            $this->markInvitationNotificationAsRead($invitation);

            return $project;
        });

        return redirect()->route('projects.show', $project)
            ->with('success', 'Vous avez rejoint le projet.');
    }

    public function decline(Request $request, ProjectInvitation $invitation): RedirectResponse
    {
        Gate::authorize('respond', $invitation);
        $invitation->update(['status' => 'declined', 'responded_at' => now()]);
        $this->markInvitationNotificationAsRead($invitation);

        return redirect()->route('notifications.index')
            ->with('success', 'Invitation refusée.');
    }

    private function markInvitationNotificationAsRead(ProjectInvitation $invitation): void
    {
        $invitation->invitedUser->notifications()
            ->where('type', ProjectInvitationNotification::class)
            ->where('data->invitation_id', $invitation->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
