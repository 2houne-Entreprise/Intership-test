<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

it('formats each task status in French', function (string $status, string $label) {
    expect(Task::factory()->make(['status' => $status])->status_label)->toBe($label);
})->with([
    ['pending', 'En attente'],
    ['in_progress', 'En cours'],
    ['done', 'Terminée'],
]);

it('includes only unfinished tasks with deadlines before today', function () {
    $this->travelTo(now()->setDate(2026, 10, 17)->setTime(12, 0));
    $project = Project::factory()->create();
    $pending = Task::factory()->for($project)->create(['deadline' => '2026-10-16']);
    $inProgress = Task::factory()->for($project)->create(['deadline' => '2026-10-15', 'status' => 'in_progress']);
    Task::factory()->for($project)->create(['deadline' => '2026-10-16', 'status' => 'done']);
    Task::factory()->for($project)->create(['deadline' => '2026-10-17']);
    Task::factory()->for($project)->create(['deadline' => '2026-10-18']);
    Task::factory()->for($project)->create(['deadline' => null]);
    Task::factory()->create(['deadline' => '2026-10-16']);

    expect($project->tasks()->overdue()->pluck('id')->all())->toBe([$pending->id, $inProgress->id]);
});

it('filters overdue tasks while keeping pagination and owner isolation', function () {
    $this->travelTo(now()->setDate(2026, 10, 17)->setTime(12, 0));
    $project = Project::factory()->create();
    $overdueTasks = Task::factory()->for($project)->count(11)->create(['deadline' => '2026-10-16']);
    $future = Task::factory()->for($project)->create(['deadline' => '2026-10-18']);
    $otherTask = Task::factory()->create(['deadline' => '2026-10-16']);

    $this->actingAs($project->user)->get(route('projects.show', [$project, 'overdue' => 1]))
        ->assertSuccessful()
        ->assertViewHas('tasks', fn ($tasks) => $tasks->total() === 11)
        ->assertDontSeeText($future->title)
        ->assertDontSeeText($otherTask->title)
        ->assertSee('overdue=1', false);

    $this->get(route('projects.show', [$project, 'overdue' => 1, 'page' => 2]))
        ->assertSuccessful()->assertSeeText($overdueTasks->first()->title);
    $this->get(route('projects.show', $project))
        ->assertViewHas('tasks', fn ($tasks) => $tasks->total() === 12);
});

it('shows an empty state when no tasks are overdue', function () {
    $project = Project::factory()->create();
    $this->actingAs($project->user)->get(route('projects.show', [$project, 'overdue' => 1]))
        ->assertSuccessful()->assertSeeText('Aucune tâche en retard.');
});

it('loads tasks in one query for multiple project previews', function () {
    $user = User::factory()->create();
    $projects = Project::factory()->for($user)->has(Task::factory()->count(2))->count(3)->create();
    $otherTask = Task::factory()->create(['title' => 'Private task from another owner']);
    $taskQueries = 0;
    DB::listen(function (QueryExecuted $query) use (&$taskQueries): void {
        if (str_contains($query->sql, 'from "tasks"')) {
            $taskQueries++;
        }
    });

    $response = $this->actingAs($user)->get(route('projects.index'))->assertSuccessful()
        ->assertViewHas('projects', fn ($items) => $items->every(fn (Project $project) => $project->relationLoaded('tasks')))
        ->assertDontSeeText($otherTask->title);

    expect($taskQueries)->toBe(1);
    foreach ($projects as $project) {
        $response->assertSeeText($project->name);
    }
    $response->assertSeeText('En attente');
});
