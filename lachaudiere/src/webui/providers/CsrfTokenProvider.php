<?php
namespace lachaudiere\webui\providers;

class CsrfTokenProvider{
    private const SESSION_KEY = '_csrf_token';

    //Génère un token csrf, le stock en session et le retourne
    public static function generate(): string{
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        //Génération alétoire d'un token stocké en session
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::SESSION_KEY] = $token;
        return $token;
    }

    //Compare le token reçu à celui stocké en session et lève une exception en cas d'échec, et supprime le token en session
    public static function check(?string $token): void{
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        //Recup le token + verif
        $sessionToken = $_SESSION[self::SESSION_KEY] ?? null;
        unset($_SESSION[self::SESSION_KEY]);
        if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
            throw new CsrfTokenException('CSRF token invalide ou manquant');
        }
    }
}