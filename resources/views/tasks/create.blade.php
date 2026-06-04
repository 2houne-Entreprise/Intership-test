<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 leading-tight">
            {{ __('Créer une tâche') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-200">
                <div class="mb-6 border-b border-slate-100 pb-4">
                    <h3 class="text-base font-bold text-slate-900">
                        {{ __('Nouvelle tâche pour') }} <span class="text-blue-950 font-extrabold">{{ $project->name }}</span>
                    </h3>
                </div>

                @if ($errors->any())
                    <x-alert type="error" class="mb-6">
                        <ul class="list-disc list-inside text-sm text-slate-900">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif

                <form method="POST" action="{{ route('tasks.store', $project) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-bold text-slate-900 mb-1.5">
                            {{ __('Titre de la tâche') }}
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            class="mt-1 block w-full rounded border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm py-2 px-3 text-slate-900 font-bold"
                            placeholder="{{ __('Titre') }}"
                            required
                        >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="status" class="block text-sm font-bold text-slate-900 mb-1.5">
                                {{ __('Statut') }}
                            </label>
                            <select
                                name="status"
                                id="status"
                                class="mt-1 block w-full rounded border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm py-2 px-3 text-slate-900 font-bold bg-white cursor-pointer"
                                required
                            >
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                    {{ __('En attente') }}
                                </option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>
                                    {{ __('En cours') }}
                                </option>
                                <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>
                                    {{ __('Terminé') }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="deadline" class="block text-sm font-bold text-slate-900 mb-1.5">
                                {{ __('Date limite') }}
                            </label>
                            <input
                                type="date"
                                id="deadline"
                                name="deadline"
                                value="{{ old('deadline') }}"
                                class="mt-1 block w-full rounded border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm py-2 px-3 text-slate-900 font-bold"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="attachment" class="block text-sm font-bold text-slate-900 mb-1.5">
                            {{ __('Pièce jointe (Optionnel)') }}
                        </label>
                        <input
                            type="file"
                            id="attachment"
                            name="attachment"
                            class="mt-1 block w-full text-sm text-slate-900 font-bold border border-slate-300 rounded shadow-sm focus:outline-none py-1.5 px-3 bg-white"
                        >
                    </div>

                    <div class="flex items-center space-x-4 pt-4 border-t border-slate-100">
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-slate-300 hover:bg-slate-50 rounded text-xs font-bold text-slate-900 uppercase tracking-widest transition"
                        >
                            {{ __('Créer') }}
                        </button>
                        <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center px-4 py-2 border border-slate-300 hover:bg-slate-50 rounded text-xs font-bold text-slate-900 uppercase tracking-widest transition">
                            {{ __('Annuler') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>