<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $project->name }}
                </h2>
                @if($project->description)
                    <p class="text-gray-500 text-sm mt-1">{{ $project->description }}</p>
                @endif
            </div>
            <div class="space-x-2">
                <a href="{{ route('projects.edit', $project) }}" 
                   class="text-amber-600 hover:text-amber-800">
                     Modifier
                </a>
                <a href="{{ route('projects.index') }}" 
                   class="text-gray-600 hover:text-gray-800">
                    ← Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Statistiques des tâches (version compacte) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 text-gray-900">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        {{-- Total --}}
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl"></span>
                            <div>
                                <div class="font-bold text-lg">{{ $stats['total'] }}</div>
                                <div class="text-xs text-gray-500">Total</div>
                            </div>
                        </div>
                        
                        {{-- En attente --}}
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl"></span>
                            <div>
                                <div class="font-bold text-lg text-yellow-600">{{ $stats['pending'] }}</div>
                                <div class="text-xs text-gray-500">En attente</div>
                            </div>
                        </div>
                        
                        {{-- En cours --}}
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl"></span>
                            <div>
                                <div class="font-bold text-lg text-purple-600">{{ $stats['in_progress'] }}</div>
                                <div class="text-xs text-gray-500">En cours</div>
                            </div>
                        </div>
                        
                        {{-- Terminées --}}
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl"></span>
                            <div>
                                <div class="font-bold text-lg text-green-600">{{ $stats['done'] }}</div>
                                <div class="text-xs text-gray-500">Terminées</div>
                            </div>
                        </div>
                        
                        {{-- En retard --}}
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl"></span>
                            <div>
                                <div class="font-bold text-lg text-red-600">{{ $stats['overdue'] }}</div>
                                <div class="text-xs text-gray-500">En retard</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section des tâches --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- En-tête --}}
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-semibold text-lg"> Liste des tâches</h3>
                        <button onclick="toggleTaskForm()" 
                                
                              style="background-color: #2563eb; color: white; font-weight: bold; padding: 8px 16px; border-radius: 8px; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1); display: inline-block;"
                                onmouseover="this.style.backgroundColor='#1d4ed8'"
                                onmouseout="this.style.backgroundColor='#2563eb'">
                                + Ajouter une tâche
                        </button>
                    </div>

                    {{-- Formulaire d'ajout --}}
                    <div id="taskForm" class="hidden mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="font-medium mb-4">Nouvelle tâche</h4>
                        
                        @if($errors->any())
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('tasks.store', $project) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <input type="text" 
                                           name="title" 
                                           placeholder="Titre de la tâche" 
                                           value="{{ old('title') }}"
                                           class="w-full border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <select name="status" class="w-full border-gray-300 rounded-lg">
                                        <option value="pending"> En attente</option>
                                        <option value="in_progress"> En cours</option>
                                        <option value="done">Terminé</option>
                                    </select>
                                </div>
                                <div>
                                    <input type="date" 
                                           name="deadline" 
                                           value="{{ old('deadline') }}"
                                           class="w-full border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <input type="file" 
                                           name="attachment" 
                                           class="w-full border-gray-300 rounded-lg">
                                </div>
                            </div>
                            <div class="flex justify-end space-x-2 mt-4">
                                <button type="button" onclick="toggleTaskForm()" 
                                        class="bg-gray-300 px-4 py-2 rounded">Annuler</button>
                                <button type="submit" 
                                        class="bg-gray-300 px-4 py-2 rounded">Créer</button>
                            </div>
                        </form>
                    </div>

                    {{-- Tableau des tâches --}}
                    @if($project->tasks->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date limite</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fichier</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($project->tasks as $task)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">{{ $task->title }}</td>
                                        <td class="px-4 py-3">
                                            <form action="{{ route('tasks.update', $task) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" onchange="this.form.submit()" 
                                                        class="text-sm border-gray-300 rounded">
                                                    <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}> En attente</option>
                                                    <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>En cours</option>
                                                    <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Terminé</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-4 py-3 {{ $task->deadline && $task->deadline < now() && $task->status != 'done' ? 'text-red-600 font-medium' : '' }}">
                                            {{ $task->deadline ? date('d/m/Y', strtotime($task->deadline)) : '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($task->attachment_path)
                                                <a href="{{ Storage::url($task->attachment_path) }}" target="_blank" class="text-blue-600 hover:underline">📎</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800">Supprimer</button>
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