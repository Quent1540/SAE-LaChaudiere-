<?php
namespace lachaudiere\webui\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use lachaudiere\webui\providers\AuthnProviderInterface;
use Slim\Routing\RouteContext;

class SignoutAction {
    protected AuthnProviderInterface $authProvider;

    public function __construct(AuthnProviderInterface $authProvider) {
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $this->authProvider->signout();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response->withHeader('Location', $routeParser->urlFor('signin'))->withStatus(302);
    }
}