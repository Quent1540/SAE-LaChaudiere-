<?php
namespace gift\appli\application_core\application\useCases;

use gift\appli\application_core\application\exceptions\CatalogueException;
use gift\appli\application_core\domain\entities\Categorie;
use gift\appli\application_core\domain\entities\Prestation;
use Illuminate\Database\QueryException;

class Catalogue implements CatalogueInterface {
    public function getCategories(): array {
        try {
            $result = Categorie::all();
            return $result->toArray();
        } catch (\Exception $e) {
            throw new CatalogueException('Erreur lors de la récupération des catégories : ' . $e->getMessage());
        }
    }

    public function getPrestations(): array {
        try {
            $result = Prestation::all();
            return $result->toArray();
        } catch (QueryException $e) {
            throw new CatalogueException('Erreur lors de la récupération des prestations : ' . $e->getMessage());
        }
    }

    public function getCategorieById(int $id): array {
        try {
            return Categorie::findOrFail($id)->toArray();
        } catch (\Exception $e) {
            throw new CatalogueException('Catégorie introuvable');
        }
    }

    public function getPrestationById(string $id): array {
        try {
            return Prestation::findOrFail($id)->toArray();
        } catch (\Exception $e) {
            throw new CatalogueException('Prestation introuvable');
        }
    }

    public function getPrestationsByCategorie(int $categ_id): array {
        try {
            return Prestation::where('cat_id', $categ_id)->get()->toArray();
        } catch (QueryException $e) {
            throw new CatalogueException('Erreur lors de la récupération des prestations'.
                ' : ' . $e->getMessage());
        }
    }
}