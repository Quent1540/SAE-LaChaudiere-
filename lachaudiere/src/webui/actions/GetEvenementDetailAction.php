<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use Slim\Exception\HttpNotFoundException;

class GetEvenementDetailAction {
    private EvenementServiceInterface $evenementService;

    public function __construct(EvenementServiceInterface $evenementService) {
        $this->evenementService = $evenementService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $id_evenement = (int)$args['id'];
            $evenement = $this->evenementService->getEvenementParId($id_evenement);

            $config = [
                'html_input' => 'strip',

                'allow_unsafe_links' => false,
            ];

            $environment = new Environment($config);
            $environment->addExtension(new CommonMarkCoreExtension());
            
            $converter = new MarkdownConverter($environment);

            $htmlDescription = $converter->convert($evenement['description'] ?? '')->getContent();

            $view = Twig::fromRequest($request);
            return $view->render($response, 'evenementDetail.twig', [
                'evenement' => $evenement,
                'html_description' => $htmlDescription
            ]);
        } catch (\Exception $e) {
            throw new HttpNotFoundException($request, "L'événement demandé n'existe pas.");
        }
    }
}