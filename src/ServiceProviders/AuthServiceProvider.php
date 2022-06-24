<?php

namespace App\ServiceProviders;


use App\Authenticator;

class AuthServiceProvider extends ServiceProvider
{

    function register(): void
    {
        $this->app->singleton(Authenticator::class, Authenticator::class);
    }
}