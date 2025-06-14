<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Exception\HttpNotFoundException;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class GetCategorieParIdAction {
    private EvenementServiceInterface $catalogue;

    public function __construct(EvenementServiceInterface $catalogue) {
        $this->catalogue = $catalogue;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $id = (int) $args['id'];
            $categorie = $this->catalogue->getCategorieById($id);
            
            if (!$categorie) {
                throw new HttpNotFoundException($request, "Catégorie non trouvée.");
            }

            $config = [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ];
            $environment = new Environment($config);
            $environment->addExtension(new CommonMarkCoreExtension());
            $converter = new MarkdownConverter($environment);
            
            $htmlDescription = $converter->convert($categorie->description ?? '')->getContent();

            $view = Twig::fromRequest($request);
            return $view->render($response, 'categorieParId.twig', [
                'categorie' => $categorie,
                'html_description' => $htmlDescription
            ]);
        } catch (\Exception $e) { 
            throw new HttpNotFoundException($request, $e->getMessage());
        }
    }
}