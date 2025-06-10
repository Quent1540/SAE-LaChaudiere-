<?php
namespace lachaudiere\webui\providers;

class CsrfTokenProvider
{
    private const SESSION_KEY = '_csrf_token';
    private static ?string $currentToken = null;

    public static function generate(): string
    {
        if (self::$currentToken !== null) {
            return self::$currentToken;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION[self::SESSION_KEY] = $token;

        self::$currentToken = $token;

        return $token;
    }


    public static function check(?string $token): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $sessionToken = $_SESSION[self::SESSION_KEY] ?? null;

        unset($_SESSION[self::SESSION_KEY]); 
        
        if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
            throw new CsrfTokenException('CSRF token invalide ou manquant');
        }
    }
}