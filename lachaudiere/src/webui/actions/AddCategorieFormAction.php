<?php
namespace lachaudiere\webui\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

//Action pour afficher le formulaire d'ajout de catégorie
class AddCategorieFormAction {
    public function __invoke(Request $request, Response $response, array $args): Response {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'ajouterCategorie.twig', [
            'erreur' => null
        ]);
    }
}