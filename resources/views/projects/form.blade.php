<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $project->exists ? 'Modifier le projet' : 'Créer un projet' }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ $project->exists ? route('projects.update', $project) : route('projects.store') }}" class="p-6 bg-white rounded-lg shadow-sm space-y-6">
                @csrf
                @if ($project->exists)
                    @method('PUT')
                @endif

                <div>
                    <x-input-label for="name" value="Nom du projet" />
                    <x-text-input id="name" name="name" type="text" class="block w-full mt-1" :value="old('name', $project->name)" required maxlength="255" autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" value="Description (facultative)" />
                    <textarea id="description" name="description" rows="5" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $project->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>Enregistrer</x-primary-button>
                    <a href="{{ route('projects.index') }}" class="underline text-gray-600">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
