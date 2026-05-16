<!DOCTYPE html>
<html>
<head>
    <title>Create Project</title>
</head>
<body>

<h1>Create Project</h1>

<form action="{{ route('projects.store') }}" method="POST">
    @csrf

    <input type="text" name="name" placeholder="Project name"><br><br>

    <textarea name="description" placeholder="Description"></textarea><br><br>

    <button type="submit">Save</button>
</form>

<a href="{{ route('projects.index') }}">Back</a>

</body>
</html>