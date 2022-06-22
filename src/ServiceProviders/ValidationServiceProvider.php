<?php

namespace App\ServiceProviders;

use Respect\Validation\Validator;

class ValidationServiceProvider extends ServiceProvider
{

    function register(): void
    {
        $this->app->set(Validator::class, Validator::class);
    }

    function boot()
    {
        // TODO: Implement boot() method.
    }
}