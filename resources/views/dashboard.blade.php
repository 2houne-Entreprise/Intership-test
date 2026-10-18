<x-app-layout>
    <div class="tf-dashboard">
        <div class="tf-dashboard-heading">
            <div class="tf-dashboard-title">
                <h1>Dashboard</h1>
                <p class="tf-dashboard-date">{{ $todayLabel }}</p>
            </div>
            <form action="{{ route('dashboard') }}" method="GET" class="tf-period-form">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M7 3.5v3M17 3.5v3M4 9.5h16"/></svg>
                <label class="sr-only" for="dashboard-range">Période du tableau de bord</label>
                <select id="dashboard-range" name="range">
                    <option value="month" @selected($range === 'month')>Ce mois-ci</option>
                    <option value="last_month" @selected($range === 'last_month')>Le mois dernier</option>
                    <option value="all" @selected($range === 'all')>Toutes les dates</option>
                </select>
                @if ($search !== '')
                    <input type="hidden" name="search" value="{{ $search }}">
                @endif
                <button type="submit" aria-label="Appliquer la période" title="Appliquer la période">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m8 10 4 4 4-4"/></svg>
                </button>
            </form>
        </div>

        <div class="tf-overview-grid">
            <section class="tf-overview-card" aria-labelledby="overview-title">
                <div class="tf-card-heading">
                    <span class="tf-icon-tile tf-icon-green" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="3"/><path d="m8 12 2.5 2.5L16 9"/></svg>
                    </span>
                    <h2 id="overview-title">Vue d’ensemble des tâches</h2>
                </div>

                <div class="tf-overview-main">
                    <div class="tf-total-block">
                        <span class="tf-muted-label">Tâches totales</span>
                        <strong class="tf-total-number">{{ $totalTasks }}</strong>
                        <p class="tf-comparison"><span aria-hidden="true">↗</span> {{ $comparison }}</p>
                    </div>
                    <div class="tf-workflow-artwork">
                        <img src="{{ asset('images/taskflow-workflow.jpeg') }}" alt="" class="tf-workflow-image">
                    </div>
                </div>

                <div class="tf-overview-breakdown">
                    <div class="tf-breakdown-item">
                        <span class="tf-breakdown-icon tf-icon-gray" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><path d="M12 7v5l3 2"/></svg>
                        </span>
                        <div>
                            <span class="tf-muted-label">En attente</span>
                            <strong>{{ $pendingTasks }}</strong>
                            <small>{{ $totalTasks > 0 ? round(($pendingTasks / $totalTasks) * 100) : 0 }}% du total</small>
                        </div>
                    </div>
                    <div class="tf-breakdown-item">
                        <span class="tf-breakdown-icon tf-icon-green" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><path d="m8 12 2.5 2.5L16 9"/></svg>
                        </span>
                        <div>
                            <span class="tf-muted-label">Terminées</span>
                            <strong>{{ $completedTasks }}</strong>
                            <small>{{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}% du total</small>
                        </div>
                    </div>
                </div>
            </section>

            <aside class="tf-stat-stack" aria-label="Indicateurs du projet">
                <a href="{{ route('projects.index') }}" class="tf-stat-card">
                    <span class="tf-stat-icon tf-icon-blue" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M3.5 7.5h7l2 2h8v9a2 2 0 0 1-2 2h-13a2 2 0 0 1-2-2z"/><path d="M3.5 9.5V6a2 2 0 0 1 2-2h4l2 2h7a2 2 0 0 1 2 2v1.5"/></svg>
                    </span>
                    <span class="tf-stat-copy"><span>Projets actifs</span><strong>{{ $activeProjects }}</strong></span>
                    <span class="tf-stat-trend">{{ $activeProjects }}<small> ce mois-ci</small></span>
                </a>
                <a href="{{ route('tasks.index') }}" class="tf-stat-card">
                    <span class="tf-stat-icon tf-icon-blue" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><path d="M12 7v5l3 2"/></svg>
                    </span>
                    <span class="tf-stat-copy"><span>Tâches en cours</span><strong>{{ $inProgressTasks }}</strong></span>
                    <span class="tf-stat-percent">{{ $totalTasks > 0 ? round(($inProgressTasks / $totalTasks) * 100) : 0 }}%<small> du total</small></span>
                </a>
                <a href="{{ route('tasks.index', ['range' => 'overdue']) }}" class="tf-stat-card">
                    <span class="tf-stat-icon tf-icon-red" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 3.5 21 20H3z"/><path d="M12 9v5m0 2.5v.1"/></svg>
                    </span>
                    <span class="tf-stat-copy"><span>Tâches en retard</span><strong>{{ $overdueTasks }}</strong></span>
                    <span class="tf-stat-percent">{{ $totalTasks > 0 ? round(($overdueTasks / $totalTasks) * 100) : 0 }}%<small> du total</small></span>
                </a>
            </aside>
        </div>

        <section id="recent-tasks" class="tf-recent-card" aria-labelledby="recent-title">
            <div class="tf-recent-heading">
                <span class="tf-icon-tile tf-icon-green" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M6 3.5h8l4 4V20a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4.5a1 1 0 0 1 1-1z"/><path d="M14 3.5v5h5M8 12h8m-8 3.5h8"/></svg>
                </span>
                <h2 id="recent-title">Mes tâches récentes</h2>
            </div>

            <div class="tf-recent-task-list">
                @forelse ($recentTasks as $task)
                    @php($isOverdue = $task->deadline?->isBefore(today()) && $task->status !== 'done')
                    <a href="{{ route('projects.show', $task->project) }}" class="tf-recent-task-row">
                        <div class="tf-recent-task-copy">
                            <h3 class="tf-recent-task-title">{{ $task->title }}</h3>
                            <p class="tf-recent-task-meta">
                                <span>{{ $task->project->name }}</span>
                                <span>{{ $task->deadline?->locale('fr')->translatedFormat('j M Y') ?? 'Sans échéance' }}</span>
                            </p>
                        </div>
                        <span class="tf-status-badge tf-status-{{ $isOverdue ? 'overdue' : $task->status }}"><span></span>{{ $isOverdue ? 'En retard' : $task->status_label }}</span>
                    </a>
                @empty
                    <p class="tf-recent-empty">Aucune tâche pour cette période. Créez un projet et ajoutez votre première tâche.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>