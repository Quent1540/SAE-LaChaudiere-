<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\exceptions\UserAlreadyExistsException;
use lachaudiere\application_core\application\exceptions\ValidationException;
use lachaudiere\webui\providers\AuthnProviderInterface;
use lachaudiere\webui\providers\CsrfTokenProvider;
use lachaudiere\webui\providers\CsrfTokenException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use lachaudiere\application_core\application\useCases\AuthnServiceInterface;

class RegisterAction {
    protected AuthnProviderInterface $authProvider;
    protected Twig $view;
    protected AuthnServiceInterface $authnService;

    public function __construct(
        AuthnProviderInterface $authProvider,
        AuthnServiceInterface $authnService,
        Twig $view
    ) {
        $this->authProvider = $authProvider;
        $this->authnService = $authnService;
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
            $email = trim($data['email'] ?? '');
            $password = $data['password'] ?? '';
            $password_confirm = $data['password_confirm'] ?? '';
            
            $submitted_data['email'] = $email;

            try {
                CsrfTokenProvider::check($data['csrf_token'] ?? null);

                $this->authnService->registerUser($email, $password, $password_confirm);
                
                $success = "L'utilisateur a été créé avec succès.";
                $submitted_data = [];

            } catch (CsrfTokenException $e) {
                $error = "Erreur de sécurité. Veuillez soumettre le formulaire à nouveau.";
            } catch (ValidationException | UserAlreadyExistsException $e) {
                $error = $e->getMessage();
            } catch (\Exception $e) {
                error_log("Erreur d'enregistrement : " . $e->getMessage());
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