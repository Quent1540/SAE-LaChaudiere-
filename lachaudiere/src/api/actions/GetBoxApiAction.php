<?php
namespace gift\appli\api\actions;

use gift\appli\application_core\application\useCases\BoxInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetBoxApiAction {
    private BoxInterface $boxService;

    public function __construct(BoxInterface $boxService) {
        $this->boxService = $boxService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $box_id = $args['id'] ?? null;
        if (!$box_id) {
            $response->getBody()->write(json_encode(['error' => 'Box ID manquant']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Récupération des prestations de la box
        $prestations = $this->boxService->getPrestationsByBox($box_id);
        $total = array_sum(array_column($prestations, 'tarif'));

        $data = [
            'type' => 'resource',
            'box' => [
                'id' => $box_id,
                'prestations' => $prestations,
                'total' => $total,
                'etat' => 'En cours'
            ]
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}