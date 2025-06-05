<?php
namespace gift\appli\webui\actions;

use gift\appli\application_core\application\exceptions\BoxException;
use gift\appli\application_core\application\useCases\BoxInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Exception\HttpNotFoundException;

class GetPrestationCoffretAction {
    private BoxInterface $box;

    public function __construct(BoxInterface $box) {
        $this->box = $box;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $prestationId = $args['id'];
            $coffretId = $args['coffret_id'];
            $prestation = $this->box->getPrestationById($prestationId);
            $view = Twig::fromRequest($request);
            return $view->render($response, 'prestationsCoffret.twig', [
                'prestation' => $prestation,
                'coffret_id' => $coffretId
            ]);
        } catch (BoxException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }
    }
}