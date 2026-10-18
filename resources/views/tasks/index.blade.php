<x-app-layout>
    <div class="tf-list-page">
        <div class="tf-list-heading flex flex-wrap justify-between gap-4">
            <div><p class="tf-eyebrow">TaskFlow</p><h1>{{ request()->boolean('attachments') ? 'Fichiers' : 'Mes tâches' }}</h1></div>
            <div class="tf-list-actions flex flex-wrap items-center gap-3">
                <form method="GET" action="{{ route('tasks.index') }}" class="tf-status-filter-form">
                    @if (request()->boolean('attachments'))
                        <input type="hidden" name="attachments" value="1">
                    @endif
                    @if (request()->filled('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <label class="sr-only" for="task-status">Filtrer par statut</label>
                    <select id="task-status" name="status" class="tf-status-filter" onchange="this.form.submit()">
                        <option value="" @selected($status === '')>Toutes les tâches</option>
                        <option value="pending" @selected($status === 'pending')>En attente</option>
                        <option value="in_progress" @selected($status === 'in_progress')>En cours</option>
                        <option value="done" @selected($status === 'done')>Terminées</option>
                        <option value="overdue" @selected($status === 'overdue')>En retard</option>
                    </select>
                </form>
                <a class="tf-btn tf-btn-primary" href="{{ route('projects.index') }}">Voir mes projets</a>
            </div>
        </div>

        <div class="tf-list-table-wrap">
            <table class="tf-recent-table">
                <thead><tr><th scope="col">Tâche</th><th scope="col">Projet</th><th scope="col">Échéance</th><th scope="col">Statut</th></tr></thead>
                <tbody>
                    @forelse ($tasks as $task)
                        @php($isOverdue = $task->deadline?->isBefore(today()) && $task->status !== 'done')
                        <tr>
                            <td><a href="{{ route('projects.show', $task->project) }}">{{ $task->title }}</a></td>
                            <td><a class="tf-table-project" href="{{ route('projects.show', $task->project) }}">{{ $task->project->name }}</a></td>
                            <td class="tf-deadline">{{ $task->deadline?->format('d/m/Y') ?? '—' }}</td>
                            <td><span class="tf-status-badge tf-status-{{ $isOverdue ? 'overdue' : $task->status }}"><span></span>{{ $isOverdue ? 'En retard' : $task->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="tf-empty-cell">Aucune tâche à afficher.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $tasks->links() }}
    </div>
</x-app-layout>