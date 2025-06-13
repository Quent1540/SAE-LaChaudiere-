<?php
namespace lachaudiere\webui\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use lachaudiere\application_core\domain\entities\Categorie;
use lachaudiere\webui\providers\CsrfTokenProvider;

//Action pour ajouter une nouvelle catégorie
class AddCategorieAction {
    public function __invoke(Request $request, Response $response, array $args): Response {
        //Rendu des vues avec Twig
        $view = Twig::fromRequest($request);
        //Recup des données du formulaire POST
        $data = $request->getParsedBody();
        $erreur = null;

        //Vérif du token CSRF
        $submittedToken = $data['csrf_token'] ?? null;
        try {
            CsrfTokenProvider::check($submittedToken);
        } catch (CsrfTokenException $e) {
            $error = "Erreur de sécurité. Veuillez réessayer.";
        }

        //Vider les champs du formulaire
        $libelle = trim($data['libelle'] ?? '');
        $description = trim($data['description'] ?? '');

        //Validation des champs
        if (empty($libelle)) {
            $erreur = "Le libellé est requis.";
        } else {
            try {
                //Création de la nouvelle catégorie
                Categorie::create([
                    'libelle' => $libelle,
                    'description' => $description,
                ]);
                //Redirection vers la liste des catégories
                return $response
                    ->withHeader('Location', '/categories')
                    ->withStatus(302);
            } catch (\Exception $e) {
                $erreur = "Erreur lors de la création : " . $e->getMessage();
            }
        }
        //Affichage du formulaire avec les erreurs si besoin
        return $view->render($response, 'ajouterCategorie.twig', [
            'erreur' => $erreur
        ]);
    }
}