<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('redirects guests to login when opening projects', function () {
    $this->get(route('projects.index'))->assertRedirect(route('login'));
});

it('allows an authenticated user to create a project', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('projects.store'), [
            'name' => 'Site E-commerce',
            'description' => 'Organiser la boutique en ligne.',
        ])
        ->assertRedirect();

    $project = Project::query()->firstOrFail();

    $this->assertModelExists($project);
    expect($project->user_id)->toBe($user->id)
        ->and($project->name)->toBe('Site E-commerce');
});

it('renders project and task management pages', function () {
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'Site E-commerce']);
    $task = $project->tasks()->create([
        'title' => 'Créer la page Login',
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->get(route('projects.index'))
        ->assertSuccessful()
        ->assertSee('Site E-commerce');

    $this->get(route('projects.create'))->assertSuccessful();
    $this->get(route('projects.edit', $project))->assertSuccessful();
    $this->get(route('projects.show', $project))
        ->assertSuccessful()
        ->assertSee('Créer la page Login')
        ->assertSee('Total des tâches')
        ->assertSee('En cours')
        ->assertSee('Terminées')
        ->assertSee('tf-project-task-card', false)
        ->assertSee(route('projects.tasks.edit', [$project, $task]), false)
        ->assertSee(route('projects.tasks.destroy', [$project, $task]), false)
        ->assertDontSee('<table', false);
    $this->get(route('projects.tasks.create', $project))->assertSuccessful();
    $this->get(route('projects.tasks.edit', [$project, $task]))->assertSuccessful();
});

it('shows an empty project state when no tasks exist', function () {
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'Nouveau projet']);

    $this->actingAs($user)
        ->get(route('projects.show', $project))
        ->assertSuccessful()
        ->assertSee('Aucune tâche pour le moment')
        ->assertSee('Ajouter une tâche')
        ->assertSee('Total des tâches');
});

it('forbids users from updating another users projects', function () {
    $owner = User::factory()->create();
    $project = $owner->projects()->create(['name' => 'Projet privé']);
    $anotherUser = User::factory()->create();

    $this->actingAs($anotherUser)
        ->patch(route('projects.update', $project), ['name' => 'Projet détourné'])
        ->assertForbidden();

    expect($project->fresh()->name)->toBe('Projet privé');
});

it('updates and deletes a project together with its stored task files', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'Projet temporaire']);
    $attachmentPath = 'attachments/brief.pdf';
    Storage::disk('public')->put($attachmentPath, 'document');
    $project->tasks()->create([
        'title' => 'Préparer le brief',
        'status' => 'pending',
        'attachment_path' => $attachmentPath,
    ]);

    $this->actingAs($user)
        ->patch(route('projects.update', $project), ['name' => 'Site E-commerce'])
        ->assertRedirect(route('projects.index'));

    expect($project->fresh()->name)->toBe('Site E-commerce');

    $this->delete(route('projects.destroy', $project))
        ->assertRedirect(route('projects.index'));

    $this->assertModelMissing($project);
    Storage::disk('public')->assertMissing($attachmentPath);
});
