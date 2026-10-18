<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectInvitationController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class)->only(['index', 'create', 'store']);

    Route::middleware('project.access')->group(function () {
        Route::resource('projects', ProjectController::class)->only(['show', 'edit', 'update', 'destroy']);
        Route::resource('projects.tasks', TaskController::class)
            ->except(['index', 'show'])
            ->scoped(['project' => 'id', 'task' => 'id']);
        Route::patch('projects/{project}/tasks/{task}/status', [TaskController::class, 'updateStatus'])
            ->scopeBindings()
            ->name('projects.tasks.status');
        Route::get('projects/{project}/tasks/{task}/attachment', [TaskController::class, 'attachment'])
            ->scopeBindings()
            ->name('projects.tasks.attachment');
        Route::get('projects/{project}/members', [ProjectMemberController::class, 'index'])
            ->name('projects.members.index');
        Route::patch('projects/{project}/members/{member}', [ProjectMemberController::class, 'update'])
            ->name('projects.members.update');
        Route::delete('projects/{project}/members/{member}', [ProjectMemberController::class, 'destroy'])
            ->name('projects.members.destroy');
        Route::post('projects/{project}/invitations', [ProjectInvitationController::class, 'store'])
            ->name('projects.invitations.store');
        Route::delete('projects/{project}/invitations/{invitation}', [ProjectInvitationController::class, 'cancel'])
            ->name('projects.invitations.destroy');
    });
});
