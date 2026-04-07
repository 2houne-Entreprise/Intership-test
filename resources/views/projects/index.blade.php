<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mes Projets') }}
            </h2>
            <a href="{{ route('projects.create') }}" 
             class="bg-white hover:bg-gray-100 text-blue-600 font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 border border-blue-200">
                    + Nouveau Projet
             </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Message de succès --}}
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    {{-- Liste des projets --}}
                    @forelse($projects as $project)
                        <div class="border-b border-gray-200 py-6 hover:bg-gray-50 transition duration-150">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <a href="{{ route('projects.show', $project) }}" 
                                       class="text-xl font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $project->name }}
                                    </a>
                                    @if($project->description)
                                        <p class="text-gray-600 mt-2">{{ $project->description }}</p>
                                    @endif
                                    <div class="mt-2 text-sm text-gray-500">
                                        Créé le {{ $project->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="flex space-x-3 ml-4">
                                    <a href="{{ route('projects.edit', $project) }}" 
                                       class="text-amber-600 hover:text-amber-800 font-medium">
                                         Modifier
                                    </a>
                                    <form action="{{ route('projects.destroy', $project) }}" 
                                          method="POST" class="inline-block"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ? Toutes les tâches associées seront également supprimées.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 font-medium">
                                             Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg">Aucun projet pour le moment.</p>
                            <a href="{{ route('projects.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                Créer votre premier projet →
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>