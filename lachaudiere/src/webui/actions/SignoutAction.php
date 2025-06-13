<?php
namespace lachaudiere\webui\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use lachaudiere\webui\providers\AuthnProviderInterface;
use Slim\Routing\RouteContext;

//Action pour la déconnexion de l'utilisateur
class SignoutAction {
    protected AuthnProviderInterface $authProvider;

    public function __construct(AuthnProviderInterface $authProvider) {
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        //Deco l'utilisateur
        $this->authProvider->signout();
        //Récup le routeur pour rediriger vers la page de connexion
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response->withHeader('Location', $routeParser->urlFor('signin'))->withStatus(302);
    }
}