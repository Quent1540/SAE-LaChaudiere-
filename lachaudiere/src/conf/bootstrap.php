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

$container = new Container();
AppFactory::setContainer($container);

require_once __DIR__ . '/../infrastructure/Eloquent.php';
Eloquent::init(__DIR__ . '/gift.db.conf.ini');


$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/../webui/views', [
    'cache' => false,
    'auto_reload' => true,
]);

$twigEnvironment = $twig->getEnvironment();

$csrfFunction = new \Twig\TwigFunction('csrf_input', function () {
    $token = CsrfTokenProvider::generate();
    return new \Twig\Markup('<input type="hidden" name="csrf_token" value="' . $token . '">', 'UTF-8');
}, ['is_safe' => ['html']]);
$twigEnvironment->addFunction($csrfFunction);


$app->add(\Slim\Views\TwigMiddleware::create($app, $twig));

$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$container->set(EvenementServiceInterface::class, fn() => new EvenementService());
$container->set(CategoriesServiceInterface::class, fn() => new CategoriesService());
$container->set(ImagesEvenementServiceInterface::class, fn() => new ImagesEvenementService());
$container->set(AuthnServiceInterface::class, fn() => new AuthnService());
$container->set(AuthnProviderInterface::class, fn($c) => new AuthnProvider($c->get(AuthnServiceInterface::class)));
$container->set(\Slim\Views\Twig::class, fn() => $twig);
$container->set(AuthMiddleware::class, function (ContainerInterface $c) {
    $authProvider = $c->get(AuthnProviderInterface::class);
    return new AuthMiddleware($authProvider);
});

$app = (require __DIR__ . '/routes.php')($app);
$app = (require __DIR__ . '/../api/routes.php')($app);

return $app;