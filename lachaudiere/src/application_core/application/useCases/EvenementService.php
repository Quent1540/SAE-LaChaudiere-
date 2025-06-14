<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\application\exceptions\EvenementException;
use lachaudiere\application_core\application\exceptions\ValidationException;
use lachaudiere\application_core\domain\entities\Categorie;
use lachaudiere\application_core\domain\entities\Evenement;
use Illuminate\Database\QueryException;
use Psr\Http\Message\UploadedFileInterface;

class EvenementService implements EvenementServiceInterface {

    private ImagesEvenementServiceInterface $imagesService;

    public function __construct(ImagesEvenementServiceInterface $imagesService) {
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

    public function getCategorieById(int $id_categorie): ?Categorie {
        try {
            return Categorie::find($id_categorie);
        } catch (\Exception $e) {
            throw new EvenementException('Erreur lors de la récupération des catégories : ' . $e->getMessage());
        }
    }

    public function getEvenements(): array {
        try {
            return Evenement::with('categorie')->get()->toArray();
        } catch (QueryException $e) {
            throw new EvenementException('Erreur lors de la récupération des événements : ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new EvenementException('Erreur inconnue : ' . $e->getMessage());
        }
    }

    public function getEvenementsParCategorie(int $id_categorie): array {
        try {
            return Evenement::where('id_categorie', $id_categorie)->get()->toArray();
        } catch (QueryException $e) {
            throw new EvenementException('Erreur lors de la récupération des événements par catégorie : ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new EvenementException('Erreur inconnue : ' . $e->getMessage());
        }
    }

    public function getEvenementsAvecCategorie(): array {
        try {
            return Evenement::with(['categorie', 'images'])
                ->orderBy('date_debut', 'asc')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            throw new EvenementException("Erreur lors de la récupération : " . $e->getMessage());
        }
    }

    public function getEvenementParId(int $id_evenement): array {
        try {
            return Evenement::with(['images', 'categorie'])->findOrFail($id_evenement)->toArray();
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

    public function createEvenementWithImage(array $data, ?UploadedFileInterface $imageFile): int {
        $titre = strip_tags(trim($data['titre'] ?? ''));
        $tarif = strip_tags(trim($data['tarif'] ?? ''));
        $id_categorie = filter_var($data['id_categorie'] ?? null, FILTER_VALIDATE_INT, ['flags' => FILTER_NULL_ON_FAILURE]);
        $date_debut = $data['date_debut'] ?? null;
        $date_fin = $data['date_fin'] ?? null;

        if (empty($titre)) {
            throw new ValidationException('Le titre est obligatoire.');
        }
        if ($id_categorie === null) {
            throw new ValidationException('La catégorie est obligatoire et doit être valide.');
        }
        if ($date_debut && !\DateTime::createFromFormat('Y-m-d\TH:i', $date_debut)) {
            throw new ValidationException('Format de la date de début invalide.');
        }
        if (!empty($date_fin) && !\DateTime::createFromFormat('Y-m-d\TH:i', $date_fin)) {
            throw new ValidationException('Format de la date de fin invalide.');
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
}