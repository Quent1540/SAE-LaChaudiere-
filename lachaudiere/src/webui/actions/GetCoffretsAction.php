<?php
namespace gift\appli\webui\actions;

use gift\appli\application_core\application\exceptions\BoxException;
use gift\appli\application_core\application\useCases\BoxInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Exception\HttpInternalServerErrorException;

class GetCoffretsAction {
    private BoxInterface $box;

    public function __construct(BoxInterface $box) {
        $this->box = $box;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $coffrets = $this->box->getThemesCoffrets();
            $view = Twig::fromRequest($request);
            return $view->render($response, 'coffrets.twig', ['coffrets' => $coffrets]);
        } catch (BoxException $e) {
            throw new HttpInternalServerErrorException($request, $e->getMessage());
        }
    }
}