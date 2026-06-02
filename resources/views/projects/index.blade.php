<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Projets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Liste des projets') }}</h3>
                    <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Créer un projet') }}
                    </a>
                </div>

                @if($projects->isEmpty())
                    <p class="text-gray-500 text-center py-4">{{ __('Aucun projet trouvé. Commencez par en créer un !') }}</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($projects as $project)
                            <div class="p-6 bg-gray-50 rounded-lg border border-gray-200 flex flex-col justify-between shadow-sm">
                                <div>
                                    <h4 class="text-xl font-bold text-gray-800 mb-2">{{ $project->name }}</h4>
                                    <p class="text-gray-600 mb-4">{{ $project->description ?? __('Aucune description fournie.') }}</p>
                                </div>
                                <div class="flex space-x-3 items-center mt-4">
                                    <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        {{ __('Modifier') }}
                                    </a>
                                    <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Voulez-vous vraiment supprimer ce projet ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
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