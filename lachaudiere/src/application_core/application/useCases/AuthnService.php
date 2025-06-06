<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\domain\entities\User;
use Ramsey\Uuid\Uuid;
use lachaudiere\application_core\application\exceptions\UserAlreadyExistsException;
use lachaudiere\application_core\application\exceptions\InvalidCredentialsException;

class AuthnService implements AuthnServiceInterface
{
    public function register(string $email, string $password, int $role = 1): bool
    {
        if (User::query()->where('email', $email)->exists()) {
            throw new UserAlreadyExistsException("L'email est déjà utilisé.");
        }

        $user = new User();
        $user->email = $email;
        $user->mot_de_passe_hash = password_hash($password, PASSWORD_BCRYPT);
        $user->role = $role;

        return $user->save();
    }

    public function verifyCredentials(string $email, string $password): User
    {
        $user = User::query()->where('email', $email)->first();

        if ($user && password_verify($password, $user->mot_de_passe_hash)) {
            return $user;
        }

        throw new InvalidCredentialsException("Identifiants invalides.");
    }
}