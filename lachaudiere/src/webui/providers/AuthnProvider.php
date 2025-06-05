<?php
namespace lachaudiere\app\webui\providers; 

use lachaudiere\application_core\application\useCases\AuthnServiceInterface; 
use lachaudiere\application_core\domain\entities\User;

class AuthnProvider implements AuthnProviderInterface {
    protected AuthnServiceInterface $authnService;

    public function __construct(AuthnServiceInterface $authnService) {
        $this->authnService = $authnService;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function getSignedInUser(): ?User {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        return User::query()->find($_SESSION['user_id']);
    }

    public function signin(string $email, string $password): bool {
        try {
            $user = $this->authnService->verifyCredentials($email, $password);
            $_SESSION['user_id'] = $user->id_utilisateur;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_role'] = $user->role;
            return true;
        } catch (\Exception $e) {
            error_log("Erreur de connexion: " . $e->getMessage());
            return false;
        }
    }

    public function signout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);
    }

    public function register(string $email, string $password, int $role = 1): bool {
        try {
            return $this->authnService->register($email, $password, $role);
        } catch (\Exception $e) {
            error_log("Erreur d'enregistrement: " . $e->getMessage());
            return false;
        }
    }

    public function isSignedIn(): bool {
        return isset($_SESSION['user_id']);
    }
}