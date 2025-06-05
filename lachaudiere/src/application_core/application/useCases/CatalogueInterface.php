<?php
namespace gift\appli\application_core\application\useCases;

interface CatalogueInterface {
    public function getCategories(): array;
    public function getPrestations(): array;
    public function getCategorieById(int $id): array;
    public function getPrestationById(string $id): array;
    public function getPrestationsByCategorie(int $categ_id): array;
}