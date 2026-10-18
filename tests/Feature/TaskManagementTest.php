<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('rejects a task with an empty title', function () {
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'Projet universitaire']);

    $this->actingAs($user)
        ->from(route('projects.show', $project))
        ->post(route('projects.tasks.store', $project), [
            'title' => '',
            'status' => 'pending',
            'deadline' => '',
        ])
        ->assertRedirect(route('projects.show', $project))
        ->assertSessionHasErrors('title');
});

it('forbids access to tasks through another users project', function () {
    $owner = User::factory()->create();
    $project = $owner->projects()->create(['name' => 'Projet privé']);
    $task = $project->tasks()->create([
        'title' => 'Tâche privée',
        'status' => 'pending',
    ]);
    $anotherUser = User::factory()->create();

    $this->actingAs($anotherUser)
        ->patch(route('projects.tasks.status', [$project, $task]), ['status' => 'done'])
        ->assertForbidden();

    expect($task->fresh()->status)->toBe('pending');
});

it('does not update a task belonging to a different project', function () {
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'Mon projet']);
    $otherProject = $user->projects()->create(['name' => 'Autre projet']);
    $task = $otherProject->tasks()->create([
        'title' => 'Tâche de l’autre projet',
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->put(route('projects.tasks.update', [$project, $task]), [
            'title' => 'Tâche détournée',
            'status' => 'done',
            'deadline' => '',
        ])
        ->assertForbidden();

    expect($task->fresh()->title)->toBe('Tâche de l’autre projet');
});

it('lets a project owner change a task status directly', function () {
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'Application mobile']);
    $task = $project->tasks()->create([
        'title' => 'Tester l’application',
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->patch(route('projects.tasks.status', [$project, $task]), ['status' => 'in_progress'])
        ->assertRedirect(route('projects.show', $project));

    expect($task->fresh()->status)->toBe('in_progress');
});

it('labels task statuses in French and finds overdue unfinished tasks', function () {
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'Projet universitaire']);
    $overdueTask = $project->tasks()->create([
        'title' => 'Rapport',
        'status' => 'pending',
        'deadline' => now()->subDay()->toDateString(),
    ]);
    $completedTask = $project->tasks()->create([
        'title' => 'Présentation',
        'status' => 'done',
        'deadline' => now()->subDay()->toDateString(),
    ]);
    $futureTask = $project->tasks()->create([
        'title' => 'Soutenance',
        'status' => 'in_progress',
        'deadline' => now()->addDay()->toDateString(),
    ]);

    expect($overdueTask->status_label)->toBe('En attente')
        ->and($completedTask->status_label)->toBe('Terminée')
        ->and(Task::overdue()->pluck('id')->all())->toContain($overdueTask->id)
        ->not->toContain($completedTask->id)
        ->not->toContain($futureTask->id);
});

it('filters tasks and files by status without exposing inaccessible projects', function () {
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'Projet accessible']);
    $project->tasks()->createMany([
        ['title' => 'Tâche en attente', 'status' => 'pending', 'deadline' => now()->addDay(), 'attachment_path' => 'pending.pdf'],
        ['title' => 'Tâche en cours', 'status' => 'in_progress', 'deadline' => now()->addDay(), 'attachment_path' => 'in-progress.pdf'],
        ['title' => 'Tâche terminée', 'status' => 'done', 'deadline' => now()->subDay(), 'attachment_path' => 'done.pdf'],
        ['title' => 'Tâche en retard en attente', 'status' => 'pending', 'deadline' => now()->subDay(), 'attachment_path' => 'overdue-pending.pdf'],
        ['title' => 'Tâche en retard en cours', 'status' => 'in_progress', 'deadline' => now()->subDay(), 'attachment_path' => 'overdue-progress.pdf'],
    ]);

    $anotherUser = User::factory()->create();
    $privateProject = $anotherUser->projects()->create(['name' => 'Projet privé']);
    $privateProject->tasks()->create([
        'title' => 'Tâche privée',
        'status' => 'pending',
        'attachment_path' => 'private.pdf',
    ]);

    $this->actingAs($user)
        ->get(route('tasks.index', ['status' => 'pending']))
        ->assertSuccessful()
        ->assertSee('Toutes les tâches')
        ->assertSee('En attente')
        ->assertSee('En cours')
        ->assertSee('Terminées')
        ->assertSee('En retard')
        ->assertSee('Voir mes projets')
        ->assertSee('Tâche en attente')
        ->assertSee('Tâche en retard en attente')
        ->assertDontSee('Tâche en cours')
        ->assertDontSee('Tâche privée');

    $this->get(route('tasks.index', ['status' => 'in_progress']))
        ->assertSuccessful()
        ->assertSee('Tâche en cours')
        ->assertSee('Tâche en retard en cours')
        ->assertDontSee('Tâche en attente');

    $this->get(route('tasks.index', ['status' => 'done']))
        ->assertSuccessful()
        ->assertSee('Tâche terminée')
        ->assertDontSee('Tâche en retard en cours');

    $this->get(route('tasks.index', ['status' => 'overdue']))
        ->assertSuccessful()
        ->assertSee('Tâche en retard en attente')
        ->assertSee('Tâche en retard en cours')
        ->assertDontSee('Tâche terminée')
        ->assertDontSee('Tâche privée');

    $this->get(route('tasks.index', ['attachments' => 1, 'status' => 'done']))
        ->assertSuccessful()
        ->assertSee('Tâche terminée')
        ->assertDontSee('Tâche en cours');

    $this->get(route('tasks.index', ['attachments' => 1, 'status' => 'overdue']))
        ->assertSuccessful()
        ->assertSee('Tâche en retard en attente')
        ->assertSee('Tâche en retard en cours')
        ->assertDontSee('Tâche terminée');
});

