<?php

namespace App\ServiceProviders;

use App\Config\PdoConnection;
use App\Repository\UserRepository;
use PDO;

class DBConnectionServiceProvider extends ServiceProvider
{

    function register(): void
    {
        $pdoConnection = new PdoConnection();
        $pdoConnection->init();

        $this->app->singleton(PDO::class, $pdoConnection->getPdoConnection());
    }

    function boot()
    {
        // TODO: Implement boot() method.
    }
}