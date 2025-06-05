<?php
namespace gift\appli\webui\providers;

use gift\appli\application_core\domain\entities\User;

interface AuthnProviderInterface {
    public function getSignedInUser(): ?User;
    public function signin(string $email, string $password): bool;
}