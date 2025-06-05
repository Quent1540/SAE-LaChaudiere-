<?php
namespace gift\appli\webui\actions;

use gift\appli\application_core\application\exceptions\BoxException;
use gift\appli\application_core\application\useCases\BoxInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Exception\HttpNotFoundException;

class GetCoffretDetailAction {
    private BoxInterface $box;

    public function __construct(BoxInterface $box) {
        $this->box = $box;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $id = (int) $args['id'];
            $prestations = $this->box->getPrestationsByCoffret($id);
            $view = Twig::fromRequest($request);
            return $view->render($response, 'coffretDetail.twig', [
                'prestations' => $prestations,
                'coffret_id' => $id
            ]);
        } catch (BoxException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }
    }
}