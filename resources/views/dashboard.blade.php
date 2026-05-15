<h1>Dashboard</h1>

<p>You are logged in.</p>

<form method="POST" action="{{ route('logout') }}">
    @csrf

    <button type="submit">
        Logout
    </button>
</form>