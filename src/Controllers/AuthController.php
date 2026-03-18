<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use Flight;

final class AuthController
{
    public static function showRegister(): void
    {
        Flight::renderView('register', ['error' => null]);
    }

    public static function register(): void
    {
        $r = Flight::request()->data;

        if (!check_csrf($r->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        $email = filter_var(trim((string)$r->email), FILTER_VALIDATE_EMAIL);
        $password = (string)($r->password ?? '');
        $firstName = trim((string)($r->first_name ?? ''));
        $lastName = trim((string)($r->last_name ?? ''));
        $country = trim((string)($r->country ?? ''));
        $role = trim((string)($r->role ?? 'freelancer'));

        if (!$email || strlen($password) < 8 || $firstName === '' || $lastName === '') {
            Flight::renderView('register', ['error' => 'Veuillez remplir correctement tous les champs.']);
            return;
        }
        if (!in_array($role, ['freelancer', 'employeur', 'contributeur'], true)) {
            $role = 'freelancer';
        }
        if (User::findByEmail($email)) {
            Flight::renderView('register', ['error' => 'Cet email est déjà utilisé.']);
            return;
        }

        $userId = User::create([
            'email' => $email,
            'password' => $password,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'country' => $country,
            'role' => $role,
        ]);

        // TODO: Envoi email réel via PHPMailer (bienvenue + vérification)
        $_SESSION['user_id'] = $userId;
        Flight::redirect('/dashboard');
    }

    public static function showLogin(): void
    {
        Flight::renderView('login', ['error' => null]);
    }

    public static function login(): void
    {
        $r = Flight::request()->data;

        if (!check_csrf($r->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        $email = filter_var(trim((string)$r->email), FILTER_VALIDATE_EMAIL);
        $password = (string)($r->password ?? '');

        $user = $email ? User::findByEmail($email) : null;
        if (!$user || !password_verify($password, $user['password_hash'])) {
            Flight::renderView('login', ['error' => 'Identifiants invalides.']);
            return;
        }

        $_SESSION['user_id'] = (int)$user['id'];
        
        if ($user['role'] === 'admin') {
            Flight::redirect('/admin');
            return;
        }
        
        Flight::redirect('/dashboard');
    }

    public static function logout(): void
    {
        session_destroy();
        Flight::redirect('/');
    }
}
