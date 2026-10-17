<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskAttachmentController extends Controller
{
    public function __invoke(Project $project, Task $task): StreamedResponse
    {
        Gate::authorize('view', $project);
        abort_unless($task->attachment_path && Storage::disk('public')->exists($task->attachment_path), 404);

        return Storage::disk('public')->download($task->attachment_path, headers: ['X-Content-Type-Options' => 'nosniff']);
    }
}
