<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use lachaudiere\infrastructure\Eloquent;
use lachaudiere\webui\providers\AuthnProviderInterface;
use lachaudiere\webui\providers\AuthnProvider;
use lachaudiere\application_core\application\useCases\AuthnServiceInterface;
use lachaudiere\application_core\application\useCases\AuthnService;
use lachaudiere\application_core\application\useCases\EvenementInterface;
use lachaudiere\application_core\application\useCases\Evenement;
use lachaudiere\application_core\application\useCases\BoxInterface;
use lachaudiere\application_core\application\useCases\Box;

// Création du conteneur
$container = new Container();
AppFactory::setContainer($container);

// Chargement de l'ORM Eloquent
require_once __DIR__ . '/../infrastructure/Eloquent.php';
Eloquent::init(__DIR__ . '/gift.db.conf.ini');

// Création de l'application
$app = AppFactory::create();

// Initialisation de Twig
$twig = Twig::create(__DIR__ . '/../webui/views', [
    'cache' => false,
    'auto_reload' => true,
]);
$app->add(\Slim\Views\TwigMiddleware::create($app, $twig));

// Middleware Slim
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Enregistrements des services dans le conteneur DI

$container->set(EvenementInterface::class, fn() => new Evenement());
$container->set(BoxInterface::class, fn() => new Box());
$container->set(AuthnServiceInterface::class, fn() => new AuthnService());
$container->set(AuthnProviderInterface::class, fn($c) => new AuthnProvider($c->get(AuthnServiceInterface::class)));

// Twig pour injection dans les actions
$container->set(Twig::class, fn() => $twig);

// Chargement des routes (Web UI et API)
$app = (require __DIR__ . '/routes.php')($app);
$app = (require __DIR__ . '/../api/routes.php')($app);

return $app;
