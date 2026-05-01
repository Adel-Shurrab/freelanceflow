<x-layouts.app title="Client Details">
    <h1>{{ $client->name }}</h1>

    <p>
        <a href="{{ route('clients.index') }}">Back to Clients</a>
        |
        <a href="{{ route('clients.edit', $client) }}">Edit Client</a>
    </p>

    <dl>
        <dt>Email</dt>
        <dd>{{ $client->email }}</dd>

        <dt>Phone</dt>
        <dd>{{ $client->phone ?? '-' }}</dd>

        <dt>Company</dt>
        <dd>{{ $client->company ?? '-' }}</dd>

        <dt>Status</dt>
        <dd>{{ $client->status->label() }}</dd>

        <dt>Notes</dt>
        <dd>{{ $client->notes ?? '-' }}</dd>

        <dt>Created At</dt>
        <dd>{{ $client->created_at->format('Y-m-d H:i') }}</dd>

        <dt>Updated At</dt>
        <dd>{{ $client->updated_at->format('Y-m-d H:i') }}</dd>
    </dl>

    <form method="POST" action="{{ route('clients.destroy', $client) }}"
        onsubmit="return confirm('Are you sure you want to delete this client?');">
        @csrf
        @method('DELETE')

        <button type="submit">Delete Client</button>
    </form>
</x-layouts.app>