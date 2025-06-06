<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\domain\entities\ImagesEvenement;
use Illuminate\Database\QueryException;
use Exception;

class ImagesEvenementService implements ImagesEvenementServiceInterface
{
    public function getImagesByEvenement(int $id_evenement): array
    {
        try {
            $images = ImagesEvenement::where('id_evenement', $id_evenement)->get();
            return $images->toArray();
        } catch (QueryException $e) {
            throw new Exception('Erreur lors de la récupération des images : ' . $e->getMessage());
        }
    }

    public function addImageEvenement(array $data): int
    {
        try {
            $image = new ImagesEvenement();
            $image->id_evenement = $data['id_evenement'];
            $image->url_image = $data['url_image'];
            $image->legende = $data['legende'] ?? null;
            $image->ordre_affichage = $data['ordre_affichage'] ?? 0;
            $image->save();

            return $image->id_image;
        } catch (QueryException $e) {
            throw new Exception('Erreur lors de l\'ajout de l\'image : ' . $e->getMessage());
        }
    }
}