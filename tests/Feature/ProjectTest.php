<?php

use App\Models\Project;
use App\Models\User;

it('redirects guests from project routes', function (string $method, string $path) {
    $this->{$method}($path)->assertRedirect(route('login'));
})->with([
    ['get', '/projects'],
    ['get', '/projects/create'],
    ['post', '/projects'],
    ['get', '/projects/1/edit'],
    ['put', '/projects/1'],
    ['delete', '/projects/1'],
])->group('us8');

it('lists only the authenticated users projects', function () {
    $user = User::factory()->create();
    $ownProject = Project::factory()->for($user)->create();
    $otherProject = Project::factory()->create();

    $this->actingAs($user)->get(route('projects.index'))
        ->assertSuccessful()
        ->assertSeeText($ownProject->name)
        ->assertDontSeeText($otherProject->name)
        ->assertViewHas('projects', fn ($projects) => $projects->total() === 1);
});

it('renders the project creation form', function () {
    $this->actingAs(User::factory()->create())->get(route('projects.create'))
        ->assertSuccessful()->assertSee('name="name"', false);
});

it('creates a project for the authenticated user despite a forged owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($user)->post(route('projects.store'), [
        'name' => 'TaskFlow',
        'description' => 'Mon projet',
        'user_id' => $otherUser->id,
    ])->assertRedirect(route('projects.index'))->assertSessionHas('status');

    $project = $user->projects()->sole();
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'user_id' => $user->id,
        'name' => 'TaskFlow',
        'description' => 'Mon projet',
    ]);
    expect($project->name)->toBe('TaskFlow')
        ->and($project->description)->toBe('Mon projet')
        ->and($project->user->is($user))->toBeTrue();
})->group('us8');

it('allows an optional project description', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->post(route('projects.store'), ['name' => 'Sans description'])
        ->assertRedirect(route('projects.index'));

    expect($user->projects()->sole()->description)->toBeNull();
});

it('validates project input and preserves old input', function (array $input, string $field) {
    $this->actingAs(User::factory()->create())->from(route('projects.create'))
        ->post(route('projects.store'), $input)
        ->assertRedirect(route('projects.create'))
        ->assertSessionHasErrors($field)
        ->assertSessionHasInput('description', $input['description']);

    $this->assertDatabaseCount('projects', 0);
})->with([
    'missing name' => [['name' => '', 'description' => 'À conserver'], 'name'],
    'long name' => [['name' => str_repeat('a', 256), 'description' => 'À conserver'], 'name'],
    'invalid description' => [['name' => 'Projet', 'description' => ['invalid']], 'description'],
]);

it('lets the owner edit and update their project without changing ownership', function () {
    $project = Project::factory()->create();
    $otherUser = User::factory()->create();
    $this->actingAs($project->user)->get(route('projects.edit', $project))->assertSuccessful();

    $this->put(route('projects.update', $project), [
        'name' => 'Nouveau nom',
        'description' => null,
        'user_id' => $otherUser->id,
    ])->assertRedirect(route('projects.index'));

    expect($project->fresh()->name)->toBe('Nouveau nom')
        ->and($project->fresh()->description)->toBeNull()
        ->and($project->fresh()->user_id)->toBe($project->user_id);
});

it('rejects an invalid update without changing the project', function () {
    $project = Project::factory()->create();
    $this->actingAs($project->user)->put(route('projects.update', $project), ['name' => ''])
        ->assertSessionHasErrors('name');

    expect($project->fresh()->name)->toBe($project->name);
});

it('forbids accessing another users project', function (string $method, string $routeName) {
    $project = Project::factory()->create();
    $this->actingAs(User::factory()->create())->{$method}(route($routeName, $project), ['name' => 'Intrusion'])
        ->assertForbidden();

    $this->assertModelExists($project);
    expect($project->fresh()->name)->toBe($project->name);
})->with([
    ['get', 'projects.edit'],
    ['put', 'projects.update'],
    ['delete', 'projects.destroy'],
]);

it('lets the owner delete their project', function () {
    $project = Project::factory()->create();
    $this->actingAs($project->user)->delete(route('projects.destroy', $project))
        ->assertRedirect(route('projects.index'))->assertSessionHas('status');

    $this->assertModelMissing($project);
});

it('returns not found for a missing project', function () {
    $this->actingAs(User::factory()->create())->get(route('projects.edit', 999))->assertNotFound();
});
