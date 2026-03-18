<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\User;
use Flight;

final class Auth
{
    public static function user(): ?array
    {
        $id = $_SESSION['user_id'] ?? null;
        if (!$id) {
            return null;
        }
        return User::findById((int) $id);
    }

    public static function requireLogin(): void
    {
        if (!self::user()) {
            Flight::redirect('/connexion');
            exit;
        }
    }

    public static function requireRole(string $role): void
    {
        $user = self::user();
        if (!$user || $user['role'] !== $role) {
            Flight::json(['error' => 'Accès refusé.'], 403);
            exit;
        }
    }

    public static function requireAnyRole(array $roles): void
    {
        $user = self::user();
        if (!$user || !in_array($user['role'], $roles, true)) {
            Flight::json(['error' => 'Accès refusé.'], 403);
            exit;
        }
    }
}
