@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $project->name }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if(session('success'))
                    <x-alert type="success" message="{{ session('success') }}" />
                @endif

                @if(session('error'))
                    <x-alert type="error" message="{{ session('error') }}" />
                @endif

                <h3 class="text-lg font-medium mb-4">Tâches du projet</h3>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Échéance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pièce jointe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($project->tasks as $task)
                        <tr class="{{ $task->deadline && $task->deadline->isPast() && $task->status !== 'done' ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $task->title }}
                                @if($task->deadline && $task->deadline->isPast() && $task->status !== 'done')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                                        En retard
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $task->status_label }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $task->deadline ? $task->deadline->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($task->attachment_path)
                                    <a href="{{ asset('storage/' . $task->attachment_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800">Voir</a>
                                @else
                                    Aucune
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <form action="{{ route('tasks.update-status', $task) }}" method="POST" enctype="multipart/form-data" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded px-2 py-1">
                                        <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>En attente</option>
                                        <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>En cours</option>
                                        <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Terminé</option>
                                    </select>
                                    <input type="file" name="attachment" class="ml-2 text-xs">
                                    <button type="submit" class="ml-2 bg-blue-500 text-white px-2 py-1 rounded text-xs">Mettre à jour</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
