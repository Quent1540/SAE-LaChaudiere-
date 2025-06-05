<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\exceptions\EvenementException;
use lachaudiere\application_core\application\useCases\EvenementInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Exception\HttpNotFoundException;

class GetCategorieParIdAction {
    private EvenementInterface $catalogue;

    public function __construct(EvenementInterface $catalogue) {
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