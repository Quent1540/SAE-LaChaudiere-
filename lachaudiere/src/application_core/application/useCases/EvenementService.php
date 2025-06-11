<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\application\exceptions\EvenementException;
use lachaudiere\application_core\domain\entities\Categorie;
use Illuminate\Database\QueryException;
use lachaudiere\application_core\domain\entities\Evenement;

class EvenementService implements EvenementServiceInterface {
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
            $evenements = Evenement::with('categorie')
                ->orderBy('date_debut', 'asc')
                ->get();
            return $evenements->toArray();
        } catch (\Exception $e) {
            throw new EvenementException("Erreur lors de la récupération : " . $e->getMessage());
        }
    }

    public function getEvenementParId(int $id_evenement): array {
        try {
            $evenement = Evenement::findOrFail($id_evenement);
            return $evenement->toArray();
        } catch (\Exception $e) {
            throw new EvenementException('Événement introuvable : ' . $e->getMessage());
        }
    }

    public function createEvenement(array $data)
    {
        try {
            $evenement = new Evenement();
            $evenement->titre = $data['titre'] ?? '';
            $evenement->description = $data['description'] ?? '';
            $evenement->tarif = $data['tarif'] ?? 0;
            $evenement->date_debut = $data['date_debut'] ?? null;
            $evenement->date_fin = $data['date_fin'] ?? null;
            $evenement->id_categorie = $data['id_categorie'] ?? null;
            $evenement->est_publie = $data['est_publie'] ?? 0;
            $evenement->id_utilisateur_creation = $data['id_utilisateur_creation'] ?? null;
            $evenement->save();

            return $evenement->id_evenement;
        } catch (\Exception $e) {
            throw new EvenementException('Erreur lors de la création de l\'événement : ' . $e->getMessage());
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
}