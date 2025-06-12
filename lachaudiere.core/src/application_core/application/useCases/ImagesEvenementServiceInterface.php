<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\domain\entities\ImagesEvenement;
use Illuminate\Database\QueryException;
use Exception;
use Psr\Http\Message\UploadedFileInterface;


interface ImagesEvenementServiceInterface
{
    public function getImagesByEvenement(int $id_evenement): array;

    public function uploadAndCreateImage(
        int $id_evenement,
        UploadedFileInterface $file,
        ?string $legende,
        int $ordre_affichage = 0
    ): int;

}