<?php
namespace gift\appli\webui\actions;

use gift\appli\webui\providers\AuthnProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SignoutAction {
    protected AuthnProviderInterface $authProvider;

    public function __construct(AuthnProviderInterface $authProvider) {
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response, $args) {
        //On supprime l'utilisateur en session
        unset($_SESSION['user_id']);
        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
    }
}