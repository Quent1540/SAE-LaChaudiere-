<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\exceptions\UserAlreadyExistsException;
use lachaudiere\webui\providers\AuthnProviderInterface;
use lachaudiere\webui\providers\CsrfTokenProvider;
use lachaudiere\webui\providers\CsrfTokenException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class RegisterAction {
    protected AuthnProviderInterface $authProvider;
    protected Twig $view;

    public function __construct(AuthnProviderInterface $authProvider, Twig $view) {
        $this->authProvider = $authProvider;
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $response, $args): Response {
        if ($this->authProvider->getRoleUser() !== 0) {
            $response->getBody()->write('<p>Vous n\'avez pas les droits suffisants pour accéder à cette page.</p>');
            return $response->withStatus(403);
        }

        $error = null;
        $success = null;
        $submitted_data = [];

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $email = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $data['password'] ?? '';
            $password_confirm = $data['password_confirm'] ?? '';
            
            $submitted_data['email'] = $email;

            try {
                CsrfTokenProvider::check($data['csrf_token'] ?? null);

                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "L'email est obligatoire et doit être une adresse valide.";
                } elseif (empty($password)) {
                    $error = "Le mot de passe est obligatoire.";
                } elseif ($password !== $password_confirm) {
                    $error = "Les mots de passe ne correspondent pas.";
                } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password)) {
                    $error = "Le mot de passe doit contenir au moins 8 caractères et une majuscule.";
                } else {
                    if ($this->authProvider->register($email, $password)) {
                        $success = "L'utilisateur a été créé avec succès.";
                        $submitted_data = [];
                    } else {
                        $error = "Une erreur inconnue est survenue lors de l'enregistrement.";
                    }
                }
            } catch (CsrfTokenException $e) {
                $error = "Erreur de sécurité. Veuillez soumettre le formulaire à nouveau.";
            } catch (UserAlreadyExistsException $e) {
                $error = $e->getMessage();
            } catch (\Exception $e) {
                $error = "Une erreur technique est survenue. Veuillez réessayer plus tard.";
            }
        }

        return $this->view->render($response, 'register.twig', [
            'error' => $error,
            'success' => $success,
            'user' => $this->authProvider->getSignedInUser(),
            'submitted_data' => $submitted_data
        ]);
    }
}