<?php
namespace lachaudiere\application_core\application\useCases;

interface EvenementServiceInterface {
    public function getCategories(): array;
    public function getEvenements(): array;
    public function getEvenementsParCategorie(int $id): array;
    public function getEvenementParId(int $id_evenement): array;
    public function getEvenementsAvecCategorie(): array;
    public function togglePublishStatus(int $id_evenement): bool;
}