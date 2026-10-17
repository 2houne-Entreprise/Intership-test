<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

it('blocks other owners before policies or controllers run', function (string $method, string $routeName, bool $withTask) {
    $task = Task::factory()->create();
    $project = $task->project;
    $parameters = $withTask ? [$project, $task] : [$project];
    Gate::before(fn (User $user, string $ability): bool => true);

    $this->actingAs(User::factory()->create())->{$method}(route($routeName, $parameters), [
        'name' => 'Intrusion',
        'title' => 'Intrusion',
        'status' => 'done',
    ])->assertForbidden();

    $this->assertModelExists($project);
    $this->assertModelExists($task);
    $this->assertDatabaseCount('tasks', 1);
    expect($project->fresh()->name)->toBe($project->name)
        ->and($task->fresh()->title)->toBe($task->title)
        ->and($task->fresh()->status)->toBe('pending');
})->with([
    ['get', 'projects.show', false],
    ['get', 'projects.edit', false],
    ['put', 'projects.update', false],
    ['patch', 'projects.update', false],
    ['delete', 'projects.destroy', false],
    ['get', 'projects.tasks.create', false],
    ['post', 'projects.tasks.store', false],
    ['get', 'projects.tasks.edit', true],
    ['put', 'projects.tasks.update', true],
    ['patch', 'projects.tasks.update', true],
    ['delete', 'projects.tasks.destroy', true],
    ['patch', 'projects.tasks.status', true],
]);

it('lets the owner reach a project and its tasks through the middleware', function () {
    $task = Task::factory()->create();
    $this->actingAs($task->project->user)
        ->get(route('projects.show', $task->project))->assertSuccessful();
    $this->get(route('projects.tasks.edit', [$task->project, $task]))->assertSuccessful();
    $this->patch(route('projects.tasks.status', [$task->project, $task]), ['status' => 'done'])
        ->assertRedirect(route('projects.show', $task->project));

    expect($task->fresh()->status)->toBe('done');
});

it('still returns not found for nonexistent projects', function () {
    $this->actingAs(User::factory()->create())->get(route('projects.show', 999))->assertNotFound();
});

it('still scopes tasks to their parent project even when the policy allows access', function () {
    $project = Project::factory()->create();
    $task = Task::factory()->create();
    Gate::before(fn (User $user, string $ability): bool => true);

    $this->actingAs($project->user)->patch(route('projects.tasks.status', [$project, $task]), ['status' => 'done'])
        ->assertNotFound();
    expect($task->fresh()->status)->toBe('pending');
});
