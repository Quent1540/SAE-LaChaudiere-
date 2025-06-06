<?php
namespace lachaudiere\webui\middleware;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteContext;
use lachaudiere\webui\providers\AuthnProvider;



class AuthMiddleware
{
    protected AuthnProvider $authProvider;

    public function __construct(AuthnProvider $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if (!$this->authProvider->isSignedIn()) { 


            $response = new SlimResponse(); 
            
            $_SESSION['auth_message'] = 'Vous devez être connecté pour accéder à cette page.';
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $loginUrl = $routeParser->urlFor('signin');
            return $response->withHeader('Location', $loginUrl)->withStatus(302);
            
        }

        $response = $handler->handle($request);
        return $response;
    }
}