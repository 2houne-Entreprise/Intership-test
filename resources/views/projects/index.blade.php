<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Mes projets</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <p role="status" class="p-4 bg-white rounded-lg text-green-700">{{ session('status') }}</p>
            @endif

            <a href="{{ route('projects.create') }}" class="inline-block underline text-gray-800">Créer un projet</a>

            @forelse ($projects as $project)
                <article class="p-6 bg-white rounded-lg shadow-sm space-y-4">
                    <h3 class="font-semibold text-lg text-gray-900"><a href="{{ route('projects.show', $project) }}" class="underline">{{ $project->name }}</a></h3>
                    <p class="text-gray-600 whitespace-pre-line break-words">{{ $project->description }}</p>
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="{{ route('projects.edit', $project) }}" class="underline text-gray-800">Modifier</a>
                        <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Supprimer ce projet ?')">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>Supprimer</x-danger-button>
                        </form>
                    </div>
                </article>
            @empty
                <p class="p-6 bg-white rounded-lg text-gray-600">Vous n’avez pas encore de projet.</p>
            @endforelse

            {{ $projects->links() }}
        </div>
    </div>
</x-app-layout>
