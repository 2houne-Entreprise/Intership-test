<?php

use App\Models\ProjectInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('stores an in-app notification when an owner invites a registered user', function () {
    $owner = User::factory()->create();
    $invitee = User::factory()->create();
    $project = $owner->projects()->create(['name' => 'TaskFlow Internship']);

    $this->actingAs($owner)
        ->post(route('projects.invitations.store', $project), [
            'email' => $invitee->email,
            'role' => 'editor',
        ])
        ->assertRedirect();

    $invitation = ProjectInvitation::query()->firstOrFail();

    expect($invitation->status)->toBe('pending')
        ->and($invitation->role)->toBe('editor')
        ->and($invitee->unreadNotifications()->count())->toBe(1);

    $this->actingAs($invitee)
        ->get(route('notifications.index'))
        ->assertSuccessful()
        ->assertSee('TaskFlow Internship')
        ->assertSee($owner->name)
        ->assertSee('Editor')
        ->assertSee('Accepter')
        ->assertSee('Refuser')
        ->assertSee('aria-label="Notifications, 1 non lues"', false);
});

it('adds an invited user to the project only after accepting as that user', function () {
    $owner = User::factory()->create();
    $invitee = User::factory()->create();
    $project = $owner->projects()->create(['name' => 'Projet partagé']);
    $invitation = $project->invitations()->create([
        'inviter_id' => $owner->id,
        'invited_user_id' => $invitee->id,
        'role' => 'viewer',
        'status' => 'pending',
    ]);
    $anotherUser = User::factory()->create();

    $this->actingAs($anotherUser)
        ->post(route('project-invitations.accept', $invitation))
        ->assertForbidden();

    expect($invitee->memberProjects()->whereKey($project->id)->exists())->toBeFalse();

    $this->actingAs($invitee)
        ->post(route('project-invitations.accept', $invitation))
        ->assertRedirect(route('projects.show', $project));

    expect($invitee->memberProjects()->whereKey($project->id)->exists())->toBeTrue()
        ->and($invitee->memberProjects()->first()->pivot->role)->toBe('viewer')
        ->and($invitation->fresh()->status)->toBe('accepted');
});

it('does not add a user who declines an invitation', function () {
    $owner = User::factory()->create();
    $invitee = User::factory()->create();
    $project = $owner->projects()->create(['name' => 'Projet partagé']);
    $invitation = $project->invitations()->create([
        'inviter_id' => $owner->id,
        'invited_user_id' => $invitee->id,
        'role' => 'editor',
        'status' => 'pending',
    ]);

    $this->actingAs($invitee)
        ->post(route('project-invitations.decline', $invitation))
        ->assertRedirect(route('notifications.index'));

    expect($invitation->fresh()->status)->toBe('declined')
        ->and($invitee->memberProjects()->whereKey($project->id)->exists())->toBeFalse();
});

it('prevents duplicate pending invites and invitations to unregistered users', function () {
    $owner = User::factory()->create();
    $invitee = User::factory()->create();
    $project = $owner->projects()->create(['name' => 'Projet partagé']);
    $this->actingAs($owner);

    $this->post(route('projects.invitations.store', $project), [
        'email' => $invitee->email,
        'role' => 'viewer',
    ])->assertRedirect();

    $this->from(route('projects.members.index', $project))
        ->post(route('projects.invitations.store', $project), [
            'email' => $invitee->email,
            'role' => 'editor',
        ])
        ->assertRedirect(route('projects.members.index', $project))
        ->assertSessionHasErrors('email');

    $this->from(route('projects.members.index', $project))
        ->post(route('projects.invitations.store', $project), [
            'email' => 'unknown@example.test',
            'role' => 'viewer',
        ])
        ->assertRedirect(route('projects.members.index', $project))
        ->assertSessionHasErrors('email');

    expect(ProjectInvitation::query()->where('status', 'pending')->count())->toBe(1);
});

it('allows editors to manage tasks but not project settings or membership', function () {
    $owner = User::factory()->create();
    $editor = User::factory()->create();
    $project = $owner->projects()->create(['name' => 'Projet partagé']);
    $project->members()->attach($editor, ['role' => 'editor']);

    $this->actingAs($editor)
        ->post(route('projects.tasks.store', $project), [
            'title' => 'Créer le tableau de bord',
            'status' => 'pending',
            'deadline' => '',
        ])
        ->assertRedirect(route('projects.show', $project));

    $task = $project->tasks()->firstOrFail();
    $this->get(route('projects.show', $project))
        ->assertSuccessful()
        ->assertSee('Ajouter une tâche')
        ->assertSee('tf-project-status-form', false)
        ->assertSee('Modifier')
        ->assertSee('Supprimer');

    $this->put(route('projects.tasks.update', [$project, $task]), [
        'title' => 'Créer le tableau de bord v2',
        'status' => 'in_progress',
    ])->assertRedirect(route('projects.show', $project));
    expect($task->fresh()->title)->toBe('Créer le tableau de bord v2');

    $this->patch(route('projects.update', $project), ['name' => 'Projet modifié'])
        ->assertForbidden();
    $this->delete(route('projects.destroy', $project))->assertForbidden();
    $this->get(route('projects.members.index', $project))->assertForbidden();
    $this->post(route('projects.invitations.store', $project), [
        'email' => User::factory()->create()->email,
        'role' => 'viewer',
    ])->assertForbidden();
});

