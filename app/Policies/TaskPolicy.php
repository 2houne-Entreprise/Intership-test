<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return $task->project->roleFor($user) !== null;
    }

    public function create(User $user, Project $project): bool
    {
        return $project->isOwnedBy($user) || $project->roleFor($user) === 'editor';
    }

    public function update(User $user, Task $task): bool
    {
        return $task->project->isOwnedBy($user) || $task->project->roleFor($user) === 'editor';
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->project->isOwnedBy($user) || $task->project->roleFor($user) === 'editor';
    }

    public function viewAttachment(User $user, Task $task): bool
    {
        return $task->project->roleFor($user) !== null;
    }
}
