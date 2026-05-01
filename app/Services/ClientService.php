<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ClientStatus;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClientService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(User $freelancer, array $data): Client
    {
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
            $client->delete();
        });
    }
}
