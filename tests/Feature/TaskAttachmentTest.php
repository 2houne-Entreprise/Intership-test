<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('authenticated user can create a task with an attachment', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $file = UploadedFile::fake()->create('document.pdf', 500);

    $response = $this->actingAs($user)->post(route('tasks.store', $project), [
        'title' => 'New Task with File',
        'status' => 'pending',
        'deadline' => '2026-06-10',
        'attachment' => $file,
    ]);

    $response->assertRedirect(route('projects.show', $project));

    $task = Task::first();
    expect($task->attachment_path)->not->toBeNull();
    Storage::disk('public')->assertExists($task->attachment_path);
});

test('authenticated user can update a task attachment', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $oldFile = UploadedFile::fake()->create('old_doc.pdf', 500);
    $oldPath = $oldFile->store('attachments', 'public');

    $task = $project->tasks()->create([
        'title' => 'Task Title',
        'status' => 'pending',
        'deadline' => '2026-06-10',
        'attachment_path' => $oldPath,
    ]);

    Storage::disk('public')->assertExists($oldPath);

    $newFile = UploadedFile::fake()->create('new_doc.pdf', 500);

    $response = $this->actingAs($user)->put(route('tasks.update', $task), [
        'title' => 'Updated Task Title',
        'status' => 'in_progress',
        'deadline' => '2026-06-12',
        'attachment' => $newFile,
    ]);

    $response->assertRedirect();
    Storage::disk('public')->assertMissing($oldPath);

    $task->refresh();
    expect($task->attachment_path)->not->toBe($oldPath);
    Storage::disk('public')->assertExists($task->attachment_path);
});

test('authenticated user can delete a task and its attachment', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $file = UploadedFile::fake()->create('doc_to_delete.pdf', 500);
    $path = $file->store('attachments', 'public');

    $task = $project->tasks()->create([
        'title' => 'Task to delete',
        'status' => 'pending',
        'deadline' => '2026-06-10',
        'attachment_path' => $path,
    ]);

    Storage::disk('public')->assertExists($path);

    $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

    $response->assertRedirect();
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    Storage::disk('public')->assertMissing($path);
});
