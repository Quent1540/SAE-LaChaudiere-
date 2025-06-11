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
        $sort = $params['sort'] ?? '';
        $periodes = explode(',', $request->getQueryParams()['periode'] ?? '');
        $now = date('Y-m-d H:i:s');

        $allEvenements = $this->evenement->getEvenements();
        $evenements = array_filter($allEvenements, fn($e) => $e['est_publie']);

        if ($periodes && $periodes[0] !== '') {
            $evenements = array_filter($evenements, function($event) use ($periodes, $now) {
                foreach ($periodes as $periode) {
                    if ($periode === 'passee' && $event['date_fin'] < $now) return true;
                    if ($periode === 'courante' && $event['date_debut'] <= $now && $event['date_fin'] >= $now) return true;
                    if ($periode === 'future' && $event['date_debut'] > $now) return true;
                }
                return false;
            });
        }
        
        if ($sort === 'date-asc') {
            usort($evenements, fn($a, $b) => strtotime($a['date_debut']) <=> strtotime($b['date_debut']));
        } elseif ($sort === 'date-desc') {
            usort($evenements, fn($a, $b) => strtotime($b['date_debut']) <=> strtotime($a['date_debut']));
        } elseif ($sort === 'titre') {
            usort($evenements, fn($a, $b) => strcmp($a['titre'], $b['titre']));
        }

        $data = [
            'type' => 'collection',
            'count' => count($evenements),
            'evenements' => array_map(function($event) {
                return [
                    'evenement' => [
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