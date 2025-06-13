<?php
namespace lachaudiere\webui\middleware;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteContext;
use lachaudiere\webui\providers\AuthnProvider;


//Pour protéger les routes qui nécessitent une authentification
class AuthMiddleware
{
    protected AuthnProvider $authProvider;

    public function __construct(AuthnProvider $authProvider) {
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response {
        //Vérif si l'utilisateur est connecté
        if (!$this->authProvider->isSignedIn()) {
            //Sinon, prépare une redirection vers la page de connexion
            $response = new SlimResponse(); 

            //Stock un message d'erreur dans la session
            $_SESSION['auth_message'] = 'Vous devez être connecté pour accéder à cette page.';
            //Prépare la redirection vers la page de connexion
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $loginUrl = $routeParser->urlFor('signin');
            return $response->withHeader('Location', $loginUrl)->withStatus(302);
            
        }
        //Si l'utilisateur est connecté, on continue le traitement de la requête
        $response = $handler->handle($request);
        return $response;
    }
}