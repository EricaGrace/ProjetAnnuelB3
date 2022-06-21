<?php

use App\ServiceProviders\DBConnectionServiceProvider;
use App\ServiceProviders\HydrationServiceProvider;
use App\ServiceProviders\RoutesServiceProvider;
use App\ServiceProviders\ViewServiceProvider;

return [
    'service-providers' => [
        RoutesServiceProvider::class,
        ViewServiceProvider::class,
        DBConnectionServiceProvider::class,
        HydrationServiceProvider::class,
    ]
];