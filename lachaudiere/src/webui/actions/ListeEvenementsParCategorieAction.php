<?php
namespace lachaudiere\webui\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use lachaudiere\application_core\application\useCases\EvenementService;
use lachaudiere\application_core\application\useCases\CategoriesService;
use Slim\Views\Twig;

class ListEvenementsParCategorieAction {
    protected EvenementService $evenementService;
    protected CategorieService $categorieService;
    protected Twig $view;

    public function __construct(EvenementService $evenementService, CategorieService $categorieService, Twig $view) {
        $this->evenementService = $evenementService;
        $this->categorieService = $categorieService;
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $idCategorie = $args['id'];
        $evenements = $this->evenementService->getByCategorieSortedByDate($idCategorie);
        $categorie = $this->categorieService->getById($idCategorie);
        return $this->view->render($response, 'liste_evenements.twig', [
            'evenements' => $evenements,
            'selected_categorie' => $categorie,
        ]);
    }
}<?php