it('allows viewers to view projects but blocks task mutations', function () {
    $owner = User::factory()->create();
    $viewer = User::factory()->create();
    $project = $owner->projects()->create(['name' => 'Projet partagé']);
    $project->members()->attach($viewer, ['role' => 'viewer']);
    $task = $project->tasks()->create([
        'title' => 'Lire la spécification',
        'status' => 'pending',
        'attachment_path' => 'attachments/spec.pdf',
    ]);
    Storage::fake('local');
    Storage::disk('local')->put('attachments/spec.pdf', 'private contents');

    $this->actingAs($viewer)
        ->get(route('projects.show', $project))
        ->assertSuccessful()
        ->assertSee('Pièce jointe')
        ->assertSee('Lire la spécification')
        ->assertDontSee('tf-project-status-form')
        ->assertDontSee('Modifier le projet')
        ->assertDontSee('projects.tasks.edit')
        ->assertDontSee('projects.tasks.destroy')
        ->assertDontSee('Ajouter une tâche');

    $this->post(route('projects.tasks.store', $project), [
        'title' => 'Tâche interdite',
        'status' => 'pending',
    ])->assertForbidden();
    $this->put(route('projects.tasks.update', [$project, $task]), [
        'title' => 'Modification interdite',
        'status' => 'done',
    ])->assertForbidden();
    $this->patch(route('projects.tasks.status', [$project, $task]), ['status' => 'done'])
        ->assertForbidden();
    $this->delete(route('projects.tasks.destroy', [$project, $task]))->assertForbidden();
    $download = $this->get(route('projects.tasks.attachment', [$project, $task]))->assertSuccessful();
    expect($download->streamedContent())->toBe('private contents');
    $this->get(route('projects.members.index', $project))->assertForbidden();
});

it('allows only the owner to manage members and cancel pending invitations', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $invitee = User::factory()->create();
    $project = $owner->projects()->create(['name' => 'Projet partagé']);
    $project->members()->attach($member, ['role' => 'editor']);
    $invitation = $project->invitations()->create([
        'inviter_id' => $owner->id,
        'invited_user_id' => $invitee->id,
        'role' => 'viewer',
        'status' => 'pending',
    ]);

    $this->actingAs($owner)
        ->patch(route('projects.members.update', [$project, $member]), ['role' => 'viewer'])
        ->assertRedirect(route('projects.members.index', $project));

    expect($project->members()->whereKey($member->id)->first()->pivot->role)->toBe('viewer');

    $this->delete(route('projects.invitations.destroy', [$project, $invitation]))
        ->assertRedirect(route('projects.members.index', $project));

    expect($invitation->fresh()->status)->toBe('cancelled');

    $this->actingAs($member)
        ->delete(route('projects.members.destroy', [$project, $owner]))
        ->assertForbidden();
});

it('denies users without a project membership', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $project = $owner->projects()->create(['name' => 'Projet privé']);
    $task = $project->tasks()->create([
        'title' => 'Document privé',
        'status' => 'pending',
        'attachment_path' => 'attachments/private.pdf',
    ]);

    $this->actingAs($stranger)
        ->get(route('projects.show', $project))
        ->assertForbidden();
    $this->get(route('projects.tasks.attachment', [$project, $task]))
        ->assertForbidden();
});

it('renders the member and pending invitation controls for the owner', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $invitee = User::factory()->create();
    $project = $owner->projects()->create(['name' => 'Portfolio Web']);
    $project->members()->attach($member, ['role' => 'editor']);
    $project->invitations()->create([
        'inviter_id' => $owner->id,
        'invited_user_id' => $invitee->id,
        'role' => 'viewer',
        'status' => 'pending',
    ]);

    $this->actingAs($owner)
        ->get(route('projects.members.index', $project))
        ->assertSuccessful()
        ->assertSee('Membres du projet')
        ->assertSee('Inviter un membre')
        ->assertSee($member->email)
        ->assertSee($invitee->email)
        ->assertSee('Annuler');
});

it('updates dashboard membership immediately after acceptance and rejects repeated membership invitations', function () {
    $owner = User::factory()->create();
    $invitee = User::factory()->create();
    $project = $owner->projects()->create(['name' => 'Application mobile']);
    $project->tasks()->create(['title' => 'Préparer les écrans', 'status' => 'pending']);
    $invitation = $project->invitations()->create([
        'inviter_id' => $owner->id,
        'invited_user_id' => $invitee->id,
        'role' => 'editor',
        'status' => 'pending',
    ]);

    $this->actingAs($invitee)
        ->post(route('project-invitations.accept', $invitation))
        ->assertRedirect(route('projects.show', $project));

    $this->get(route('dashboard', ['range' => 'all']))
        ->assertSuccessful()
        ->assertSee('Préparer les écrans');

    $this->actingAs($owner)
        ->from(route('projects.members.index', $project))
        ->post(route('projects.invitations.store', $project), [
            'email' => $invitee->email,
            'role' => 'viewer',
        ])
        ->assertRedirect(route('projects.members.index', $project))
        ->assertSessionHasErrors('email');

    expect($invitee->memberProjects()->whereKey($project->id)->count())->toBe(1);
});
