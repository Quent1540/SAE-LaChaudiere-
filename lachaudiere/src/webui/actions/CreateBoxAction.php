<?php
namespace gift\appli\webui\actions;

use gift\appli\application_core\application\useCases\Box;
use gift\appli\webui\providers\AuthnProvider;
use gift\appli\webui\providers\CsrfTokenProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CreateBoxAction {
    protected AuthnProvider $authProvider;

    public function __construct(AuthnProvider $authProvider) {
        $this->authProvider = $authProvider;
    }
    public function __invoke(Request $request, Response $response, $args)
    {
        $method = $request->getMethod();
        $view = Twig::fromRequest($request);

        if ($method === 'GET') {
            $csrf_token = CsrfTokenProvider::generate();
            return $view->render($response, 'createBox.twig', [
                'csrf_token' => $csrf_token
            ]);
        }

        $data = $request->getParsedBody();
        \gift\appli\webui\providers\CsrfTokenProvider::check($data['csrf_token'] ?? null);

        $libelle = $data['libelle'] ?? '';
        $description = $data['description'] ?? '';
        $kdo = isset($data['kdo']) ? 1 : 0;
        $message_kdo = $data['message_kdo'] ?? '';
        $montant = $data['montant'] ?? 0;
        $statut = 1;

        $user = $this->authProvider->getSignedInUser();
        if (!$user) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'error.twig', [
                'message' => 'Vous devez être connecté pour créer une box.'
            ]);
        }
        $createur_id = $user->id;

        $boxService = new Box();
        $id = $boxService->createBox($createur_id, $libelle, $description, $montant, $kdo, $message_kdo, $statut);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['box_id'] = $id;

        return $view->render($response, 'createBox.twig', [
            'success' => true,
            'libelle' => $libelle,
            'csrf_token' => CsrfTokenProvider::generate()
        ]);
    }
}