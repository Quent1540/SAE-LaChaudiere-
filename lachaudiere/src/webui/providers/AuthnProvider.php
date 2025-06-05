<?php
namespace lachaudiere\webui\providers;

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
        if (!isset($_SESSION['user_id'])) return null;
        return User::query()->find($_SESSION['user_id']);
    }
    public function signin(string $email, string $password): bool {
        try {
            $user = $this->authnService->verifyCredentials($email, $password);
            $_SESSION['user_id'] = $user->id;
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function register(string $email, string $password): bool {
        return $this->authnService->register($email, $password);
    }
}