<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('projects.show', $project) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">{{ $project->name }}</a>
        <h2 class="mt-1 text-xl font-semibold text-gray-900">Ajouter une tâche</h2>
    </x-slot>

    <section class="py-8 sm:py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg sm:p-8">
                <x-task-form :project="$project" />
            </div>
        </div>
    </section>
</x-app-layout>