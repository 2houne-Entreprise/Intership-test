<?php
use App\Models\User;


test('guest is redirected to login when accessing projects', function () {

    $response = $this->get('/projects');

    $response->assertRedirect('/login');
});



test('authenticated user can create a project', function () {

    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/projects', [
            'name' => 'Mon Projet',
            'description' => 'Description test',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'name' => 'Mon Projet',
        'user_id' => $user->id,
    ]);
});