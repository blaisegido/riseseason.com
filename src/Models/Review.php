<?php

declare(strict_types=1);

namespace App\Models;

use Flight;

final class Review
{
    public static function create(array $data): bool
    {
        $db = Flight::get('db');
        
        $stmt = $db->prepare('
            INSERT INTO reviews (gig_id, order_id, buyer_id, freelancer_id, rating, comment)
            VALUES (:gig_id, :order_id, :buyer_id, :freelancer_id, :rating, :comment)
        ');
        
        return $stmt->execute([
            'gig_id'        => $data['gig_id'],
            'order_id'      => $data['order_id'],
            'buyer_id'      => $data['buyer_id'],
            'freelancer_id' => $data['freelancer_id'],
            'rating'        => $data['rating'],
            'comment'       => $data['comment'],
        ]);
    }

    public static function byFreelancer(int $freelancerId): array
    {
        $stmt = Flight::get('db')->prepare('
            SELECT r.*, b.username as buyer_username, b.profile_photo as buyer_photo, g.title as gig_title
            FROM reviews r
            JOIN users b ON b.id = r.buyer_id
            JOIN gigs g ON g.id = r.gig_id
            WHERE r.freelancer_id = :fid
            ORDER BY r.created_at DESC
        ');
        $stmt->execute(['fid' => $freelancerId]);
        return $stmt->fetchAll();
    }

    public static function byGig(int $gigId): array
    {
        $stmt = Flight::get('db')->prepare('
            SELECT r.*, b.username as buyer_username, b.profile_photo as buyer_photo
            FROM reviews r
            JOIN users b ON b.id = r.buyer_id
            WHERE r.gig_id = :gid
            ORDER BY r.created_at DESC
        ');
        $stmt->execute(['gid' => $gigId]);
        return $stmt->fetchAll();
    }

    public static function getStats(int $freelancerId): array
    {
        $stmt = Flight::get('db')->prepare('
            SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
            FROM reviews
            WHERE freelancer_id = :fid
        ');
        $stmt->execute(['fid' => $freelancerId]);
        $stats = $stmt->fetch();
        
        return [
            'avg_rating' => $stats['avg_rating'] ? round((float)$stats['avg_rating'], 1) : 0,
            'total_reviews' => (int)$stats['total_reviews']
        ];
    }
    
    public static function hasReviewed(int $orderId): bool
    {
        $stmt = Flight::get('db')->prepare('SELECT id FROM reviews WHERE order_id = :oid LIMIT 1');
        $stmt->execute(['oid' => $orderId]);
        return (bool)$stmt->fetch();
    }
}
