<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\domain\entities\ImagesEvenement;
use Illuminate\Database\QueryException;
use Exception;

interface ImagesEvenementServiceInterface
{
    public function getImagesByEvenement(int $id_evenement): array;

    public function addImageEvenement(array $data): int;

}