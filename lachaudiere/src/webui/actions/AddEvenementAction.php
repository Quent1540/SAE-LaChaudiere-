<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use lachaudiere\webui\providers\AuthnProviderInterface;
use lachaudiere\webui\providers\CsrfTokenProvider;
use lachaudiere\webui\providers\CsrfTokenException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class AddEvenementAction {
    private EvenementServiceInterface $evenementService;
    private AuthnProviderInterface $authProvider;

    public function __construct(
        EvenementServiceInterface $evenementService,
        AuthnProviderInterface $authProvider
    ) {
        $this->evenementService = $evenementService;
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response): Response {
        $view = Twig::fromRequest($request);
        $data = $request->getParsedBody();

        try {
            CsrfTokenProvider::check($data['csrf_token'] ?? null);
        } catch (CsrfTokenException $e) {
            return $view->render($response->withStatus(403), 'error.twig', [
                'message' => 'Erreur de sécurité. Le formulaire a peut-être expiré. Veuillez réessayer.'
            ]);
        }

        $titre = filter_var(trim($data['titre'] ?? ''), FILTER_SANITIZE_STRING);
        $description = filter_var(trim($data['description'] ?? ''), FILTER_SANITIZE_STRING);
        $legende = filter_var(trim($data['legende'] ?? 'Image principale'), FILTER_SANITIZE_STRING);
        
        $tarif = filter_var($data['tarif'] ?? 0, FILTER_VALIDATE_FLOAT, ['flags' => FILTER_NULL_ON_FAILURE]);
        $id_categorie = filter_var($data['id_categorie'] ?? null, FILTER_VALIDATE_INT, ['flags' => FILTER_NULL_ON_FAILURE]);
        $date_debut = $data['date_debut'] ?? null;
        $date_fin = $data['date_fin'] ?? null;

        if (empty($titre)) {
            return $view->render($response->withStatus(400), 'error.twig', ['message' => 'Le titre est obligatoire.']);
        }
        if ($tarif === null || $tarif < 0) {
            return $view->render($response->withStatus(400), 'error.twig', ['message' => 'Le tarif doit être un nombre positif.']);
        }
        if ($id_categorie === null) {
            return $view->render($response->withStatus(400), 'error.twig', ['message' => 'La catégorie est obligatoire et doit être valide.']);
        }
        if ($date_debut && !\DateTime::createFromFormat('Y-m-d\TH:i', $date_debut)) {
             return $view->render($response->withStatus(400), 'error.twig', ['message' => 'Format de la date de début invalide.']);
        }
        if (!empty($date_fin) && !\DateTime::createFromFormat('Y-m-d\TH:i', $date_fin)) {
             return $view->render($response->withStatus(400), 'error.twig', ['message' => 'Format de la date de fin invalide.']);
        }

        $user = $this->authProvider->getSignedInUser();
        if (!$user) {
            return $view->render($response, 'error.twig', ['message' => 'Vous devez être connecté.']);
        }

        try {
            $eventData = [
                'titre' => $titre,
                'description' => $description,
                'tarif' => $tarif,
                'date_debut' => $date_debut,
                'date_fin' => empty($date_fin) ? null : $date_fin,
                'id_categorie' => $id_categorie,
                'est_publie' => isset($data['est_publie']) ? 1 : 0,
                'legende' => $legende,
                'id_utilisateur_creation' => $user->id_utilisateur,
            ];

            $uploadedFiles = $request->getUploadedFiles();
            $imageFile = $uploadedFiles['image'] ?? null;

            $this->evenementService->createEvenementWithImage($eventData, $imageFile);

            return $view->render($response, 'evenementCree.twig', [
                'success' => true,
                'titre' => $titre
            ]);

        } catch (\Exception $e) {
            error_log('Event Creation Failed: ' . $e->getMessage());
            return $view->render($response->withStatus(500), 'error.twig', [
                'message' => 'Une erreur interne est survenue lors de la création de l\'événement.'
            ]);
        }
    }
}