<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $project->name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('projects.edit', $project) }}" 
                   class="text-amber-600 hover:text-amber-800">
                    ✏️ Modifier le projet
                </a>
                <a href="{{ route('projects.index') }}" 
                   class="text-gray-600 hover:text-gray-800">
                    ← Retour aux projets
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Description du projet --}}
            @if($project->description)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="font-semibold text-lg mb-2">Description</h3>
                        <p class="text-gray-700">{{ $project->description }}</p>
                    </div>
                </div>
            @endif

            {{-- Section des tâches --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- En-tête des tâches --}}
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-semibold text-lg">📋 Tâches</h3>
                        <button onclick="toggleTaskForm()" 
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                            + Ajouter une tâche
                        </button>
                    </div>

                    {{-- Formulaire d'ajout de tâche (caché par défaut) --}}
                    <div id="taskForm" class="hidden mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="font-medium mb-4">Nouvelle tâche</h4>
                        <form action="{{ route('tasks.store', $project) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <input type="text" 
                                           name="title" 
                                           placeholder="Titre de la tâche" 
                                           class="w-full border-gray-300 rounded-lg"
                                           required>
                                </div>
                                <div>
                                    <select name="status" class="w-full border-gray-300 rounded-lg">
                                        <option value="pending">En attente</option>
                                        <option value="in_progress">En cours</option>
                                        <option value="done">Terminé</option>
                                    </select>
                                </div>
                                <div>
                                    <input type="date" 
                                           name="deadline" 
                                           class="w-full border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <input type="file" 
                                           name="attachment" 
                                           class="w-full border-gray-300 rounded-lg">
                                </div>
                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="toggleTaskForm()" 
                                            class="bg-gray-300 px-4 py-2 rounded">Annuler</button>
                                    <button type="submit" 
                                            class="bg-blue-500 text-white px-4 py-2 rounded">Créer</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Liste des tâches --}}
                    @if($project->tasks->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date limite</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fichier</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($project->tasks as $task)
                                    <tr>
                                        <td class="px-6 py-4">{{ $task->title }}</td>
                                        <td class="px-6 py-4">
                                            <form action="{{ route('tasks.update', $task) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" onchange="this.form.submit()" class="text-sm border-gray-300 rounded">
                                                    <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>⏳ En attente</option>
                                                    <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>🔄 En cours</option>
                                                    <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>✅ Terminé</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4">{{ $task->deadline ? date('d/m/Y', strtotime($task->deadline)) : '-' }}</td>
                                        <td class="px-6 py-4">
                                            @if($task->attachment_path)
                                                <a href="{{ Storage::url($task->attachment_path) }}" target="_blank" class="text-blue-600 hover:underline">📎 Télécharger</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Supprimer cette tâche ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800">🗑️</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-6">Aucune tâche pour ce projet.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleTaskForm() {
            const form = document.getElementById('taskForm');
            form.classList.toggle('hidden');
        }
    </script>
</x-app-layout>