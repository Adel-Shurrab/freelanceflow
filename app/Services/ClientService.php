<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ClientStatus;
use App\Enums\InvoiceStatus;
use App\Enums\ProjectStatus;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientService
{
    public function __construct(
        private readonly PlanLimitService $planLimitService,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(User $freelancer, array $data): Client
    {
        $this->planLimitService->ensureCanActivateClient($freelancer);

        return DB::transaction(function () use ($freelancer, $data): Client {
            return $freelancer->clients()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'company' => $data['company'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => ClientStatus::Active,
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Client $client, array $data): Client
    {
        return DB::transaction(function () use ($client, $data): Client {
            $client->update($data);

            return $client->refresh();
        });
    }

    public function delete(Client $client): void
    {
        DB::transaction(function () use ($client): void {
            if ($client->projects()->exists()) {
                throw ValidationException::withMessages([
                    'client' => 'This client has projects and should be archived instead of deleted.',
                ]);
            }

            $client->delete();
        });
    }

    public function archive(Client $client): Client
    {
        $hasUnpaidInvoices = $this->hasUnpaidInvoices($client);

        if ($hasUnpaidInvoices) {
            throw ValidationException::withMessages([
                'client' => 'This client has unpaid invoices and cannot be archived.',
            ]);
        }

        return DB::transaction(function () use ($client): Client {
            $client->status = ClientStatus::Archived;
            $client->save();

            $client->projects()
                ->where('status', ProjectStatus::Active->value)
                ->update([
                    'status' => ProjectStatus::OnHold,
                ])
            ;

            return $client->refresh();
        });
    }

    public function restore(Client $client): Client
    {
        return DB::transaction(function () use ($client): Client {
            $this->planLimitService->ensureCanActivateClient($client->user);

            $client->update([
                'status' => ClientStatus::Active,
            ]);

            return $client->refresh();
        });
    }

    private function hasUnpaidInvoices(Client $client): bool
    {
        return $client->invoices()
            ->whereIn('invoices.status', [
                InvoiceStatus::Sent->value,
                InvoiceStatus::Overdue->value,
            ])
            ->exists()
        ;
    }
}
