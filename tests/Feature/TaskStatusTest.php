<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

it('updates only the status from the task table', function (string $status) {
    $task = Task::factory()->create(['deadline' => '2026-12-01']);
    $this->actingAs($task->project->user)
        ->get(route('projects.show', $task->project))
        ->assertSuccessful()
        ->assertSee(route('projects.tasks.status', [$task->project, $task]), false)
        ->assertSee('name="_token"', false)
        ->assertSee('Mettre à jour');

    $this->patch(route('projects.tasks.status', [$task->project, $task]), [
        'status' => $status,
        'title' => 'Must not change',
        'deadline' => null,
    ])->assertRedirect(route('projects.show', $task->project));

    expect($task->fresh()->status)->toBe($status)
        ->and($task->fresh()->title)->toBe($task->title)
        ->and($task->fresh()->deadline->format('Y-m-d'))->toBe('2026-12-01');

    $this->get(route('projects.show', $task->project))
        ->assertSee('role="status"', false)
        ->assertSeeText('Statut de la tâche mis à jour.');
})->with(['pending', 'in_progress', 'done']);

it('shows validation errors in the reusable alert', function (mixed $status) {
    $task = Task::factory()->create();
    $this->actingAs($task->project->user)->from(route('projects.show', $task->project))
        ->patch(route('projects.tasks.status', [$task->project, $task]), ['status' => $status])
        ->assertSessionHasErrors('status');

    $this->withCookie(config('session.cookie'), session()->getId());

    $this->get(route('projects.show', $task->project))
        ->assertSuccessful()->assertSee('role="alert"', false)
        ->assertSeeText('Veuillez corriger les erreurs suivantes :');
    expect($task->fresh()->status)->toBe('pending');
})->with([null, 'cancelled']);

it('requires login for quick status updates', function () {
    $task = Task::factory()->create();
    $this->patch(route('projects.tasks.status', [$task->project, $task]), ['status' => 'done'])
        ->assertRedirect(route('login'));
    expect($task->fresh()->status)->toBe('pending');
});

it('forbids quick status updates by another owner', function () {
    $task = Task::factory()->create();
    $this->actingAs(User::factory()->create())
        ->patch(route('projects.tasks.status', [$task->project, $task]), ['status' => 'done'])
        ->assertForbidden();
    expect($task->fresh()->status)->toBe('pending');
});

it('rejects a quick status update through the wrong project', function () {
    $task = Task::factory()->create();
    $project = Project::factory()->create();
    $this->actingAs($project->user)
        ->patch(route('projects.tasks.status', [$project, $task]), ['status' => 'done'])
        ->assertNotFound();
    expect($task->fresh()->status)->toBe('pending');
});

it('escapes messages and merges attributes in alerts', function () {
    $this->blade('<x-alert class="mt-2">{{ $message }}</x-alert>', ['message' => '<script>alert(1)</script>'])
        ->assertSee('&lt;script&gt;', false)->assertDontSee('<script>', false)
        ->assertSee('mt-2');
});
