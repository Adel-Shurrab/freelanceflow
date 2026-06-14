<?php

use App\Exceptions\BaseException;
use App\Http\Middleware\ApiVersionMiddleware;
use App\Http\Middleware\CheckPlanLimitMiddleware;
use App\Http\Middleware\ForceJsonMiddleware;
use App\Http\Middleware\LogAdminActionMiddleware;
use App\Providers\AppServiceProvider;
use App\Providers\RepositoryServiceProvider;
use App\Providers\ViewServiceProvider;
use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        AppServiceProvider::class,
        RepositoryServiceProvider::class,
        ViewServiceProvider::class,
        \App\Providers\AuthServiceProvider::class,
        \App\Providers\EventServiceProvider::class,
        \App\Providers\Filament\AdminPanelProvider::class,
        \App\Providers\TelescopeServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            ForceJsonMiddleware::class,
            ApiVersionMiddleware::class,
        ]);

        $middleware->alias([
            'plan.limit' => CheckPlanLimitMiddleware::class,
            'log.admin' => LogAdminActionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(
            function (BaseException $exception, Request $request) {
                if (! $request->expectsJson() && ! $request->is('api/*')) {
                    return null;
                }

                return ApiResponse::error(
                    $exception->getMessage(),
                    $exception->getContext() ?: null,
                    $exception->httpStatusCode(),
                );
            },
        );

        $exceptions->render(
            function (ModelNotFoundException $exception, Request $request) {
                if (! $request->expectsJson() && ! $request->is('api/*')) {
                    return null;
                }

                return ApiResponse::notFound();
            },
        );

        $exceptions->render(
            function (AuthorizationException $exception, Request $request) {
                if (! $request->expectsJson() && ! $request->is('api/*')) {
                    return null;
                }

                return ApiResponse::forbidden($exception->getMessage() ?: 'Forbidden');
            },
        );
    })->create()
;
