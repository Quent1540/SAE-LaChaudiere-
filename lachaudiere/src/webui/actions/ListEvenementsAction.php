<?php
namespace lachaudiere\webui\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use lachaudiere\application_core\application\useCases\EvenementServiceInterface;

class ListEvenementsAction {
    private EvenementServiceInterface $evenementService;

    public function __construct(EvenementServiceInterface $evenementService) {
        $this->evenementService = $evenementService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $evenements = $this->evenementService->getEvenementsAvecCategorie();

        $view = Twig::fromRequest($request);
        return $view->render($response, 'listeEvenements.twig', [
            'evenements' => $evenements
        ]);
    }
}

