<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">TaskFlow</p>
                <h2 class="mt-1 text-2xl font-semibold text-gray-900">Mes projets</h2>
            </div>
            <a href="{{ route('projects.create') }}" class="tf-btn tf-btn-primary">Nouveau projet</a>
        </div>
    </x-slot>

    <section class="tf-list-page">
        <div class="tf-project-list-grid">
            @forelse ($projects as $project)
                @php
                    $members = collect([$project->user])->merge($project->members)->unique('id')->values();
                    $userRole = $project->roleFor(Auth::user());
                    $roleLabel = match ($userRole) {
                        'owner' => 'Owner',
                        'editor' => 'Editor',
                        'viewer' => 'Viewer',
                        default => 'Membre',
                    };
                    $completedTasks = $project->completed_tasks_count;
                    $completion = $project->tasks_count > 0 ? (int) round(($completedTasks / $project->tasks_count) * 100) : 0;
                @endphp

                <article class="tf-project-card">
                    <div class="tf-project-card-header">
                        <div class="tf-project-title-wrap">
                            <h3>{{ $project->name }}</h3>
                            <p>{{ $project->description ?: 'Aucune description.' }}</p>
                        </div>
                        <span class="tf-project-role tf-role-{{ $userRole }}">{{ $roleLabel }}</span>
                    </div>

                    <div class="tf-project-stats">
                        <div>
                            <span>Tâches</span>
                            <strong>{{ $project->tasks_count }}</strong>
                        </div>
                        <div>
                            <span>Progression</span>
                            <strong>{{ $completion }}%</strong>
                        </div>
                    </div>

                    <div class="tf-project-members-row">
                        <div class="project-member-avatars" aria-label="Membres du projet">
                            @foreach ($members->take(4) as $member)
                                @php
                                    $memberRole = $member->id === $project->user_id
                                        ? 'Owner'
                                        : ucfirst((string) ($member->pivot->role ?? 'Membre'));
                                @endphp
                                <div class="project-member-avatar" title="{{ $member->name }} • {{ $memberRole }}">
                                    @if ($member->profile_photo_path)
                                        <img src="{{ $member->profile_photo_url }}" alt="{{ $member->name }}" class="profile-photo">
                                    @else
                                        <span>{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim($member->name), 0, 1)) }}</span>
                                    @endif
                                </div>
                            @endforeach
                            @if ($members->count() > 4)
                                <div class="project-member-avatar project-member-avatar-more">
                                    +{{ $members->count() - 4 }}
                                </div>
                            @endif
                        </div>

                        <div class="tf-project-actions">
                            <a href="{{ route('projects.show', $project) }}" class="tf-btn tf-btn-secondary">Ouvrir</a>
                            @can('update', $project)
                                <a href="{{ route('projects.edit', $project) }}" class="tf-btn tf-btn-secondary">Modifier</a>
                            @endcan
                            @can('delete', $project)
                                <form method="POST" action="{{ route('projects.destroy', $project) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="tf-btn tf-btn-danger">Supprimer</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </article>
            @empty
                <div class="tf-project-empty">
                    <h3>Aucun projet pour le moment</h3>
                    <p>Créez votre premier projet pour commencer à organiser vos tâches.</p>
                    <a href="{{ route('projects.create') }}" class="tf-btn tf-btn-primary">Créer un projet</a>
                </div>
            @endforelse
        </div>
    </section>
</x-app-layout>
