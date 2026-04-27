<?php

use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function () {
        Route::get('/health', function () {
            return ApiResponse::success(
                data: [
                    'api_version' => 1,
                    'app_version' => config('app.version'),
                ],
                message: 'FreelanceFlow API is running.',
            );
        })->name('health');

        /*
        |--------------------------------------------------------------------------
        | Public API Routes
        |--------------------------------------------------------------------------
        */

        // Route::post('/login', [ApiAuthController::class, 'login'])->name('login');
        // Route::post('/register', [ApiAuthController::class, 'register'])->name('register');
        // Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');

        /*
        |--------------------------------------------------------------------------
        | Protected API Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('auth:sanctum')
            ->group(function () {
                // Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

                // Route::apiResource('clients', ApiClientController::class);
                // Route::apiResource('projects', ApiProjectController::class);
                // Route::apiResource('invoices', ApiInvoiceController::class);
            });
    });