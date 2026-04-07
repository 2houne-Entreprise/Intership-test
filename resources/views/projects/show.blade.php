{{-- Layout principal de l'application --}}
<x-app-layout>
    
    {{-- Section header avec le titre du projet --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $project->name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('projects.edit', $project) }}" 
                   class="text-amber-600 hover:text-amber-800">
                     Modifier le projet
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
            
            {{-- Affichage de la description du projet si elle existe --}}
            @if($project->description)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="font-semibold text-lg mb-2">Description</h3>
                        <p class="text-gray-700">{{ $project->description }}</p>
                    </div>
                </div>
            @endif

            {{-- Section principale : Gestion des tâches --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- En-tête de la section tâches --}}
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-semibold text-lg">Liste des tâches</h3>
                        <button onclick="toggleTaskForm()" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-200">
                            + Ajouter une tâche
                        </button>
                    </div>

                    {{-- Formulaire d'ajout de tâche (caché par défaut) --}}
                    <div id="taskForm" class="hidden mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="font-medium mb-4"> Nouvelle tâche</h4>
                        
                        {{-- Affichage des erreurs de validation --}}
                        @if($errors->any())
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Formulaire de création d'une tâche --}}
                        <form action="{{ route('tasks.store', $project) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="space-y-4">
                                
                                {{-- Champ : Titre de la tâche (obligatoire) --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Titre <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="title" 
                                           placeholder="Ex: Créer la page d'accueil" 
                                           value="{{ old('title') }}"
                                           class="w-full border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                                    @error('title')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Champ : Statut de la tâche --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Statut <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status" class="w-full border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}> En attente</option>
                                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}> En cours</option>
                                        <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}> Terminé</option>
                                    </select>
                                    @error('status')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Champ : Date limite (optionnelle) --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Date limite
                                    </label>
                                    <input type="date" 
                                           name="deadline" 
                                           value="{{ old('deadline') }}"
                                           class="w-full border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('deadline') border-red-500 @enderror">
                                    @error('deadline')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Champ : Pièce jointe (optionnelle) --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Pièce jointe
                                    </label>
                                    <input type="file" 
                                           name="attachment" 
                                           class="w-full border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('attachment') border-red-500 @enderror">
                                    <p class="text-xs text-gray-500 mt-1">Format accepté : PDF, image, etc. (Max 2 Mo)</p>
                                    @error('attachment')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Boutons d'action du formulaire --}}
                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="toggleTaskForm()" 
                                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium px-4 py-2 rounded-lg transition duration-200">
                                        Annuler
                                    </button>
                                    <button type="submit" 
                                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium px-4 py-2 rounded-lg transition duration-200">
                                        Créer la tâche
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Tableau d'affichage des tâches --}}
                    @if($project->tasks->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Titre
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Statut
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date limite
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fichier
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($project->tasks as $task)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        {{-- Titre de la tâche --}}
                                        <td class="px-6 py-4 text-gray-900">
                                            {{ $task->title }}
                                        </td>
                                        
                                        {{-- Formulaire de changement rapide de statut --}}
                                        <td class="px-6 py-4">
                                            <form action="{{ route('tasks.update', $task) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" 
                                                        onchange="this.form.submit()" 
                                                        class="text-sm border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                                                    <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}> En attente</option>
                                                    <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}> En cours</option>
                                                    <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Terminé</option>
                                                </select>
                                            </form>
                                        </td>
                                        
                                        {{-- Date limite formatée --}}
                                        <td class="px-6 py-4 text-gray-500">
                                            {{ $task->deadline ? date('d/m/Y', strtotime($task->deadline)) : '-' }}
                                        </td>
                                        
                                        {{-- Lien de téléchargement de la pièce jointe --}}
                                        <td class="px-6 py-4">
                                            @if($task->attachment_path)
                                                <a href="{{ Storage::url($task->attachment_path) }}" 
                                                   target="_blank" 
                                                   class="text-blue-600 hover:text-blue-800 hover:underline">
                                                     Télécharger
                                                </a>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Bouton de suppression --}}
                                        <td class="px-6 py-4">
                                            <form action="{{ route('tasks.destroy', $task) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-800 transition duration-200"
                                                        title="Supprimer">
                                                     Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        {{-- Message lorsqu'aucune tâche n'existe --}}
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg">Aucune tâche pour ce projet.</p>
                            <p class="text-gray-400 text-sm mt-2">Cliquez sur "+ Ajouter une tâche" pour commencer.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Script JavaScript pour afficher/masquer le formulaire d'ajout de tâche --}}
    <script>
        function toggleTaskForm() {
            const form = document.getElementById('taskForm');
            form.classList.toggle('hidden');
        }
    </script>

</x-app-layout>