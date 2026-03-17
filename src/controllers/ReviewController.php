<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Auth;
use App\Models\Order;
use App\Models\Review;
use Flight;

final class ReviewController
{
    public static function submit(int $orderId): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $r = Flight::request()->data;

        if (!check_csrf($r->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        $order = Order::findById($orderId);
        if (!$order) {
            Flight::halt(404, 'Commande introuvable.');
        }

        // Seul l'acheteur peut laisser un avis
        if ((int)$order['buyer_id'] !== (int)$user['id']) {
            Flight::halt(403, 'Action non autorisée.');
        }

        // La commande doit être complétée
        if ($order['status'] !== 'completed') {
            $_SESSION['error'] = 'Vous ne pouvez laisser un avis que sur une commande terminée.';
            Flight::redirect("/commande/{$orderId}");
            return;
        }

        // Un seul avis par commande
        if (Review::hasReviewed($orderId)) {
            $_SESSION['error'] = 'Vous avez déjà laissé un avis pour cette commande.';
            Flight::redirect("/commande/{$orderId}");
            return;
        }

        $rating = (int)($r->rating ?? 0);
        $comment = trim((string)($r->comment ?? ''));

        if ($rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'La note doit être comprise entre 1 et 5.';
            Flight::redirect("/commande/{$orderId}");
            return;
        }

        if (mb_strlen($comment) < 5) {
            $_SESSION['error'] = 'Le commentaire est trop court (min 5 caractères).';
            Flight::redirect("/commande/{$orderId}");
            return;
        }

        Review::create([
            'gig_id'        => (int)$order['gig_id'],
            'order_id'      => $orderId,
            'buyer_id'      => (int)$user['id'],
            'freelancer_id' => (int)$order['seller_id'],
            'rating'        => $rating,
            'comment'       => $comment,
        ]);

        $_SESSION['success'] = 'Merci ! Votre avis a été publié.';
        Flight::redirect("/commande/{$orderId}");
    }
}
