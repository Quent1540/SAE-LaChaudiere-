<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\application\exceptions\EvenementException;
use lachaudiere\application_core\application\exceptions\ValidationException;
use lachaudiere\application_core\domain\entities\Categorie;
use Illuminate\Database\QueryException;
use lachaudiere\application_core\domain\entities\Evenement;
use Psr\Http\Message\UploadedFileInterface;

class EvenementService implements EvenementServiceInterface {

    private ImagesEvenementServiceInterface $imagesService;

    public function __construct(ImagesEvenementServiceInterface $imagesService) {
        $this->imagesService = $imagesService;
    }

    public function findEvenements(array $criteria = []): array {
        $query = Evenement::query();

        if (empty($criteria['include_unpublished'])) {
            $query->where('est_publie', true);
        }

        if (!empty($criteria['id_categorie'])) {
            $query->where('id_categorie', $criteria['id_categorie']);
        }

        if (!empty($criteria['periode'])) {
            $now = date('Y-m-d H:i:s');
            switch ($criteria['periode']) {
                case 'passee':
                    $query->where('date_fin', '<', $now);
                    break;
                case 'future':
                    $query->where('date_debut', '>', $now);
                    break;
                case 'courante':
                    $query->where('date_debut', '<=', $now)
                          ->where('date_fin', '>=', $now);
                    break;
            }
        }

        $sortOrder = $criteria['sort'] ?? 'date-asc';
        switch ($sortOrder) {
            case 'date-desc':
                $query->orderBy('date_debut', 'desc');
                break;
            case 'titre':
                $query->orderBy('titre', 'asc');
                break;
            case 'date-asc':
            default:
                $query->orderBy('date_debut', 'asc');
                break;
        }

        $query->with(['categorie', 'images']);
        return $query->get()->toArray();
    }

    public function getEvenements(): array {
        return $this->findEvenements(['include_unpublished' => true]);
    }

    public function getEvenementsAvecCategorie(): array {
        return $this->findEvenements(['include_unpublished' => true, 'sort' => 'date-asc']);
    }

    public function getEvenementsParCategorie(int $id_categorie): array {
        return $this->findEvenements([
            'id_categorie' => $id_categorie,
            'include_unpublished' => true,
            'sort' => 'date-asc'
        ]);
    }

    public function getCategories(): array {
        try {
            return Categorie::all()->toArray();
        } catch (\Exception $e) {
            throw new EvenementException('Erreur lors de la récupération des catégories : ' . $e->getMessage());
        }
    }

    public function getCategorieById(int $id_categorie): ?Categorie {
        return Categorie::find($id_categorie);
    }

    public function getEvenementParId(int $id_evenement): array {
        try {
            $evenement = Evenement::with(['images', 'categorie'])->findOrFail($id_evenement);
            return $evenement->toArray();
        } catch (\Exception $e) {
            throw new EvenementException('Événement introuvable : ' . $e->getMessage());
        }
    }

    public function togglePublishStatus(int $id_evenement): bool {
        try {
            $evenement = Evenement::findOrFail($id_evenement);
            $evenement->est_publie = !$evenement->est_publie;
            return $evenement->save();
        } catch (\Exception $e) {
            throw new EvenementException('Erreur lors de la mise à jour du statut de publication : ' . $e->getMessage());
        }
    }

    public function createCategorie(string $libelle, string $description): Categorie {
        $safeLibelle = strip_tags(trim($libelle));
        if (empty($safeLibelle)) {
            throw new ValidationException("Le libellé de la catégorie est obligatoire.");
        }

        $safeDescription = strip_tags(trim($description));

        try {
            return Categorie::create([
                'libelle' => $safeLibelle,
                'description' => $safeDescription,
            ]);
        } catch (QueryException $e) {
            throw new EvenementException("Erreur de base de données lors de la création de la catégorie.", 0, $e);
        }
    }

    public function createEvenementWithImage(array $data, ?UploadedFileInterface $imageFile): int {
        $titre = strip_tags(trim($data['titre'] ?? ''));
        if (empty($titre)) {
            throw new ValidationException('Le titre est obligatoire.');
        }

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
            if ($e instanceof ValidationException) {
                throw $e;
            }
            throw new EvenementException('Erreur lors de la création de l\'événement : ' . $e->getMessage());
        }
    }
}