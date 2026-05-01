<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly ClientService $clientService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Client::class);

        $clients = Client::query()
            ->forUser($request->user())
            ->latest()
            ->paginate(10)
        ;

        return view('clients.index', [
            'clients' => $clients,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Client::class);

        return view('clients.create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $client = $this->clientService->create(
            $request->user(),
            $request->validated(),
        );

        return redirect()
            ->route('clients.show', $client)
            ->with('status', 'Client created successfully.')
        ;
    }

    public function show(Request $request, int $client): View
    {
        $client = $this->findClientForCurrentUserOrFail($request->user(), $client);

        $this->authorize('view', $client);

        return view('clients.show', [
            'client' => $client,
        ]);
    }

    public function edit(Request $request, int $client): View
    {
        $client = $this->findClientForCurrentUserOrFail($request->user(), $client);

        $this->authorize('update', $client);

        return view('clients.edit', [
            'client' => $client,
        ]);
    }

    public function update(UpdateClientRequest $request, int $client): RedirectResponse
    {
        $client = $this->findClientForCurrentUserOrFail($request->user(), $client);

        $this->authorize('update', $client);

        $this->clientService->update($client, $request->validated());

        return redirect()
            ->route('clients.show', $client)
            ->with('status', 'Client updated successfully.')
        ;
    }

    public function destroy(Request $request, int $client): RedirectResponse
    {
        $client = $this->findClientForCurrentUserOrFail($request->user(), $client);

        $this->authorize('delete', $client);

        $this->clientService->delete($client);

        return redirect()
            ->route('clients.index')
            ->with('status', 'Client deleted successfully.')
        ;
    }

    private function findClientForCurrentUserOrFail(User $user, int $clientId): Client
    {
        $query = Client::query();

        if ($user->role !== UserRole::SuperAdmin) {
            $query->forUser($user);
        }

        return $query->findOrFail($clientId);
    }
}
