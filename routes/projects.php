<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskStatusController;
use Illuminate\Support\Facades\Route;

Route::resource('projects', ProjectController::class);
Route::patch('projects/{project}/tasks/{task}/status', TaskStatusController::class)
    ->scopeBindings()
    ->name('projects.tasks.status');
Route::resource('projects.tasks', TaskController::class)
    ->only(['create', 'store', 'edit', 'update', 'destroy'])
    ->scoped();
