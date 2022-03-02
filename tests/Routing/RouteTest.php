<?php

namespace App\Tests\Routing;

use App\Routing\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
  /**
   * @dataProvider getRegexProvider
   */
  public function testGetRegex(string $path, string $expectedRegex)
  {
    $route = new Route(
      $path,
      "TestController",
      "testMethod"
    );

    $this->assertEquals($expectedRegex, $route->getRegex());
  }

  public function getRegexProvider()
  {
    yield "No GET param" => ["/", "/^\/$/"];
    yield "One GET param" => ["/user/{id}", "/^\/user\/(?P<id>.+)$/"];
    yield "Two GET params" => ["/blog/{slug}/{id}", "/^\/blog\/(?P<slug>.+)\/(?P<id>.+)$/"];
  }
}
