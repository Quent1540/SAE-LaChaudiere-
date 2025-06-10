<?php
namespace lachaudiere\webui\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use lachaudiere\application_core\domain\entities\Categorie;

class AddCategorieAction {
    public function __invoke(Request $request, Response $response, array $args): Response {
        $view = Twig::fromRequest($request);
        $data = $request->getParsedBody();
        $erreur = null;

        $libelle = trim($data['libelle'] ?? '');
        $description = trim($data['description'] ?? '');

        if (empty($libelle)) {
            $erreur = "Le libellé est requis.";
        } else {
            try {
                Categorie::create([
                    'libelle' => $libelle,
                    'description' => $description,
                ]);
                return $response
                    ->withHeader('Location', '/categories')
                    ->withStatus(302);
            } catch (\Exception $e) {
                $erreur = "Erreur lors de la création : " . $e->getMessage();
            }
        }

        return $view->render($response, 'ajouterCategorie.twig', [
            'erreur' => $erreur
        ]);
    }
}