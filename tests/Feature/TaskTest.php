<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

it('requires authentication for task routes', function (string $method, string $path) {
    $this->{$method}($path)->assertRedirect(route('login'));
})->with([
    ['get', '/projects/1'],
    ['get', '/projects/1/tasks/create'],
    ['post', '/projects/1/tasks'],
    ['get', '/projects/1/tasks/1/edit'],
    ['put', '/projects/1/tasks/1'],
    ['delete', '/projects/1/tasks/1'],
]);

it('shows only the selected projects tasks and renders task forms', function () {
    $task = Task::factory()->create();
    $otherTask = Task::factory()->create();
    $this->actingAs($task->project->user)->get(route('projects.show', $task->project))
        ->assertSuccessful()->assertSeeText($task->title)->assertDontSeeText($otherTask->title);
    $this->get(route('projects.tasks.create', $task->project))->assertSuccessful();
    $this->get(route('projects.tasks.edit', [$task->project, $task]))->assertSuccessful();
});

it('creates a task with an allowed status on the route project', function (string $status) {
    $project = Project::factory()->create();
    $otherProject = Project::factory()->create();
    $this->actingAs($project->user)->post(route('projects.tasks.store', $project), [
        'title' => 'Préparer la démo',
        'status' => $status,
        'deadline' => '2026-12-01',
        'project_id' => $otherProject->id,
    ])->assertRedirect(route('projects.show', $project));

    $task = $project->tasks()->sole();
    expect($task->title)->toBe('Préparer la démo')
        ->and($task->status)->toBe($status)
        ->and($task->deadline->format('Y-m-d'))->toBe('2026-12-01')
        ->and($task->project->is($project))->toBeTrue();
})->with(['pending', 'in_progress', 'done']);

it('allows tasks without a deadline', function () {
    $project = Project::factory()->create();
    $this->actingAs($project->user)->post(route('projects.tasks.store', $project), [
        'title' => 'Sans échéance', 'status' => 'pending', 'deadline' => '',
    ])->assertRedirect(route('projects.show', $project));

    expect($project->tasks()->sole()->deadline)->toBeNull();
});

it('validates task creation and updates and preserves input', function (string $field, mixed $value, bool $updating) {
    $task = Task::factory()->create();
    $project = $task->project;
    $formUrl = $updating ? route('projects.tasks.edit', [$project, $task]) : route('projects.tasks.create', $project);
    $targetUrl = $updating ? route('projects.tasks.update', [$project, $task]) : route('projects.tasks.store', $project);
    $method = $updating ? 'put' : 'post';
    $input = array_replace(['title' => 'Titre conservé', 'status' => 'pending', 'deadline' => null], [$field => $value]);

    $this->actingAs($project->user)->from($formUrl)->{$method}($targetUrl, $input)
        ->assertRedirect($formUrl)->assertSessionHasErrors($field);

    $errorMessage = session('errors')->first($field);
    $formResponse = $this->withCookie(config('session.cookie'), session()->getId())
        ->get($formUrl)->assertSuccessful()->assertSeeText($errorMessage);

    if ($field !== 'title') {
        $formResponse->assertSee('Titre conservé');
    }

    $this->assertDatabaseCount('tasks', 1);
    expect($task->fresh()->title)->toBe($task->title);
})->with([
    'empty title' => ['title', ''],
    'long title' => ['title', str_repeat('a', 256)],
    'missing status' => ['status', null],
    'unknown status' => ['status', 'cancelled'],
    'invalid date' => ['deadline', 'not-a-date'],
])->with([false, true])->group('us8');

it('updates a task and clears its deadline without changing its project', function () {
    $task = Task::factory()->create(['deadline' => '2026-12-01']);
    $otherProject = Project::factory()->create();
    $this->actingAs($task->project->user)->put(route('projects.tasks.update', [$task->project, $task]), [
        'title' => 'Terminée', 'status' => 'done', 'deadline' => null, 'project_id' => $otherProject->id,
    ])->assertRedirect(route('projects.show', $task->project));

    expect($task->fresh()->title)->toBe('Terminée')
        ->and($task->fresh()->status)->toBe('done')
        ->and($task->fresh()->deadline)->toBeNull()
        ->and($task->fresh()->project_id)->toBe($task->project_id);
});

it('forbids access to another owners project and tasks', function (string $method, string $routeName) {
    $task = Task::factory()->create();
    $parameters = str_contains($routeName, 'tasks') ? [$task->project, $task] : [$task->project];
    if (in_array($routeName, ['projects.tasks.create', 'projects.tasks.store'])) {
        $parameters = [$task->project];
    }

    $this->actingAs(User::factory()->create())->{$method}(route($routeName, $parameters), [
        'title' => 'Intrusion', 'status' => 'done',
    ])->assertForbidden();

    $this->assertDatabaseCount('tasks', 1);
    expect($task->fresh()->title)->toBe($task->title);
})->with([
    ['get', 'projects.show'],
    ['get', 'projects.tasks.create'],
    ['post', 'projects.tasks.store'],
    ['get', 'projects.tasks.edit'],
    ['put', 'projects.tasks.update'],
    ['delete', 'projects.tasks.destroy'],
]);

it('rejects a task bound to the wrong project', function (string $method, string $routeName) {
    $project = Project::factory()->create();
    $task = Task::factory()->create();
    $this->actingAs($project->user)->{$method}(route($routeName, [$project, $task]), [
        'title' => 'Intrusion', 'status' => 'done',
    ])->assertNotFound();

    $this->assertModelExists($task);
    expect($task->fresh()->title)->toBe($task->title);
})->with([
    ['get', 'projects.tasks.edit'],
    ['put', 'projects.tasks.update'],
    ['delete', 'projects.tasks.destroy'],
]);

it('deletes an owned task', function () {
    $task = Task::factory()->create();
    $this->actingAs($task->project->user)->delete(route('projects.tasks.destroy', [$task->project, $task]))
        ->assertRedirect(route('projects.show', $task->project));

    $this->assertModelMissing($task);
});

it('removes related tasks when a project is deleted', function () {
    $task = Task::factory()->create();
    $this->actingAs($task->project->user)->delete(route('projects.destroy', $task->project))
        ->assertRedirect(route('projects.index'));

    $this->assertModelMissing($task);
});
