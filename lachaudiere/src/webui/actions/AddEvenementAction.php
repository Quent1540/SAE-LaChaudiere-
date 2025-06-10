<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\useCases\CategoriesServiceInterface;
use lachaudiere\application_core\application\useCases\EvenementService;
use lachaudiere\application_core\application\useCases\ImagesEvenementService;
use lachaudiere\webui\providers\AuthnProvider;
use lachaudiere\webui\providers\CsrfTokenProvider;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddEvenementAction {
    protected AuthnProvider $authProvider;
    protected $evenementService;
    protected $categoriesService;
    protected $imagesEvenementService;

    public function __construct(
        AuthnProvider              $authProvider,
        EvenementService           $evenementService,
        CategoriesServiceInterface $categoriesService,
        ImagesEvenementService     $imagesEvenementService
    ) {
        $this->authProvider = $authProvider;
        $this->evenementService = $evenementService;
        $this->categoriesService = $categoriesService;
        $this->imagesEvenementService = $imagesEvenementService;
    }

    public function __invoke(Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $data = $request->getParsedBody();
        CsrfTokenProvider::check($data['csrf_token'] ?? null);

        $titre = $data['titre'] ?? '';
        $description = $data['description'] ?? '';
        $tarif = $data['tarif'] ?? 0;
        $date_debut = $data['date_debut'] ?? null;
        $date_fin = $data['date_fin'] ?? null;
        $id_categorie = $data['id_categorie'] ?? null;
        $est_publie = isset($data['est_publie']) ? 1 : 0;

        $legende = $data['legende'] ?? 'Image principale';
        $ordre_affichage = 0;

        $user = $this->authProvider->getSignedInUser();
        if (!$user) {
            return $view->render($response, 'error.twig', [
                'message' => 'Vous devez être connecté pour créer un événement.'
            ]);
        }

        $id = $this->evenementService->createEvenement([
            'titre' => $titre,
            'description' => $description,
            'tarif' => $tarif,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'id_categorie' => $id_categorie,
            'est_publie' => $est_publie,
            'id_utilisateur_creation' => $user->id_utilisateur,
        ]);

        //a modifier
        $uploadedFiles = $request->getUploadedFiles();
        $imageFile = $uploadedFiles['image'] ?? null;
        $url_image = null;
        if ($imageFile && $imageFile->getError() === UPLOAD_ERR_OK) {
            $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $imageFile->getClientFilename());
            $uploadDir = __DIR__ . '/../../../public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $imageFile->moveTo($uploadDir . $filename);
            $url_image = '/uploads/' . $filename;
        }

        if ($url_image) {
            $this->imagesEvenementService->addImageEvenement([
                'id_evenement' => $id,
                'url_image' => $url_image,
                'legende' => $legende,
                'ordre_affichage' => $ordre_affichage
            ]);
        }

        return $view->render($response, 'evenementCree.twig', [
            'success' => true,
            'titre' => $titre
        ]);
    }
}