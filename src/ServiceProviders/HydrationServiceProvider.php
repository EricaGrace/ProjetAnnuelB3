<?php

namespace App\ServiceProviders;

use App\Database\Hydration\Hydrator;
use App\Database\Hydration\HydratorInterface;

class HydrationServiceProvider extends ServiceProvider
{

    function register(): void
    {
        $this->app->singleton([HydratorInterface::class, Hydrator::class], Hydrator::class);
    }

    function boot()
    {
        // TODO: Implement boot() method.
    }
}