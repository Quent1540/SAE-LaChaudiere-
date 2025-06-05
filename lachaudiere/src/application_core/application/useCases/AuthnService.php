<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\application\useCases;
use lachaudiere\application_core\domain\entities\User;
use Ramsey\Uuid\Uuid;

class AuthnService implements AuthnServiceInterface {
    public function register(string $email, string $password): bool
    {
        if (User::query()->where('user_id', $email)->exists()) {
            return false;
        }
        $user = new User();
        $user->id = Uuid::uuid4()->toString(); //On genere un UUID pour l'id de l'utilisateur
        $user->user_id = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->role = 1; //On met le role utilisateur par défaut
        return $user->save();
    }
    public function verifyCredentials(string $email, string $password): User {
        try {
            $user = User::query()->where('user_id', $email)->first();
            if ($user && password_verify($password, $user->password)) {
                return $user;
            }
            throw new \Exception("Invalid credentials");
        } catch (\Exception $e) {
            throw new \Exception("Invalid credentials");
        }
    }
}