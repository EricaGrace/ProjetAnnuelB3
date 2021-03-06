<?php

namespace App\ServiceProviders;

use App\Auth\Authenticator;
use App\Controller\GlobalController;
use App\Routing\Router;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class ViewServiceProvider extends ServiceProvider
{

    function register(): void
    {
        // Registers the twig filesystem loader
        $this->app->singleton(
            FilesystemLoader::class,
            $loader = new FilesystemLoader(__DIR__ . '/../../templates')
        );

        $this->app->singleton(Environment::class, new Environment($loader, [
            'debug' => $_ENV['APP_ENV'] === 'dev',
            'cache' => __DIR__ . '/../../var/cache',
        ]));
    }

    function boot(Environment $twig, Router $router, Authenticator $authenticator)
    {

        $twig->addGlobal('router', $router);
        $twig->addGlobal('auth', $authenticator);
        $twig->addFunction(new TwigFunction('route', fn(...$params) => $router->route(...$params)));
        $twig->addFunction(new TwigFunction('dump', 'dump'));

        $method = $this->app->callClassMethod(GlobalController::class, 'getGlobalData');
        $twig->addGlobal('global', $method);
    }
}