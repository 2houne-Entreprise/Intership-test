<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $task->exists ? 'Modifier la tâche' : 'Ajouter une tâche' }} — {{ $project->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ $task->exists ? route('projects.tasks.update', [$project, $task]) : route('projects.tasks.store', $project) }}" class="p-6 bg-white rounded-lg shadow-sm space-y-6">
                @csrf
                @if ($task->exists)
                    @method('PUT')
                @endif

                <div>
                    <x-input-label for="title" value="Titre" />
                    <x-text-input id="title" name="title" type="text" class="block w-full mt-1" :value="old('title', $task->title)" required maxlength="255" autofocus />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="status" value="Statut" />
                    <select id="status" name="status" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="pending" @selected(old('status', $task->status ?? 'pending') === 'pending')>En attente</option>
                        <option value="in_progress" @selected(old('status', $task->status) === 'in_progress')>En cours</option>
                        <option value="done" @selected(old('status', $task->status) === 'done')>Terminée</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="deadline" value="Échéance (facultative)" />
                    <x-text-input id="deadline" name="deadline" type="date" class="block w-full mt-1" :value="old('deadline', $task->deadline?->format('Y-m-d'))" />
                    <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>Enregistrer</x-primary-button>
                    <a href="{{ route('projects.show', $project) }}" class="underline text-gray-600">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
