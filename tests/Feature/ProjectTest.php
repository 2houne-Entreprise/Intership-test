<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login()
    {
        $response = $this->get(route('projects.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_project()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $projectData = [
            'name' => 'Mon projet de test',
            'description' => 'Description du projet de test'
        ];
        
        $response = $this->post(route('projects.store'), $projectData);
        
        $response->assertRedirect(route('projects.index'));
        
        $this->assertDatabaseHas('projects', [
            'name' => 'Mon projet de test',
            'description' => 'Description du projet de test',
            'user_id' => $user->id
        ]);
    }
    
    public function test_user_can_only_see_his_own_projects()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        Project::create([
            'user_id' => $user1->id, 
            'name' => 'Projet de User 1',
            'description' => 'Description du projet 1'
        ]);
        
        Project::create([
            'user_id' => $user2->id, 
            'name' => 'Projet de User 2',
            'description' => 'Description du projet 2'
        ]);
        
        $this->actingAs($user1);
        
        $response = $this->get(route('projects.index'));
        
        $response->assertSee('Projet de User 1');
        $response->assertDontSee('Projet de User 2');
    }
    
    public function test_project_creation_validation_fails_without_name()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $response = $this->post(route('projects.store'), [
            'name' => '',
            'description' => 'Test description'
        ]);
        
        // Vérifier qu'il y a une erreur de validation
        $response->assertSessionHasErrors('name');
        
        // Vérifier que le projet n'a PAS été créé
        $this->assertDatabaseMissing('projects', [
            'description' => 'Test description'
        ]);
    }
    
    public function test_user_can_update_his_own_project()
    {
        $user = User::factory()->create();
        
        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'Ancien nom',
            'description' => 'Ancienne description'
        ]);
        
        $this->actingAs($user);
        
        $response = $this->put(route('projects.update', $project), [
            'name' => 'Nouveau nom',
            'description' => 'Nouvelle description'
        ]);
        
        $response->assertRedirect(route('projects.index'));
        
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Nouveau nom',
            'description' => 'Nouvelle description'
        ]);
    }
    
    public function test_user_can_delete_his_own_project()
    {
        $user = User::factory()->create();
        
        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'Projet à supprimer',
            'description' => 'Description'
        ]);
        
        $this->actingAs($user);
        
        $response = $this->delete(route('projects.destroy', $project));
        
        $response->assertRedirect(route('projects.index'));
        
        $this->assertDatabaseMissing('projects', [
            'id' => $project->id
        ]);
    }
    
    public function test_user_cannot_update_another_users_project()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $project = Project::create([
            'user_id' => $user1->id,
            'name' => 'Projet de user1',
            'description' => 'Description'
        ]);
        
        $this->actingAs($user2);
        
        $response = $this->put(route('projects.update', $project), [
            'name' => 'Nouveau nom',
            'description' => 'Nouvelle description'
        ]);
        
        $response->assertStatus(403);
        
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Projet de user1'
        ]);
    }
    
    public function test_project_name_has_max_length_validation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $longName = str_repeat('a', 300);
        
        $response = $this->post(route('projects.store'), [
            'name' => $longName,
            'description' => 'Test'
        ]);
        
        // Vérifier qu'il y a une erreur de validation
        $response->assertSessionHasErrors('name');
    }
}