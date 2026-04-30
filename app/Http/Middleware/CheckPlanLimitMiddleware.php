<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\ClientStatus;
use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Models\User;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimitMiddleware
{
    public function handle(Request $request, Closure $next, string $resource): Response
    {
        $user = $request->user();

        if (! ($user instanceof User)) {
            abort(401);
        }

        if (in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true)) {
            return $next($request);
        }

        $planType = $user->plan_type;

        $limit = match ($resource) {
            'clients' => $planType->maxClients(),
            'projects' => $planType->maxProjects(),
            default => PHP_INT_MAX,
        };

        if ($limit === PHP_INT_MAX) {
            return $next($request);
        }

        $current = match ($resource) {
            'clients' => $this->activeClientCount($user),
            'projects' => $this->activeProjectCount($user),
            default => 0,
        };

        if ($current >= $limit) {
            return ApiResponse::forbidden(
                "You have reached the {$planType->label()} limit of {$limit} active {$resource}. Please upgrade your plan to continue.",
            );
        }

        return $next($request);
    }

    private function activeClientCount(User $user): int
    {
        return $user->clients()
            ->where('status', '!=', ClientStatus::Archived->value, 'and')
            ->count('*')
        ;
    }

    private function activeProjectCount(User $user): int
    {
        return $user->projects()
            ->whereIn('status', [
                ProjectStatus::Active->value,
                ProjectStatus::OnHold->value,
            ], 'and', false)
            ->count('*')
        ;
    }
}
