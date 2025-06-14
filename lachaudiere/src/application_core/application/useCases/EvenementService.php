<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\application\exceptions\EvenementException;
use lachaudiere\application_core\domain\entities\Categorie;
use Illuminate\Database\QueryException;
use lachaudiere\application_core\domain\entities\Evenement;
use Psr\Http\Message\UploadedFileInterface;

//Service pour la gestion des événements
class EvenementService implements EvenementServiceInterface {

    private ImagesEvenementServiceInterface $imagesService;

    public function __construct(ImagesEvenementServiceInterface $imagesService) {
        $this->imagesService = $imagesService;
    }

    //Recup toutes les catégories
    public function getCategories(): array {
        try {
            $result = Categorie::all();
            return $result->toArray();
        } catch (\Exception $e) {
            throw new EvenementException('Erreur lors de la récupération des catégories : ' . $e->getMessage());
        }
    }

    public function getCategorieById(int $id_categorie): ?Categorie {
        try {
            $result = Categorie::find($id_categorie);
            return $result;
        } catch (\Exception $e) {
            throw new EvenementException('Erreur lors de la récupération des catégories : ' . $e->getMessage());
        }
    }

    //Recup tous les événements avec leurs catégories
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

    //Recup les événements d'une catégorie donnée
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

    //Recup les événements avec leurs catégories et images, triés par date de début
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

    //Recup un événement par son id avec ses images et catégorie
    public function getEvenementParId(int $id_evenement): array {
        try {
            $evenement = Evenement::with(['images', 'categorie'])->findOrFail($id_evenement);
            return $evenement->toArray();
        } catch (\Exception $e) {
            throw new EvenementException('Événement introuvable : ' . $e->getMessage());
        }
    }

    //Bascule le statut de publication d'un événement (publié/non publié)
    public function togglePublishStatus(int $id_evenement): bool {
        try {
            $evenement = Evenement::findOrFail($id_evenement);
            $evenement->est_publie = !$evenement->est_publie;
            return $evenement->save();
        } catch (\Exception $e) {
            throw new EvenementException('Erreur lors de la mise à jour du statut de publication : ' . $e->getMessage());
        }
    }

    //Créé un événement et ajoute une image si fournie
    public function createEvenementWithImage(array $data, ?UploadedFileInterface $imageFile): int {
        try {
            //Transaction
            \Illuminate\Database\Capsule\Manager::beginTransaction();

            $evenement = new Evenement();
            $evenement->fill($data);
            $evenement->save();

            //Si une image est fournie est valide, on l'ajoute à l'événement
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