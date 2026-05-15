<h1>Register</h1>

<form method="POST" action="/register">

    @csrf

    <div>
        <input
            type="text"
            name="name"
            placeholder="Name"
            required
        >
    </div>

    <br>

    <div>
        <input
            type="email"
            name="email"
            placeholder="Email"
            required
        >
    </div>

    <br>

    <div>
        <input
            type="password"
            name="password"
            placeholder="Password"
            required
        >
    </div>

    <br>

    <div>
        <input
            type="password"
            name="password_confirmation"
            placeholder="Confirm Password"
            required
        >
    </div>

    <br>

    <button type="submit">
        Register
    </button>

</form>