<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\useCases\CategoriesServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use lachaudiere\application_core\application\useCases\EvenementServiceInterface;

//Action pour afficher la liste des événements, filtrée ou non
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
        //Récup des paramètres de la requête GET
        $params = $request->getQueryParams();
        $categories = $this->categorieService->getCategories();

        $selected_categorie = null;
        //Si un paramètre de catégorie est passé, on filtre les événements par cette catégorie
        if (!empty($params['categorie'])) {
            $selected_categorie = $this->categorieService->getCategorieById($params['categorie']);
            $evenements = $this->evenementService->getEvenementsParCategorie($params['categorie']);
        } else {
            //Sinon, on récupère tous les événements avec leurs catégories
            $evenements = $this->evenementService->getEvenementsAvecCategorie();
        }

        //Rendu Twig
        $view = Twig::fromRequest($request);
        return $view->render($response, 'listeEvenements.twig', [
            'evenements' => $evenements,
            'categories' => $categories,
            'selected_categorie' => $selected_categorie
        ]);
    }
}