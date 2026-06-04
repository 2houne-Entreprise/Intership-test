<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 leading-tight">
            {{ __('Créer un projet') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-200">
                <div class="mb-6 border-b border-slate-100 pb-4">
                    <h3 class="text-base font-bold text-slate-900">{{ __('Nouveau projet') }}</h3>
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

                <form method="POST" action="{{ route('projects.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-bold text-slate-900 mb-1.5">{{ __('Nom du projet') }}</label>
                        <input type="text" name="name" id="name" required class="mt-1 block w-full rounded border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm py-2 px-3 text-slate-900 font-bold" placeholder="{{ __('Nom du projet') }}">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-bold text-slate-900 mb-1.5">{{ __('Description') }}</label>
                        <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm py-2 px-3 text-slate-900 font-bold" placeholder="{{ __('Description du projet (facultatif)') }}"></textarea>
                    </div>

                    <div class="flex items-center space-x-4 pt-4 border-t border-slate-100">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-slate-300 hover:bg-slate-50 rounded text-xs font-bold text-slate-900 uppercase tracking-widest transition">
                            {{ __('Créer') }}
                        </button>
                        <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 hover:bg-slate-50 rounded text-xs font-bold text-slate-900 uppercase tracking-widest transition">
                            {{ __('Annuler') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
