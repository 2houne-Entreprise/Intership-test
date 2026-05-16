<!DOCTYPE html>
<html>
<head>
    <title>Projects</title>
</head>
<body>

<h1>My Projects</h1>

<a href="{{ route('projects.create') }}">Create Project</a>

<hr>

@foreach ($projects as $project)
    <div>
        <h3>{{ $project->name }}</h3>
        <p>{{ $project->description }}</p>

        <a href="{{ route('projects.show', $project) }}">View</a>
        <a href="{{ route('projects.edit', $project) }}">Edit</a>

        <form action="{{ route('projects.destroy', $project) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit">Delete</button>
        </form>

        <hr>
    </div>
@endforeach

</body>
</html>