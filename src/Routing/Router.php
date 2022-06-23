<?php

namespace App\Routing;

use App\Application;
use App\Routing\Attribute\Route as RouteAttribute;
use App\Utils\Filesystem;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionMethod;

class Router
{
    private const CONTROLLERS_NAMESPACE = "App\\Controller\\";
    private const CONTROLLERS_DIR = __DIR__ . "/../Controller";
    /** @var Route[] */
    private array $routes = [];
    private ArgumentResolver $argumentResolver;
    private Application $app;

    public function __construct(
        Application        $app,
        ArgumentResolver   $argumentResolver
    )
    {
        $this->app = $app;
        $this->argumentResolver = $argumentResolver;
    }

    /**
     * Executes a route based on provided URI and HTTP method.
     *
     * @param string $uri
     * @param string $httpMethod
     * @return void
     * @throws RouteNotFoundException
     */
    public function execute(string $uri, string $httpMethod)
    {
        $route = $this->getRoute($uri, $httpMethod);

        if ($route === null) {
            throw new RouteNotFoundException();
        }

        return $this->app->callClassMethod($route->getController(), $route->getMethod(), ...$route->getGetParams());
    }

    /**
     * Get a route. Returns null if not found
     *
     * @param string $uri
     * @param string $httpMethod
     * @return Route|null
     */
    public function getRoute(string $uri, string $httpMethod): ?Route
    {
        foreach ($this->routes as $route) {
            if ($this->argumentResolver->match($uri, $route) && $route->getHttpMethod() === $httpMethod) {
                $params = $this->argumentResolver->getGetParams($uri, $route);

                $route->setGetParams($params);
                return $route;
            }
        }

        return null;
    }

    public function registerRoutes(): void
    {
        $classNames = Filesystem::getClassNames(self::CONTROLLERS_DIR);

        foreach ($classNames as $class) {
            $this->registerClassRoutes($class);
        }
    }

    public function registerClassRoutes(string $className): void
    {
        $fqcn = self::CONTROLLERS_NAMESPACE . $className;
        $reflection = new ReflectionClass($fqcn);

        if ($reflection->isAbstract()) {
            return;
        }

        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $attributes = $method->getAttributes(RouteAttribute::class);

            foreach ($attributes as $attribute) {
                /** @var RouteAttribute */
                $route = $attribute->newInstance();

                $this->addRoute(new Route(
                    $route->getPath(),
                    $fqcn,
                    $method->getName(),
                    $route->getHttpMethod(),
                    $route->getName()
                ));
            }
        }
    }

    /**
     * Add a route into the router internal array
     *
     * @param string $name
     * @param string $url
     * @param string $httpMethod
     * @param string $controller Controller class
     * @param string $method
     * @return self
     */
    public function addRoute(Route $route): self
    {
        $this->routes[] = $route;

        return $this;
    }

    public function getRouteUriFromName(string $name, array $values = []): ?string
    {
        $route = $this->getRouteByName($name);

        return !$route ? null : $this->argumentResolver->setGetParams($route, $values);

    }

    public function getRouteByName(string $name): ?Route
    {
        $route = array_filter($this->routes, fn(Route $route) => $route->getName() === $name);

        return !empty($route) ? array_values($route)[0] : null;
    }
}
