<?php
declare(strict_types=1);

use Slim\App;

return function (App $app): App{
    $app->get('/api/categories', \lachaudiere\api\actions\GetCategoriesApiAction::class);
    $app->get('/api/evenements', \lachaudiere\api\actions\GetEvenementsApiAction::class);
    $app->get('/api/categories/{id_categorie}/evenements', \lachaudiere\api\actions\GetEvenementsParCategorieApiAction::class);
    $app->get('/api/evenements/{id_evenement}', \lachaudiere\api\actions\GetEvenementParIdApiAction::class);
    $app->get('/api/images/{filename:.+}', \lachaudiere\api\actions\GetImageAction::class)
              ->setName('api_get_image');
    return $app;
};