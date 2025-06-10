<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\useCases\CategoriesServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use lachaudiere\application_core\application\useCases\EvenementServiceInterface;

class ListEvenementsAction {
    private EvenementServiceInterface $evenementService;
    private CategoriesServiceInterface $categorieService;

    public function __construct(
        EvenementServiceInterface $evenementService,
        CategoriesServiceInterface $categorieService
    ) {
        $this->evenementService = $evenementService;
        $this->categorieService = $categorieService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $categories = $this->categorieService->getCategories();
        $evenements = $this->evenementService->getEvenementsAvecCategorie();

        $view = Twig::fromRequest($request);
        return $view->render($response, 'listeEvenements.twig', [
            'evenements' => $evenements,
            'categories' => $categories,
            'selected_categorie' => null
        ]);
    }
}