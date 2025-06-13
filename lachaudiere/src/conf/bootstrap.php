<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use lachaudiere\infrastructure\Eloquent;
use lachaudiere\webui\providers\AuthnProviderInterface;
use lachaudiere\webui\providers\AuthnProvider;
use lachaudiere\webui\providers\CsrfTokenProvider;
use lachaudiere\application_core\application\useCases\AuthnServiceInterface;
use lachaudiere\application_core\application\useCases\AuthnService;
use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use lachaudiere\application_core\application\useCases\EvenementService;
use lachaudiere\application_core\application\useCases\CategoriesServiceInterface;
use lachaudiere\application_core\application\useCases\CategoriesService;
use lachaudiere\application_core\application\useCases\ImagesEvenementServiceInterface;
use lachaudiere\application_core\application\useCases\ImagesEvenementService;
use lachaudiere\webui\middleware\AuthMiddleware;
use Psr\Container\ContainerInterface;
use lachaudiere\webui\actions\AddEvenementAction;

$container = new Container();
AppFactory::setContainer($container);

//Init Eloquent ORM
require_once __DIR__ . '/../infrastructure/Eloquent.php';
Eloquent::init(__DIR__ . '/gift.db.conf.ini');

//Création de l'app slim
$app = AppFactory::create();

//Config de twig pour les vues
$twig = Twig::create(__DIR__ . '/../webui/views', [
    'cache' => false,
    'auto_reload' => true,
]);

$twigEnvironment = $twig->getEnvironment();

//Fonction twig pour générer le token CSRF
$csrfFunction = new \Twig\TwigFunction('csrf_input', function () {
    $token = CsrfTokenProvider::generate();
    return new \Twig\Markup('<input type="hidden" name="csrf_token" value="' . $token . '">', 'UTF-8');
}, ['is_safe' => ['html']]);

$twigEnvironment->addFunction($csrfFunction);

//Fonction twig pour générer les URLs
$twig->getEnvironment()->addFunction(new \Twig\TwigFunction('url_for', [\Slim\Views\TwigRuntimeExtension::class, 'urlFor']));

//Ajout du middleware Twig
$app->add(\Slim\Views\TwigMiddleware::create($app, $twig));
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$container->set('settings.upload_dir', __DIR__ . '/../../public/uploads');

//Déclaration des services
$container->set(ImagesEvenementServiceInterface::class, function(ContainerInterface $c) {
    return new ImagesEvenementService($c->get('settings.upload_dir'));
});

$container->set(EvenementServiceInterface::class, function(ContainerInterface $c) {
    return new EvenementService($c->get(ImagesEvenementServiceInterface::class));
});

$container->set(CategoriesServiceInterface::class, fn() => new CategoriesService());

$container->set(AuthnServiceInterface::class, fn() => new AuthnService());

$container->set(AuthnProviderInterface::class, function(ContainerInterface $c) {
    return new AuthnProvider($c->get(AuthnServiceInterface::class));
});

$container->set(AddEvenementAction::class, function(ContainerInterface $c) {
    return new AddEvenementAction(
        $c->get(EvenementServiceInterface::class),
        $c->get(AuthnProviderInterface::class)
    );
});

$container->set(Twig::class, fn() => $twig); 

$container->set(AuthMiddleware::class, function (ContainerInterface $c) {
    return new AuthMiddleware($c->get(AuthnProviderInterface::class));
});

//Chargement des routes
$app = (require __DIR__ . '/routes.php')($app);
$app = (require __DIR__ . '/../api/routes.php')($app);

//Middleware CORS global
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

return $app;