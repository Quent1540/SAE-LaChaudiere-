<?php
namespace gift\appli\webui\actions;

use gift\appli\application_core\application\exceptions\CatalogueException;
use gift\appli\application_core\application\useCases\CatalogueInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Exception\HttpNotFoundException;

class GetPrestationsParCategorieAction {
    private CatalogueInterface $catalogue;

    public function __construct(CatalogueInterface $catalogue) {
        $this->catalogue = $catalogue;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $id = (int) $args['id'];
            $categorie = $this->catalogue->getCategorieById($id);
            $categorie['prestations'] = $this->catalogue->getPrestationsByCategorie($id);
            $view = Twig::fromRequest($request);
            return $view->render($response, 'prestationsParCategorie.twig', ['categorie' => $categorie]);
        } catch (CatalogueException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }
    }
}