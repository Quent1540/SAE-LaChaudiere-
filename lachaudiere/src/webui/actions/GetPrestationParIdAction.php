<?php
namespace gift\appli\webui\actions;

use gift\appli\application_core\application\exceptions\CatalogueException;
use gift\appli\application_core\application\useCases\CatalogueInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Exception\HttpNotFoundException;

class GetPrestationParIdAction {
    private CatalogueInterface $catalogue;

    public function __construct(CatalogueInterface $catalogue) {
        $this->catalogue = $catalogue;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $id = $args['id'];
            $prestation = $this->catalogue->getPrestationById($id);
            $view = Twig::fromRequest($request);
            return $view->render($response, 'prestationParId.twig', ['prestation' => $prestation]);
        } catch (CatalogueException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }
    }
}