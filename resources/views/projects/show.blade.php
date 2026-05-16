<!DOCTYPE html>
<html>
<head>
    <title>Project Details</title>
</head>
<body>

<h1>{{ $project->name }}</h1>

<p>{{ $project->description }}</p>

<hr>

{{-- ================= TASK FORM ================= --}}
<h2>Add Task</h2>

<form method="POST" action="{{ route('tasks.store', $project) }}">
    @csrf

    {{-- Title --}}
    <div>
        <label>Title</label>
        <input type="text" name="title" value="{{ old('title') }}">

        @error('title')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>

    {{-- Status --}}
    <div>
        <label>Status</label>
        <select name="status">
            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>
                Pending
            </option>

            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>
                In Progress
            </option>

            <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>
                Done
            </option>
        </select>

        @error('status')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>

    {{-- Deadline --}}
    <div>
        <label>Deadline</label>
        <input type="date" name="deadline" value="{{ old('deadline') }}">

        @error('deadline')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit">Add Task</button>
</form>

<hr>

{{-- ================= TASK LIST ================= --}}
<h2>Tasks</h2>

@if($project->tasks->count() > 0)
    @foreach($project->tasks as $task)
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">

            <strong>{{ $task->title }}</strong>

            <p>Status: {{ $task->status }}</p>

            <p>Deadline: {{ $task->deadline ?? 'No deadline' }}</p>

            {{-- DELETE TASK --}}
            <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                @csrf
                @method('DELETE')

                <button type="submit">Delete</button>
            </form>

        </div>
    @endforeach
@else
    <p>No tasks yet.</p>
@endif

<hr>

<a href="{{ route('projects.index') }}">Back</a>

</body>
</html>