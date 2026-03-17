<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Auth;
use App\Models\SavedGig;
use Flight;

final class SavedGigController
{
    /**
     * POST /gig/@id/save
     * Toggle la sauvegarde d'un gig. Répond en JSON.
     */
    public static function toggle(int $gigId): void
    {
        if (!check_csrf(Flight::request()->data->csrf ?? null)) {
            Flight::json(['ok' => false, 'message' => 'CSRF invalide.'], 419);
            return;
        }

        $user = Auth::user();
        if (!$user) {
            Flight::json(['ok' => false, 'redirect' => '/connexion'], 401);
            return;
        }

        $saved = SavedGig::toggle((int) $user['id'], $gigId);

        Flight::json(['ok' => true, 'saved' => $saved]);
    }

    /**
     * GET /sauvegardes
     * Affiche les gigs sauvegardés de l'utilisateur connecté.
     */
    public static function index(): void
    {
        Auth::requireLogin();
        $user = Auth::user();

        Flight::renderView('saved-gigs', [
            'gigs' => SavedGig::byUser((int) $user['id']),
        ], 'admin');
    }
}
