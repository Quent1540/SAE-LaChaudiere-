<?php
namespace gift\appli\api\actions;

use gift\appli\application_core\application\useCases\CatalogueInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetCategoriesApiAction {
    private CatalogueInterface $catalogue;

    public function __construct(CatalogueInterface $catalogue) {
        $this->catalogue = $catalogue;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $categories = $this->catalogue->getCategories();
        $data = [
            'type' => 'collection',
            'count' => count($categories),
            'categories' => array_map(function($cat) {
                return [
                    'categorie' => [
                        'id' => $cat['id'],
                        'libelle' => $cat['libelle'],
                        'description' => $cat['description'],
                    ],
                    'links' => [
                        'self' => [
                            'href' => '/categories/' . $cat['id'] . '/',
                        ]
                    ]
                ];
            }, $categories)
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}