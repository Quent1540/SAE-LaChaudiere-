<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use League\CommonMark\CommonMarkConverter;
use Slim\Exception\HttpNotFoundException;

//Action pour afficher les détails d'un événement
class GetEvenementDetailAction {
    private EvenementServiceInterface $evenementService;

    public function __construct(EvenementServiceInterface $evenementService) {
        $this->evenementService = $evenementService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            //Récup l'id de l'événement depuis les paramètres de l'URL
            $id_evenement = (int)$args['id'];
            $evenement = $this->evenementService->getEvenementParId($id_evenement);

            //Convertir la description Markdown en HTML
            $converter = new CommonMarkConverter();
            $htmlDescription = $converter->convert($evenement['description'] ?? '')->getContent();

            //Rendu de la vue avec Twig
            $view = Twig::fromRequest($request);
            return $view->render($response, 'evenementDetail.twig', [
                'evenement' => $evenement,
                'html_description' => $htmlDescription
            ]);
        } catch (\Exception $e) {
            throw new HttpNotFoundException($request, "L'événement demandé n'existe pas.");
        }
    }
}