<x-layouts.app title="Register">
    <h1>Create your account</h1>

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <div>
            <label for="name">Name</label><br>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>
            @error('name')
                <p style="color: #dc2626;">{{ $message }}</p>
            @enderror
        </div>

        <br>

        <div>
            <label for="email">Email</label><br>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required>
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

        <div>
            <label for="password_confirmation">Confirm Password</label><br>
            <input id="password_confirmation" name="password_confirmation" type="password" required>
            @error('password_confirmation')
                <p style="color: #dc2626;">{{ $message }}</p>
            @enderror
        </div>

        <br>

        <button type="submit">Register</button>
    </form>

    <p>
        Already have an account?
        <a href="{{ route('login') }}">Login</a>
    </p>
</x-layouts.app>