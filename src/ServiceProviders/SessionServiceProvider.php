<?php

namespace App\ServiceProviders;

use App\Session\Session;
use App\Session\SessionInterface;

class SessionServiceProvider extends ServiceProvider
{

    function register(): void
    {
        $this->app->singleton(SessionInterface::class, Session::class);
    }

    function boot()
    {
        // TODO: Implement boot() method.
    }
}