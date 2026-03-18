<?php

declare(strict_types=1);

namespace App\Models;

use Flight;

final class Order
{
    public static function create(array $data): int
    {
        $db = Flight::get('db');
        $amount = (float)$data['amount'];
        $sellerId = (int)$data['seller_id'];

        // Commission logic: 2% for Premium, 10% for Free
        $rate = User::isPremium($sellerId) ? 0.02 : 0.10;
        $commission = $amount * $rate;
        $net = $amount - $commission;

        $stmt = $db->prepare('
            INSERT INTO orders (buyer_id, seller_id, gig_id, amount, commission, net_to_seller, status)
            VALUES (:buyer_id, :seller_id, :gig_id, :amount, :commission, :net, "pending")
        ');
        $stmt->execute([
            'buyer_id'   => $data['buyer_id'],
            'seller_id'  => $data['seller_id'],
            'gig_id'     => $data['gig_id'],
            'amount'     => $amount,
            'commission' => $commission,
            'net'        => $net,
        ]);

        return (int)$db->lastInsertId();
    }

    public static function findById(int $id): ?array
    {
        $stmt = Flight::get('db')->prepare('
            SELECT o.*, 
                   g.title as gig_title, g.main_image as gig_image,
                   b.username as buyer_username, b.first_name as buyer_first_name, b.last_name as buyer_last_name,
                   s.username as seller_username, s.first_name as seller_first_name, s.last_name as seller_last_name
            FROM orders o
            JOIN gigs g ON g.id = o.gig_id
            JOIN users b ON b.id = o.buyer_id
            JOIN users s ON s.id = o.seller_id
            WHERE o.id = :id
        ');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function markAsPaid(int $orderId, string $paymentIntentId): void
    {
        $stmt = Flight::get('db')->prepare('
            UPDATE orders SET status = "paid", stripe_payment_intent_id = :pi WHERE id = :id
        ');
        $stmt->execute(['id' => $orderId, 'pi' => $paymentIntentId]);
    }

    public static function complete(int $orderId): bool
    {
        $db = Flight::get('db');
        $order = self::findById($orderId);

        if (!$order || $order['status'] === 'completed') {
            return false;
        }

        $db->beginTransaction();
        try {
            // 1. Mettre à jour le statut de la commande
            $stmt = $db->prepare('UPDATE orders SET status = "completed" WHERE id = :id');
            $stmt->execute(['id' => $orderId]);

            // 2. Créditer le portefeuille du vendeur
            Wallet::credit((int)$order['seller_id'], (float)$order['net_to_seller'], "Vente Gig #{$order['gig_id']} (Commande #{$orderId})", $orderId);

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    public static function countCompletedBySeller(int $sellerId): int
    {
        $stmt = Flight::get('db')->prepare('SELECT COUNT(*) FROM orders WHERE seller_id = :sid AND status = "completed"');
        $stmt->execute(['sid' => $sellerId]);
        return (int)$stmt->fetchColumn();
    }
}
