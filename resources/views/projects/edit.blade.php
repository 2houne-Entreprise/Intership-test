<!DOCTYPE html>
<html>
<head>
    <title>Edit Project</title>
</head>
<body>

<h1>Edit Project</h1>

<form action="{{ route('projects.update', $project) }}" method="POST">
    @csrf
    @method('PUT')

    <input type="text" name="name" value="{{ $project->name }}"><br><br>

    <textarea name="description">{{ $project->description }}</textarea><br><br>

    <button type="submit">Update</button>
</form>

<a href="{{ route('projects.index') }}">Back</a>

</body>
</html>