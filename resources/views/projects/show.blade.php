<!DOCTYPE html>
<html>
<head>
    <title>Project Details</title>
</head>
<body>

<h1>{{ $project->name }}</h1>

<p>{{ $project->description }}</p>

<a href="{{ route('projects.index') }}">Back</a>

</body>
</html>