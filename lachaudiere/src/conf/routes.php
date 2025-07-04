<?php
declare(strict_types=1);

use lachaudiere\webui\actions\AddCategorieFormAction;
use lachaudiere\webui\actions\AddEvenementAction;
use lachaudiere\webui\actions\AddCategorieAction;
use lachaudiere\webui\actions\AddEvenementFormAction;
use lachaudiere\webui\actions\GetCategorieParIdAction;
use lachaudiere\webui\actions\TogglePublishAction;
use lachaudiere\webui\actions\GetCategoriesAction;
use lachaudiere\webui\actions\GetEvenementDetailAction;
use lachaudiere\webui\actions\ListEvenementsAction;
use lachaudiere\webui\actions\RegisterAction;
use lachaudiere\webui\actions\SignoutAction;
use lachaudiere\webui\providers\AuthnProviderInterface;
use Slim\App;
use lachaudiere\webui\actions\SigninAction;
use lachaudiere\webui\middleware\AuthMiddleware;

return function(App $app): App {
    $app->map(['GET','POST'], '/signin', SigninAction::class)->setName('signin');
    $app->post('/signout', SignoutAction::class)->setName('signout');
    $app->get('/', function ($request, $response, $args) use ($app) {
        $container = $app->getContainer();
        $authProvider = $container->get(AuthnProviderInterface::class);
        $user = $authProvider->getSignedInUser();
        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'home.twig', [
            'user' => $user
        ]);
    })->setName("home");


    // METTRE LES ROUTES RESTREINTES ICI
    $app->group('', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->map(['GET','POST'], '/register', RegisterAction::class)->setName('register');
        $group->get('/categories', GetCategoriesAction::class)->setName('list_categories');
        $group->get('/categorie/show', AddCategorieFormAction::class)->setName('show_categorie_form');
        $group->post('/categorie/create', AddCategorieAction::class)->setName('create_categorie');
        $group->get('/categorie/{id}', GetCategorieParIdAction::class)->setName('categorie');
        $group->post('/evenement/create', AddEvenementAction::class)->setName('create_evenement');
        $group->get('/evenement/show', AddEvenementFormAction::class)->setName('show_evenement_form');
        $group->get('/evenements', ListEvenementsAction::class)->setName('list_evenements');
        $group->post('/evenement/{id}/toggle-publish', TogglePublishAction::class)->setName('toggle-publish-event');
        $group->get('/evenement/{id}/details', GetEvenementDetailAction::class)->setName('event_details');
    })->add(AuthMiddleware::class);

    return $app;
};