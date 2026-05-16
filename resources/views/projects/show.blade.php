@extends('layouts.app')

@section('content')

<h1>{{ $project->name }}</h1>
<p>{{ $project->description }}</p>

<hr>

<h2>Add Task</h2>

<form method="POST" action="{{ route('tasks.store', $project) }}">
    @csrf

    <input type="text" name="title" placeholder="Title">

    <select name="status">
        <option value="pending">Pending</option>
        <option value="in_progress">In Progress</option>
        <option value="done">Done</option>
    </select>

    <input type="date" name="deadline">

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
        <tr>
            <td>{{ $task->title }}</td>

            <td>
                <form method="POST" action="{{ route('tasks.update', $task) }}">
                    @csrf
                    @method('PUT')

                    <select name="status" onchange="this.form.submit()">
                        <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Done</option>
                    </select>
                </form>
            </td>

            <td>{{ $task->deadline }}</td>
        </tr>
    @endforeach
</table>

@endsection