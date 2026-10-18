<?php

namespace App\Notifications;

use App\Models\ProjectInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProjectInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(public ProjectInvitation $invitation) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'project_id' => $this->invitation->project_id,
            'project_name' => $this->invitation->project->name,
            'inviter_name' => $this->invitation->inviter->name,
            'role' => $this->invitation->role,
        ];
    }
}
