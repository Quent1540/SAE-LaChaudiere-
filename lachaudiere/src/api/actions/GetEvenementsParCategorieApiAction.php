<?php
namespace lachaudiere\api\actions;

use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetEvenementsParCategorieApiAction {
    private EvenementServiceInterface $evenement;

    public function __construct(EvenementServiceInterface $evenement) {
        $this->evenement = $evenement;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $params = $request->getQueryParams();
        $params['id_categorie'] = (int)$args['id_categorie'];

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
                        'date_fin' => $event['date_fin'],
                        'id_categorie' => $event['id_categorie'],
                    ],
                    'links' => [
                        'self' => [
                            'href' => '/evenements/' . $event['id_evenement'] . '/',
                        ]
                    ]
                ];
            }, $evenements)
        ];
        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withStatus(200);
    }
}