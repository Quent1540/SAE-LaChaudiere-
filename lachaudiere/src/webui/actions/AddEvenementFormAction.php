<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddEvenementFormAction {
    protected EvenementServiceInterface $evenementService;

    public function __construct(EvenementServiceInterface $evenementService) {
        $this->evenementService = $evenementService;
    }

    public function __invoke(Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $categories = $this->evenementService->getCategories();
        return $view->render($response, 'creerEvenement.twig', [
            'categories' => $categories
        ]);
    }
}