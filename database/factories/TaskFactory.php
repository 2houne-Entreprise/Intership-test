<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => $this->faker->sentence(4),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'done']),
            'deadline' => $this->faker->optional()->date(),
        ];
    }
}