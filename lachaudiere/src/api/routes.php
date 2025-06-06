<?php
declare(strict_types=1);

use Slim\App;

return function (App $app): App{
    $app->get('/api/categories', \lachaudiere\api\actions\GetCategoriesApiAction::class);
    return $app;
};