<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use lachaudiere\webui\providers\AuthnProviderInterface;
use lachaudiere\webui\providers\CsrfTokenProvider;
use lachaudiere\webui\providers\CsrfTokenException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use lachaudiere\application_core\application\exceptions\ValidationException;

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

            $user = $this->authProvider->getSignedInUser();
            if (!$user) {
                return $view->render($response->withStatus(403), 'error.twig', ['message' => 'Vous devez être connecté.']);
            }
            
            // Préparer les données pour le service
            $eventData = [
                'titre' => $data['titre'] ?? '',
                'description' => $data['description'] ?? '',
                'tarif' => $data['tarif'] ?? '',
                'date_debut' => $data['date_debut'] ?? null,
                'date_fin' => $data['date_fin'] ?? null,
                'id_categorie' => $data['id_categorie'] ?? null,
                'est_publie' => isset($data['est_publie']) ? 1 : 0,
                'legende' => $data['legende'] ?? 'Image principale',
                'id_utilisateur_creation' => $user->id_utilisateur,
            ];

            $uploadedFiles = $request->getUploadedFiles();
            $imageFile = $uploadedFiles['image'] ?? null;

            $this->evenementService->createEvenementWithImage($eventData, $imageFile);

            return $view->render($response, 'evenementCree.twig', [
                'success' => true,
                'titre' => $data['titre']
            ]);

        } catch (CsrfTokenException $e) {
            return $view->render($response->withStatus(403), 'error.twig', ['message' => 'Erreur de sécurité. Le formulaire a peut-être expiré.']);
        } catch (ValidationException $e) {
            return $view->render($response->withStatus(400), 'error.twig', ['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log('Event Creation Failed: ' . $e->getMessage());
            return $view->render($response->withStatus(500), 'error.twig', ['message' => 'Une erreur interne est survenue.']);
        }
    }
}