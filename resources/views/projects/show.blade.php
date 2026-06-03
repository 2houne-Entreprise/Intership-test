<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du projet') }}
            </h2>
            <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                &larr; {{ __('Retour aux projets') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Project Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-850 mb-2">{{ $project->name }}</h3>
                        <p class="text-gray-650 leading-relaxed">{{ $project->description ?? __('Aucune description fournie.') }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Modifier le projet') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tasks Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Tâches du projet') }}</h3>
                    <a href="{{ route('tasks.create', $project) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Ajouter une tâche') }}
                    </a>
                </div>

                @if ($errors->any())
                    <x-alert type="error" class="mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif

                @if($project->tasks->isEmpty())
                    <p class="text-gray-500 text-center py-4">{{ __('Aucune tâche trouvée pour ce projet.') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Titre') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Date Limite') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                                        {{ __('Statut') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($project->tasks as $task)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d/m/Y') : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form method="POST" action="{{ route('tasks.update', $task) }}" class="inline-block">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="title" value="{{ $task->title }}">
                                                <input type="hidden" name="deadline" value="{{ $task->deadline }}">
                                                
                                                <select name="status" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-1">
                                                    <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>{{ __('En attente') }}</option>
                                                    <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>{{ __('En cours') }}</option>
                                                    <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>{{ __('Terminé') }}</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Voulez-vous vraiment supprimer cette tâche ?')" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
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
