<?php
namespace lachaudiere\webui\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use lachaudiere\webui\providers\AuthnProviderInterface;


class DashboardAction {
    protected Twig $view;
    protected AuthnProviderInterface $authProvider;

    public function __construct(Twig $view, AuthnProviderInterface $authProvider) {
        $this->view = $view;
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        return $this->view->render($response, 'dashboard.twig', [
            'auth_user' => $this->authProvider->getSignedInUser()
        ]);
    }
}