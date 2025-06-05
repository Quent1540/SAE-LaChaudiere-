<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';
use DI\Container;
use Slim\Factory\AppFactory;
use gift\appli\infrastructure\Eloquent;

//Création du conteneur
$container = new Container();
AppFactory::setContainer($container);

//Chargement d'Eloquent ORM
require_once __DIR__ . '/../infrastructure/Eloquent.php';
Eloquent::init(__DIR__ . '/gift.db.conf.ini');

//Création de l'application
$app = AppFactory::create();

//Enregistrement du service Catalogue dans le conteneur DI
$app->getContainer()->set(
    \gift\appli\application_core\application\useCases\CatalogueInterface::class,
    function() {
        return new \gift\appli\application_core\application\useCases\Catalogue();
    }
);

//Twig
$twig = \Slim\Views\Twig::create(__DIR__ . '/../webui/views', [
    'cache' => false, //__DIR__ . '/../views/cache',
    'auto_reload' => true
]);
$app->add(\Slim\Views\TwigMiddleware::create($app, $twig)) ;
//$app->setBasePath('/Projet_Giftbox/giftAppli/public');
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

//Enregistrement du service AuthnProvider dans le conteneur DI
$app->getContainer()->set(
    \gift\appli\webui\providers\AuthnProviderInterface::class,
    function($c) {
        $authnService = $c->get(\gift\appli\application_core\application\useCases\AuthnService::class);
        return new \gift\appli\webui\providers\AuthnProvider($authnService);
    }
);

//Enregistrement du service AuthnService dans le conteneur DI
$app->getContainer()->set(
    \gift\appli\application_core\application\useCases\AuthnServiceInterface::class,
    function() {
        return new \gift\appli\application_core\application\useCases\AuthnService();
    }
    );
//Enregistrement du service Box dans le conteneur DI
$app->getContainer()->set(
    \gift\appli\application_core\application\useCases\BoxInterface::class,
    function() {
        return new \gift\appli\application_core\application\useCases\Box();
    }
);

//Chargement des routes
$app = (require_once __DIR__ . '/routes.php')($app);
//Chargement des routes API
$app = (require_once __DIR__ . '/../api/routes.php')($app);

return $app;