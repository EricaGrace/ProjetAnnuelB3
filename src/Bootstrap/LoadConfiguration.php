<?php

namespace App\Bootstrap;

use App\Utils\Config;

class LoadConfiguration extends Bootstrapper
{
    public function bootstrap()
    {
        $this->app->set('config', Config::class);
    }
}