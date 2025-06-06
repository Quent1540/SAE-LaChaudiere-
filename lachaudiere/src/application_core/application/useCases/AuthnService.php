<?php
namespace lachaudiere\application_core\application\useCases; 

use lachaudiere\application_core\domain\entities\User; 
use Ramsey\Uuid\Uuid;
use lachaudiere\application_core\application\exceptions\UserAlreadyExistsException;
use lachaudiere\application_core\application\exceptions\InvalidCredentialsException;

class AuthnService implements AuthnServiceInterface {

    public function register(string $email, string $password, int $role = 1): bool
    {
        if (User::query()->where('email', $email)->exists()) {
<<<<<<< HEAD
            return false;
=======
            throw new UserAlreadyExistsException("L'email est déjà utilisé.");
>>>>>>> 7c99e6798dbfe59650fc9ac26b6f61fb83dd7362
        }

        $user = new User();
<<<<<<< HEAD
        $user->id = Uuid::uuid4()->toString(); //On genere un UUID pour l'id de l'utilisateur
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->role = 1; //On met le role utilisateur par défaut
        return $user->save();
    }
    public function verifyCredentials(string $email, string $password): User {
        try {
            $user = User::query()->where('email', $email)->first();
            if ($user && password_verify($password, $user->password)) {
                return $user;
            }
            throw new \Exception("Invalid credentials");
        } catch (\Exception $e) {
            throw new \Exception("Invalid credentials");
=======
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
>>>>>>> 7c99e6798dbfe59650fc9ac26b6f61fb83dd7362
        }

        throw new InvalidCredentialsException("Identifiants invalides.");
    }
}