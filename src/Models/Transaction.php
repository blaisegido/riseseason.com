<?php

declare(strict_types=1);

namespace App\Models;

use Flight;

final class Transaction
{
    public static function log(int $userId, string $type, float $amount, int $seeds, string $status = 'completed'): int
    {
        $db = Flight::get('db');
        $stmt = $db->prepare('
            INSERT INTO transactions (user_id, type, amount, seeds, status)
            VALUES (:user_id, :type, :amount, :seeds, :status)
        ');
        $stmt->execute([
            'user_id' => $userId,
            'type' => $type,
            'amount' => $amount,
            'seeds' => $seeds,
            'status' => $status
        ]);
        return (int) $db->lastInsertId();
    }

    public static function byUser(int $userId): array
    {
        $stmt = Flight::get('db')->prepare('SELECT * FROM transactions WHERE user_id = :uid ORDER BY created_at DESC');
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }
}
