<x-app-layout>
    @php
        $totalTasks = $project->tasks->count();
        $inProgressTasks = $project->tasks->where('status', 'in_progress')->count();
        $completedTasks = $project->tasks->where('status', 'done')->count();
    @endphp

    <div class="tf-project-show">
        <header class="tf-project-show-header">
            <div class="tf-project-show-intro">
                <a href="{{ route('projects.index') }}" class="tf-project-back">← Mes projets</a>
                <h1>{{ $project->name }}</h1>
                <p>{{ $project->description ?: 'Aucune description pour ce projet.' }}</p>
                @if ($overdueTasks->isNotEmpty())
                    <span class="tf-project-overdue-note">{{ $overdueTasks->count() }} tâche(s) en retard</span>
                @endif
            </div>
            <nav class="tf-project-show-actions" aria-label="Actions du projet">
                @can('manageMembers', $project)
                    <a href="{{ route('projects.members.index', $project) }}" class="tf-btn tf-btn-secondary">Membres</a>
                @endcan
                @can('update', $project)
                    <a href="{{ route('projects.edit', $project) }}" class="tf-btn tf-btn-secondary">Modifier</a>
                @endcan
                @can('createTask', $project)
                    <a href="{{ route('projects.tasks.create', $project) }}" class="tf-btn tf-btn-primary">Ajouter une tâche</a>
                @endcan
            </nav>
        </header>

        <section class="tf-project-stat-grid" aria-label="Statistiques du projet">
            <article class="tf-project-stat-card">
                <span>Total des tâches</span>
                <strong>{{ $totalTasks }}</strong>
            </article>
            <article class="tf-project-stat-card">
                <span>En cours</span>
                <strong>{{ $inProgressTasks }}</strong>
            </article>
            <article class="tf-project-stat-card">
                <span>Terminées</span>
                <strong>{{ $completedTasks }}</strong>
            </article>
        </section>

        <section class="tf-project-task-section" aria-labelledby="project-tasks-title">
            <div class="tf-project-task-section-heading">
                <div>
                    <h2 id="project-tasks-title">Tâches du projet</h2>
                    <p>{{ $totalTasks }} tâche(s) au total</p>
                </div>
            </div>

            @forelse ($project->tasks as $task)
                @php($isOverdue = $overdueTasks->contains('id', $task->id))
                <article class="tf-project-task-card">
                    <div class="tf-project-task-content">
                        <div class="tf-project-task-title-row">
                            <h3>{{ $task->title }}</h3>
                            <span @class([
                                'tf-status-badge',
                                'tf-status-overdue' => $isOverdue,
                                'tf-status-pending' => ! $isOverdue && $task->status === 'pending',
                                'tf-status-in_progress' => ! $isOverdue && $task->status === 'in_progress',
                                'tf-status-done' => ! $isOverdue && $task->status === 'done',
                            ])><span></span>{{ $isOverdue ? 'En retard' : $task->status_label }}</span>
                        </div>
                        <p class="tf-project-task-deadline">
                            <span>Deadline</span>
                            <time @if ($task->deadline) datetime="{{ $task->deadline->toDateString() }}" @endif>{{ $task->deadline?->format('d/m/Y') ?? 'Non définie' }}</time>
                        </p>
                    </div>

                    <div class="tf-project-task-actions">
                        @can('update', $task)
                            <form method="POST" action="{{ route('projects.tasks.status', [$project, $task]) }}" class="tf-project-status-form">
                                @csrf
                                @method('PATCH')
                                <label class="sr-only" for="status-{{ $task->id }}">Statut de {{ $task->title }}</label>
                                <select id="status-{{ $task->id }}" name="status" class="tf-project-status-select">
                                    <option value="pending" @selected($task->status === 'pending')>En attente</option>
                                    <option value="in_progress" @selected($task->status === 'in_progress')>En cours</option>
                                    <option value="done" @selected($task->status === 'done')>Terminée</option>
                                </select>
                                <button type="submit" class="tf-btn tf-btn-secondary tf-project-status-submit">Changer</button>
                            </form>
                        @endcan
                        @if ($task->attachment_path)
                            @can('viewAttachment', $task)
                                <a href="{{ route('projects.tasks.attachment', [$project, $task]) }}" class="tf-btn tf-btn-secondary">Pièce jointe</a>
                            @endcan
                        @endif
                        @can('update', $task)
                            <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="tf-btn tf-btn-secondary">Modifier</a>
                        @endcan
                        @can('delete', $task)
                            <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $task]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="tf-btn tf-btn-danger">Supprimer</button>
                            </form>
                        @endcan
                    </div>
                </article>
            @empty
                <div class="tf-project-empty-state">
                    <span class="tf-project-empty-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M5 7.5h14M5 12h14M5 16.5h8"/><path d="M3.5 4.5h17v15h-17z"/></svg>
                    </span>
                    <h3>Aucune tâche pour le moment</h3>
                    <p>Les tâches de ce projet apparaîtront ici.</p>
                    @can('createTask', $project)
                        <a href="{{ route('projects.tasks.create', $project) }}" class="tf-btn tf-btn-primary">Ajouter une tâche</a>
                    @endcan
                </div>
            @endforelse
        </section>
    </div>
</x-app-layout>