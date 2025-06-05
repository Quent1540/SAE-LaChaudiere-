<?php
namespace lachaudiere\webui\actions;

use lachaudiere\webui\providers\AuthnProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class SigninAction {
    protected AuthnProviderInterface $authProvider;

    public function __construct(AuthnProviderInterface $authProvider) {
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response, $args) {
        $error = null;
        //On verifie si cest un post(si on valide le formulaire) sinon(si on arrive sur la page depuis le menu principal), on ne fait rien
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            if ($this->authProvider->signin($email, $password)) {
                return $response
                    ->withHeader('Location', '/')
                    ->withStatus(302);
            }
            $error = "Identifiants invalides.";
        }
        $user = $this->authProvider->getSignedInUser();
        $view = Twig::fromRequest($request);
        return $view->render($response, 'signin.twig', [
            'error' => $error,
            'user' => $user
        ]);
    }
}