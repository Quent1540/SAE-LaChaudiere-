<?php
declare(strict_types=1);

use gift\appli\webui\actions\AddEvenementAction;
use gift\appli\webui\actions\RegisterAction;
use gift\appli\webui\actions\SignoutAction;
use Slim\App;
use gift\appli\webui\actions\SigninAction;

return function(App $app): App {
    $app->get('/categories', GetCategoriesAction::class);
    $app->map(['GET','POST'], '/signin', SigninAction::class);
    $app->post('/signout', SignoutAction::class);
    $app->map(['GET','POST'], '/register', RegisterAction::class);
    $app->get('/categorie/{id}', GetCategorieParIdAction::class);
    $app->get('/prestations', \gift\appli\webui\actions\GetPrestationsAction::class);
    $app->get('/prestation/{id}', GetPrestationParIdAction::class);
    $app->get('/categories/{id}/prestations', GetPrestationsParCategorieAction::class);
    $app->get('/', function ($request, $response, $args) use ($app) {
        $container = $app->getContainer();
        $authProvider = $container->get(\gift\appli\webui\providers\AuthnProviderInterface::class);
        $user = $authProvider->getSignedInUser();
        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'home.twig', [
         'user' => $user
        ]);
});
    $app->get('/coffrets', \gift\appli\webui\actions\GetCoffretsAction::class);
    $app->get('/coffret/{id}', \gift\appli\webui\actions\GetCoffretDetailAction::class);
    $app->map(['GET', 'POST'], '/box/create', \gift\appli\webui\actions\AddEvenementAction::class);
    $app->get('/coffret/{coffret_id}/prestation/{id}', \gift\appli\webui\actions\GetPrestationCoffretAction::class);
    $app->post('/box/prestation/add', \gift\appli\webui\actions\AddEvenementAction::class);
    $app->get('/box/courante', \gift\appli\webui\actions\GetBoxCouranteAction::class);
    $app->get('/evenement/create', AddEvenementAction::class);
    return $app;
};