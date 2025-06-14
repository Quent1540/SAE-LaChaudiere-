<?php
namespace lachaudiere\application_core\application\useCases;
use lachaudiere\application_core\domain\entities\Categorie;


interface EvenementServiceInterface {
    public function getEvenements(): array;
    public function getEvenementsParCategorie(int $id): array;
    public function getEvenementParId(int $id_evenement): array;
    public function getEvenementsAvecCategorie(): array;
    public function togglePublishStatus(int $id_evenement): bool;
    public function getCategories(): array;
    public function getCategorieById(int $id_categorie): ?Categorie;
    public function createCategorie(string $libelle, string $description): Categorie;
}