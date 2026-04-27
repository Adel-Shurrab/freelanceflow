<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

// Route::get('/login', ...)->name('login');
// Route::post('/login', ...);
// Route::get('/register', ...)->name('register');
// Route::post('/register', ...);

/*
|--------------------------------------------------------------------------
| Authenticated App Routes
|--------------------------------------------------------------------------|
*/

Route::middleware([
    'auth',
    // 'verified',
])
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard.index');
        })->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Freelancer Resources
        |--------------------------------------------------------------------------
        | Route::resource('clients', ClientController::class);
        | Route::resource('projects', ProjectController::class);
        | Route::resource('invoices', InvoiceController::class);
        */
    })
;

/*
|--------------------------------------------------------------------------
| Client Portal Routes
|--------------------------------------------------------------------------
*/

Route::prefix('portal')
    ->name('portal.')
    ->middleware([
        'auth',
        //  role:client
    ])
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('client-portal.dashboard');
        })->name('dashboard');

        // Route::get('/projects', [ClientPortalProjectController::class, 'index'])->name('projects.index');
        // Route::get('/invoices', [ClientPortalInvoiceController::class, 'index'])->name('invoices.index');
    })
;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware([
        'auth',
        'log.admin',
        //  role:admin,super_admin
    ])
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Route::resource('users', AdminUserController::class);
        // Route::patch('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
    });
