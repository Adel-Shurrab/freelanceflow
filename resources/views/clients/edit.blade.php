<x-layouts.app title="Edit Client">
    <h1>Edit Client</h1>

    <p>
        <a href="{{ route('clients.show', $client) }}">Back to Client</a>
        |
        <a href="{{ route('clients.index') }}">Back to Clients</a>
    </p>

    <form method="POST" action="{{ route('clients.update', $client) }}">
        @csrf
        @method('PATCH')

        <div style="margin-bottom: 12px;">
            <label for="name">Name</label><br>
            <input id="name" type="text" name="name" value="{{ old('name', $client->name) }}" required>

            @error('name')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 12px;">
            <label for="email">Email</label><br>
            <input id="email" type="email" name="email" value="{{ old('email', $client->email) }}" required>

            @error('email')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 12px;">
            <label for="phone">Phone</label><br>
            <input id="phone" type="text" name="phone" value="{{ old('phone', $client->phone) }}">

            @error('phone')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 12px;">
            <label for="company">Company</label><br>
            <input id="company" type="text" name="company" value="{{ old('company', $client->company) }}">

            @error('company')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 12px;">
            <label for="notes">Notes</label><br>
            <textarea id="notes" name="notes" rows="5">{{ old('notes', $client->notes) }}</textarea>

            @error('notes')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">Update Client</button>
    </form>
</x-layouts.app>