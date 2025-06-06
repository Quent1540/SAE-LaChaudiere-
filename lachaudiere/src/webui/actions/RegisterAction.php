<?php
namespace lachaudiere\webui\actions;

use lachaudiere\webui\providers\AuthnProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class RegisterAction {
    protected AuthnProviderInterface $authProvider;

    public function __construct(AuthnProviderInterface $authProvider) {
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response, $args) {
        $error = null;
        $success = null;
        if($this->authProvider->getRoleUser() !== 0 ) {
            $error = "Vous devez être un superadmin pour créer un utilisateur.";
        }
        else{
            if ($request->getMethod() === 'POST') {
                $data = $request->getParsedBody();
                $email = $data['email'] ?? '';
                $password = $data['password'] ?? '';
                $password_confirm = $data['password_confirm'] ?? '';

                if ($password !== $password_confirm) {
                    $error = "Les mots de passe ne correspondent pas.";
                } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password)) {
                    $error = "Le mot de passe doit contenir au moins 8 caractères et une majuscule.";
                } elseif ($this->authProvider->register($email, $password)) {
                    $success = "L'utilisateur a été créé avec succès.";
                } else {
                    $error = "Cet email est déjà utilisé.";
                }
            }
        }
        $user = $this->authProvider->getSignedInUser();
        $view = Twig::fromRequest($request);
        return $view->render($response, 'register.twig', [
            'error' => $error,
            'success' => $success,
            'user' => $user
        ]);
    }
}