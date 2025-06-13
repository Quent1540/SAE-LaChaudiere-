<?php
namespace lachaudiere\webui\providers;

//Token CSRF pour sécuriser les formulaires
class CsrfTokenProvider {
    private const SESSION_KEY = '_csrf_token';
    private static ?string $currentToken = null;

    //Générer un nouveau token CSRF et le stocker en session
    public static function generate(): string {
        //Si un token existe déjà, le réutiliser
        if (self::$currentToken !== null) {
            return self::$currentToken;
        }

        //Démarrer la session si elle n'est pas déjà active
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        //Génére un token aléatoire
        $token = bin2hex(random_bytes(32));
        //Stock en session
        $_SESSION[self::SESSION_KEY] = $token;

        self::$currentToken = $token;

        return $token;
    }

    //Vérif la validité du token CSRF soumis par un formulaire
    public static function check(?string $token): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        //Récup le token en session
        $sessionToken = $_SESSION[self::SESSION_KEY] ?? null;

        //Supprime le token de session après vérification pour éviter les réutilisations
        unset($_SESSION[self::SESSION_KEY]); 

        //Vérif si le token est présent et correspond à celui en session
        if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
            throw new CsrfTokenException('CSRF token invalide ou manquant');
        }
    }
}