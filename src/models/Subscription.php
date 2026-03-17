<?php

declare(strict_types=1);

namespace App\Models;

use Flight;

final class Subscription
{
    public static function getUserPlan(int $userId): array
    {
        $stmt = Flight::get('db')->prepare('
            SELECT * FROM subscriptions 
            WHERE user_id = :uid AND status = "active" 
            AND (expires_at IS NULL OR expires_at > NOW())
            ORDER BY created_at DESC LIMIT 1
        ');
        $stmt->execute(['uid' => $userId]);
        $sub = $stmt->fetch();

        return $sub ?: ['plan_name' => 'basic', 'status' => 'active'];
    }

    public static function subscribe(int $userId, string $plan, int $days = 30): void
    {
        $db = Flight::get('db');
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$days} days"));

        $stmt = $db->prepare('
            INSERT INTO subscriptions (user_id, plan_name, status, expires_at)
            VALUES (:uid, :plan, "active", :expires)
            ON DUPLICATE KEY UPDATE status = "active", expires_at = :expires
        ');
        $stmt->execute([
            'uid'     => $userId,
            'plan'    => $plan,
            'expires' => $expiresAt,
        ]);
    }
}
