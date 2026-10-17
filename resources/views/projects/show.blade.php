@extends('layouts.app')
    @section('header')
        <h2 class="font-semibold text-xl text-gray-800">{{ $project->name }}</h2>
    @endsection

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <p class="text-gray-600 whitespace-pre-line break-words">{{ $project->description }}</p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('projects.index') }}" class="underline text-gray-800">Mes projets</a>
                <a href="{{ route('projects.tasks.create', $project) }}" class="underline text-gray-800">Ajouter une tâche</a>
            </div>

            <form method="GET" action="{{ route('projects.show', $project) }}" class="flex flex-wrap items-center gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="overdue" value="1" @checked($overdueOnly) class="rounded border-gray-300 text-indigo-600">
                    Tâches en retard uniquement
                </label>
                <x-primary-button>Filtrer</x-primary-button>
                @if ($overdueOnly)
                    <a href="{{ route('projects.show', $project) }}" class="underline">Toutes les tâches</a>
                @endif
            </form>

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
                                <td class="p-4">
                                    <p class="mb-2">{{ $task->status_label }}</p>
                                    <form method="POST" action="{{ route('projects.tasks.status', [$project, $task]) }}" class="flex flex-wrap items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <label for="status-{{ $task->id }}" class="sr-only">Statut de {{ $task->title }}</label>
                                        <select id="status-{{ $task->id }}" name="status" required class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @foreach (\App\Models\Task::STATUS_LABELS as $status => $label)
                                                <option value="{{ $status }}" @selected($task->status === $status)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <x-primary-button>Mettre à jour</x-primary-button>
                                    </form>
                                </td>
                                <td class="p-4">{{ $task->deadline?->format('d/m/Y') ?? '—' }}</td>
                                <td class="p-4">
                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="underline">Modifier</a>
                                        @if ($task->attachment_path)
                                            <a href="{{ route('projects.tasks.attachment', [$project, $task]) }}" class="underline">Pièce jointe</a>
                                        @endif
                                        <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $task]) }}" onsubmit="return confirm('Supprimer cette tâche ?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button>Supprimer</x-danger-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-gray-600">{{ $overdueOnly ? 'Aucune tâche en retard.' : 'Aucune tâche pour ce projet.' }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $tasks->links() }}
        </div>
    </div>
@endsection
