<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mes Projets') }}
            </h2>
           <a href="{{ route('projects.create') }}" 
                style="background-color: #2563eb; color: white; font-weight: bold; padding: 8px 16px; border-radius: 8px; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1); display: inline-block;"
                onmouseover="this.style.backgroundColor='#1d4ed8'"
                onmouseout="this.style.backgroundColor='#2563eb'">
                    + Nouveau Projet
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Messages de succès --}}
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Liste des projets --}}
                    @forelse($projects as $project)
                        <div class="border-b border-gray-200 py-5 last:border-b-0 hover:bg-gray-50 transition duration-150">
                            
                            {{-- Ligne principale : Nom + boutons --}}
                            <div class="flex justify-between items-center">
                                
                                {{-- Informations du projet --}}
                                <div class="flex-1">
                                    <a href="{{ route('projects.show', $project) }}" 
                                       class="text-lg font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $project->name }}
                                    </a>
                                    
                                    {{-- Affichage du nombre de tâches --}}
                                    <div class="flex items-center space-x-4 mt-1">
                                        <span class="text-sm text-gray-500">
                                            {{ $project->tasks_count }} tâche(s)
                                        </span>
                                        @if(isset($project->overdue_count) && $project->overdue_count > 0)
                                            <span class="text-sm text-red-500">
                                                 {{ $project->overdue_count }} en retard
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Date de création --}}
                                    <p class="text-xs text-gray-400 mt-2">
                                         Créé le {{ $project->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                                
                               {{-- Boutons d'action --}}
                                <div class="flex space-x-2 ml-4">
                                    {{-- Bouton Modifier --}}
                                    <a href="{{ route('projects.edit', $project) }}" 
                                  
                                         style="background-color: #2563eb; color: white; font-weight: bold; padding: 8px 16px; border-radius: 8px; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1); display: inline-block;"
                                            onmouseover="this.style.backgroundColor='#1d4ed8'"
                                            onmouseout="this.style.backgroundColor='#2563eb'">
                                    Modifier
                                    </a>
                                    
                                    {{-- Bouton Supprimer --}}
                                    <form action="{{ route('projects.destroy', $project) }}" 
                                        method="POST" 
                                        onsubmit="return confirm('Supprimer ce projet ?')"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-600 hover:bg-red-700 text-white font-medium px-3 py-1.5 rounded-lg text-sm transition duration-200 shadow-sm">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg">Aucun projet pour le moment.</p>
                            <a href="{{ route('projects.create') }}" 
                               class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                Créer votre premier projet →
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>