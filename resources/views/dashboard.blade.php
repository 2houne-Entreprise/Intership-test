<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @php
        $user = auth()->user();
        $userProjects = $user->projects()->with('tasks')->latest()->get();
        $totalProjects = $userProjects->count();
        
        $totalTasks = $userProjects->sum(function($project) {
            return $project->tasks->count();
        });

        $completedTasks = $userProjects->sum(function($project) {
            return $project->tasks->where('status', 'done')->count();
        });

        $pendingTasks = $userProjects->sum(function($project) {
            return $project->tasks->where('status', 'pending')->count();
        });

        $inProgressTasks = $userProjects->sum(function($project) {
            return $project->tasks->where('status', 'in_progress')->count();
        });

        $recentProjects = $userProjects->take(3);
    @endphp

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Header Card -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-6">
                <h3 class="text-2xl font-bold text-slate-900">
                    Bonjour, {{ $user->name }}
                </h3>
                <p class="text-slate-700 mt-2 text-sm">
                    Bienvenue dans votre gestionnaire de projets. Suivez l'avancement de vos tâches et gérez vos objectifs.
                </p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Projects Stat -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <span class="block text-3xl font-bold text-black">{{ $totalProjects }}</span>
                    <span class="text-xs font-bold text-blue-950 uppercase tracking-wider">Projets Actifs</span>
                </div>

                <!-- Total Tasks Stat -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <span class="block text-3xl font-bold text-black">{{ $totalTasks }}</span>
                    <span class="text-xs font-bold text-blue-950 uppercase tracking-wider">Tâches Totales</span>
                </div>

                <!-- In Progress Tasks Stat -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <span class="block text-3xl font-bold text-black">{{ $inProgressTasks }}</span>
                    <span class="text-xs font-bold text-blue-950 uppercase tracking-wider">En Cours</span>
                </div>

                <!-- Completed Tasks Stat -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <span class="block text-3xl font-bold text-black">{{ $completedTasks }}</span>
                    <span class="text-xs font-bold text-blue-950 uppercase tracking-wider">Terminées</span>
                </div>
            </div>

            <!-- Quick Actions & Recent Projects Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Panel: Quick Actions -->
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                        <h4 class="text-base font-bold text-slate-900 mb-4">Actions Rapides</h4>
                        <div class="space-y-3">
                            <a href="{{ route('projects.create') }}" class="block p-3 border border-slate-200 hover:bg-slate-50 rounded-lg text-sm font-semibold text-blue-950 transition duration-150">
                                &rarr; {{ __('Créer un Nouveau Projet') }}
                            </a>
                            <a href="{{ route('projects.index') }}" class="block p-3 border border-slate-200 hover:bg-slate-50 rounded-lg text-sm font-semibold text-blue-950 transition duration-150">
                                &rarr; {{ __('Voir tous mes projets') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Recent Projects -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-base font-bold text-slate-900">Projets Récents</h4>
                            <a href="{{ route('projects.index') }}" class="text-sm font-semibold text-blue-950 hover:underline transition">
                                {{ __('Voir tout') }} &rarr;
                            </a>
                        </div>

                        @if($recentProjects->isEmpty())
                            <div class="text-center py-8 text-slate-700 space-y-2">
                                <p class="text-sm">Aucun projet en cours.</p>
                                <a href="{{ route('projects.create') }}" class="inline-block text-sm font-bold text-blue-950 hover:underline">
                                    {{ __('Créer votre premier projet') }}
                                </a>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($recentProjects as $project)
                                    @php
                                        $totalProjTasks = $project->tasks->count();
                                        $completedProjTasks = $project->tasks->where('status', 'done')->count();
                                        $percent = $totalProjTasks > 0 ? round(($completedProjTasks / $totalProjTasks) * 100) : 0;
                                    @endphp
                                    <div class="p-4 border border-slate-200 rounded-lg hover:bg-slate-50 transition duration-150">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <h5 class="text-base font-bold text-slate-900">
                                                    <a href="{{ route('projects.show', $project) }}" class="hover:underline">{{ $project->name }}</a>
                                                </h5>
                                                <p class="text-xs text-slate-700 mt-1">{{ $project->description ?? __('Aucune description') }}</p>
                                            </div>
                                            <span class="text-xs font-bold text-blue-950">
                                                {{ $percent }}% {{ __('Complété') }}
                                            </span>
                                        </div>
                                        <div class="space-y-2">
                                            <!-- Simple Progress bar -->
                                            <div class="w-full bg-slate-100 rounded h-2 overflow-hidden">
                                                <div class="bg-blue-950 h-2 rounded transition-all duration-300" style="width: {{ $percent }}%"></div>
                                            </div>
                                            <div class="flex justify-between items-center text-xs text-slate-700">
                                                <span>{{ $totalProjTasks }} {{ trans_choice('tâche|tâches', $totalProjTasks) }}</span>
                                                <span>{{ $completedProjTasks }} {{ __('terminées') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
