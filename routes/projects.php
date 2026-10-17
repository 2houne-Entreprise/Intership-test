<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskAttachmentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Middleware\EnsureUserOwnsProject;
use Illuminate\Support\Facades\Route;

Route::resource('projects', ProjectController::class)
    ->middlewareFor(['show', 'edit', 'update', 'destroy'], EnsureUserOwnsProject::class);
Route::get('projects/{project}/tasks/{task}/attachment', TaskAttachmentController::class)
    ->scopeBindings()
    ->middleware(EnsureUserOwnsProject::class)
    ->name('projects.tasks.attachment');
Route::patch('projects/{project}/tasks/{task}/status', TaskStatusController::class)
    ->scopeBindings()
    ->middleware(EnsureUserOwnsProject::class)
    ->name('projects.tasks.status');
Route::resource('projects.tasks', TaskController::class)
    ->only(['create', 'store', 'edit', 'update', 'destroy'])
    ->middleware(EnsureUserOwnsProject::class)
    ->scoped();
