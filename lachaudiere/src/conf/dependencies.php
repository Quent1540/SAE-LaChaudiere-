<?php
use lachaudiere\application_core\application\useCases\AuthnService;
use lachaudiere\application_core\application\useCases\EvenementService;
use lachaudiere\application_core\application\useCases\ImagesEvenementService;
use lachaudiere\application_core\application\useCases\CategoriesServiceInterface;
use lachaudiere\webui\providers\AuthnProvider;
use lachaudiere\webui\actions\AddEvenementAction;

return [
    'evenementService' => \DI\autowire(EvenementService::class),
    'imagesEvenementService' => \DI\autowire(ImagesEvenementService::class),
    'categoriesService' => \DI\autowire(CategoriesServiceInterface::class),
    'authnService' => \DI\autowire(AuthnService::class),
    'authProvider' => \DI\autowire(AuthnProvider::class)
        ->constructor(\DI\get('authnService')),
    AddEvenementAction::class => \DI\autowire(AddEvenementAction::class)
        ->constructor(
            \DI\get('authProvider'),
            \DI\get('evenementService'),
            \DI\get('categoriesService'),
            \DI\get('imagesEvenementService')
        ),
];