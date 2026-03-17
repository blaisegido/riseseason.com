<?php

declare(strict_types=1);

namespace App\Models;

use Flight;

final class SavedGig
{
    /**
     * Bascule la sauvegarde d'un gig pour un utilisateur.
     * Retourne true si le gig est maintenant sauvegardé, false sinon.
     */
    public static function toggle(int $userId, int $gigId): bool
    {
        $db = Flight::get('db');

        $stmt = $db->prepare('SELECT 1 FROM saved_gigs WHERE user_id = :uid AND gig_id = :gid LIMIT 1');
        $stmt->execute(['uid' => $userId, 'gid' => $gigId]);

        if ($stmt->fetch()) {
            $db->prepare('DELETE FROM saved_gigs WHERE user_id = :uid AND gig_id = :gid')
               ->execute(['uid' => $userId, 'gid' => $gigId]);
            return false;
        }

        $db->prepare('INSERT INTO saved_gigs (user_id, gig_id) VALUES (:uid, :gid)')
           ->execute(['uid' => $userId, 'gid' => $gigId]);
        return true;
    }

    /**
     * Retourne la liste des gig_id sauvegardés par l'utilisateur.
     * Utilisé pour pré-colorer les icônes marque-page dans les vues.
     */
    public static function savedIdsByUser(int $userId): array
    {
        $stmt = Flight::get('db')->prepare('SELECT gig_id FROM saved_gigs WHERE user_id = :uid');
        $stmt->execute(['uid' => $userId]);
        return array_column($stmt->fetchAll(), 'gig_id');
    }

    /**
     * Retourne les gigs complets sauvegardés par l'utilisateur (pour la page /sauvegardes).
     */
    public static function byUser(int $userId): array
    {
        $stmt = Flight::get('db')->prepare('
            SELECT g.*, g.price_base AS price_eur, c.name AS category,
                   u.username, u.country, u.profile_photo
            FROM saved_gigs sg
            JOIN gigs g  ON g.id = sg.gig_id
            JOIN users u ON u.id = g.user_id
            LEFT JOIN categories c ON c.id = g.category_id
            WHERE sg.user_id = :uid
              AND g.status = "approved"
            ORDER BY sg.created_at DESC
        ');
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }
}
