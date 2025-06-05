<?php
namespace lachaudiere\webui\actions;

use lachaudiere\webui\providers\AuthnProvider;
use lachaudiere\webui\providers\CsrfTokenProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class AddEvenementAction {
    protected AuthnProvider $authProvider;
    protected $evenementService;
    protected $categorieService;

    public function __construct(AuthnProvider $authProvider, $evenementService, $categorieService) {
        $this->authProvider = $authProvider;
        $this->evenementService = $evenementService;
        $this->categorieService = $categorieService;
    }

    public function __invoke(Request $request, Response $response, $args) {
        $method = $request->getMethod();
        $view = Twig::fromRequest($request);

        // Récupérer les catégories pour le formulaire
        $categories = $this->categorieService->getCategories();

        if ($method === 'GET') {
            $csrf_token = CsrfTokenProvider::generate();
            return $view->render($response, 'creerEvenement.twig', [
                'csrf_token' => $csrf_token,
                'categories' => $categories
            ]);
        }

        $data = $request->getParsedBody();
        CsrfTokenProvider::check($data['csrf_token'] ?? null);

        $titre = $data['titre'] ?? '';
        $description = $data['description'] ?? ''; // markdown
        $tarif = $data['tarif'] ?? 0;
        $date_debut = $data['date_debut'] ?? null;
        $date_fin = $data['date_fin'] ?? null;
        $id_categorie = $data['id_categorie'] ?? null;
        $est_publie = isset($data['est_publie']) ? 1 : 0;


        $user = $this->authProvider->getSignedInUser();
        if (!$user) {
            return $view->render($response, 'error.twig', [
                'message' => 'Vous devez être connecté pour créer un événement.'
            ]);
        }

        // Création de l'événement (à adapter selon ton service)
        $id = $this->evenementService->createEvenement([
            'titre' => $titre,
            'description' => $description, // markdown
            'tarif' => $tarif,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'id_categorie' => $id_categorie,
            'image' => $image,
            'est_publie' => $est_publie,
            'createur_id' => $user->id
        ]);

        return $view->render($response, 'evenementCree.twig', [
            'success' => true,
            'titre' => $titre
        ]);
    }
}