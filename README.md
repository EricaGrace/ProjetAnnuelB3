# Projet Annuel

## Changelog

- Created a Config class for retrieving configuration values
- Allowed container services to be referenced by multiple aliases by passing an array to `set()` :

````php
$app->set(KernelInterface::class, $kernel)
// OR : 
$app->set([KernelInterface::class, 'kernel'], $kernel)
````

- Separated the bootstrapping of our applications into Bootstrappers extending Bootstrapper
- Extracted the `getMethodServiceParams()` method of the router into a `Container::make()` method that can be used
  anywere
- The make method is a way of resolving dependencies by injecting them from our IoC container into the class constructor

> Attention, cela a rendu notre container non conforme à psr-11. La méthode `get` devrait prendre l'implémentation de `make` pour être conforme. Ce n'est pas encore le cas pour des raisons de compatibilité. Du refactoring reste à faire.

Les service providers nous permettent, au lieu de faire ça:

````php
// index.php
$router = new Router($container, new ArgumentResolver());
$router->registerRoutes();
````

De créer un service provider puis de pouvoir récupérer le routeur depuis

````php
// RoutesServiceProvider.php
class RoutesServiceProvider extends ServiceProvider
{
    function register(): void
    {
        $this->app->set([Router::class, 'router'], Router::class);
    }
    
    function boot()
    {
        $router = $app->make('router');
        $router->registerRoutes();
    }
}
````

On a pu déplacer le chargement des variables d'environnement dans un bootstrapper.

Service providers : classes USED by our app Bootstrappers : service providers REQUIRED by our app

Séparer les service providers des bootstrappers permet de pouvoir réutiliser la code 'project-agnostic' sur un autre
projet à la manière d'un framework.

Pour améliorer le container:
Actuellement, le container stocke "en vrac" les services. Il instancie les classes en résolvant automatiquement et
récursivement les dépendances si le service est sous forme de FQCN. Il retourne simplement l'objet si il est stocké tel
quel. On pourrait gérer de la manière dont sont résolu nos services, notamment avec des singletons (ex pour le routeur),
ou avec une closure. Cela donnerait plus de flexibilité qu'actuellement.

ToDo:

- Require php 8.1 in composer
- replace all keys (=aliases) from within the container
- add dot notation to config class using the php dot notation package
- Route class should use a callable instead of 2 arguments

````php
// Instead of this : 
$router->addRoute(new Route('/test-index', IndexController::class, "indextest"));

// Callable would allow us to register routes on the fly
$router->addRoute(new Route('/test-index', function() {
    return $this->view('index.html.twig')
} ));

// While somewhat preserving the original syntax:  
$router->addRoute(new Route('/test-index', [IndexController::class, "indextest"]));

````