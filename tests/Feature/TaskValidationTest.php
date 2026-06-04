<?php

use App\Models\User;
use App\Models\Project;

test('task title is required', function () {

    $user = User::factory()->create();

    $project = Project::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->post(route('tasks.store', $project), [
            'title' => '',
            'status' => 'pending',
        ]);

    $response->assertSessionHasErrors('title');
});