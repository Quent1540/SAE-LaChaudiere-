<?php
namespace lachaudiere\api\actions;

use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetCategoriesApiAction {
    private EvenementServiceInterface $evenement;

    public function __construct(EvenementServiceInterface $evenement) {
        $this->evenement = $evenement;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $categories = $this->evenement->getCategories();
        $data = [
            'type' => 'collection',
            'count' => count($categories),
            'categories' => array_map(function($event) {
                return [
                    'categorie' => [
                        'id_categorie' => $event['id_categorie'],
                        'libelle' => $event['libelle'],
                        'description' => $event['description'],
                    ],
                    'links' => [
                        'self' => [
                            'href' => '/categories/' . $event['id_categorie'] . '/',
                        ]
                    ]
                ];
            }, $categories)
        ];
        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withStatus(200);
    }
}