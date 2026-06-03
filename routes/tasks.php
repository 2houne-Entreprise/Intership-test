<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::middleware(['auth', 'own.project'])->group(function () {

    Route::get('/projects/{project}/tasks/create', [TaskController::class, 'create'])
        ->name('tasks.create');

    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])
        ->name('tasks.store');

    Route::put('/tasks/{task}', [TaskController::class, 'update'])
        ->name('tasks.update');

    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
        ->name('tasks.destroy');
});