<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\RepositoryServiceProvider;
use App\Providers\TelescopeServiceProvider;
use App\Providers\ViewServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    EventServiceProvider::class,
    RepositoryServiceProvider::class,
    TelescopeServiceProvider::class,
    ViewServiceProvider::class,
];
