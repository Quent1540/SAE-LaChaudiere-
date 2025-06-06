<?php
namespace lachaudiere\api\actions;

use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetEvenementParIdApiAction {
    private EvenementServiceInterface $evenement;

    public function __construct(EvenementServiceInterface $evenement) {
        $this->evenement = $evenement;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $id_evenement = (int)$args['id_evenement'];
        $evenement = $this->evenement->getEvenementParId($id_evenement);
        $data = [
            'type' => 'evenement',
            'evenement' => [
                'titre' => $evenement['titre'],
                'description' => $evenement['description'],
                'tarif' => $evenement['tarif'],
                'date_debut' => $evenement['date_debut'],
                'date_fin' => $evenement['date_fin'],
                'id_categorie' => $evenement['id_categorie'],
                'est_publie' => $evenement['est_publie'],
                'id_utilisateur_creation' => $evenement['id_utilisateur_creation'],
            ],
            'links' => [
                'self' => [
                    'href' => '/evenements/' . $id_evenement . '/',
                ]
            ]
        ];
        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withStatus(200);
    }
}