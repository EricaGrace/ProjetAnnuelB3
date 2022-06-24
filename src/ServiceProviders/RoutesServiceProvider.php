<?php

namespace App\ServiceProviders;

use App\Routing\ArgumentResolver;
use App\Routing\Router;

class RoutesServiceProvider extends ServiceProvider
{

    function register(): void
    {
        $this->app->singleton(Router::class, Router::class);
    }

    function boot(Router $router)
    {
        $router->registerRoutes();
    }
}