<?php
namespace lachaudiere\webui\providers;

use lachaudiere\application_core\domain\entities\User;

interface AuthnProviderInterface {
    public function getSignedInUser(): ?User;
    public function signin(string $email, string $password): bool;
    public function signout(): void;
    public function register(string $email, string $password, int $role = 1): bool;
    public function isSignedIn(): bool;
}