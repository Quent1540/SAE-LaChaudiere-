<?php
namespace lachaudiere\webui\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use lachaudiere\application_core\application\useCases\EvenementService;
use Slim\Views\Twig;

class ListeEvenementsAction {
    protected EvenementService $evenementService;
    protected Twig $view;

    public function __construct(EvenementService $evenementService, Twig $view) {
        $this->evenementService = $evenementService;
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $evenements = $this->evenementService->getEvenements();
        usort($evenements, function($a, $b) {
            return strtotime($a['date_debut']) <=> strtotime($b['date_debut']);
        });
        return $this->view->render($response, 'liste_evenements.twig', [
            'evenements' => $evenements,
        ]);
    }
}