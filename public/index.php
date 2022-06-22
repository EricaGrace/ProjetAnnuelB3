<?php

require_once __DIR__ . "/../vendor/autoload.php";

if (
    php_sapi_name() !== 'cli' &&
    preg_match('/\.(?:png|jpg|jpeg|gif|ico)$/', $_SERVER['REQUEST_URI'])
) {
    return false;
}

use App\Application;
use App\Routing\RouteNotFoundException;
use App\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

$app = new Application(dirname(__DIR__));
$kernel = $app->bindKernel();

$app->bootstrap($kernel->getBootstrapers());

$request = Request::createFromGlobals();
$app->set([Request::class, 'request'], $request);

$response = $kernel->handle($request);
$response->send();
