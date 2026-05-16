@extends('layouts.app')

@section('content')
<h1>{{ $project->name }}</h1>
<p>{{ $project->description }}</p>

<hr>

<h2>Add Task</h2>

@if(session('success'))
    <div style="color:green">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div style="color:red">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('tasks.store', $project) }}">
    @csrf
    <input type="text" name="title" placeholder="Title" value="{{ old('title') }}">
    
    <select name="status">
        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
        <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Done</option>
    </select>

    <input type="date" name="deadline" value="{{ old('deadline') }}">

    <button type="submit">Add Task</button>
</form>

<hr>

<h2>Tasks</h2>

<table border="1" cellpadding="5">
    <tr>
        <th>Title</th>
        <th>Status</th>
        <th>Deadline</th>
    </tr>

   @foreach($project->tasks as $task)
    <tr @if($task->deadline && \Carbon\Carbon::parse($task->deadline)->isPast()) style="background-color:#fdd;" @endif>
        <td>{{ $task->title }}</td>
        <td>
            <form method="POST" action="{{ route('tasks.update', $task) }}">
                @csrf
                @method('PUT')
                <select name="status" onchange="this.form.submit()">
                    <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>En cours</option>
                    <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Terminée</option>
                </select>
            </form>
        </td>
        <td>{{ $task->deadline ?? 'No deadline' }}</td>
    </tr>
@endforeach
</table>

<a href="{{ route('projects.index') }}">⬅ Back to Projects</a>

@endsection