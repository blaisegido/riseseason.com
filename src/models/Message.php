<?php

declare(strict_types=1);

namespace App\Models;

use Flight;

final class Message
{
    public static function send(int $senderId, int $receiverId, string $content): void
    {
        $stmt = Flight::get('db')->prepare('
            INSERT INTO messages (sender_id, receiver_id, content)
            VALUES (:s, :r, :c)
        ');
        $stmt->execute(['s' => $senderId, 'r' => $receiverId, 'c' => $content]);
    }

    public static function conversations(int $userId): array
    {
        $stmt = Flight::get('db')->prepare('
            SELECT DISTINCT
              IF(m.sender_id = :uid_if, m.receiver_id, m.sender_id) as partner_id
            FROM messages m
            WHERE m.sender_id = :uid_sender OR m.receiver_id = :uid_receiver
            ORDER BY m.id DESC
        ');
        $stmt->execute([
            'uid_if' => $userId,
            'uid_sender' => $userId,
            'uid_receiver' => $userId,
        ]);
        $partners = $stmt->fetchAll();

        $result = [];
        foreach ($partners as $p) {
            $user = User::findById((int)$p['partner_id']);
            if ($user) $result[] = $user;
        }
        return $result;
    }

    public static function between(int $userA, int $userB): array
    {
        $stmt = Flight::get('db')->prepare('
            SELECT * FROM messages
            WHERE (sender_id = :a1 AND receiver_id = :b1) OR (sender_id = :b2 AND receiver_id = :a2)
            ORDER BY created_at ASC
        ');
        $stmt->execute([
            'a1' => $userA,
            'b1' => $userB,
            'b2' => $userB,
            'a2' => $userA,
        ]);
        return $stmt->fetchAll();
    }
}
