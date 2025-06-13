<?php
namespace lachaudiere\webui\actions;

use lachaudiere\application_core\application\useCases\CategoriesServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Exception\HttpNotFoundException;
// AJOUTER CES USES
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

//Action afficher une catégorie par son id
class GetCategorieParIdAction {
    private CategoriesServiceInterface $catalogue;

    public function __construct(CategoriesServiceInterface $catalogue) {
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