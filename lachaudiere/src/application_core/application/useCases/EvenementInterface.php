<?php
namespace lachaudiere\application_core\application\useCases;

interface EvenementInterface {
    public function getCategories(): array;
    public function getEvenements(): array;
    public function getEvenementsParCategorie(int $id): array;
    public function getEvenementParId(int $id_evenement): array;
}