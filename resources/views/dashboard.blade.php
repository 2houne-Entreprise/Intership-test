<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h1>Dashboard</h1>

<p>Select an operation:</p>

<hr>

<!-- CREATE -->
<a href="{{ route('projects.create') }}">
    ➕ Create Project
</a>

<br><br>

<!-- READ (LIST ALL) -->
<a href="{{ route('projects.index') }}">
    📂 View All Projects
</a>

<br><br>

<hr>

<h3>Extra (optional)</h3>

<p>You can edit/delete inside the project list page.</p>

<hr>

<!-- LOGOUT -->
<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit">Logout</button>
</form>

</body>
</html>