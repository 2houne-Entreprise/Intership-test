<!DOCTYPE html>
<html>
<head>
    <title>My App</title>
</head>
<body>

{{-- NAVBAR --}}
<div style="padding:10px; background:#eee;">
    <a href="{{ route('dashboard') }}">Dashboard</a> |
    <a href="{{ route('projects.index') }}">Projects</a>
</div>

<hr>

{{-- SUCCESS MESSAGE --}}
@if(session('success'))
    <div style="color:green;">
        {{ session('success') }}
    </div>
@endif

{{-- ERROR MESSAGE --}}
@if($errors->any())
    <div style="color:red;">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<hr>
<x-alert />
{{-- PAGE CONTENT --}}
@yield('content')

</body>
</html>