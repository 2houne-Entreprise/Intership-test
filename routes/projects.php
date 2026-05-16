<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Route::resource('projects', ProjectController::class);