<form method="POST" action="/login">
    @csrf

    <input type="email" name="email" />
    <input type="password" name="password" />
    
    @error('password')
        {{ $message }}<
    @enderror

    <button type="submit">Login</button>

</form>