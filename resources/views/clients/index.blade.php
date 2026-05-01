<x-layouts.app title="Clients">
    <h1>Clients</h1>

    <p>
        <a href="{{ route('clients.create') }}">Create Client</a>
        |
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </p>

    <form method="GET" action="{{ route('clients.index') }}" style="margin-bottom: 16px;">
        <div style="margin-bottom: 8px;">
            <label for="search">Search</label><br>
            <input id="search" type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                placeholder="Search by name or email">

            @error('search')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 8px;">
            <label for="status">Status</label><br>
            <select id="status" name="status">
                <option value="">All statuses</option>

                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>

            @error('status')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">Apply Filters</button>

        <a href="{{ route('clients.index') }}">Clear</a>
    </form>

    @if ($clients->isEmpty())
        <p>No clients found.</p>
    @else
        <table border="1" cellpadding="8" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($clients as $client)
                    <tr>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->company ?? '-' }}</td>
                        <td>{{ $client->status->label() }}</td>
                        <td>{{ $client->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('clients.show', $client) }}">View</a>
                            |
                            <a href="{{ route('clients.edit', $client) }}">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 16px;">
            {{ $clients->links() }}
        </div>
    @endif
</x-layouts.app>