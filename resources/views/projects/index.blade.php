@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Mes Projets
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium">Liste des projets</h3>
                    <a href="{{ route('projects.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Nouveau projet
                    </a>
                </div>

                @if($projects->isEmpty())
                    <p class="text-gray-500">Aucun projet trouvé.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($projects as $project)
                        <div class="bg-gray-50 p-6 rounded-lg shadow">
                            <h4 class="text-xl font-semibold mb-2">
                                <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $project->name }}
                                </a>
                            </h4>
                            <p class="text-gray-600 mb-4">{{ $project->description ?? 'Aucune description' }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">
                                    {{ $project->tasks->count() }} tâche{{ $project->tasks->count() > 1 ? 's' : '' }}
                                </span>
                                <div class="space-x-2">
                                    <a href="{{ route('projects.edit', $project) }}" class="text-blue-600 hover:text-blue-800 text-sm">Modifier</a>
                                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')">Supprimer</button>
                                    </form>
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
@endsection
