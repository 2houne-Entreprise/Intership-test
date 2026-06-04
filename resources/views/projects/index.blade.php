<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-slate-900 leading-tight">
                {{ __('Mes Projets') }}
            </h2>
            <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 hover:bg-slate-50 rounded-lg text-xs font-bold text-slate-900 uppercase tracking-widest transition">
                + {{ __('Créer un projet') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-slate-200">
                <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                    <h3 class="text-base font-bold text-slate-900">{{ __('Liste des projets') }}</h3>
                    <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded">
                        {{ $projects->count() }} {{ trans_choice('projet|projets', $projects->count()) }}
                    </span>
                </div>

                @if($projects->isEmpty())
                    <div class="text-center py-12">
                        <h4 class="text-base font-bold text-slate-900">{{ __('Aucun projet trouvé') }}</h4>
                        <p class="mt-1 text-sm text-slate-700">{{ __('Commencez par créer votre premier projet.') }}</p>
                        <div class="mt-6">
                            <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 hover:bg-slate-50 rounded-lg text-xs font-bold text-slate-900 uppercase tracking-widest transition">
                                {{ __('Créer un projet') }}
                            </a>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($projects as $project)
                            @php
                                $totalProjTasks = $project->tasks->count();
                                $completedProjTasks = $project->tasks->where('status', 'done')->count();
                                $percent = $totalProjTasks > 0 ? round(($completedProjTasks / $totalProjTasks) * 100) : 0;
                            @endphp
                            <div class="p-5 bg-white rounded-lg border border-slate-200 hover:bg-slate-50 flex flex-col justify-between transition duration-150">
                                <div>
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="text-base font-bold text-slate-900 hover:underline">
                                            <a href="{{ route('projects.show', $project) }}">
                                                {{ $project->name }}
                                            </a>
                                        </h4>
                                        <span class="text-xs font-bold text-blue-950">
                                            {{ $percent }}%
                                        </span>
                                    </div>
                                    <p class="text-slate-700 text-sm mb-4 line-clamp-2">{{ $project->description ?? __('Aucune description fournie.') }}</p>
                                    
                                    <!-- Simple Progress bar -->
                                    <div class="space-y-1.5 mb-4">
                                        <div class="w-full bg-slate-100 rounded h-1.5 overflow-hidden">
                                            <div class="bg-blue-950 h-1.5 rounded transition-all duration-300" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <div class="flex justify-between items-center text-xs text-slate-700">
                                            <span>{{ $totalProjTasks }} {{ trans_choice('tâche|tâches', $totalProjTasks) }}</span>
                                            <span>{{ $completedProjTasks }} {{ __('terminées') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex space-x-4 items-center mt-2 pt-3 border-t border-slate-150">
                                    <a href="{{ route('projects.show', $project) }}" class="text-xs font-bold text-blue-950 hover:underline">
                                        {{ __('Voir') }}
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}" class="text-xs font-bold text-slate-900 hover:underline">
                                        {{ __('Modifier') }}
                                    </a>
                                    <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Voulez-vous vraiment supprimer ce projet ?')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-slate-900 hover:underline">
                                            {{ __('Supprimer') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>