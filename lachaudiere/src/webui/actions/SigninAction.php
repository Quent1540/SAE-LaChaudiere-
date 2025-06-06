<?php
namespace lachaudiere\webui\actions; 

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use lachaudiere\webui\providers\AuthnProviderInterface;
use lachaudiere\webui\providers\CsrfTokenProvider;
use lachaudiere\webui\providers\CsrfTokenException;
use Slim\Routing\RouteContext;

class SigninAction {
    protected AuthnProviderInterface $authProvider;
    protected Twig $view;

   
    public function __construct(AuthnProviderInterface $authProvider, Twig $view) {
        $this->authProvider = $authProvider;
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $routeContext = RouteContext::fromRequest($request);
        $router = $routeContext->getRouteParser();

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $submittedToken = $data['csrf_token'] ?? null;
            $email = trim($data['email'] ?? ''); 
            $password = $data['password'] ?? '';

            try {
                CsrfTokenProvider::check($submittedToken);

                if (empty($email) || empty($password)) {
                    throw new \InvalidArgumentException("Email et mot de passe sont requis.");
                }

                if ($this->authProvider->signin($email, $password)) {
                    return $response->withHeader('Location', $router->urlFor('admin.dashboard'))->withStatus(302);
                } else {
                    throw new \Exception("Identifiants incorrects.");
                }
            } catch (CsrfTokenException $e) {
                $error = "Erreur de sécurité. Veuillez réessayer.";
            } catch (\InvalidArgumentException $e) {
                $error = $e->getMessage();
            } catch (\Exception $e) { 
                $error = $e->getMessage(); 
            }

            $csrfToken = CsrfTokenProvider::generate();
            $csrfToken = "test";
            return $this->view->render($response, 'signin.twig', [
                'csrf_token' => $csrfToken,
                'error' => $error ?? null,
                'submitted_email' => $email,
            ]);

        } else {

            if ($this->authProvider->isSignedIn()) {
                return $response->withHeader('Location', $router->urlFor('admin.dashboard'))->withStatus(302);
            }

            $csrfToken = CsrfTokenProvider::generate();
            return $this->view->render($response, 'signin.twig', [
                'csrf_token' => $csrfToken,
                'error' => null,
            ]);
        }
    }
}