<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        Gate::authorize('viewAny', Task::class);

        if ($request->query('view') === 'calendar') {
            return redirect()->route('tasks.index', $request->except('view'));
        }

        $filters = $request->validate([
            'status' => ['nullable', 'in:pending,in_progress,done,overdue'],
        ]);
        $status = $filters['status'] ?? ($request->query('range') === 'overdue' ? 'overdue' : '');
        $projectIds = Project::accessibleTo($request->user())->pluck('projects.id');
        $tasks = Task::query()
            ->whereIn('project_id', $projectIds)
            ->with('project:id,name')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhereHas('project', fn ($project) => $project->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($request->boolean('attachments'), fn ($query) => $query->whereNotNull('attachment_path'))
            ->when($status === 'overdue', fn ($query) => $query->overdue())
            ->when(in_array($status, ['pending', 'in_progress', 'done'], true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('tasks.index', compact('status', 'tasks'));
    }

    public function create(Request $request, string $project): View
    {
        $project = Project::query()->findOrFail($project);
        Gate::authorize('createTask', $project);

        return view('tasks.create', compact('project'));
    }

    public function store(StoreTaskRequest $request, string $project): RedirectResponse
    {
        $project = Project::query()->findOrFail($project);
        Gate::authorize('createTask', $project);
        $validated = $request->validated();
        $attachment = $request->file('attachment');
        unset($validated['attachment']);

        if ($attachment !== null) {
            $validated['attachment_path'] = $attachment->store('attachments');
        }

        $project->tasks()->create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Tâche créée avec succès.');
    }

    public function edit(Request $request, string $project, string $task): View
    {
        $project = Project::query()->findOrFail($project);
        $task = $project->tasks()->findOrFail($task);
        Gate::authorize('update', $task);

        return view('tasks.edit', compact('project', 'task'));
    }

    public function update(UpdateTaskRequest $request, string $project, string $task): RedirectResponse
    {
        $project = Project::query()->findOrFail($project);
        $task = $project->tasks()->findOrFail($task);
        Gate::authorize('update', $task);
        $validated = $request->validated();
        $attachment = $request->file('attachment');
        $previousAttachmentPath = $task->attachment_path;
        unset($validated['attachment']);

        if ($attachment !== null) {
            $validated['attachment_path'] = $attachment->store('attachments');
        }

        $task->update($validated);

        if ($attachment !== null && $previousAttachmentPath !== null) {
            Storage::disk('local')->delete($previousAttachmentPath);
            Storage::disk('public')->delete($previousAttachmentPath);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Tâche modifiée avec succès.');
    }

    public function destroy(Request $request, string $project, string $task): RedirectResponse
    {
        $project = Project::query()->findOrFail($project);
        $task = $project->tasks()->findOrFail($task);
        Gate::authorize('delete', $task);
        $attachmentPath = $task->attachment_path;
        $task->delete();

        if ($attachmentPath !== null) {
            Storage::disk('local')->delete($attachmentPath);
            Storage::disk('public')->delete($attachmentPath);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Tâche supprimée avec succès.');
    }

    public function updateStatus(Request $request, string $project, string $task): RedirectResponse
    {
        $project = Project::query()->findOrFail($project);
        $task = $project->tasks()->findOrFail($task);
        Gate::authorize('update', $task);
        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,done'],
        ]);

        $task->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Statut de la tâche modifié.');
    }

    public function attachment(Request $request, string $project, string $task): StreamedResponse
    {
        $project = Project::query()->findOrFail($project);
        $task = $project->tasks()->findOrFail($task);
        Gate::authorize('viewAttachment', $task);
        abort_if($task->attachment_path === null, 404);

        $disk = Storage::disk('local')->exists($task->attachment_path) ? 'local' : 'public';

        return Storage::disk($disk)->download($task->attachment_path);
    }
}
