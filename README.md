# Sciences-U - B3 IW - PHP7-8 MVC from scratch

- [Introduction](#introduction)
- [Démarrage](#démarrage-du-projet-avec-composer)
  - [Mise à jour de Composer](#mise-à-jour-de-composer)
  - [Initialisation du projet](#initialisation-du-projet)
  - [Autoloading PSR-4](#déclaration-de-lautoloading-psr-4)
  - [Point d'entrée de l'application](#définition-dun-point-dentrée-pour-notre-application)
- [Lancement du projet en ligne de commande](#premier-lancement-du-projet-en-ligne-de-commande)
- [Un point sur MVC](#point-théorique-rapide-sur-le-modèle-mvc)
  - [Modèle](#modèle)
  - [Vue](#vue)
  - [Contrôleur](#contrôleur)
- [Le modèle](#le-modèle)
  - [Création de la base de données](#création-de-la-base-de-données)
  - [Installation de Doctrine](#installation-de-doctrine)
  - [Configuration de Doctrine](#configuration-de-doctrine)
  - [Création d'une entité](#création-dune-première-entité)
  - [Insertion d'un enregistrement](#insertion-dun-enregistrement-en-base-de-données)
  - [Le problème des assets](#le-problème-des-assets-fichiers-statiques)
- [Un point sur les dépendances](#un-point-sur-les-dépendances)
  - [Récuperation du projet](#récupération-du-projet)
  - [Mise à jour des dépendances](#mise-à-jour-des-dépendances)
- [Configuration](#configuration)
  - [Les fichiers .env](#les-fichiers-env)
- [Les contrôleurs et le routage des requêtes](#les-contrôleurs-et-le-routage-des-requêtes)
  - [Contrôleurs](#contrôleurs)
  - [Routeur](#routeur)
    - [Un point rapide sur la gestion des erreurs et l'écriture des méthodes](#un-point-rapide-sur-la-gestion-des-erreurs-et-lécriture-des-méthodes)
- [La vue](#la-vue)
- [Retour sur le routeur : l'injection de dépendances](#retour-sur-le-routeur--linjection-de-dépendances)
  - [Identifier les dépendances avec l'API Reflection](#identifier-les-dépendances-avec-lapi-reflection)
  - [Fournir des dépendances à l'aide d'un container de services](#fournir-des-dépendances-à-laide-dun-container-de-services)
- [Ecrire des tests unitaires](#ecrire-des-tests-unitaires)
  - [Principaux avantages](#principaux-avantages)
  - [PHPUnit](#phpunit)
  - [Ecriture de tests pour notre container de services](#ecriture-de-tests-pour-notre-container-de-services)
  - [Lancer la suite de tests](#lancer-la-suite-de-tests)
  - [Générer un rapport de couverture de code](#générer-un-rapport-de-couverture-de-code)
- [Retour sur le routeur (BIS) - Les attributs PHP8](#retour-sur-le-routeur-bis---les-attributs-php8)
  - [Les attributs, c'est quoi ?](#les-attributs-cest-quoi-)
  - [Créer un attribut](#créer-un-attribut)
  - [Utiliser l'attribut dans nos contrôleurs](#utiliser-lattribut-dans-nos-contrôleurs)
  - [Lire les attributs d'un élément](#lire-les-attributs-dun-élément)

## Introduction

Ce module vise à créer une application PHP adoptant une architecture MVC.

Il existe aujourd'hui des solutions telles que Symfony ou Laravel, pour ne citer que les plus populaires, adoptant déjà ce modèle.

Ainsi, dans ce module, nous allons nous attarder sur les outils et mécanismes de PHP permettant, à partir d'un projet vide, de construire cette architecture. Le fait de ne pas s'appuyer sur la structure initiale d'un framework devrait permettre de comprendre et démystifier bon nombre de procédés utilisés par ces frameworks.

Enfin, il existe probablement des milliers de façons d'implémenter un MVC. Nous viserons ici une approche "full objet", en tentant, dans le temps qui nous est imparti, d'introduire et garder en tête des notions d'architecture logicielle pour justifier les différents choix effectués.

## Démarrage du projet avec Composer

On va créer un dossier vierge et l'ouvrir avec VSCode. Dans un terminal positionné à la racine, on initialise un dépôt Git local avec `git init`.

Composer est l'outil qui va nous permettre, dans notre projet, de gérer l'auto-chargement des classes (autoloading) ainsi que les dépendances de notre projet : les librairies externes que nous installerons et utiliserons.

### Mise à jour de Composer

`composer self-update`

### Initialisation du projet

```bash
composer init
```

#### Informations du projet

Renseigner un nom et une description. Pour le nom, tout en minuscules, sans espaces ni accents, avec uniquement des tirets (`-`) pour séparer les mots.

Le nom de votre projet doit être séparé en 2 parties : `vendor/package`.

La partie `vendor` correspond, en quelque sorte, à la personne ou bien la compagnie qui a réalisé le projet/package.

La partie package donne un nom concret à votre package/projet.

Dans mon cas, `ld-web/mvc`, par exemple.

> Ce type de fonctionnement peut se retrouver dans d'autres gestionnaires de packages, comme `npm` pour NodeJS par exemple. On peut trouver par exemple le package `@angular/cli`. Ici, le "vendor" est précédé d'un `@`

Composer va automatiquement créer un fichier `.gitignore` dans lequel il ajoutera le dossier `vendor`. En effet, ce dossier est créé automatiquement par Composer et contient les fichiers d'auto-chargement de classes ainsi que les dépendances. Nous n'avons donc pas besoin de le pousser vers le dépôt distant. N'importe quel développeur souhaitant récupérer ce projet peut clôner ce dépôt et effectuer un `composer install`, le dossier `vendor` sera recréé automatiquement.

> Quand Composer va vous demander si vous voulez ajouter des dépendances de manière interactive, répondez non. De même pour les dépendances de développement. Pour le moment nous n'avons aucune dépendance à ajouter, et ensuite, leur système interactif est un peu bizarre...

Finalement, Composer va créer un fichier `composer.json` décrivant les propriétés de notre projet.

### Déclaration de l'autoloading PSR-4

Afin d'organiser la structure de notre projet, nous allons déclarer, dans notre fichier `composer.json`, la méthode d'auto-chargement de nos classes que nous souhaitons qu'il applique.

> L'auto-chargement (ou `autoloading`), en PHP, intervient quand on souhaite utiliser une classe. PHP va chercher de quel(s) moyen(s) il dispose pour trouver le fichier de définition de cette classe. PSR-4 est une recommandation définissant une manière particulière d'aller chercher une classe. Plus d'infos et des exemples [ici](https://www.php-fig.org/psr/psr-4/)

Ainsi, nous allons renseigner la propriété `autoloading` de notre objet de configuration :

```javascript
{
  //...
  "autoload": {
    "psr-4": {
      "App\\": "src/"
    }
  }
  // ...
}
```

Nous indiquons ici à Composer que le préfixe d'espace de nom `App` correspond au dossier `src`, à la racine de notre projet. Nous allons donc créer ce dossier, et c'est dans celui-ci que se trouveront les différentes classes de notre application.

Enfin, ces classes seront organisées selon la recommandation PSR-4.

Par exemple , si je veux charger la classe ayant le FQCN `App\Controller\IndexController` :

- `App\` correspond au dossier `src/`
- On prend toutes les parties du FQCN (Fully Qualified Class Name) **sauf la dernière qui correspond au nom de la classe**, pour construire le chemin où aller chercher le fichier de la classe `IndexController`
- On en déduit donc que le fichier `IndexController.php` se trouvera dans `src/Controller/`
- Nous avons localisé le fichier de définition de la classe grâce à PSR-4

> La méthode d'autoloading PSR-4 est très largement utilisée dans l'écosystème PHP. Par exemple, dans [Symfony](https://github.com/symfony/symfony/blob/5.4/composer.json#L164) ou encore [Laravel](https://github.com/laravel/laravel/blob/8.x/composer.json#L23)

Pour finir, nous allons générer une première version du dossier `vendor` en demandant à Composer de générer les fichiers d'autoloading :

```bash
composer dump-autoload
```

### Définition d'un point d'entrée pour notre application

Traditionnellement, sur un site PHP, on va créer un fichier de script par page (par exemple `index.php`, `product.php`, ...).

Cette structure peut vite devenir redondante, surtout à mesure que le projet prend du volume.

L'idée que nous allons implémenter dans notre projet est de **bootstraper** notre application : définir un point d'entrée unique, qui réceptionnera les requêtes.

Ensuite, que ce soit via le serveur interne de PHP en ligne de commande, ou bien via un serveur web comme Apache ou NGINX, on va désigner ce fichier comme point d'entrée en routant toutes les requêtes vers lui.

Nous allons définir ce fichier dans le dossier `public` et l'appeler tout simplement `index.php`.

> Cette méthode est également adoptée dans les projets Symfony, mais aussi dans la [structure de Laravel](https://github.com/laravel/laravel/blob/8.x/public/index.php)

A présent, ce fichier nous permettra de centraliser l'initialisation des différentes parties de notre application, puis de router la requête selon les besoins.

Nous intégrons en priorité l'inclusion de l'autoloader Composer :

> Fichier : `public/index.php`

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
```

Ce fichier est le **point d'entrée** de l'autoloading PSR-4 généré par Composer. Il est **indispensable** si vous souhaitez que vos classes ainsi que celles de vos dépendances soient chargées correctement.

> On retrouve également cette inclusion dans [Laravel](https://github.com/laravel/laravel/blob/8.x/public/index.php#L34)

## Premier lancement du projet en ligne de commande

Nous avons maintenant un squelette applicatif qui utilise Composer pour démarrer notre projet MVC.

Nous pouvons définir une commande pour le lancer :

```bash
# -t pour définir le répertoire racine de l'application
# Le nom du fichier en dernier pour définir le point d'entrée unique
php -S localhost:8000 -t public/ public/index.php
```

Si on exécute cette commande puis qu'on se rend sur `localhost:8000`, on a normalement une page blanche : c'est normal, dans `public/index.php`, on ne fait qu'inclure l'autoloader Composer, et rien d'autre.

L'essentiel est de s'assurer qu'on n'a pas d'erreur.

Enfin, pour éviter d'avoir à utiliser cette commande à chaque fois, on peut définir un **script Composer** qui l'exécutera pour nous :

> Fichier : `composer.json`

```javascript
{
  //...
  "scripts": {
    "start": "php -S localhost:8000 -t public/ public/index.php"
  }
  //...
}
```

On pourra ensuite facilement lancer le serveur depuis un terminal avec `composer start`.

**Note** : Il faut également ajouter la désactivation du timeout Composer dans le fichier, sinon par défaut la commande va s'interrompre au bout de 5 minutes :

```javascript
{
  //...
  "config": {
    "process-timeout": 0
  }
  //...
}
```

## Point théorique rapide sur le modèle MVC

L'architecture MVC (Modèle - Vue - Contrôleur) constitue une évolution des architectures classiques, dans la mesure où elle apporte une **séparation de responsabilités**, pour les répartir dans différentes **couches** :

### Modèle

Le modèle va être la couche de données. C'est au niveau du modèle que nous **définirons** des classes PHP, que nous appellerons des **entités**. Ces entités seront automatiquement transformées en **tables** dans la base de données. Nous pourrons ensuite utiliser et manipuler des instances de ces classes pour effectuer des opérations dans la base de données.

### Vue

La vue va être chargée **d'afficher les données**. Cette couche regroupera l'ensemble des templates nécessaires à un affichage cohérent de l'application.

### Contrôleur

Les différents **contrôleurs** que nous créerons dans notre application auront pour simple but de **coordonner** le modèle et la vue. C'est à ce niveau que se trouveront les principales briques **logiques** de l'application. Le rôle du contrôleur est d'agir en tant que **glue** entre le modèle et la vue.

## Le modèle

Afin d'éviter d'avoir à écrire une énorme quantité de classes gérant la génération de requêtes SQL via des méthodes diverses pour communiquer avec une base de données, nous pouvons ajouter et utiliser la première **dépendance** de notre projet : l'[ORM Doctrine](https://www.doctrine-project.org/index.html).

> Un ORM (Object Relational Mapper) permet simplement, depuis notre application, de communiquer avec une base de données en utilisant une syntaxe objet

### Création de la base de données

Rendez-vous dans PhpMyAdmin et créez une nouvelle base de données, `php_mvc`.

Dans cette application, nous ne disposons malheureusement pas des commandes fournies par des frameworks comme Symfony ou Laravel, nous permettant de créer automatiquement la base de données. Nous la créons donc manuellement, au préalable.

### Installation de Doctrine

Une page est disponible sur leur documentation pour son [installation et sa configuration](https://www.doctrine-project.org/projects/doctrine-orm/en/2.8/reference/configuration.html) sans framework.

Le package qui nous intéresse est `doctrine/orm` (on retrouve ici la partie `vendor/package`).

Avec Composer, en ligne de commande, on peut ajouter cette dépendance : `composer require doctrine/orm`.

Automatiquement, Composer ajoute notre dépendance dans le fichier `composer.json`.

Egalement, il a créé automatiquement un fichier `composer.lock` contenant **les versions précises des packages installés**. En effet, Doctrine ORM déclare lui-même des dépendances. Composer parcourt et installe donc en cascade les différentes dépendances.

Dans notre fichier `composer.json` on ne voit donc que Doctrine, mais dans le fichier `composer.lock` apparaissent tous les packages installés.

> Le fichier `composer.lock` est versionné. Cela permet à quelqu'un souhaitant récupérer ce projet d'installer précisément les mêmes versions que celles que nous avons, avec un simple `composer install`

Concernant le format des versions lui-même, il faut savoir que Composer utilise le versioning sémantique ([SemVer](https://semver.org/lang/fr/)).

Outils utiles :

- [SemVer Cheatsheet](https://devhints.io/semver)
- [Online SemVer Checker](https://jubianchi.github.io/semver-check/#/)

### Configuration de Doctrine

Nous utiliserons le point d'entrée de notre application pour charger et configurer Doctrine.

Doctrine fonctionne avec des **entités**, classes PHP transformées en tables de notre base de données.

Nous allons donc lui fournir le chemin vers le dossier dans lequel se trouveront nos entités : `src/Entity` (nos classes d'entités auront donc le namespace `App\Entity`).

Nous activons ensuite le mode développement, puis définissons les coordonnées de la base de données.

> Dans une prochaine étape, nous déporterons les identifiants de connexion à la base dans des fichiers séparés, non versionnés

Finalement, nous récupérons un objet de configuration, puis créons un gestionnaire d'entités (`EntityManager`) à l'aide des coordonnées de connexion et de l'objet de configuration.

> C'est cet `EntityManager` qui nous permettra d'échanger avec notre base de données

Enfin, selon les préconisations de la documentation, pour pouvoir utiliser des commandes de la console et créer notre schéma, le mettre à jour, etc..., nous créons un fichier `cli-config.php` à la racine du projet.

Une fois Doctrine configuré, depuis notre terminal, nous pouvons exécuter la commande suivante pour consulter les commandes Doctrine disponibles : `php vendor/bin/doctrine`.

![Doctrine commands](docs/doctrine_commands.png "Doctrine commands")

### Création d'une première entité

Nous allons créer une entité `User`. Cette entité sera donc une classe PHP utilisant les **annotations Doctrine**, pour permettre à Doctrine d'analyser le format de l'entité, et pouvoir impacter la base de données automatiquement en conséquence.

Dans le dossier `src/Entity`, créer un fichier `User.php`.

Les différentes annotations utilisées (`Entity`, `Table`, `Column`, ...) définissent donc les différentes propriétés de l'entité ([référence de toutes les annotations](https://www.doctrine-project.org/projects/doctrine-orm/en/2.8/reference/annotations-reference.html#index)).

Pour valider le schéma créé, exécuter :

```bash
php vendor/bin/doctrine orm:validate-schema
```

Normalement, Doctrine va pouvoir indiquer que le format d'entité est correct, mais que la base de données n'est pas synchronisée avec notre codebase !

Nous allons donc créer le schéma de la base de données automatiquement, avec la commande suivante :

```bash
php vendor/bin/doctrine orm:schema-tool:create
```

Si on retourne dans PhpMyAdmin, on remarque que la table a été créée.

### Insertion d'un enregistrement en base de données

Nous avons à présent configuré Doctrine et créé notre base de données et notre schéma.

Nous allons tenter de créer un enregistrement `User` dans la table associée, depuis notre application.

Pour ce faire, nous allons implémenter 3 étapes :

- Création d'une instance d'objet `User` et affectation de ses champs
- Persistence de l'objet auprès du gestionnaire d'entités
- Déclenchement de l'insertion de l'objet ou des objets persistés avec `flush` sur le gestionnaire d'entités

```php
$user = new User();

$user->setName("Bob")
  ->setFirstName("John")
  ->setUsername("Bobby")
  ->setPassword("randompass")
  ->setEmail("bob@bob.com")
  ->setBirthDate(new DateTime('1981-02-16'));

// Persist permet uniquement de dire au gestionnaire d'entités de gérer l'entité passée en paramètre
// Persist ne déclenche pas automatiquement une insertion
$entityManager->persist($user);
// Pour déclencher l'insertion, on doit appeler la méthode "flush" sur le gestionnaire d'entités
$entityManager->flush();
```

### Le problème des assets (fichiers statiques)

Lorsqu'on consulte `localhost:8000`, le navigateur envoie en réalité 2 requêtes :

- Une pour notre page, vers `/`
- Une autre pour récupérer le favicon `/favicon.ico`

Etant donné que nous avons configuré notre serveur pour qu'il redirige tout vers `public/index.php`, alors notre requête est exécutée 2 fois, et il en résulte que deux enregistrements sont persistés en base de données.

Nous devons donc filtrer les requêtes entrantes, pour pouvoir servir les éléments statiques directement, sans mettre en oeuvre la logique de notre application.

Pour commencer, nous pouvons indiquer dans notre point d'entrée la chose suivante : si on vient bien d'une page web, et que l'URI demandée termine par une extension de fichier image, alors on retourne `false`. Cela permet d'envoyer directement la ressource demandée, ou bien une 404 si elle n'est pas trouvée.

## Un point sur les dépendances

### Récupération du projet

Lorsqu'on clône un projet existant, on n'a pas toutes les dépendances installées par défaut. On ne dispose que des sources de l'application.

On a également deux fichiers : `composer.json`, là où on déclare nos dépendances, et `composer.lock`, qui vient **verrouiller** l'état de toutes nos dépendances.

Lors de la récupération du projet, suite à un `git clone` par exemple, on exécutera simplement `composer install` pour installer l'ensemble des dépendances dans le dossier `vendor/`.

> C'est pour ça que notre dossier `vendor/` se trouve dans le fichier `.gitignore`. On n'a pas besoin de versionner les dépendances du projet, puisque n'importe qui peut les installer avec `composer install` quand il le récupère sur sa machine

### Mise à jour des dépendances

Lorsqu'on souhaite mettre à jour les dépendances du projet, suite à un correctif par exemple, qui donnerait une nouvelle version "de patch", on exécutera la commande `composer update`.

Si une nouvelle version satisfait l'intervalle de versions acceptables qu'on a déclaré dans notre fichier `composer.json`, alors une mise à jour sera effectuée.

Par exemple, suite à notre `composer update`, le package `doctrine/orm` est passé de la version `2.10.1` à la version `2.10.2`.

Le fichier `composer.lock` est alors mis à jour en conséquence, pour retenir que la version de Doctrine ORM utilisée dans notre application est bien la `2.10.2`.

## Configuration

Nous avons un autre problème à régler : la configuration pour accéder à la base de données se trouve dans le fichier `public/index.php`, et est versionnée. Elle apparaît donc dans ce dépôt Github, en clair.

Ensuite, si quelqu'un d'autre clône ce dépôt, alors soit on sera obligé de s'adapter aux paramètres déclarés dans le fichier, soit on devra changer les paramètres. Mais si on les change, alors du point de vue de Git, il y aura un changement de fichier à commiter.

Nous avons donc besoin d'externaliser la configuration de notre application, et de pouvoir ensuite y faire référence depuis notre application.

Par ailleurs, pour allier flexibilité et sécurité, on ne verra donc plus les paramètres en clair dans le code versionné.

### Les fichiers .env

Les fichiers .env permettent de stocker des paires de clés/valeurs.

Ensuite, on peut utiliser un package comme `symfony/dotenv` pour lire le contenu du fichier et le mapper automatiquement dans le tableau superglobal `$_ENV` de PHP.

On installe donc le composant DotEnv de Symfony : `composer require symfony/dotenv`.

Ensuite, on peut donc déclarer des valeurs par défaut pour nos variables, dans un fichier `.env`.

Les valeurs effectivement utilisées en local, sur notre machine, peuvent quant à elle être déclarée dans un fichier `.env.local`, non versionné sur Git.

> Le fichier `.env` contient donc des valeurs par défaut et sera versionné. Le fichier `.env.local`, non versionné, viendra, pour chaque environnement différent, écraser les valeurs par défaut, pour avoir la configuration adaptée à chaque machine (ou environnement). Du point de vue de l'application, on se contente donc de faire référence à la configuration, sans utiliser de valeurs explicites

Par ailleurs, le composer DotEnv de Symfony introduit également une variable `APP_ENV`, positionnée par défaut à la valeur `dev`. Cette variable peut permettre de configurer les packages selon l'environnement dans lequel on se trouve.

## Les contrôleurs et le routage des requêtes

Pour le moment, notre page d'accueil crée un utilisateur et l'enregistre en BDD. Tout ça se déroule dans le fichier `public/index.php`.

Mais nous préférerions pouvoir définir plusieurs endroits correspondant aux différentes "pages" de notre application.

Par ailleurs, on aimerait pouvoir les définir en étant adapté à un format d'URL comme `/user/profile`, ou encore `/admin/product/edit/5`.

Pour réceptionner les requêtes et les traiter, on va créer une couche de **contrôleurs**.

Ensuite, pour pouvoir trouver le bon contrôleur à exécuter lors de la réception d'une requête, nous aurons besoin d'un **routeur**.

### Contrôleurs

Comme indiqué précédemment, un contrôleur n'agit qu'en tant que **glue** entre le modèle et la vue.

On peut donc déplacer le bout de code qui crée un utilisateur dans une classe `App\Controller\IndexController`

### Routeur

Dans l'index, il est donc maintenant question d'enregistrer des routes, puis de dispatcher une requête entrante auprès du routeur, afin qu'il puisse router cette requête vers le bon contrôleur, en fonction de ses routes enregistrées.

La classe `Router` est donc définie dans un premier temps avec les méthodes suivantes :

- `addRoute` pour ajouter une route
- `execute` pour router une requête vers la bonne route
- `getRoute` pour vérifier qu'une route correspondant à une URL et une méthode HTTP existe ou non

Dans le fichier `public/index.php`, on peut donc ajouter notre route pour la page d'accueil :

```php
$router->addRoute(
  'home',
  '/',
  'GET',
  IndexController::class,
  'index'
);
```

On peut ajouter/déclarer autant de routes qu'on souhaite dans notre application, puis appeler la méthode `execute` avec l'URL demandée par un client :

```php
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

$router->execute($requestUri, $requestMethod);
```

#### Un point rapide sur la gestion des erreurs et l'écriture des méthodes

Du point de vue de notre `Router`, nous devons stocker des routes, puis être capable d'en retrouver une si besoin et de l'exécuter.

Nous avons donc défini 3 méthodes permettant d'implémenter ces différentes fonctionnalités.

La signature de la méthode `getRoute` nous indique qu'elle peut retourner un `array` ou bien `null`.

Lorsqu'on appelle `execute` sur notre routeur, nous allons donc gérer le cas dans lequel aucune route n'est trouvée (valeur `null`).

Ceci pourrait être fait de diverses manières. Nous avons opté pour une levée d'exception en cas de route non trouvée.

Ainsi, nous pouvons signaler à tout code appelant notre routeur qu'une route n'a pas été trouvée, et ainsi déléguer à ce code appelant la **responsabilité** de l'action à effectuer.

Ce qu'il faut retenir ici, c'est que ce n'est probablement pas le rôle du routeur de décider quoi faire en cas de page non trouvée, mais davantage au code qui appelle le routeur de se débrouiller avec ça.

Ainsi, dans notre cas, il peut être plus judicieux de gérer l'erreur avec un bloc `try...catch` au niveau de notre fichier `index.php` :

```php
try {
  $router->execute($requestUri, $requestMethod);
} catch (RouteNotFoundException $e) {
  http_response_code(404);
  echo "Page non trouvée";
}
```

> La définition d'une classe d'exception personnalisée, ici `RouteNotFoundException`, nous permet d'une part une gestion plus fine des exceptions éventuellement levées par notre routeur, mais également d'écrire un code plus clair : nous pouvons **lire** beaucoup plus facilement que dans notre fichier `index.php`, en cas de route non trouvée, on envoie un code 404 et un texte "Page non trouvée"

## La vue

Nous avons monté la couche de **Modèle** et la couche de **Contrôleurs** dans notre application.

Il nous manque encore de quoi afficher les données correctement. Pour monter cette dernière couche, nous allons utiliser un moteur de template, [Twig](https://twig.symfony.com/).

La documentation est assez claire pour l'installation. Ajout de la dépendance via Composer, puis adaptation du code dans `public/index.php` afin de désigner le dossier racine des templates.

Nous ajouterons tout de même la recompilation du cache à chaque rafraîchissement, uniquement en mode `dev` : `'debug' => ($_ENV['APP_ENV'] === 'dev')`. On utilise la variable d'environnement `APP_ENV` comme indiqué dans la partie `Configuration`, pour avoir une configuration dynamique, en fonction de l'environnement.

Cette dépendance envers la vue peut revenir très souvent dans les contrôleurs. En fait, nous allons considérer qu'il nous la faut à chaque fois, donc dans tous les contrôleurs.

Pour éviter de devoir la déclarer dans chaque méthode de contrôleur, nous allons remonter l'instance de Twig dans une classe parente, `AbstractController`, en tant qu'attribut protégé.

Ainsi, toutes les méthodes des contrôleurs, qui seront des enfants de la classe `AbstractController`, pourront accéder à l'instance de Twig nous permettant de générer les vues.

Enfin, nous enregistrerons, **pour le moment**, l'instance de Twig comme attribut du routeur, donc de la classe `Router`, pour pouvoir la désigner et l'injecter de manière explicite lors de la construction d'un contrôleur.

## Retour sur le routeur : l'injection de dépendances

Le routeur réalisé jusqu'à maintenant est capable de trouver, pour une URL donnée, le contrôleur associé.

Mais il n'est pas capable de fournir les paramètres adéquats lors de l'exécution d'une méthode. C'est ce que nous constatons avec la méthode `index` de l'`IndexController`, dont la signature ressemble à ça :

```php
public function index(EntityManager $em) {
  // ...
}
```

Le contrôleur est dépendant d'un gestionnaire d'entités pour pouvoir s'exécuter correctement. Le paramètre attendu est donc une **dépendance** du contrôleur `index`.

Nous avons donc besoin, lorsque nous routons une requête :

- d'identifier les dépendances d'un contrôleur qui aurait été trouvé par le routeur
- de pouvoir chercher quelque part si nous disposons d'une instance de classe satisfaisant le type du paramètre, que l'on pourrait donc **injecter** dans la méthode

### Identifier les dépendances avec l'API Reflection

Pour identifier les dépendances, ou paramètres d'une méthode, nous pouvons nous appuyer sur l'API [Reflection](https://www.php.net/manual/en/book.reflection).
Cette API nous permet d'inspecter les **métadonnées** d'une classe, d'une méthode, fonction, etc...

Quand notre routeur trouve une méthode correspondant à l'URL de la requête, donc un contrôleur, nous n'avons donc plus qu'à instancier un objet [ReflectionMethod](https://www.php.net/manual/en/class.reflectionmethod.php) et récupérer les différents paramètres.

```php
$methodInfos = new ReflectionMethod($controllerName . '::' . $method);
$methodParameters = $methodInfos->getParameters();
```

Ensuite, nous pouvons boucler sur les paramètres pour récupérer le type, le nom, etc...

```php
foreach ($methodParameters as $param) {
  $paramName = $param->getName();
  $paramType = $param->getType()->getName();
}
```

Retrouvez le commit concerné [ici](https://github.com/ld-web/php_b3_su_mvc_2021/commit/c27976a61d20f35d88609d8dd3771aa3e4a6d5d6).

### Fournir des dépendances à l'aide d'un container de services

Une fois que notre routeur a identifié les dépendances du contrôleur, il lui faut un moyen de chercher si une instance de classe peut y correspondre.

En fait, ces dépendances peuvent aussi être appelées **services**. Notre méthode a besoin d'un service, d'une brique applicative.

Nous allons donc réaliser un petit **container de services**, c'est-à-dire une classe contenant nos services, et que nous pouvons utiliser pour ajouter un service, vérifier l'existence d'un service, ou encore tout simplement **récupérer** un service.

Nous avons déjà vu ensemble la recommandation _PSR-4_, concernant l'auto-chargement des classes dans une application.

Il se trouve qu'il existe une autre recommandation pour la création d'un container de services : [**PSR-11**](https://www.php-fig.org/psr/psr-11/).

Cette recommandation présente une **interface** PHP, `ContainerInterface`, définissant des méthodes à implémenter pour réaliser un container de services.

Si nous voulons réaliser un container compatible PSR-11, nous devons donc réaliser une classe implémentant cette interface.

> **Pourquoi réaliser un container compatible PSR-11 ?** Tout simplement car il s'agit d'une recommandation PHP standard, donc forcément connue par bon nombre de personnes dans la communauté. Par ailleurs, les containers de services faisant référence dans l'écosystème PHP sont compatibles PSR-11 ([celui de Symfony par exemple](https://symfony.com/doc/current/components/dependency_injection.html)). Autant essayer d'appliquer également de bonnes pratiques lors de notre réalisation

Notre container sera très simple : un tableau (`array`) associatif, avec pour clé un identifiant, et pour valeur l'instance de service associée.

Retrouvez le container de services [ici](https://github.com/ld-web/php_b3_su_mvc_2021/blob/master/src/DependencyInjection/Container.php).

Une fois notre container réalisé, nous pouvons l'initialiser à la phase de bootstrap de notre application, dans le fichier `public/index.php` :

```php
use App\DependencyInjection\Container;

//...

// Service Container
$container = new Container();
$container->set(EntityManager::class, $entityManager);
$container->set(Environment::class, $twig);
```

Puis l'associer à notre routeur :

```php
$router = new Router($container);
```

Dans notre routeur, définissons à présent une méthode `getMethodParams(string $controller, string $method)` qui ira récupérer les paramètres nécessaires dans le container de services.

Nous pourrons utiliser cette méthode à 2 moments :

- La construction de la classe de contrôleurs
- L'appel d'une méthode de contrôleur

```php
//...
$controllerName = $route['controller'];
$constructorParams = $this->getMethodParams($controllerName, '__construct');
$controller = new $controllerName(...$constructorParams);

$method = $route['method'];
$params = $this->getMethodParams($controllerName, $method);
//...
```

> Les "..." à la construction du contrôleur permettent d'éclater un tableau et d'injecter un à un ses éléments en tant que paramètre d'une méthode (ici le constructeur). Ceci s'appelle précisément du [argument unpacking](https://wiki.php.net/rfc/argument_unpacking)

Enfin, nous pouvons à présent appeler notre contrôleur avec les paramètres identifiés et récupérés dans le container de services, à l'aide de la méthode de la SPL [call_user_func_array](https://www.php.net/manual/en/function.call-user-func-array) :

```php
call_user_func_array(
  [$controller, $method],
  $params
);
```

## Ecrire des tests unitaires

L'écriture de tests dans une application est une tâche souvent longue, donc mise de côté dans les projets.

La présence de tests automatisés peut cependant augmenter significativement la qualité et la fiabilité de votre code, et donc réduire les risques de bugs. C'est spécialement utile sur des parties importantes, voire critiques de votre application.

### Principaux avantages

- Vous pouvez définir des scénarios d'exécution et bénéficier de l'exécution d'un outil qui va valider ces scénarios (dans PHP, PHPUnit par exemple)
- Si vous changez quelque chose dans votre codebase et que cela invalide un test que vous aviez préalablement écrit (donc un scénario qui était déjà établi et devait fonctionner d'une certaine façon), alors l'échec du test vous prévient directement que quelque chose ne va pas
- L'écriture de tests peut vous amener à questionner le code que vous écrivez. Un code **testable** est généralement un code bien écrit, car les éléments sont mieux isolés, séparés les uns des autres. Il est donc plus facile d'écrire des tests pour un tel code

### PHPUnit

Dans notre projet, nous allons utiliser [PHPUnit](https://phpunit.de/).

Pour l'installer, nous utilisons Composer :

```bash
composer require --dev phpunit/phpunit
```

> Le `--dev` sert à indiquer à Composer qu'il s'agit d'une dépendance de **développement** et pas de production. Nous n'aurons pas besoin de lancer nos tests sur une machine de production, donc il n'y aura pas besoin d'installer PHPUnit sur notre production. Les dépendances de développement sont souvent relatives à des outils pour les développeurs (framework de tests, Linter, formatteur de code, analyseur de qualité de code, etc...)

---

> A l'installation de PHPUnit, un binaire est déposé dans le dossier `vendor/bin`, nommé `phpunit`. C'est ce binaire qu'on utilisera pour lancer nos tests

### Ecriture de tests pour notre container de services

Si l'on veut tester une classe donnée, alors nous allons créer un dossier `tests` à la racine de notre projet.

Le but de ce dossier est de reproduire l'arborescence définie dans le dossier `src` de notre application.

La classe de tests doit être suffixée par `Test`.

Pour notre container, nous allons donc créer le fichier `tests/DependencyInjection/ContainerTest.php`.

```php
namespace App\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
  // Méthodes de tests
}
```

**Note** : il faut indiquer à Composer que le namespace `App\Tests` peut être résolu par le dossier `tests` :

```json
"autoload-dev": {
  "psr-4": {
    "App\\Tests\\": "tests/"
  }
},
```

Nous conservons donc PSR-4 pour le nommage de nos classes de tests.

Une méthode de tests présente un scénario. Son nom est préfixé par `test` :

```php
class ContainerTest extends TestCase
{
  public function testHasNotService()
  {
    $container = new Container();
    $hasService = $container->has('test');
    $this->assertFalse($hasService);
  }
}
```

Dans un test, comme par exemple ci-dessus, il faut faire des **assertions** pour vérifier un résultat attendu. Dans notre exemple, le scénario de test crée un container et vérifie qu'il n'y a pas de service ayant pour identifiant "test" dedans.

Il existe [tout un tas d'assertions](https://phpunit.readthedocs.io/en/latest/assertions.html) pour vérifier un résultat attendu. Vous devez utiliser les assertions pour qu'un test soit valide.

Retrouvez l'ensemble des tests pour notre container [ici](https://github.com/ld-web/php_b3_su_mvc_2021/blob/master/tests/DependencyInjection/ContainerTest.php).

### Lancer la suite de tests

Pour lancer PHPUnit et exécuter les tests qu'on a écrits, il suffit d'utiliser le binaire créé automatiquement à l'installation de PHPUnit, et lui indiquer en paramètre le dossier des tests. On utilisera également l'option `--testdox` pour avoir un affichage mieux formaté :

```bash
vendor/bin/phpunit tests --testdox
```

![PHPUnit output](docs/phpunit_output.png "PHPUnit output")

Afin d'éviter d'avoir à taper la commande de lancement de tests à chaque fois, créons un script Composer qui lancera la commande à notre place :

```json
"scripts": {
  "start": "php -S localhost:8000 -t public/ public/index.php",
  "test": "phpunit tests --testdox"
}
```

> On peut se permettre d'indiquer uniquement `phpunit` dans un script Composer, car Composer ira chercher automatiquement dans le dossier `vendor/bin` s'il existe ou non un binaire portant ce nom

Ainsi, nous pouvons lancer nos tests simplement avec la commande `composer test`.

### Générer un rapport de couverture de code

> **Note importante** : l'extension [XDEBUG](https://xdebug.org/) doit être installée et activée dans votre configuration PHP pour que cela puisse fonctionner

PHPUnit peut générer un rapport de couverture de code, nous indiquant, pour chaque classe, la quantité de code couverte par les tests que nous avons écrits. Cela peut être utile pour contrôler les parties de notre code couvertes, donc validées par des tests.

Dans un premier temps, nous allons demander à PHPUnit de générer un fichier de configuration, dans lequel se trouvera par défaut le filtre des fichiers à intégrer dans l'analyse de couverture de code : `vendor/bin/phpunit --generate-configuration`.

Un fichier `phpunit.xml` est créé à la racine du projet.

Nous allons changer le paramètre `forceCoversAnnotation` et indiquer `false` pour qu'il détermine lui-même quelle partie du code est couverte ou non, sans qu'on lui indique quoi que ce soit.

Enfin, nous pouvons lancer nos tests avec l'option `--coverage-html coverage` pour qu'il génère un rapport au format HTML et le dépose dans le dossier `coverage`. On ajoutera également ce dossier `coverage` au fichier `.gitignore` de notre projet. On ne veut pas commiter ni pusher un tel fichier généré automatiquement par un outil tiers.

```bash
vendor/bin/phpunit tests --testdox --coverage-html coverage
```

PHPUnit nous indique que la variable d'environnement `XDEBUG_MODE` doit avoir la valeur `coverage` pour que la génération du rapport fonctionne :

```bash
XDEBUG_MODE=coverage vendor/bin/phpunit tests --testdox --coverage-html coverage
```

Nous pouvons ensuite consulter le rapport en ouvrant le fichier `index.html` du dossier `coverage` :

![Coverage](docs/coverage.png "Coverage")

Nous retrouvons notre classe `Container` dans `DependencyInjection`, qui a visiblement une couverture à 100%. Mais au global, la couverture est très faible bien sûr.

Pour finir, comme nous l'avons fait avant de générer le rapport de couverture de code, nous pouvons créer un script Composer qui exécutera tout ce dont nous aurons besoin : mettre la variable `XDEBUG_MODE` à `coverage`, et exécuter la commande PHPUnit avec les paramètres adéquats.

Nous pouvons même faire encore mieux : séparer les scripts `test` et `test:coverage` afin de ne générer le rapport de couverture quand nous le voudrons. Nous pouvons même référencer `test` dans `test:coverage` pour ne pas avoir à nous répéter !

```json
"scripts": {
  "start": "php -S localhost:8000 -t public/ public/index.php",
  "test": "phpunit tests --testdox",
  "test:coverage": [
    "@putenv XDEBUG_MODE=coverage",
    "@test --coverage-html coverage"
  ]
}
```

## Retour sur le routeur (BIS) - Les attributs PHP8

La version 8 de PHP apporte de nouvelles fonctionnalités au langage ([8.0](https://www.php.net/releases/8.0/en.php), [8.1](https://www.php.net/releases/8.1/en.php)).

Nous allons reprendre notre routeur pour utiliser une de ces fonctionnalités, [les attributs](https://www.php.net/manual/en/language.attributes.php).

### Les attributs, c'est quoi ?

Les attributs permettent de définir de manière structurée et native des **métadonnées** sur des éléments définis dans notre application : classes, méthodes, etc...

Les métadonnées d'un élément vont nous permettre de pouvoir lire des informations concernant cet élément : sur une méthode par exemple, s'agit-il d'une route ? La présence d'un attribut nous permettra de détecter automatiquement ce genre de choses.

Avant la version 8 de PHP, le mécanisme largement utilisé pour définir des métadonnées sur un élément était les **annotations** : on définissait des métadonnées dans le _DocBlock_ de l'élément, donc en commentaire, de manière structurée.

Mais cette méthode n'était pas réellement native au langage.

L'arrivée des attributs permet d'utiliser le même type de syntaxe mais de manière beaucoup plus structurée.

Exemple avec les annotations (avant PHP8) :

```php
class PostsController
{
    /**
     * @Route("/api/posts/{id}", methods={"GET"})
     */
    public function get($id) { /* ... */ }
}
```

Exemple avec les attributs (à partir de PHP8) :

```php
class PostsController
{
    #[Route("/api/posts/{id}", methods: ["GET"])]
    public function get($id) { /* ... */ }
}
```

Ce que nous allons faire, c'est donc créer un attribut PHP8, `Route`, que nous allons ensuite utiliser sur nos méthodes de contrôleurs. Ainsi, nous pourrons **détecter** automatiquement toutes les routes à enregistrer dans notre routeur.

Nous n'aurons donc plus besoin d'ajouter manuellement un appel à `addRoute` dans notre fichier `public/index.php`.

### Créer un attribut

Pour créer un attribut, on crée une classe, que l'on annote avec l'attribut `#[Attribute]`.

```php
namespace App\Routing\Attribute;

use Attribute;

#[Attribute]
class Route
{
  //...
}
```

> On peut également restreindre sur quel type d'élément de langage l'attribut pourra être utilisé (classe, méthode, ...). Voir [ce lien](https://www.php.net/manual/en/language.attributes.classes.php), exemple n°2

Dans cette classe, on va reproduire la structure d'une route telle qu'on en a besoin dans notre routeur : nom, URL (path), méthode HTTP.

> Nous n'aurons pas besoin de redéfinir la classe de contrôleurs et la méthode. En effet, lorsque nous traiterons cet attribut, nous serons précisément dans le contexte de la classe et la méthode. Nul besoin de se répéter, donc.

Retrouvez la classe `Route` [ici](https://github.com/ld-web/php_b3_su_mvc_2021/blob/master/src/Routing/Attribute/Route.php).

### Utiliser l'attribut dans nos contrôleurs

Une fois notre attribut écrit, nous pouvons donc l'ajouter à nos méthodes de contrôleurs :

```php
namespace App\Controller;

use App\Routing\Attribute\Route;
use Doctrine\ORM\EntityManager;

class IndexController extends AbstractController
{
  #[Route(path: "/")]
  public function index(EntityManager $em)
  {
    //...
  }
}
```

La syntaxe est très simple : nous appelons l'attribut comme si nous appelions son **constructeur**. En réalité, quand nous aurons besoin d'instancier cet attribut, PHP va utiliser les arguments nommés ([autre nouvelle fonctionnalité de PHP8](https://www.php.net/releases/8.0/en.php#named-arguments)) que nous lui indiquons pour le construire.

### Lire les attributs d'un élément

Nous avons créé notre attribut, et l'avons utilisé dans nos contrôleurs. Il nous faut maintenant modifier notre routeur, pour le rendre capable d'aller chercher tout seul l'ensemble des routes déclarées dans notre application.

Pour ce faire, nous aurons besoin d'implémenter les étapes suivantes :

- Récupérer les classes de contrôleurs (donc scanner le répertoire des contrôleurs)
- Pour chaque classe, récupérer les éventuels attributs `Route` déclarés dessus
- Si on dispose d'un attribut `Route`, alors l'instancier et le passer à notre méthode `addRoute`

Nous définissons donc une méthode `registerRoutes` qui se chargera de tout ça pour nous ! Ensuite, nous n'aurons plus qu'à appeler cette méthode depuis le bootstrap de notre application.

La définition de la méthode (avant refactorisation) est disponible dans [ce commit](https://github.com/ld-web/php_b3_su_mvc_2021/commit/d46a030528477ca58f2dc5aada9e00e553489e4f#diff-cc75cf1c7e7edc826fe7fbc1a2d190a5e8628666c5108e2566945c90e85b03ad). Elle ne fait qu'implémenter l'algorithme défini dans la liste ci-dessus.

La lecture d'un attribut se fait avec la méthode `getAttributes`, ajoutée à l'API Reflection dans la version 8 de PHP.

```php
// Pour chaque méthode d'une classe de contrôleurs, on va chercher s'il y a un ou plusieurs attributs Route
foreach ($methods as $method) {
  $attributes = $method->getAttributes(Route::class); // On filtre pour ne récupérer que les attributs Route. Ici ce n'est que ce type qui nous intéresse

  foreach ($attributes as $attribute) {
    $route = $attribute->newInstance(); // On instancie la route
    // Puis on traite l'ajout de la route récupérée...
    // ...
  }
}
```

Pour finir, dans `public/index.php`, nous faison l'appel :

```php
// Routage
$router = new Router($container);
$router->registerRoutes();
```

Nos routes sont à présent enregistrées automatiquement au bootstrap de notre application.

La déclaration des routes, quant à elle, se trouve directement "sur" nos contrôleurs. C'est plus pratique car nous regroupons les informations d'un contrôleur à un seul endroit.
