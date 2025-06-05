<?php
namespace lachaudiere\webui\providers;

use lachaudiere\application_core\domain\entities\User;

interface AuthnProviderInterface {
    public function getSignedInUser(): ?User;
    public function signin(string $email, string $password): bool;
}