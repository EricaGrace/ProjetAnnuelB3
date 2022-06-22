<?php

namespace App\Bootstrap;

use App\Utils\Config;
use Exception;

class RegisterServiceProviders extends Bootstrapper
{
    public function bootstrap()
    {
        $services = [];
        foreach ($this->getServiceProvidersFromConfig() as $service) {
            $service = $this->app->make($service);
            $service->register();

            $services[] = $service;
        }

        $this->bootServices($services);
    }

    private function getServiceProvidersFromConfig()
    {
        $config = $this->app->make(Config::class);

        try {
            return $config->get('application')['service-providers'];
        } catch (Exception) {
            return [];
        }
    }

    private function bootServices($services)
    {
        foreach ($services as $service) {
            if (method_exists($service, 'boot')) {
                $service->boot();
            }
        }
    }
}