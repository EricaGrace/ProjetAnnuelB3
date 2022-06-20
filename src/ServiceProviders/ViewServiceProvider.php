<?php

namespace App\ServiceProviders;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ViewServiceProvider extends ServiceProvider
{

    function register(): void
    {
        // Registers the twig filesystem loader
        $this->app->set(
            FilesystemLoader::class,
            $loader = new FilesystemLoader(__DIR__ . '/../../templates')
        );

        $this->app->set(Environment::class, new Environment($loader, [
            'debug' => $_ENV['APP_ENV'] === 'dev',
            'cache' => __DIR__ . '/../../var/cache',
        ]));
    }

    function boot()
    {
        // TODO: Implement boot() method.
    }
}