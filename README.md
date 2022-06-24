# Projet Annuel


## Changelog

### Remaniement de l'application

On a séparé le bootstrapping de notre application (= les services qui sont absolument nécessaire au fonctionnement le plus basique de notre application), par des 'Bootstrappers'.

La méthode `getMethodServiceParams()` du routeur a été extraite en une fonction du container `Container::make()` pour resolver nos dépendances AUTOMATIQUEMENT, en injectant toute dépendance requise dans le constructeur de la classe.
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

Service providers : classes USED by our app Bootstrappers : service providers REQUIRED by our app

Séparer les service providers des bootstrappers permet de pouvoir réutiliser la code 'project-agnostic' sur un autre
projet à la manière d'un framework.

### Hydratation des données :

Pour l'hydratation des données, on a créé un Hydrator implémentant HydratorInterface, qui est automatiquement injecté
dans tous les repositories par notre container. Il expose une méthode `hydrate(array $values, object $object)` qui,
appelée dans un repository, retourne l'objet hydraté.

Pour ce faire, il parcoure le tableau de `$values` et pour chaque valeur, trouve sa propriété et devine son setter.  
Avant d'appeler ce setter, il regarde si un attribut "Hydrator" existe sur la propriété.

Enfin, si aucun setter n'est trouvé et que l'attribut Hydrator n'est pas défini, l'hydrateur essaiera en dernier recours
de setter la propriété avec Réflection.

````php
class User
{
    // ...
    
    #[Hydrator(strategy: DateTimeStrategy::class)]
    private DateTime $birthDate;
    
    // ...
}
````

L'attribut Hydrator définit une stratégie. Chaque Stratégie est une classe implémentant
l'interface `App\Database\Hydration\Strategies\StrategyInterface`. Les stratégies exposent donc elles aussi et à coup
sûr, une méthode `hydrate()`.

Si l'attribut Hydrator est trouvé, l'hydrateur appellera la méthode `hydrate()` de la stratégie plutôt que le setter
qu'il a deviné.

L'implémentation suivante permet par exemple de setter la propriété birthdate de l'entité 'User' en retournant un
DateTime.

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

À noter que cette façon d'hydrater fonctionne bien, mais "provoque" le problème n+1 Si l'on récupère 10 Users, on
effectuera la requette principale avec le `findAll()` de l'AbstractRepository, + 10 autres requêtes pour récupérer les
parrains. Soit n+1 requêtes. Sur 10 users, cela reste négligeable, mais l'entité Event possède des références vers
EventCategory, User, et Venue...

### Kernel, Response & PSR-7

Jusqu'à présent, notre application ne disposait que d'un objet Request que l'on pouvait manipuler pour récupérer les
informations de la requête entrante. La requête était passée à notre routeur qui se chargeait d'appeler le controlleur
associé, celui-ci appliquait sa logique, puis faisait un `echo` d'une template twig par exemple. Ce echo envoie
automatiquement les headers et le contenu de la page.

On a décidé de remplacer ce fonctionnement par un objet `Symfony\Component\HttpFoundation\Response` qui détient le
content, les headers, et le response code.
> /!\ Response de Symfony n'est pas compatible avec PSR-7. Il peut l'être à l'aide de [PSR-7 bridge](https://symfony.com/doc/current/components/psr7.html)

Le nouveau fonctionnement est le suivant:

1. La requête est créée puis passée au Kernel
2. Le Kernel crée un objet Response
3. Il appelle le routeur qui peut retourner soit une string, soit un objet Response
4. Le kernel renvoie un objet Response qui est envoyée avec la méthode `send()`

-> Les méthodes du routeur peuvent désormais `return` une template twig, ou une Response.

Par exemple:

````php
// IndexController.php

// exemple avec une template twig
#[Route(path: "/", httpMethod: "GET")]
public function index(Request $request)
{
  return $this->twig->render('index.html.twig', [
      'request' => $request
  ]);
}
  
// exemple avec un RedirectResponse
#[Route(path: "/", httpMethod: "GET")]
public function index()
{
    return new RedirectResponse('/contact');
}
````

### Le `route()` helper

Pour suivre l'exemple de Laravel (symfony aussi j'imagine), on a ajouté à Twig un helper `route()` qui pointe vers la
fonction `getRouteUriFromName` du routeur. On passe aussi notre routeur à Twig (ce qui n'est pas absolument nécessaire,
mais why not).

````php
// ViewServiceProvider.php
$twig->addGlobal('router', $router);
$twig->addFunction(new TwigFunction('route', fn(...$params) => $router->getRouteUriFromName(...$params)));
````

La fonction `getRouteUriFromName()` et donc `route()` dans le contexte de twig prend deux paramètres: le nom de la
route, puis un tableau associatif de valeurs.
`getRouteUriFromName` retrouvera la route, et remplira automatiquement l'uri avec les valeurs passées par exemple :

Ce qui nous permet de faire :

````php
// dans le contexte de php:
$router->route('user_edit', ['id' => 12]) // donnera '/user/edit/1'

// dans le contexte twig:
{{ route('user_edit', {'id': 12}) }} // donnera '/user/edit/1'
````

### Principe de responsabilité unique : refactoring du routeur

