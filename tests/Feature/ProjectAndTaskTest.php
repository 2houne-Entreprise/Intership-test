<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectAndTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/projects');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_create_project()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/projects', [
            'name' => 'Mon Projet',
            'description' => 'Description du projet',
        ]);

        $response->assertRedirect('/projects');
        $this->assertDatabaseHas('projects', [
            'name' => 'Mon Projet',
            'user_id' => $user->id,
        ]);
    }

    
    public function test_task_creation_fails_without_title()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post("/projects/{$project->id}/tasks", [
            'title' => '', 
            'status' => 'pending',
        ]);

        $response->assertSessionHasErrors('title');
    }
}