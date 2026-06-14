<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\LogoutController;
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\ClientController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'create'])
        ->name('register')
    ;

    Route::post('register', [RegisterController::class, 'store'])
        ->name('register.store')
    ;

    Route::get('login', [LoginController::class, 'create'])
        ->name('login')
    ;

    Route::post('login', [LoginController::class, 'store'])
        ->name('login.store')
    ;
});

Route::middleware('auth')->group(function () {

    Route::patch('clients/{client}/archive', [ClientController::class, 'archive'])
        ->name('clients.archive')
    ;

    Route::patch('clients/{client}/restore', [ClientController::class, 'restore'])
        ->name('clients.restore')
    ;

    Route::resource('clients', ClientController::class);

    Route::get('dashboard', DashboardController::class)
        ->name('dashboard')
    ;

    Route::post('logout', LogoutController::class)
        ->name('logout')
    ;
});
