<?php

namespace App\Bootstrap;

use App\Application;

abstract class Bootstrapper
{

    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    abstract public function bootstrap();
}