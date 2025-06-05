<?php
declare(strict_types=1);

use Slim\App;

return function (App $app): App{
    $app->get('/api/categories', \gift\appli\api\actions\GetCategoriesApiAction::class);
    $app->get('/api/prestations', \gift\appli\api\actions\GetPrestationsApiAction::class);
    $app->get('/api/categorie/{id}/prestations', \gift\appli\api\actions\GetPrestationsParCategorieApiAction::class);
    $app->get('/api/box/{id}', \gift\appli\api\actions\GetBoxApiAction::class);
    return $app;
};