<?php

namespace App\Policies;

use App\Models\ProjectInvitation;
use App\Models\User;

class ProjectInvitationPolicy
{
    public function view(User $user, ProjectInvitation $invitation): bool
    {
        return $invitation->invited_user_id === $user->id
            || $invitation->project->isOwnedBy($user);
    }

    public function respond(User $user, ProjectInvitation $invitation): bool
    {
        return $invitation->invited_user_id === $user->id
            && $invitation->status === 'pending';
    }

    public function cancel(User $user, ProjectInvitation $invitation): bool
    {
        return $invitation->status === 'pending'
            && $invitation->project->isOwnedBy($user);
    }
}
