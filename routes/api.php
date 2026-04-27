<?php

use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware([
        'force.json',
        'api.version',
    ])
    ->group(function () {
        Route::get('/health', function () {
            return ApiResponse::success(
                data: [
                    'api_version' => 1,
                    'app_version' => config('app.version'),
                ],
                message: 'FreelanceFlow API is running.',
            );
        });
    });