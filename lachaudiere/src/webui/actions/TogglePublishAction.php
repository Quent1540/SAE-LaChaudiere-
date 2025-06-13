<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use lachaudiere\webui\providers\CsrfTokenProvider;
use lachaudiere\webui\providers\CsrfTokenException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

//Action pour basculer l'état de publication d'un événement
class TogglePublishAction {
    private EvenementServiceInterface $evenementService;

    public function __construct(EvenementServiceInterface $evenementService) {
        $this->evenementService = $evenementService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            //Récup des données du formulaire POST
            $data = $request->getParsedBody();
            //Vérification du token CSRF
            CsrfTokenProvider::check($data['csrf_token'] ?? null);

            //Récup de l'id de l'événement depuis les params d'URL
            $id_evenement = (int)$args['id'];
            //Changer l'état de publication de l'événement
            $this->evenementService->togglePublishStatus($id_evenement);

        } catch (CsrfTokenException $e) {
            error_log('CSRF Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            error_log('Toggle Publish Error: ' . $e->getMessage());
        }

        //Redirection vers la liste des événements
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $redirectUrl = $routeParser->urlFor('list_evenements');

        return $response->withHeader('Location', $redirectUrl)->withStatus(302);
    }
}