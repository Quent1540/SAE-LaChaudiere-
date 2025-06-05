<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\domain\entities\User;

interface AuthnServiceInterface {
    public function register(string $email, string $password): bool;
    public function verifyCredentials(string $email, string $password): User;
}