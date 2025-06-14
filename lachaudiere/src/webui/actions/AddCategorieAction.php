<?php
namespace lachaudiere\webui\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use lachaudiere\webui\providers\CsrfTokenProvider;
use lachaudiere\webui\providers\CsrfTokenException;
use lachaudiere\application_core\application\useCases\EvenementServiceInterface;
use lachaudiere\application_core\application\exceptions\ValidationException;

class AddCategorieAction {
    private EvenementServiceInterface $evenementService;
    
    public function __construct(EvenementServiceInterface $evenementService) {
        $this->evenementService = $evenementService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $view = Twig::fromRequest($request);
        $data = $request->getParsedBody();
        $erreur = null;

        try {
            CsrfTokenProvider::check($data['csrf_token'] ?? null);
            
            $libelle = $data['libelle'] ?? '';
            $description = $data['description'] ?? '';

            $this->evenementService->createCategorie($libelle, $description);

            return $response
                ->withHeader('Location', '/categories')
                ->withStatus(302);

        } catch (CsrfTokenException $e) {
            $erreur = "Erreur de sécurité. Veuillez réessayer.";
        } catch (ValidationException $e) {
            $erreur = $e->getMessage();
        } catch (\Exception $e) {
            $erreur = "Erreur lors de la création : " . $e->getMessage();
        }
        
        return $view->render($response->withStatus(400), 'ajouterCategorie.twig', [
            'erreur' => $erreur
        ]);
    }
}