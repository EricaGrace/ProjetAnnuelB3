# Projet Annuel

## ToDo:

- replace all keys (=aliases) from within the container
- add dot notation to config class using the php dot notation package
- Route class should use a callable instead of 2 arguments

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

### Hydratation des données : 
Pour l'hydratation des données, on a créé un Hydrator implémentant HydratorInterface, qui est automatiquement injecté dans tous les repositories par notre container.
Il expose une méthode `hydrate(array $values, object $object)` qui, appelée dans un repository, retourne l'objet hydraté.

Pour ce faire, il parcoure le tableau de `$values` et pour chaque valeur, trouve sa propriété et devine son setter.  
Avant d'appeler ce setter, il regarde si un attribut "Hydrator" existe sur la propriété.

Enfin, si aucun setter n'est trouvé et que l'attribut Hydrator n'est pas défini, l'hydrateur essaiera en dernier recours de setter la propriété avec Réflection.

````php
class User
{
    // ...
    
    #[Hydrator(strategy: DateTimeStrategy::class)]
    private DateTime $birthDate;
    
    // ...
}
````

L'attribut Hydrator définit une stratégie. Chaque Stratégie est une classe implémentant l'interface `App\Database\Hydration\Strategies\StrategyInterface`.
Les stratégies exposent donc elles aussi et à coup sûr, une méthode `hydrate()`.

Si l'attribut Hydrator est trouvé, l'hydrateur appellera la méthode `hydrate()` de la stratégie plutôt que le setter qu'il a deviné.

L'implémentation suivante permet par exemple de setter la propriété birthdate de l'entité 'User' en retournant un DateTime.
````php
class DateTimeStrategy implements StrategyInterface
{
    public function hydrate($value)
    {
        try {
            return new DateTime($value);
        } catch (Exception $e) {
            return new DateTime();
        }
    }
````
---
### Les stratégies sont automatiquement créés par le container, ce qui nous offre une grande flexibilité :

> **Par exemple:**   
> Pour instaurer un système de parrainage, on peut ajouter à la table 'user' de la bdd une colonne 'parrain' qui détient une clé étrangère vers l'id d'un autre utilisateur.
> 
> On rajoute la propriété à notre Entité:
> ````php
> class User
> {
>     // ...
>     
>     #[Hydrator(strategy: UserStrategy::class)]
>     private ?User $parrain;
>     
>     // ...
> ````
> 
> Et on créé la classe UserStrategy. Comme UserStrategy est instancié par le IoC container, ses dépendances sont automatiquement injectées.  
> On peut donc type-hinter le `UserRepository`, puis l'utiliser dans notre fonction hydrate pour retourner le parrain de l'utilisateur :
> 
> ````php
> use App\Repository\UserRepository;
> 
> class UserStrategy implements StrategyInterface
> {
> 
>     private UserRepository $repository;
> 
>     public function __construct(UserRepository $repository)
>     {
>         $this->repository = $repository;
>     }
> 
>     public function hydrate($id)
>     {
>         return $id ? $this->repository->find($id) : null;
>     }
> }
> ````
> 
> Lorsque l'on `dump()` un utilisateur ayant un parrain on obtient : 
> 
> ![dump](docs/hydration-user.png)