it('redirects the removed calendar view to the regular task list', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('tasks.index', ['view' => 'calendar']))
        ->assertRedirect(route('tasks.index'));

    $this->get(route('tasks.index'))
        ->assertSuccessful()
        ->assertSee('Mes tâches')
        ->assertDontSee('Calendrier des tâches');
});

it('stores a task attachment on the public disk', function () {
    Storage::fake('local');
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'Site E-commerce']);

    $this->actingAs($user)
        ->post(route('projects.tasks.store', $project), [
            'title' => 'Documentation',
            'status' => 'pending',
            'deadline' => '',
            'attachment' => UploadedFile::fake()->create('brief.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect();

    $task = $project->tasks()->firstOrFail();

    expect($task->attachment_path)->toStartWith('attachments/');
    Storage::disk('local')->assertExists($task->attachment_path);
});

it('rejects attachments with an unsupported type', function () {
    Storage::fake('local');
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'Site E-commerce']);

    $this->actingAs($user)
        ->from(route('projects.tasks.create', $project))
        ->post(route('projects.tasks.store', $project), [
            'title' => 'Documentation',
            'status' => 'pending',
            'attachment' => UploadedFile::fake()->create('script.txt', 10, 'text/plain'),
        ])
        ->assertRedirect(route('projects.tasks.create', $project))
        ->assertSessionHasErrors('attachment');
});

it('rejects attachments larger than two megabytes', function () {
    Storage::fake('local');
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'Site E-commerce']);

    $this->actingAs($user)
        ->from(route('projects.tasks.create', $project))
        ->post(route('projects.tasks.store', $project), [
            'title' => 'Documentation',
            'status' => 'pending',
            'attachment' => UploadedFile::fake()->create('brief.pdf', 2049, 'application/pdf'),
        ])
        ->assertRedirect(route('projects.tasks.create', $project))
        ->assertSessionHasErrors('attachment');
});

it('replaces and removes task attachments', function () {
    Storage::fake('local');
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'Site E-commerce']);

    $this->actingAs($user)
        ->post(route('projects.tasks.store', $project), [
            'title' => 'Documentation',
            'status' => 'pending',
            'attachment' => UploadedFile::fake()->create('old.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect();

    $task = $project->tasks()->firstOrFail();
    $oldPath = $task->attachment_path;

    $this->put(route('projects.tasks.update', [$project, $task]), [
        'title' => 'Documentation',
        'status' => 'pending',
        'attachment' => UploadedFile::fake()->create('new.pdf', 100, 'application/pdf'),
    ])->assertRedirect();

    $newPath = $task->fresh()->attachment_path;
    expect($newPath)->not->toBe($oldPath);
    Storage::disk('local')->assertMissing($oldPath);
    Storage::disk('local')->assertExists($newPath);

    $this->delete(route('projects.tasks.destroy', [$project, $task]))->assertRedirect();
    Storage::disk('local')->assertMissing($newPath);
});
