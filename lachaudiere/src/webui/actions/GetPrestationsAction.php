<?php
namespace gift\appli\webui\actions;

use gift\appli\application_core\application\exceptions\CatalogueException;
use gift\appli\application_core\application\useCases\CatalogueInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class GetPrestationsAction {
    private CatalogueInterface $catalogue;

    public function __construct(CatalogueInterface $catalogue) {
        $this->catalogue = $catalogue;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $prestations = $this->catalogue->getPrestations();
            $view = Twig::fromRequest($request);
            return $view->render($response, 'prestations.twig', ['prestations' => $prestations]);
        } catch (CatalogueException $e) {
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $e->getMessage());
        }
    }
}