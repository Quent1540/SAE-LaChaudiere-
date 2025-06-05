<?php
namespace gift\appli\webui\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddPrestationBoxAction {
    public function __invoke(Request $request, Response $response, $args){
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $box_id = $_SESSION['box_id'] ?? null;
        if (!$box_id) {
            $response->getBody()->write('Aucun coffret sélectionné');
            return $response->withStatus(400);
        }
        $data = $request->getParsedBody();
        $presta_id = $data['presta_id'] ?? null;
        if (!$presta_id) {
            $response->getBody()->write('ID prestation manquant');
            return $response->withStatus(400);
        }
        $pdo = new \PDO('mysql:host=sql;dbname=gift', 'root', 'root');
        $stmt = $pdo->prepare("INSERT INTO box2presta (box_id, presta_id, quantite) VALUES (?, ?, ?)");
        $stmt->execute([$box_id, $presta_id, 1]);
        $response->getBody()->write('
            <script>
                alert("Prestation ajoutée au coffret !");
                window.location.href = "/prestations";
            </script>
        ');
        return $response;
    }
}