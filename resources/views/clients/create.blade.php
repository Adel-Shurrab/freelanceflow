<x-layouts.app title="Create Client">
    <h1>Create Client</h1>

    <p>
        <a href="{{ route('clients.index') }}">Back to Clients</a>
    </p>

    <form method="POST" action="{{ route('clients.store') }}">
        @csrf

        <div style="margin-bottom: 12px;">
            <label for="name">Name</label><br>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>

            @error('name')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 12px;">
            <label for="email">Email</label><br>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>

            @error('email')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 12px;">
            <label for="phone">Phone</label><br>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}">

            @error('phone')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 12px;">
            <label for="company">Company</label><br>
            <input id="company" type="text" name="company" value="{{ old('company') }}">

            @error('company')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 12px;">
            <label for="notes">Notes</label><br>
            <textarea id="notes" name="notes" rows="5">{{ old('notes') }}</textarea>

            @error('notes')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">Create Client</button>
    </form>
</x-layouts.app>