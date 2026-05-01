<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class PlanLimitService
{
    public function ensureCanCreateClient(User $user): void
    {
        $limit = $user->plan_type->maxClients();

        if ($limit === PHP_INT_MAX) {
            return;
        }

        $current = $user->clients()
            ->active()
            ->count()
        ;

        if ($current < $limit) {
            return;
        }

        throw ValidationException::withMessages([
            'plan' => "You have reached the {$user->plan_type->label()} limit of {$limit} active clients. Please upgrade your plan to continue.",
        ]);
    }
}
