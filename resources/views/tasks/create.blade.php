<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer une tâche') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 text-red-600">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('tasks.store', $project) }}">
                    @csrf

                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700">
                            Titre
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            Statut
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        >
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>
                                In Progress
                            </option>

                            <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>
                                Done
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="deadline" class="block text-sm font-medium text-gray-700">
                            Date limite
                        </label>

                        <input
                            type="date"
                            id="deadline"
                            name="deadline"
                            value="{{ old('deadline') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <button
                        type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md"
                    >
                        Créer la tâche
                    </button>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>