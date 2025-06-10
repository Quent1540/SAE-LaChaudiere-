<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use lachaudiere\webui\providers\CsrfTokenProvider;
use lachaudiere\webui\providers\CsrfTokenException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

class TogglePublishAction {
    private EvenementServiceInterface $evenementService;

    public function __construct(EvenementServiceInterface $evenementService) {
        $this->evenementService = $evenementService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $data = $request->getParsedBody();
            CsrfTokenProvider::check($data['csrf_token'] ?? null);

            $id_evenement = (int)$args['id'];
            $this->evenementService->togglePublishStatus($id_evenement);

        } catch (CsrfTokenException $e) {
            error_log('CSRF Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            error_log('Toggle Publish Error: ' . $e->getMessage());
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $redirectUrl = $routeParser->urlFor('list_evenements');

        return $response->withHeader('Location', $redirectUrl)->withStatus(302);
    }
}