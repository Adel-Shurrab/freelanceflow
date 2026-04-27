<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware([
        'auth',
        'log.admin.action',
    ])
    ->group(function () {
        // admin routes
    })
;
