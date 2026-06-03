<?php

use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {

    Route::resource('projects', ProjectController::class)
        ->only(['index', 'show', 'create', 'store']);

    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])
        ->middleware('own.project')
        ->name('projects.edit');

    Route::put('/projects/{project}', [ProjectController::class, 'update'])
        ->middleware('own.project')
        ->name('projects.update');

    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])
        ->middleware('own.project')
        ->name('projects.destroy');
});