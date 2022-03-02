<?php

// Inclut l'autoloader généré par Composer
require_once __DIR__ . "/../vendor/autoload.php";

if (
  php_sapi_name() !== 'cli' &&
  preg_match('/\.(?:png|jpg|jpeg|gif|ico)$/', $_SERVER['REQUEST_URI'])
) {
  return false;
}

use App\Config\PdoConnection;
use App\Config\TwigEnvironment;
use App\DependencyInjection\Container;
use App\Repository\UserRepository;
use App\Routing\ArgumentResolver;
use App\Routing\RouteNotFoundException;
use App\Routing\Router;
use App\Session\Session;
use App\Session\SessionInterface;
use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;

// Env vars - Possibilité d'utiliser le pattern Adapter
// Pour pouvoir varier les dépendances qu'on utilise
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/../.env');

// PDO
$pdoConnection = new PdoConnection();
$pdoConnection->init(); // Connexion à la BDD
$userRepository = new UserRepository($pdoConnection->getPdoConnection());

// Twig - Vue
$twigEnvironment = new TwigEnvironment();
$twig = $twigEnvironment->init();

// Service Container
$container = new Container();
$container->set(Environment::class, $twig);
$container->set(SessionInterface::class, new Session());
$container->set(UserRepository::class, $userRepository);

// Routage
$router = new Router($container, new ArgumentResolver());
$router->registerRoutes();

if (php_sapi_name() === 'cli') {
  return;
}

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

try {
  $router->execute($requestUri, $requestMethod);
} catch (RouteNotFoundException $e) {
  http_response_code(404);
  echo $twig->render('404.html.twig', ['title' => $e->getMessage()]);
}
