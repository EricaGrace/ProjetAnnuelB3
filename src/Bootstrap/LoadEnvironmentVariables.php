<?php

namespace App\Bootstrap;

use App\Application;
use App\Utils\Config;
use Symfony\Component\Dotenv\Dotenv;

class LoadEnvironmentVariables extends Bootstrapper
{

    public function bootstrap()
    {
        $dotenv = new Dotenv();
        $dotenv->loadEnv($this->app->basePath . '/.env');

        $this->app->singleton('env', $dotenv);
    }
}