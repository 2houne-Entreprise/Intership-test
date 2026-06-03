<?php

use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy']);
});
