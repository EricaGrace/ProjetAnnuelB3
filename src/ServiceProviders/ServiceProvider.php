<?php

namespace App\ServiceProviders;

use App\Application;
use Psr\Container\ContainerInterface;

abstract class ServiceProvider
{

    protected ContainerInterface $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    abstract function register(): void;

    //abstract function boot();
}