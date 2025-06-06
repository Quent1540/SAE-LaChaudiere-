<?php
namespace lachaudiere\api\actions;

use lachaudiere\application_core\application\useCases\EvenementInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetEvenementsApiAction {
    private EvenementInterface $evenement;

    public function __construct(EvenementInterface $evenement) {
        $this->evenement = $evenement;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $evenements = $this->evenement->getEvenements();
        //Pour trier par date de début
        usort($evenements, function($a, $b) {
            return strtotime($a['date_debut']) <=> strtotime($b['date_debut']);
        });
        $data = [
            'type' => 'collection',
            'count' => count($evenements),
            'evenements' => array_map(function($event) {
                return [
                    'evenement' => [
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
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}