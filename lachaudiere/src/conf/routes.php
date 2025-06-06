<?php
declare(strict_types=1);

use lachaudiere\appli\webui\actions\AddEvenementAction;
use lachaudiere\webui\actions\RegisterAction;
use lachaudiere\webui\actions\SignoutAction;
use lachaudiere\webui\actions\DashboardAction;
use lachaudiere\webui\providers\AuthnProviderInterface;
use lachaudiere\webui\actions\GetCategorieParIdAction;
use lachaudiere\webui\actions\GetCategoriesAction;
use Slim\App;
use lachaudiere\webui\actions\SigninAction;
use lachaudiere\webui\middleware\AuthMiddleware;

return function(App $app): App {
    $app->map(['GET','POST'], '/signin', SigninAction::class)->setName('signin');
    $app->post('/signout', SignoutAction::class);
    $app->map(['GET','POST'], '/register', RegisterAction::class);
    $app->get('/', function ($request, $response, $args) use ($app) {
        $container = $app->getContainer();
        $authProvider = $container->get(AuthnProviderInterface::class);
        $user = $authProvider->getSignedInUser();
        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'home.twig', [
         'user' => $user
        ]);
    });

    // METTRE LES ROUTES RESTREINTES ICI
    $app->group('', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->get('/categories', GetCategoriesAction::class);
        $group->get('/categorie/{id}', GetCategorieParIdAction::class);
        $group->get('/coffrets', \lachaudiere\appli\webui\actions\GetCoffretsAction::class);
        $group->get('/coffret/{id}', \lachaudiere\appli\webui\actions\GetCoffretDetailAction::class);
        $group->get('/admin/dashboard', DashboardAction::class)->setName('admin.dashboard');
        $group->map(['GET', 'POST'], '/box/create', AddEvenementAction::class);
        $group->post('/box/prestation/add', AddEvenementAction::class);
        $group->get('/box/courante', \lachaudiere\appli\webui\actions\GetBoxCouranteAction::class);
        $group->get('/evenement/create', AddEvenementAction::class);
        $group->get('/coffret/{coffret_id}/prestation/{id}', \lachaudiere\appli\webui\actions\GetPrestationCoffretAction::class);
        $group->get('/prestations', \lachaudiere\appli\webui\actions\GetPrestationsAction::class);
        $group->get('/prestation/{id}', GetPrestationParIdAction::class);
        $group->get('/categories/{id}/prestations', GetPrestationsParCategorieAction::class);
    })->add(AuthMiddleware::class);

    return $app;
};