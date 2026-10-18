<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">Modifier le projet</h2>
    </x-slot>

    <section class="py-8 sm:py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg sm:p-8">
                <x-project-form :project="$project" />
            </div>
        </div>
    </section>
</x-app-layout>