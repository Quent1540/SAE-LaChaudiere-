<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\useCases\CategoriesServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Exception\HttpNotFoundException;

class GetCategorieParIdAction {
    private CategoriesServiceInterface $catalogue;

    public function __construct(CategoriesServiceInterface $catalogue) {
        $this->catalogue = $catalogue;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $id = (int) $args['id'];
            $categorie = $this->catalogue->getCategorieById($id);
            $view = Twig::fromRequest($request);
            return $view->render($response, 'categorieParId.twig', ['categorie' => $categorie]);
        } catch (EvenementException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }
    }
}