<?php

namespace App\Bootstrap;

use App\Application;
use App\Routing\RouteNotFoundException;
use App\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Kernel implements KernelInterface
{

    protected Application $app;
    protected array $bootstrappers = [
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

    public function handle(Request $request): Response
    {
        return $this->sendRequestThroughRouter($request, new Response());
    }

    private function sendRequestThroughRouter(Request $request, Response $response): Response
    {
        $router = $this->app->make(Router::class);
        $twig = $this->app->make(Environment::class);

        $uri = $request->server->get('REQUEST_URI');
        $method = $request->server->get('REQUEST_METHOD');

        try {
            $content = $router->execute($uri, $method);

            // if the router returns a Response object, we return it to be sent.
            // OTHERWISE, it must be a string, so we set the content of our response.
            return ($content instanceof Response) ? $content : $response->setContent($content);
        } catch (RouteNotFoundException $e) {
            $response->setStatusCode(404);
            return $response->setContent(
                $twig->render('404.html.twig', ['title' => $e->getMessage()])
            );
        }
    }
}