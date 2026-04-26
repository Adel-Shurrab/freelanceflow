<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// use App\Contracts\Repositories\ClientRepositoryInterface;
// use App\Contracts\Repositories\ProjectRepositoryInterface;
// use App\Repositories\ClientRepository;
// use App\Repositories\ProjectRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        foreach ($this->bindings() as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }

    private function bindings(): array
    {
        return [
            // ClientRepositoryInterface::class => ClientRepository::class,
            // ProjectRepositoryInterface::class => ProjectRepository::class,
        ];
    }
}