<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\domain\entities\ImagesEvenement;
use Illuminate\Database\QueryException;
use Exception;
use Psr\Http\Message\UploadedFileInterface;

//Service pour la gestion des images associées aux événements
class ImagesEvenementService implements ImagesEvenementServiceInterface {
    //Chemin de stockage des images uploadées
    private string $uploadPath;

    public function __construct(string $uploadPath) {
        //Le chemin doit se terminer par un /
        $this->uploadPath = rtrim($uploadPath, '/') . '/';
    }

    //Recup toutes les images associées à un événement
    public function getImagesByEvenement(int $id_evenement): array {
        try {
            $images = ImagesEvenement::where('id_evenement', $id_evenement)->get();
            return $images->toArray();
        } catch (QueryException $e) {
            throw new Exception('Erreur lors de la récupération des images : ' . $e->getMessage());
        }
    }

    //Upload une image et crée son enregistrement en base pour un événement
    public function uploadAndCreateImage(
        int $id_evenement,
        UploadedFileInterface $file,
        ?string $legende,
        int $ordre_affichage = 0
    ): int {
        //Vérif l'absence d'erreur lors de l'upload
        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Erreur lors de l\'upload du fichier.');
        }

        //Vérif le type MIME autorisé
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getClientMediaType(), $allowedTypes)) {
            throw new \RuntimeException('Type de fichier non autorisé.');
        }

        //Créé le répertoire de stockage s'il n'existe pas
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }

        //Génère un nom de fichier unique pour éviter les collisions
        $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $file->getClientFilename());
        $file->moveTo($this->uploadPath . $filename);

        // $relativePath = '/uploads/' . $filename;
        $relativePath = '/api/images/'. $filename;

        //Ajouter l'image à la base
        return $this->addImageEvenement([
            'id_evenement' => $id_evenement,
            'url_image' => $relativePath,
            'legende' => $legende ?? 'Image principale',
            'ordre_affichage' => $ordre_affichage
        ]);
    }

    //Ajoute une image d'événement en base
    public function addImageEvenement(array $data): int {
        try {
            $image = new ImagesEvenement();
            $image->id_evenement = $data['id_evenement'];
            $image->url_image = $data['url_image'];
            $image->legende = $data['legende'] ?? null;
            $image->ordre_affichage = $data['ordre_affichage'] ?? 0;
            $image->save();

            return $image->id_image;
        } catch (QueryException $e) {
            throw new Exception('Erreur lors de l\'ajout de l\'image en base de données : ' . $e->getMessage());
        }
    }
}