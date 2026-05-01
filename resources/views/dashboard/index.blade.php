<x-layouts.app title="Dashboard">
    <h1>Dashboard</h1>

    <p>Welcome, {{ auth()->user()->name }}.</p>

    <ul>
        <li>Email: {{ auth()->user()->email }}</li>
        <li>Role: {{ auth()->user()->role->label() }}</li>
        <li>Plan: {{ auth()->user()->plan_type->label() }}</li>
    </ul>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</x-layouts.app>