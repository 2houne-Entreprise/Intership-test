@props(['project', 'task' => null])

<form method="POST" action="{{ $task ? route('projects.tasks.update', [$project, $task]) : route('projects.tasks.store', $project) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($task)
        @method('PUT')
    @endif

    <div>
        <x-input-label for="title" value="Titre de la tâche" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $task?->title)" required maxlength="255" autofocus />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="status" value="Statut" />
        <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
            <option value="pending" @selected(old('status', $task?->status ?? 'pending') === 'pending')>En attente</option>
            <option value="in_progress" @selected(old('status', $task?->status) === 'in_progress')>En cours</option>
            <option value="done" @selected(old('status', $task?->status) === 'done')>Terminée</option>
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="deadline" value="Date limite (facultative)" />
        <x-text-input id="deadline" name="deadline" type="date" class="mt-1 block w-full" :value="old('deadline', $task?->deadline?->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="attachment" value="Pièce jointe (PDF, JPG ou PNG, 2 Mo maximum)" />
        <input id="attachment" name="attachment" type="file" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:font-semibold file:text-gray-700 hover:file:bg-gray-200">
        <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
        @if ($task?->attachment_path)
            <a href="{{ route('projects.tasks.attachment', [$project, $task]) }}" class="mt-2 inline-block text-sm font-medium text-emerald-800 hover:text-emerald-950">Ouvrir le fichier actuel</a>
        @endif
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>{{ $task ? 'Enregistrer la tâche' : 'Ajouter la tâche' }}</x-primary-button>
        <a href="{{ route('projects.show', $project) }}" class="tf-btn tf-btn-secondary">Annuler</a>
    </div>
</form>