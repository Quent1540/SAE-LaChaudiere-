<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\application\exceptions\EvenementException;
use lachaudiere\application_core\domain\entities\Categorie;
use Illuminate\Database\QueryException;
use lachaudiere\application_core\domain\entities\Evenement;
use Psr\Http\Message\UploadedFileInterface;

class EvenementService implements EvenementServiceInterface {

    private ImagesEvenementServiceInterface $imagesService;

    public function __construct(ImagesEvenementServiceInterface $imagesService)
    {
        $this->imagesService = $imagesService;
    }

    public function getCategories(): array {
        try {
            $result = Categorie::all();
            return $result->toArray();
        } catch (\Exception $e) {
            throw new EvenementException('Erreur lors de la récupération des catégories : ' . $e->getMessage());
        }
    }

    public function getEvenements(): array {
        try {
            $result = Evenement::with('categorie')->get();
            return $result->toArray();
        } catch (QueryException $e) {
            throw new EvenementException('Erreur lors de la récupération des événements : ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new EvenementException('Erreur inconnue : ' . $e->getMessage());
        }
    }

    public function getEvenementsParCategorie(int $id_categorie): array {
        try {
            $result = Evenement::where('id_categorie', $id_categorie)->get();
            return $result->toArray();
        } catch (QueryException $e) {
            throw new EvenementException('Erreur lors de la récupération des événements par catégorie : ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new EvenementException('Erreur inconnue : ' . $e->getMessage());
        }
    }

    public function getEvenementsAvecCategorie(): array {
        try {
            $evenements = Evenement::with(['categorie', 'images'])
                ->orderBy('date_debut', 'asc')
                ->get();
            return $evenements->toArray();
        } catch (\Exception $e) {
            throw new EvenementException("Erreur lors de la récupération : " . $e->getMessage());
        }
    }

    public function getEvenementParId(int $id_evenement): array {
        try {
            $evenement = Evenement::with(['images', 'categorie'])->findOrFail($id_evenement);
            return $evenement->toArray();
        } catch (\Exception $e) {
            throw new EvenementException('Événement introuvable : ' . $e->getMessage());
        }
    }


    public function togglePublishStatus(int $id_evenement): bool
    {
        try {
            $evenement = Evenement::findOrFail($id_evenement);
            $evenement->est_publie = !$evenement->est_publie;
            return $evenement->save();
        } catch (\Exception $e) {
            throw new EvenementException('Erreur lors de la mise à jour du statut de publication : ' . $e->getMessage());
        }
    }

    public function createEvenementWithImage(array $data, ?UploadedFileInterface $imageFile): int
    {
        try {
            \Illuminate\Database\Capsule\Manager::beginTransaction();

            $evenement = new Evenement();
            $evenement->fill($data);
            $evenement->save();
            
            if ($imageFile && $imageFile->getError() === UPLOAD_ERR_OK) {
                $this->imagesService->uploadAndCreateImage(
                    $evenement->id_evenement,
                    $imageFile,
                    $data['legende'] ?? null
                );
            }
            
            \Illuminate\Database\Capsule\Manager::commit();

            return $evenement->id_evenement;

        } catch (\Exception $e) {
            \Illuminate\Database\Capsule\Manager::rollback();
            throw new EvenementException('Erreur lors de la création de l\'événement : ' . $e->getMessage());
        }
    }
}