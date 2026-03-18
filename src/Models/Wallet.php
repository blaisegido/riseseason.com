<?php

declare(strict_types=1);

namespace App\Models;

use Flight;

final class Wallet
{
    public static function get(int $userId): array
    {
        $stmt = Flight::get('db')->prepare('SELECT * FROM user_wallets WHERE user_id = :uid');
        $stmt->execute(['uid' => $userId]);
        $wallet = $stmt->fetch();

        if (!$wallet) {
            // Création automatique si absent
            Flight::get('db')->prepare('INSERT IGNORE INTO user_wallets (user_id) VALUES (:uid)')->execute(['uid' => $userId]);
            return self::get($userId);
        }

        return $wallet;
    }

    public static function credit(int $userId, float $amount, string $description, ?int $orderId = null): void
    {
        $db = Flight::get('db');
        // Mise à jour du solde
        $stmt = $db->prepare('UPDATE user_wallets SET balance = balance + :amount WHERE user_id = :uid');
        $stmt->execute(['amount' => $amount, 'uid' => $userId]);

        // Enregistrement transaction
        $stmt = $db->prepare('
            INSERT INTO transactions (user_id, order_id, type, amount, description)
            VALUES (:uid, :oid, "credit", :amount, :desc)
        ');
        $stmt->execute([
            'uid'    => $userId,
            'oid'    => $orderId,
            'amount' => $amount,
            'desc'   => $description,
        ]);
    }

    public static function withdraw(int $userId, float $amount): bool
    {
        $db = Flight::get('db');
        $wallet = self::get($userId);

        if ((float)$wallet['balance'] < $amount) {
            return false;
        }

        $db->beginTransaction();
        try {
            // Débiter le solde
            $stmt = $db->prepare('UPDATE user_wallets SET balance = balance - :amount WHERE user_id = :uid');
            $stmt->execute(['amount' => $amount, 'uid' => $userId]);

            // Enregistrement transaction
            $stmt = $db->prepare('
                INSERT INTO transactions (user_id, type, amount, description)
                VALUES (:uid, "withdrawal", :amount, "Retrait de fonds")
            ');
            $stmt->execute([
                'uid'    => $userId,
                'amount' => $amount,
            ]);

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            return false;
        }
    }
}
