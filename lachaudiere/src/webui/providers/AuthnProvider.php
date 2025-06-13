<?php
namespace lachaudiere\webui\providers;

use lachaudiere\application_core\application\useCases\AuthnServiceInterface; 
use lachaudiere\application_core\domain\entities\User;
use lachaudiere\webui\providers\AuthnProviderInterface;

//Fournisseur d'authentification pour la gestion des utilisateurs connectés
class AuthnProvider implements AuthnProviderInterface {
    protected AuthnServiceInterface $authnService;

    public function __construct(AuthnServiceInterface $authnService) {
        $this->authnService = $authnService;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    //Récup l'utilisateur connecté
    public function getSignedInUser(): ?User {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        return User::query()->find($_SESSION['user_id']);
    }

    //Récup le role de l'utilisateur connecté
    public function getRoleUser(): ?int {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        return User::query()->find($_SESSION['user_id'])->role ?? null;
    }

    //Tente de connecter l'utilisateur avec ses identifiants
    public function signin(string $email, string $password): bool {
        try {
            $user = $this->authnService->verifyCredentials($email, $password);
            //Stock les infos de l'utilisateur dans la session
            $_SESSION['user_id'] = $user->id_utilisateur;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_role'] = $user->role;
            return true;
        } catch (\Exception $e) {
            error_log("Erreur de connexion: " . $e->getMessage());
            return false;
        }
    }

    //Déconnecte l'utilisateur en supprimant ses infos de la session
    public function signout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);
    }

    //Enregistre un nouvel utilisateur avec son email, mot de passe et role (1 par défaut)
    public function register(string $email, string $password, int $role = 1): bool {
        try {
            return $this->authnService->register($email, $password, $role);
        } catch (\Exception $e) {
            error_log("Erreur d'enregistrement: " . $e->getMessage());
            return false;
        }
    }

    //Vérif si l'utilisateur est connecté
    public function isSignedIn(): bool {
        return isset($_SESSION['user_id']);
    }
}