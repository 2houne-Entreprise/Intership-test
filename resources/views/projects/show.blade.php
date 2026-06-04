<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-slate-900 leading-tight">
                {{ __('Détails du projet') }}
            </h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('projects.index') }}" class="inline-flex items-center px-3 py-1.5 border border-slate-350 rounded-lg text-xs font-bold text-slate-900 hover:bg-slate-50 transition">
                    &larr; {{ __('Retour') }}
                </a>
                <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-350 rounded-lg text-xs font-bold text-slate-900 hover:bg-slate-50 transition">
                    {{ __('Modifier le projet') }}
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 'done')->count();
        $inProgressTasks = $project->tasks->where('status', 'in_progress')->count();
        $pendingTasks = $project->tasks->where('status', 'pending')->count();
        $percent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    @endphp

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Project Card & Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-200">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
                    <div class="lg:col-span-2 space-y-2">
                        <h3 class="text-xl font-bold text-slate-900">{{ $project->name }}</h3>
                        <p class="text-slate-700 text-sm leading-relaxed">{{ $project->description ?? __('Aucune description fournie.') }}</p>
                    </div>
                    
                    <!-- Progress Card -->
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 space-y-3">
                        <div class="flex justify-between items-center text-xs font-bold text-slate-900">
                            <span>{{ __('Progression') }}</span>
                            <span>{{ $percent }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded h-2 overflow-hidden">
                            <div class="bg-blue-950 h-2 rounded transition-all duration-300" style="width: {{ $percent }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-slate-900 font-medium">
                            <span>{{ __('Attente') }}: {{ $pendingTasks }}</span>
                            <span>{{ __('En cours') }}: {{ $inProgressTasks }}</span>
                            <span>{{ __('Terminé') }}: {{ $completedTasks }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-200">
                <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                    <h3 class="text-base font-bold text-slate-900">{{ __('Tâches du projet') }}</h3>
                    <a href="{{ route('tasks.create', $project) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-350 rounded-lg text-xs font-bold text-slate-900 hover:bg-slate-50 transition">
                        + {{ __('Ajouter une tâche') }}
                    </a>
                </div>

                @if ($errors->any())
                    <x-alert type="error" class="mb-6">
                        <ul class="list-disc list-inside text-sm text-slate-900">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif

                @if($project->tasks->isEmpty())
                    <p class="text-slate-700 text-center py-6 text-sm">{{ __('Aucune tâche trouvée pour ce projet.') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead>
                                <tr class="text-slate-900 text-left bg-slate-50">
                                    <th scope="col" class="px-6 py-3 text-xs font-bold uppercase tracking-wider">
                                        {{ __('Titre') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-bold uppercase tracking-wider">
                                        {{ __('Date Limite') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-bold uppercase tracking-wider">
                                        {{ __('Modifier Statut') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-bold uppercase tracking-wider">
                                        {{ __('Statut') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-bold uppercase tracking-wider">
                                        {{ __('Pièce jointe') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @foreach($project->tasks as $task)
                                    @php
                                        $isOverdue = $task->deadline && \Carbon\Carbon::parse($task->deadline)->isPast() && $task->status !== 'done';
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                                            {{ $task->title }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                            @if($task->deadline)
                                                <span class="{{ $isOverdue ? 'text-rose-600 font-bold' : '' }}">
                                                    {{ \Carbon\Carbon::parse($task->deadline)->format('d/m/Y') }}
                                                </span>
                                                @if($isOverdue)
                                                    <span class="ml-1 text-xs font-bold text-rose-600">({{ __('En retard') }})</span>
                                                @endif
                                            @else
                                                <span class="text-slate-400 font-medium">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form method="POST" action="{{ route('tasks.update', $task) }}" class="inline-block">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="title" value="{{ $task->title }}">
                                                <input type="hidden" name="deadline" value="{{ $task->deadline }}">
                                                
                                                <select name="status" onchange="this.form.submit()" class="rounded border-slate-300 text-xs py-1 px-2 text-slate-900 font-bold bg-white cursor-pointer focus:border-slate-500 focus:ring-slate-500">
                                                    <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>{{ __('En attente') }}</option>
                                                    <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>{{ __('En cours') }}</option>
                                                    <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>{{ __('Terminé') }}</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                                            {{ $task->status_label }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($task->attachment_path)
                                                <a href="{{ Storage::url($task->attachment_path) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-blue-950 hover:underline">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <span>{{ __('Télécharger') }}</span>
                                                </a>
                                            @else
                                                <span class="text-xs text-slate-400 font-medium italic">-</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Voulez-vous vraiment supprimer cette tâche ?')" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-bold text-slate-900 hover:underline">
                                                    {{ __('Supprimer') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
