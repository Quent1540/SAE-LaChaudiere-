<?php
namespace lachaudiere\api\actions;

use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetEvenementsApiAction {
    private EvenementServiceInterface $evenement;

    public function __construct(EvenementServiceInterface $evenement) {
        $this->evenement = $evenement;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $params = $request->getQueryParams();
        
        $evenements = $this->evenement->findEvenements($params);

        $data = [
            'type' => 'collection',
            'count' => count($evenements),
            'evenements' => array_map(function($event) {
                return [
                    'evenement' => [
                        'id' => $event['id_evenement'],
                        'titre' => $event['titre'],
                        'date_debut' => $event['date_debut'],
                        'id_categorie' => $event['id_categorie'],
                        'categorie_libelle' => $event['categorie']['libelle'] ?? null,
                        'images' => array_map(fn($img) => ['url' => $img['url_image'], 'legende' => $img['legende']], $event['images'] ?? [])
                    ],
                    'links' => [
                        'self' => ['href' => '/api/evenements/' . $event['id_evenement']]
                    ]
                ];
            }, array_values($evenements))
        ];

        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8')
                         ->withHeader('Access-Control-Allow-Origin', '*');
    }
}