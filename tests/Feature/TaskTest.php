<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * CA3: Test que la validation échoue si le titre de la tâche est vide
     */
    public function test_task_validation_fails_when_title_is_empty()
    {
        $user = User::factory()->create();
        
        // Créer un projet manuellement
        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'Projet test',
            'description' => 'Description'
        ]);
        
        $this->actingAs($user);
        
        $response = $this->post(route('tasks.store', $project), [
            'title' => '',
            'status' => 'pending'
        ]);
        
        $response->assertSessionHasErrors('title');
    }
    
    /**
     * Test la création réussie d'une tâche
     */
    public function test_authenticated_user_can_create_a_task()
    {
        $user = User::factory()->create();
        
        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'Projet test',
            'description' => 'Description'
        ]);
        
        $this->actingAs($user);
        
        $taskData = [
            'title' => 'Ma tâche de test',
            'status' => 'pending',
            'deadline' => '2025-12-31'
        ];
        
        $response = $this->post(route('tasks.store', $project), $taskData);
        
        $response->assertRedirect(route('projects.show', $project));
        
        $this->assertDatabaseHas('tasks', [
            'title' => 'Ma tâche de test',
            'status' => 'pending',
            'project_id' => $project->id
        ]);
    }
    
    /**
     * Test la validation du statut de la tâche
     */
    public function test_task_validation_fails_with_invalid_status()
    {
        $user = User::factory()->create();
        
        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'Projet test',
            'description' => 'Description'
        ]);
        
        $this->actingAs($user);
        
        $response = $this->post(route('tasks.store', $project), [
            'title' => 'Test',
            'status' => 'status_invalide'
        ]);
        
        $response->assertSessionHasErrors('status');
    }
    
    /**
     * Test la mise à jour du statut d'une tâche
     */
    public function test_user_can_update_task_status()
    {
        $user = User::factory()->create();
        
        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'Projet test',
            'description' => 'Description'
        ]);
        
        $task = Task::create([
            'project_id' => $project->id,
            'title' => 'Tâche test',
            'status' => 'pending'
        ]);
        
        $this->actingAs($user);
        
        $response = $this->put(route('tasks.update', $task), [
            'status' => 'done'
        ]);
        
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'done'
        ]);
    }
    
    /**
     * Test qu'un utilisateur ne peut pas modifier une tâche d'un autre projet
     */
    public function test_user_cannot_update_task_from_another_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $project1 = Project::create([
            'user_id' => $user1->id,
            'name' => 'Projet User 1',
            'description' => 'Description'
        ]);
        
        $task = Task::create([
            'project_id' => $project1->id,
            'title' => 'Tâche test',
            'status' => 'pending'
        ]);
        
        $this->actingAs($user2);
        
        $response = $this->put(route('tasks.update', $task), [
            'status' => 'done'
        ]);
        
        $response->assertStatus(403);
    }
}