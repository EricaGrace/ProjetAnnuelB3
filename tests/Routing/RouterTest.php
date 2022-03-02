<?php

namespace App\Tests\Routing;

use App\Routing\ArgumentResolver;
use App\Routing\Route;
use App\Routing\Router;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class RouterTest extends TestCase
{
  private Router $router;

  public function setUp(): void
  {
    $mockContainer = $this->createMock(ContainerInterface::class);
    $this->router = new Router($mockContainer, new ArgumentResolver());
  }

  public function testNoRouteFound()
  {
    $route = $this->router->getRoute('/user/edit/123', 'GET');
    $this->assertNull($route);
  }

  public function testGetRoute()
  {
    /** @var MockObject|Route */
    $route = $this->createMock(Route::class);

    $route->method('getRegex')
      ->willReturn("/^\/user\/edit\/(?P<id>.+)$/");
    $route->method('getHttpMethod')
      ->willReturn('GET');

    $this->router->addRoute($route);

    $route = $this->router->getRoute('/user/edit/123', 'GET');
    $this->assertInstanceOf(Route::class, $route);
  }
}
