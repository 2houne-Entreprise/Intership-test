@props(['project' => null])

<form method="POST" action="{{ $project ? route('projects.update', $project) : route('projects.store') }}" class="space-y-6">
    @csrf
    @if ($project)
        @method('PUT')
    @endif

    <div>
        <x-input-label for="name" value="Nom du projet" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $project?->name)" required maxlength="255" autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" value="Description" />
        <textarea id="description" name="description" rows="5" maxlength="5000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">{{ old('description', $project?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>{{ $project ? 'Enregistrer les modifications' : 'Créer le projet' }}</x-primary-button>
        <a href="{{ $project ? route('projects.show', $project) : route('projects.index') }}" class="tf-btn tf-btn-secondary">Annuler</a>
    </div>
</form>