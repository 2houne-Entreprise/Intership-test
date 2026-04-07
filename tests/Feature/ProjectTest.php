<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

test('guest cannot access projects', function () {
    $response = $this->get('/projects');

    $response->assertRedirect('/login');
});

test('user can create project', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/projects', ['name' => 'Test Project']);

    $response->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'name' => 'Test Project',
        'user_id' => $user->id,
    ]);
});

test('task validation fails', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->post("/projects/{$project->id}/tasks", [
            'title' => '',
            'status' => 'pending',
        ]);

    dump($response->status());
    dump($response->baseResponse->getSession()->all());
    $response->assertSessionHasErrors('title');
});
