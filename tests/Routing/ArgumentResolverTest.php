<?php

namespace App\Tests\Routing;

use App\Routing\ArgumentResolver;
use App\Routing\Route;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ArgumentResolverTest extends TestCase
{
  private ArgumentResolver $argumentResolver;

  public function setUp(): void
  {
    $this->argumentResolver = new ArgumentResolver();
  }

  /**
   * @dataProvider urlMatchesRoutesWithParamsProvider
   */
  public function testUrlMatchesRouteWithParams(string $url, string $routeRegex)
  {
    /** @var MockObject|Route */
    $route = $this->createMock(Route::class);

    $route->method('getRegex')
      ->willReturn($routeRegex);

    $this->assertTrue($this->argumentResolver->match($url, $route));
  }

  public function urlMatchesRoutesWithParamsProvider()
  {
    yield "User edit with user ID" => ['/user/edit/156', "/^\/user\/edit\/(?P<id>.+)$/"];
    yield "Blog URL with slug & ID" => ['/blog/fake-slug-title/12304', "/^\/blog\/(?P<slug>.+)\/(?P<id>.+)$/"];
  }

  /**
   * @dataProvider getGetParamsProvider
   */
  public function testGetGetParams(string $url, string $routeRegex, array $expectedParamKeys)
  {
    /** @var MockObject|Route */
    $route = $this->createMock(Route::class);

    $route->method('getRegex')
      ->willReturn($routeRegex);

    $params = $this->argumentResolver->getGetParams($url, $route);
    foreach ($expectedParamKeys as $expected) {
      $this->assertArrayHasKey($expected, $params);
    }
  }

  public function getGetParamsProvider()
  {
    yield "User edit with user ID" => ['/user/edit/156', "/^\/user\/edit\/(?P<id>.+)$/", ['id']];
    yield "Blog URL with slug & ID" => ['/blog/fake-slug-title/12304', "/^\/blog\/(?P<slug>.+)\/(?P<id>.+)$/", ['slug', 'id']];
  }
}
