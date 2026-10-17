@extends('layouts.app')
    @section('header')
        <h2 class="font-semibold text-xl text-gray-800">{{ $task->exists ? 'Modifier la tâche' : 'Ajouter une tâche' }} — {{ $project->name }}</h2>
    @endsection

@section('content')

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" enctype="multipart/form-data" action="{{ $task->exists ? route('projects.tasks.update', [$project, $task]) : route('projects.tasks.store', $project) }}" class="p-6 bg-white rounded-lg shadow-sm space-y-6">
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
                        @foreach (\App\Models\Task::STATUS_LABELS as $status => $label)
                            <option value="{{ $status }}" @selected(old('status', $task->status ?? 'pending') === $status)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="deadline" value="Échéance (facultative)" />
                    <x-text-input id="deadline" name="deadline" type="date" class="block w-full mt-1" :value="old('deadline', $task->deadline?->format('Y-m-d'))" />
                    <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <div>
                        <x-input-label for="attachment" value="Pièce jointe (facultative)" />
                        <input id="attachment" name="attachment" type="file" accept=".pdf,.txt,.jpg,.jpeg,.png" aria-describedby="attachment-help" class="block w-full mt-1">
                        <p id="attachment-help" class="text-sm text-gray-600">PDF, texte, JPG ou PNG — 5 Mo maximum. Un nouveau fichier remplace la pièce jointe actuelle.</p>
                        <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                        @if ($task->attachment_path)
                            <a href="{{ route('projects.tasks.attachment', [$project, $task]) }}" class="underline">Télécharger la pièce jointe actuelle</a>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>Enregistrer</x-primary-button>
                    <a href="{{ route('projects.show', $project) }}" class="underline text-gray-600">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
