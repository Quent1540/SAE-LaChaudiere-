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
        $filename = $args['filename'];

        if ($filename !== basename($filename)) {
            throw new HttpNotFoundException($request, "Accès non autorisé.");
        }

        $imagePath = __DIR__ . '/../../../public/uploads/' . $filename;
        // echo $imagePath;
        // die();

        if (!file_exists($imagePath)) {
            throw new HttpNotFoundException($request, "Image non trouvée.");
        }

        $imageContent = file_get_contents($imagePath);
        $finfo = new \finfo(FILEINFO_MIME_TYPE); 
        $mimeType = $finfo->file($imagePath);

        $response->getBody()->write($imageContent);

        return $response
            ->withHeader('Content-Type', $mimeType)
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
    }
}