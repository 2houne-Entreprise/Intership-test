<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

it('stores an attachment under a generated name and exposes a download link', function () {
    $project = Project::factory()->create();
    $this->actingAs($project->user)->post(route('projects.tasks.store', $project), [
        'title' => 'Document', 'status' => 'pending',
        'attachment' => UploadedFile::fake()->createWithContent('notes.txt', 'TaskFlow notes'),
        'attachment_path' => 'forged.txt',
    ])->assertRedirect(route('projects.show', $project));

    $task = $project->tasks()->sole();
    expect($task->attachment_path)->toStartWith('attachments/')->not->toBe('attachments/notes.txt');
    Storage::disk('public')->assertExists($task->attachment_path);
    $this->get(route('projects.show', $project))->assertSee(route('projects.tasks.attachment', [$project, $task]), false);
    $this->get(route('projects.tasks.attachment', [$project, $task]))
        ->assertDownload(basename($task->attachment_path))->assertStreamedContent('TaskFlow notes');
});

it('replaces an attachment and removes the old file', function () {
    Storage::disk('public')->put('attachments/old.txt', 'old');
    $task = Task::factory()->create(['attachment_path' => 'attachments/old.txt']);
    $this->actingAs($task->project->user)->put(route('projects.tasks.update', [$task->project, $task]), [
        'title' => $task->title, 'status' => 'done',
        'attachment' => UploadedFile::fake()->createWithContent('new.txt', 'new'),
    ])->assertRedirect();
    Storage::disk('public')->assertMissing('attachments/old.txt');
    Storage::disk('public')->assertExists($task->fresh()->attachment_path);
});

it('keeps the attachment when editing without a new file', function () {
    Storage::disk('public')->put('attachments/keep.txt', 'keep');
    $task = Task::factory()->create(['attachment_path' => 'attachments/keep.txt']);
    $this->actingAs($task->project->user)->put(route('projects.tasks.update', [$task->project, $task]), [
        'title' => $task->title, 'status' => 'done', 'attachment_path' => 'forged.txt',
    ])->assertRedirect();
    expect($task->fresh()->attachment_path)->toBe('attachments/keep.txt');
    Storage::disk('public')->assertExists('attachments/keep.txt');
});

it('rejects unsafe types extensions and oversized attachments', function (string $filename, string $mime, int $size) {
    $project = Project::factory()->create();
    $this->actingAs($project->user)->post(route('projects.tasks.store', $project), [
        'title' => 'Document', 'status' => 'pending',
        'attachment' => UploadedFile::fake()->create($filename, $size, $mime),
    ])->assertSessionHasErrors('attachment');
    $this->assertDatabaseCount('tasks', 0);
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
})->with([
    ['script.php', 'application/x-httpd-php', 1],
    ['image.svg', 'image/svg+xml', 1],
    ['script.php', 'text/plain', 1],
    ['large.pdf', 'application/pdf', 5121],
]);

it('keeps the old file when replacement validation fails', function () {
    Storage::disk('public')->put('attachments/old.txt', 'old');
    $task = Task::factory()->create(['attachment_path' => 'attachments/old.txt']);
    $this->actingAs($task->project->user)->put(route('projects.tasks.update', [$task->project, $task]), [
        'title' => $task->title, 'status' => 'pending',
        'attachment' => UploadedFile::fake()->create('large.pdf', 5121, 'application/pdf'),
    ])->assertSessionHasErrors('attachment');
    expect($task->fresh()->attachment_path)->toBe('attachments/old.txt');
    Storage::disk('public')->assertExists('attachments/old.txt');
});

it('rejects unauthorized uploads before writing a file', function () {
    $project = Project::factory()->create();
    $this->actingAs(User::factory()->create())->post(route('projects.tasks.store', $project), [
        'title' => 'Document', 'status' => 'pending',
        'attachment' => UploadedFile::fake()->createWithContent('notes.txt', 'notes'),
    ])->assertForbidden();
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});

it('protects attachment downloads and scoped task binding', function () {
    Storage::disk('public')->put('attachments/notes.txt', 'notes');
    $task = Task::factory()->create(['attachment_path' => 'attachments/notes.txt']);
    $this->get(route('projects.tasks.attachment', [$task->project, $task]))->assertRedirect(route('login'));
    $otherProject = Project::factory()->create();
    $this->actingAs($otherProject->user)->get(route('projects.tasks.attachment', [$task->project, $task]))->assertForbidden();
    $this->get(route('projects.tasks.attachment', [$otherProject, $task]))->assertNotFound();
});

it('returns not found for absent or missing attachment files', function (?string $path) {
    $task = Task::factory()->create(['attachment_path' => $path]);
    $this->actingAs($task->project->user)->get(route('projects.tasks.attachment', [$task->project, $task]))->assertNotFound();
})->with([null, 'attachments/missing.txt']);

it('removes the attachment when its task is deleted', function () {
    Storage::disk('public')->put('attachments/delete.txt', 'delete');
    $task = Task::factory()->create(['attachment_path' => 'attachments/delete.txt']);
    $this->actingAs($task->project->user)->delete(route('projects.tasks.destroy', [$task->project, $task]))->assertRedirect();
    $this->assertModelMissing($task);
    Storage::disk('public')->assertMissing('attachments/delete.txt');
});

it('does not save a task when file storage fails', function () {
    $project = Project::factory()->create();
    Storage::shouldReceive('disk')->with('public')->andReturnSelf();
    Storage::shouldReceive('putFile')->once()->andReturn(false);
    $this->actingAs($project->user)->post(route('projects.tasks.store', $project), [
        'title' => 'Document', 'status' => 'pending',
        'attachment' => UploadedFile::fake()->createWithContent('notes.txt', 'notes'),
    ])->assertStatus(500);
    $this->assertDatabaseCount('tasks', 0);
});
