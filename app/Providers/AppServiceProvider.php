<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;
use App\Policies\ClientPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        Gate::policy(Client::class, ClientPolicy::class);

        Gate::before(function (User $user, string $ability) {
            if ($user->role === UserRole::SuperAdmin) {
                return true;
            }

            return null;
        });
    }
}
