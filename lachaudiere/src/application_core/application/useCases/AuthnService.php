<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\domain\entities\User;
use Ramsey\Uuid\Uuid;
use lachaudiere\application_core\application\exceptions\UserAlreadyExistsException;
use lachaudiere\application_core\application\exceptions\InvalidCredentialsException;
use lachaudiere\application_core\application\exceptions\ValidationException;

//Service d'authentification pour la gestion des utilisateurs
class AuthnService implements AuthnServiceInterface {

    //Inscription d'un nouvel utilisateur
    // METHODE ANCIENNE VOIR registerUser
    public function register(string $email, string $password, int $role = 1): bool {
        if (User::query()->where('email', $email)->exists()) {
            throw new UserAlreadyExistsException("L'email est déjà utilisé.");
        }

        $user = new User();
        $user->email = $email;
        //Hash le mdp
        $user->mot_de_passe_hash = password_hash($password, PASSWORD_BCRYPT);
        $user->role = $role;
        //Sauvegarde l'utilisateur en base
        return $user->save();
    }

    //Vérif des identifiants de l'utilisateur
    public function verifyCredentials(string $email, string $password): User {
        //Recherche l'utilisateur par email
        $user = User::query()->where('email', $email)->first();

        //Vérif le mdr fourni avec le hash en base
        if ($user && password_verify($password, $user->mot_de_passe_hash)) {
            return $user;
        }

        throw new InvalidCredentialsException("Identifiants invalides.");
    }

    public function registerUser(string $email, string $password, string $passwordConfirm, int $role = 1): User
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("L'email est obligatoire et doit être une adresse valide.");
        }
        if ($password !== $passwordConfirm) {
            throw new ValidationException("Les mots de passe ne correspondent pas.");
        }
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password)) {
            throw new ValidationException("Le mot de passe doit contenir au moins 8 caractères et une majuscule.");
        }
        if (User::query()->where('email', $email)->exists()) {
            throw new UserAlreadyExistsException("L'email est déjà utilisé.");
        }

        $user = new User();
        $user->email = $email;
        $user->mot_de_passe_hash = password_hash($password, PASSWORD_BCRYPT);
        $user->role = $role;
        $user->save();

        return $user;
    }

}