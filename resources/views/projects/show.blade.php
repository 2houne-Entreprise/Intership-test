<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $project->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <p role="status" class="p-4 bg-white rounded-lg text-green-700">{{ session('status') }}</p>
            @endif

            <p class="text-gray-600 whitespace-pre-line break-words">{{ $project->description }}</p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('projects.index') }}" class="underline text-gray-800">Mes projets</a>
                <a href="{{ route('projects.tasks.create', $project) }}" class="underline text-gray-800">Ajouter une tâche</a>
            </div>

            <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
                <table class="w-full text-left">
                    <caption class="p-4 font-semibold text-left">Tâches du projet</caption>
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th scope="col" class="p-4">Titre</th>
                            <th scope="col" class="p-4">Statut</th>
                            <th scope="col" class="p-4">Échéance</th>
                            <th scope="col" class="p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            <tr class="border-b border-gray-200">
                                <td class="p-4 break-words">{{ $task->title }}</td>
                                <td class="p-4">{{ $task->status }}</td>
                                <td class="p-4">{{ $task->deadline?->format('d/m/Y') ?? '—' }}</td>
                                <td class="p-4">
                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="underline">Modifier</a>
                                        <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $task]) }}" onsubmit="return confirm('Supprimer cette tâche ?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button>Supprimer</x-danger-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-gray-600">Aucune tâche pour ce projet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $tasks->links() }}
        </div>
    </div>
</x-app-layout>
