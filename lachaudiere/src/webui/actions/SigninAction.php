<?php
namespace lachaudiere\webui\actions; 

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use lachaudiere\webui\providers\AuthnProviderInterface;
use lachaudiere\webui\providers\CsrfTokenProvider;
use lachaudiere\webui\providers\CsrfTokenException;
use Slim\Routing\RouteContext;

//Action pour la gestion de la connexion des utilisateurs
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

        //Si la requête est une POST, on traite la connexion
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $email = trim($data['email'] ?? ''); 
            $password = $data['password'] ?? '';
            $submittedToken = $data['csrf_token'] ?? null;

            try {
                //Vérif du token CSRF
                CsrfTokenProvider::check($submittedToken);

                if (empty($email) || empty($password)) {
                    throw new \InvalidArgumentException("Email et mot de passe sont requis.");
                }
                //Tente de connecter l'utilisateur
                if ($this->authProvider->signin($email, $password)) {
                    return $response->withHeader('Location', $router->urlFor('home'))->withStatus(302);
                } else {
                    throw new \Exception("Identifiants incorrects.");
                }
            } catch (CsrfTokenException $e) {
                $error = "Erreur de sécurité. Veuillez réessayer.";
            } catch (\InvalidArgumentException $e) {
                //Email ou mdp vide
                $error = $e->getMessage();
            } catch (\Exception $e) {
                $error = $e->getMessage(); 
            }

            //Affiche le formulaire avec l'erreur
            return $this->view->render($response, 'signin.twig', [
                'error' => $error ?? null,
                'submitted_email' => $email,
            ]);

        } else {
            //Redirige vers la page d'accueil si l'utilisateur est déjà connecté
            if ($this->authProvider->isSignedIn()) {
                return $response->withHeader('Location', $router->urlFor('home'))->withStatus(302);
            }

            //Si un message d'erreur est stocké dans la session, on l'affiche
            if (isset($_SESSION['auth_message'])) {
                $m_error = $_SESSION['auth_message'];
                unset($_SESSION['auth_message']);
                return $this->view->render($response, 'signin.twig', [
                'error' => $m_error,
            ]);
            }

            //Affiche le formulaire de connexion sans erreur
            return $this->view->render($response, 'signin.twig', [
                'error' => null,
            ]);
        }
    }
}