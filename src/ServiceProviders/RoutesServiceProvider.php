<?php

namespace App\ServiceProviders;

use App\Routing\ArgumentResolver;
use App\Routing\Router;

class RoutesServiceProvider extends ServiceProvider
{

    function register(): void
    {
        $this->app->set(Router::class, Router::class);
    }

    function boot()
    {
        $router = $this->app->make(Router::class);
        $router->registerRoutes();
    }
}