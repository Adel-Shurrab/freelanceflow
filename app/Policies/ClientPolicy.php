<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Freelancer;
    }

    public function view(User $user, Client $client): bool
    {
        return $user->role === UserRole::Freelancer
            && $client->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Freelancer;
    }

    public function update(User $user, Client $client): bool
    {
        return $this->view($user, $client);
    }

    public function delete(User $user, Client $client): bool
    {
        return $this->view($user, $client);
    }

    public function archive(User $user, Client $client): bool
    {
        return $this->view($user, $client);
    }

    public function restore(User $user, Client $client): bool
    {
        return $this->view($user, $client);
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return false;
    }
}
