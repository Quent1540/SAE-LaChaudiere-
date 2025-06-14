<?php
namespace lachaudiere\webui\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use lachaudiere\application_core\domain\entities\Categorie;
use lachaudiere\webui\providers\CsrfTokenProvider;
use lachaudiere\webui\providers\CsrfTokenException;

class AddCategorieAction {
    public function __invoke(Request $request, Response $response, array $args): Response {
        $view = Twig::fromRequest($request);
        $data = $request->getParsedBody();
        $erreur = null;

        try {
            CsrfTokenProvider::check($data['csrf_token'] ?? null);
        } catch (CsrfTokenException $e) {
            $erreur = "Erreur de sécurité. Veuillez réessayer.";
            return $view->render($response->withStatus(403), 'ajouterCategorie.twig', ['erreur' => $erreur]);
        }
        
        $libelle = filter_var(trim($data['libelle'] ?? ''), FILTER_SANITIZE_STRING);
        $description = filter_var(trim($data['description'] ?? ''), FILTER_SANITIZE_STRING);

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
        
        return $view->render($response->withStatus(400), 'ajouterCategorie.twig', [
            'erreur' => $erreur
        ]);
    }
}