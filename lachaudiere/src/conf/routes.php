<?php
declare(strict_types=1);

use lachaudiere\appli\webui\actions\AddEvenementAction;
use lachaudiere\webui\actions\RegisterAction;
use lachaudiere\webui\actions\SignoutAction;
use lachaudiere\webui\actions\DashboardAction;
use lachaudiere\webui\providers\AuthnProviderInterface;
use Slim\App;
use lachaudiere\webui\actions\SigninAction;

return function(App $app): App {
    $app->get('/categories', GetCategoriesAction::class);
    $app->map(['GET','POST'], '/signin', SigninAction::class)->setName('signin');
    $app->post('/signout', SignoutAction::class);
    $app->map(['GET','POST'], '/register', RegisterAction::class);
    $app->get('/categorie/{id}', GetCategorieParIdAction::class);
    $app->get('/admin/dashboard',DashboardAction::class)->setName('admin.dashboard');
    $app->get('/prestations', \lachaudiere\appli\webui\actions\GetPrestationsAction::class);
    $app->get('/prestation/{id}', GetPrestationParIdAction::class);
    $app->get('/categories/{id}/prestations', GetPrestationsParCategorieAction::class);
    $app->get('/', function ($request, $response, $args) use ($app) {
        $container = $app->getContainer();
        $authProvider = $container->get(AuthnProviderInterface::class);
        $user = $authProvider->getSignedInUser();
        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'home.twig', [
         'user' => $user
        ]);
});
    $app->get('/coffrets', \lachaudiere\appli\webui\actions\GetCoffretsAction::class);
    $app->get('/coffret/{id}', \lachaudiere\appli\webui\actions\GetCoffretDetailAction::class);
    $app->map(['GET', 'POST'], '/box/create', \lachaudiere\appli\webui\actions\AddEvenementAction::class);
    $app->get('/coffret/{coffret_id}/prestation/{id}', \lachaudiere\appli\webui\actions\GetPrestationCoffretAction::class);
    $app->post('/box/prestation/add', \lachaudiere\appli\webui\actions\AddEvenementAction::class);
    $app->get('/box/courante', \lachaudiere\appli\webui\actions\GetBoxCouranteAction::class);
    $app->get('/evenement/create', AddEvenementAction::class);
    return $app;
};