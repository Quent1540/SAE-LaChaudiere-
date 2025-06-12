<?php
namespace lachaudiere\api\actions;

use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//Action API pour récupérer la liste des événements d'une catégorie
class GetEvenementsParCategorieApiAction {
    private EvenementServiceInterface $evenement;

    public function __construct(EvenementServiceInterface $evenement) {
        $this->evenement = $evenement;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        //Récupération de la période de la requête
        $periodes = explode(',', $request->getQueryParams()['periode'] ?? '');
        $now = date('Y-m-d H:i:s');
        //Recupération de l'id de la catégorie depuis les arguments de la requête
        $id_categorie = (int)$args['id_categorie'];
        //Recupération des événements de la catégorie via le service métier
        $evenements = $this->evenement->getEvenementsParCategorie($id_categorie);

        //Filtrer les événements selon les périodes demandées
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

        //Pour trier par date de début
        usort($evenements, function($a, $b) {
            return strtotime($a['date_debut']) <=> strtotime($b['date_debut']);
        });

        //Structure de la réponse JSON
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
        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withStatus(200);
    }
}