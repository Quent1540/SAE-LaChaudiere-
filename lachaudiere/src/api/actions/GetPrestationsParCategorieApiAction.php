<?php
namespace gift\appli\api\actions;

use gift\appli\application_core\application\useCases\CatalogueInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetPrestationsParCategorieApiAction {
    private CatalogueInterface $catalogue;

    public function __construct(CatalogueInterface $catalogue) {
        $this->catalogue = $catalogue;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $id = (int) $args['id'];
        $prestations = $this->catalogue->getPrestationsByCategorie($id);
        $data = [
            'type' => 'collection',
            'count' => count($prestations),
            'prestations' => array_map(function($prestation) {
                return [
                    'prestation' => [
                        'id' => $prestation['id'],
                        'libelle' => $prestation['libelle'],
                        'description' => $prestation['description'],
                    ],
                    'links' => [
                        'self' => [
                            'href' => '/prestations/' . $prestation['id'] . '/',
                        ]
                    ]
                ];
            }, $prestations)
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}