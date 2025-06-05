<?php
namespace gift\appli\webui\actions;

use gift\appli\webui\providers\CsrfTokenProvider;
use gift\appli\application_core\application\useCases\BoxInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class GetBoxCouranteAction {
    protected BoxInterface $boxService;

    public function __construct(BoxInterface $boxService) {
        $this->boxService = $boxService;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $view = Twig::fromRequest($request);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $box_id = $_SESSION['box_id'] ?? null;

        if (!$box_id) {
            $response->getBody()->write('Aucune box en courante');
            return $response->withStatus(404);
        }

        $box = [
            'prestations' => $this->boxService->getPrestationsByBox($box_id),
            'total' => 0,
        ];
        $box['total'] = array_sum(array_column($box['prestations'], 'tarif'));

        return $view->render($response, 'boxCourante.twig', [
            'box' => $box,
            'csrf_token' => CsrfTokenProvider::generate()
        ]);
    }
}