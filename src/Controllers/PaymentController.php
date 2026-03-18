<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Auth;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Gig;
use Flight;

final class PaymentController
{
    public static function showPlans(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        
        Flight::renderView('dashboard/plans', [
            'user' => $user,
            'isPremium' => User::isPremium((int)$user['id']),
            'gigCount' => Gig::countByUser((int)$user['id'])
        ], 'admin');
    }

    public static function buyPremium(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $r = Flight::request()->data;

        if (!check_csrf($r->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        // Simulation de paiement réussi (15 000 FCFA)
        $db = Flight::get('db');
        $stmt = $db->prepare('
            UPDATE users SET 
                subscription_status = "premium",
                subscription_expires_at = DATE_ADD(COALESCE(subscription_expires_at, CURRENT_TIMESTAMP), INTERVAL 1 YEAR)
            WHERE id = :id
        ');
        $stmt->execute(['id' => (int)$user['id']]);

        Transaction::log((int)$user['id'], 'subscription', 15000.00, 0, 'completed');

        $_SESSION['success'] = 'Votre abonnement Premium a été activé pour 1 an !';
        Flight::redirect('/profil');
    }

    public static function buySeeds(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $r = Flight::request()->data;

        if (!check_csrf($r->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        $amount = (int)($r->amount ?? 0);
        
        if ($amount < 500) {
            $_SESSION['error'] = 'Le montant minimum est de 500 FCFA (soit 500 Seeds).';
            Flight::redirect('/mon-compte/plans');
            return;
        }

        // 1 FCFA = 1 Seed
        $seeds = $amount;
        User::addSeeds((int)$user['id'], $seeds);
        Transaction::log((int)$user['id'], 'seeds_purchase', (float)$amount, $seeds, 'completed');

        $_SESSION['success'] = "Rechargement réussi ! Vous avez reçu $seeds Seeds.";
        Flight::redirect('/mon-compte/plans');
    }

    public static function sponsorGig(int $id): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $r = Flight::request()->data;

        if (!check_csrf($r->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        $days = (int)($r->days ?? 0);
        if ($days <= 0) {
            $_SESSION['error'] = 'Nombre de jours invalide.';
            Flight::redirect('/mes-services');
            return;
        }

        // Coût : 200 Seeds par jour
        $cost = $days * 200;

        if (User::spendSeeds((int)$user['id'], $cost)) {
            Gig::sponsor($id, $days);
            Transaction::log((int)$user['id'], 'sponsorship_spend', 0, $cost, 'completed');
            $_SESSION['success'] = "Votre service est maintenant sponsorisé pour $days jours !";
        } else {
            $_SESSION['error'] = "Seeds insuffisants. Il vous faut $cost Seeds.";
        }

        Flight::redirect('/mes-services');
    }
}
