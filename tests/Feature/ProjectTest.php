<?php

use App\Models\Project;
use App\Models\User;

test('guests are redirected to login', function () {
    $this->get(route('projects.index'))->assertRedirect(route('login'));
    $this->get(route('projects.create'))->assertRedirect(route('login'));
    $this->post(route('projects.store'), [])->assertRedirect(route('login'));
});

test('authenticated user can view projects index and only see their own projects', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userProject = Project::factory()->create(['user_id' => $user->id, 'name' => 'My Special Project']);
    $otherProject = Project::factory()->create(['user_id' => $otherUser->id, 'name' => 'Someone Elses Project']);

    $response = $this->actingAs($user)->get(route('projects.index'));

    $response->assertStatus(200);
    $response->assertSee('My Special Project');
    $response->assertDontSee('Someone Elses Project');
});

test('authenticated user can create a project with valid data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('projects.store'), [
        'name' => 'New Project',
        'description' => 'New Description',
    ]);

    $response->assertRedirect(route('projects.index'));
    $this->assertDatabaseHas('projects', [
        'user_id' => $user->id,
        'name' => 'New Project',
        'description' => 'New Description',
    ]);
});

test('authenticated user cannot create a project with invalid data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('projects.store'), [
        'name' => '', // required
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('authenticated user can update their own project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put(route('projects.update', $project), [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ]);

    $response->assertRedirect(route('projects.index'));
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ]);
});

test('authenticated user cannot update another users project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id, 'name' => 'Original Name']);

    $response = $this->actingAs($user)->put(route('projects.update', $project), [
        'name' => 'Updated Name',
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Original Name',
    ]);
});

test('authenticated user can delete their own project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('projects.destroy', $project));

    $response->assertRedirect(route('projects.index'));
    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

test('authenticated user cannot delete another users project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->delete(route('projects.destroy', $project));

    $response->assertStatus(403);
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
    ]);
});
