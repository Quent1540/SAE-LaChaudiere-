<?php
namespace lachaudiere\api\actions;

use lachaudiere\application_core\application\exceptions\EvenementException;
use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetEvenementParIdApiAction {
    private EvenementServiceInterface $evenementService;

    public function __construct(EvenementServiceInterface $evenementService) {
        $this->evenementService = $evenementService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $id_evenement = (int)$args['id_evenement'];
            $evenement = $this->evenementService->getEvenementParId($id_evenement);

            if (!$evenement['est_publie']) {
                 $response->getBody()->write(json_encode(['error' => 'Ressource non disponible ou non publiée.']));
                 return $response->withStatus(404)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $data = [
                'type' => 'resource',
                'evenement' => [
                    'id' => $evenement['id_evenement'],
                    'titre' => $evenement['titre'],
                    'description' => $evenement['description'],
                    'tarif' => $evenement['tarif'],
                    'date_debut' => $evenement['date_debut'],
                    'date_fin' => $evenement['date_fin'],
                    'id_categorie' => $evenement['id_categorie'],
                    'images' => array_map(fn($img) => ['url' => $img['url_image'], 'legende' => $img['legende']], $evenement['images'] ?? [])
                ],
                'links' => [
                    'self' => ['href' => '/api/evenements/' . $id_evenement]
                ]
            ];

            $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8')->withHeader('Access-Control-Allow-Origin', '*');

        } catch (EvenementException $e) {
            $response->getBody()->write(json_encode(['error' => 'Événement non trouvé.']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}