Jusqu'à présent, notre routeur ne respectait pas le principe de responsabilité unique. En effet, instanciait le
controller associé et appellait la méthode en lui injectant ses dépendances.

On a alors extrait cette responsabilité en la donnant au Container via une méthode `callClassMethod`. Cela aura permis
de réduire de 11 lignes la méthode `execute()` du routeur, et de supprimer totalement la
méthode `getMethodServiceParams()`. Cela permet en plus de pouvoir réutiliser `callClassMethod()` depuis n'importe où
dans notre application, en restant DRY (= sans répéter la logique d'injection de dépendances).

### Amélioration du container: singletons

Nous avions déjà fait évoluer notre container d'un simple bac dans lequel on met des objets en vrac en un container qui
sait résolver des dépendances et les injecter automatiquement. Aussi bien et fonctionnel que cela puisse paraître, la
façon dont il résolvait les objets posait un sérieux problème. Lorsque le container résolvait une dépendance, il
l'auto-enregistrait dans sa liste de services pour ressortir l'OBJET la prochaine fois que l'on lui demanderait.
Autrement dit: le container ne savait faire que des singletons.

On a donc créé la méthode `singleton()` qui n'est qu'un alias pour la méthode `set()` à laquelle on a rajouté le
paramètre facultatif `bool $singleton = false`.

Si l'on passe `true` à ce paramètre, `set()` enregistrera le service dans un tableau supplémentaire $singleton.

Ce sera ensuite lors de la résolution avec `make()` que le container décidera s'il doit enregistrer l'objet instancié
dans ses services ou non.

On peut désormais `register()` les services providers avec la méthode `singleton()`. Par exemple, le routeur qui a
besoin de rester le même à chaque fois puisqu'il détient les routes et qu'on ne souhaite pas rappeler la fonction
registerRoutes() à chaque fois que l'on résolve le routeur.

````php
class RoutesServiceProvider extends ServiceProvider
{

    function register(): void
    {
        $this->app->singleton(Router::class, Router::class);
    }

    function boot()
    {
        $router = $this->app->make(Router::class);
        $router->registerRoutes();
    }
}
````

Dorénavant, appeler `set()` aura comme comportement par défaut de ne PAS faire de singleton, et les objets seront
instanciés à chaque fois qu'il seront résolvés.

### DI des méthodes boot des Service Providers

Avoir la méthode callClassMethod() dans le container et non plus dans le routeur nous permet également de faire de
l'injection dans les méthodes boot de nos provides. Alors qu'avant il fallait faire :

````php
function boot()
{
    $router = $this->app->make(Router::class);
    $router->registerRoutes();
}
````

On peut maintenant type-hinter nos dépendances dans la méthode boot() plutôt que dans le constructeur, à la manière d'un
controlleur :

````php
function boot(Router $router)
{
    $router->registerRoutes();
}
````

### Rendu des templates : `return404()`, `render()` et `renderIf()`

Que se passe-t-il si dans un controller, un repository ne renvoie aucun résultat? Par exemple, si un slug n'est pas
trouvé ?

````php
$category = $categoryRepository->findBySlug($slug) // returns null
````

On ne voudrait pas que le repository lance une exception, ce n'est ni logique, ni son rôle. On pourrait faire par
exemple :

````php
// EventsController.php

$category = $categoryRepository->findBySlug($slug); // returns null

if ($category) {
    return $this->twig->render('Evenement/EvenementCategorie.html.twig', [
        'category' => $category
    ]);
}
return $this->twig->render('404.html.twig');
````

Mais la syntaxe est extrêmement verbeuse et répéter cette logique dans chaque méthode serait une énorme perte de temps,
en plus de ne pas être DRY. Et c'est sans compter les conséquences qu'auraient le changement de moteur de templating.

Au lieu de ça, on a choisi de créer dans le AbstractController, trois méthodes.

- `return404()`
- `render()`
- `renderIf()`

`return404()` lance une `RouteNotFoundException` lorsqu'elle est invoquée. Celle-ci est catchée par le Kernel qui
décidera de rendre le template de la page 404.

`render()` est un wrapper autour de `twig->render()`. Il facilite le changement de dépendances:  si l'on souhaite
utiliser un autre moteur de templating, on peut facilement adapter la méthode. De plus, les controlleurs appelleront
dorénavant:

````php
$this->render();
````

et non plus :

````php
$this->twig->render();
````

Ce qui enlève les références à twig. C'est plus rapide à écrire, et en cas de remplacement de twig, il n'y aura
absolument rien à changer.

Enfin, `renderIf()` permet de conditionner le rendu. Si les conditions passent, alors elle appellera `render()`. Sinon,
elle appellera `return404()` au premier échec.

Toutes ces méthodes nous permettent de remplacer le code de plus haut, par un code plus efficace, lisible et versatile :

````php
// EventsController.php
$category = $categoryRepository->findBySlug($slug); // returns null

return $this->renderIf('Evenement/EvenementCategorie.html.twig', [
    'category' => $category
], $category); // On peut ajouter autant de conditions que voulu
````

## Pour aller plus loin:

- add dot notation to config class using the php dot notation package
- Passer un callable PHP au routeur plutôt que deux paramètres :
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
- Ajouter des middlewares
- Extraire la logique de validation des Entités hors des Controllers.