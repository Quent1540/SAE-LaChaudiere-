<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\useCases\CategoriesServiceInterface;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//Action qui affiche le formulaire de création d'un événement
class AddEvenementFormAction {
    protected $categoriesService;

    public function __construct(CategoriesServiceInterface $categoriesService) {
        $this->categoriesService = $categoriesService;
    }

    public function __invoke(Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $categories = $this->categoriesService->getCategories();
        return $view->render($response, 'creerEvenement.twig', [
            'categories' => $categories
        ]);
    }
}