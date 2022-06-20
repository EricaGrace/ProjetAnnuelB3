<?php

namespace App\Bootstrap;

use App\Application;

class Kernel implements KernelInterface
{

    protected Application $app;
    protected array $bootstrappers = [
        \App\Bootstrap\LoadConfiguration::class,
        \App\Bootstrap\LoadEnvironmentVariables::class,
        \App\Bootstrap\RegisterServiceProviders::class
    ];

    protected array $middleware = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    function getBootstrapers(): array
    {
        return $this->bootstrappers;
    }
}