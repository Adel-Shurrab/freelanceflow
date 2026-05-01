<x-layouts.app title="Login">
    <h1>Login</h1>

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <div>
            <label for="email">Email</label><br>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <p style="color: #dc2626;">{{ $message }}</p>
            @enderror
        </div>

        <br>

        <div>
            <label for="password">Password</label><br>
            <input id="password" name="password" type="password" required>
            @error('password')
                <p style="color: #dc2626;">{{ $message }}</p>
            @enderror
        </div>

        <br>

        <label>
            <input type="checkbox" name="remember" value="1">
            Remember me
        </label>

        <br><br>

        <button type="submit">Login</button>
    </form>

    <p>
        No account?
        <a href="{{ route('register') }}">Register</a>
    </p>
</x-layouts.app>