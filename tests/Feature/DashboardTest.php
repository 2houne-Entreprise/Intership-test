<?php

use App\Models\User;
use Carbon\Carbon;

it('shows real task metrics and recent tasks only from the current users projects', function () {
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'TaskFlow Internship']);
    $project->tasks()->createMany([
        ['title' => 'Revoir les maquettes', 'status' => 'pending', 'deadline' => now()->subDay()],
        ['title' => 'Préparer la base de données', 'status' => 'in_progress'],
        ['title' => 'Finaliser le README', 'status' => 'done'],
    ]);

    $anotherUser = User::factory()->create();
    $anotherProject = $anotherUser->projects()->create(['name' => 'Projet privé']);
    $anotherProject->tasks()->create([
        'title' => 'Cette tâche ne doit pas apparaître',
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Vue d’ensemble des tâches')
        ->assertSee('Projets actifs')
        ->assertSee('Tâches en cours')
        ->assertSee('Tâches en retard')
        ->assertSee('Mes tâches récentes')
        ->assertSee('tf-recent-task-row', false)
        ->assertSee('Revoir les maquettes')
        ->assertSee('TaskFlow Internship')
        ->assertSee('En attente')
        ->assertSee(route('projects.show', $project), false)
        ->assertSee('tf-workflow-image', false)
        ->assertSee(asset('images/taskflow-workflow.jpeg'), false)
        ->assertDontSee('tf-recent-table')
        ->assertDontSee('tf-chart-line')
        ->assertDontSee('Cette tâche ne doit pas apparaître')
        ->assertDontSee('Évolution des tâches');

    expect(is_file(public_path('images/taskflow-workflow.jpeg')))->toBeTrue();
});

it('includes projects shared with the current user in dashboard data', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = $owner->projects()->create(['name' => 'Projet partagé']);
    $project->members()->attach($member, ['role' => 'viewer']);
    $project->tasks()->create([
        'title' => 'Consulter le rapport',
        'status' => 'pending',
    ]);

    $this->actingAs($member)
        ->get(route('dashboard', ['range' => 'all']))
        ->assertSuccessful()
        ->assertSee('Consulter le rapport')
        ->assertSee('Projet partagé');
});

it('shows the current Nouakchott date independently from the selected task range', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-17 10:00:00', 'Africa/Nouakchott'));

    try {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertSuccessful()
            ->assertSee('17 octobre 2026')
            ->assertSee('Ce mois-ci')
            ->assertDontSee('1 oct. 2026')
            ->assertDontSee('31 oct. 2026');

        $this->get(route('dashboard', ['range' => 'last_month']))
            ->assertSuccessful()
            ->assertSee('17 octobre 2026')
            ->assertSee('Le mois dernier')
            ->assertSee('value="last_month" selected', false);
    } finally {
        Carbon::setTestNow();
    }
});
