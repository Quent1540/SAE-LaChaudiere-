<?php

declare(strict_types=1);

namespace lachaudiere\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

class GetImageAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        //Récup le nom du fichier depuis les paramètres d'URL
        $filename = $args['filename'];

        //Sécurité : refuse les chemins contenant des sous-répertoires
        if ($filename !== basename($filename)) {
            throw new HttpNotFoundException($request, "Accès non autorisé.");
        }

        //Chemin absolu vers le fichier image
        $imagePath = __DIR__ . '/../../../public/uploads/' . $filename;
        // echo $imagePath;
        // die();

        if (!file_exists($imagePath)) {
            throw new HttpNotFoundException($request, "Image non trouvée.");
        }

        //Lit le contenu du fichier image
        $imageContent = file_get_contents($imagePath);
        //Détermine le type MIME du fichier (ex: image/jpeg)
        $finfo = new \finfo(FILEINFO_MIME_TYPE); 
        $mimeType = $finfo->file($imagePath);

        // Écrit le contenu de l'image dans la réponse HTTP
        $response->getBody()->write($imageContent);

        return $response
            ->withHeader('Content-Type', $mimeType)
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
    }
}