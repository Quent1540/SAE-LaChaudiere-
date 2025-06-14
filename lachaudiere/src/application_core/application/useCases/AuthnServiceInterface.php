<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\domain\entities\User;

interface AuthnServiceInterface {
    // on garde l'ancienne au cas où mais plus utile 
    public function register(string $email, string $password, int $role = 1): bool;
    public function verifyCredentials(string $email, string $password): User;
    public function registerUser(string $email, string $password, string $passwordConfirm, int $role = 1): User;
    
}