<?php

declare(strict_types=1);

namespace App\Models;

use Flight;
use PDO;

final class User
{
    public static function findById(int $id): ?array
    {
        $stmt = Flight::get('db')->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = Flight::get('db')->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public static function findByUsername(string $username): ?array
    {
        $stmt = Flight::get('db')->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        return $stmt->fetch() ?: null;
    }

    public static function all(): array
    {
        return Flight::get('db')->query('SELECT id,email,first_name,last_name,username,country,role,created_at FROM users ORDER BY id DESC')->fetchAll();
    }

    public static function create(array $data): int
    {
        $pdo = Flight::get('db');
        $username = self::generateUsername($data['first_name'], $data['last_name'], $pdo);

        $stmt = $pdo->prepare('
            INSERT INTO users (email,password_hash,first_name,last_name,username,country,role,verified_email,last_seen)
            VALUES (:email,:password_hash,:first_name,:last_name,:username,:country,:role,0,CURRENT_TIMESTAMP)
        ');
        $stmt->execute([
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $username,
            'country' => $data['country'],
            'role' => $data['role'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function updateProfile(int $id, array $data): void
    {
        $stmt = Flight::get('db')->prepare('
            UPDATE users SET 
                bio = :bio, 
                skills = :skills,
                title = :title,
                daily_rate = :daily_rate,
                languages = :languages,
                availability = :availability,
                website = :website,
                linkedin = :linkedin,
                github = :github
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $id,
            'bio' => $data['bio'],
            'skills' => $data['skills'],
            'title' => $data['title'],
            'daily_rate' => $data['daily_rate'],
            'languages' => $data['languages'],
            'availability' => $data['availability'],
            'website' => $data['website'],
            'linkedin' => $data['linkedin'],
            'github' => $data['github']
        ]);
    }

    public static function updatePhoto(int $id, string $fileName): void
    {
        $stmt = Flight::get('db')->prepare('UPDATE users SET profile_photo = :f WHERE id = :id');
        $stmt->execute(['f' => $fileName, 'id' => $id]);
    }

    public static function generateUsername(string $first, string $last, PDO $pdo): string
    {
        $base = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '.', $first . '.' . $last), '.'));
        $username = $base ?: 'membre';
        $i = 1;

        while (true) {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :u LIMIT 1');
            $stmt->execute(['u' => $username]);
            if (!$stmt->fetch()) {
                return $username;
            }
            $username = $base . $i++;
        }
    }

    public static function refreshLevel(int $userId): string
    {
        $db = Flight::get('db');
        $completedCount = Order::countCompletedBySeller($userId);
        $reviewStats = Review::getStats($userId);
        $avgRating = $reviewStats['avg_rating'];

        $newLevel = 'nouveau';

        if ($completedCount >= 50 && $avgRating >= 4.8) {
            $newLevel = 'elite';
        } elseif ($completedCount >= 20 && $avgRating >= 4.5) {
            $newLevel = 'expert';
        } elseif ($completedCount >= 5 && $avgRating >= 4.0) {
            $newLevel = 'confirmé';
        }

        $stmt = $db->prepare('UPDATE users SET level = :lvl WHERE id = :id');
        $stmt->execute(['lvl' => $newLevel, 'id' => $userId]);

        return $newLevel;
    }
    public static function updateLastSeen(int $id): void
    {
        $stmt = Flight::get('db')->prepare('UPDATE users SET last_seen = CURRENT_TIMESTAMP WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function checkAvailabilityExpiration(int $userId, array $userData): array
    {
        // Only for freelancers or based on business logic
        if (($userData['availability'] ?? 'unavailable') === 'unavailable') {
            return $userData;
        }

        $lastSeen = isset($userData['last_seen']) ? strtotime($userData['last_seen']) : time();
        $tenDaysAgo = time() - (10 * 24 * 60 * 60);

        if ($lastSeen < $tenDaysAgo) {
            $stmt = Flight::get('db')->prepare("UPDATE users SET availability = 'unavailable' WHERE id = :id");
            $stmt->execute(['id' => $userId]);
            $userData['availability'] = 'unavailable';
        }

        return $userData;
    }

    public static function isPremium(int $userId): bool
    {
        $stmt = Flight::get('db')->prepare('
            SELECT subscription_status, subscription_expires_at 
            FROM users 
            WHERE id = :id 
              AND subscription_status = "premium" 
              AND (subscription_expires_at IS NULL OR subscription_expires_at > CURRENT_TIMESTAMP)
            LIMIT 1
        ');
        $stmt->execute(['id' => $userId]);
        return (bool) $stmt->fetch();
    }

    public static function addSeeds(int $userId, int $amount): void
    {
        $stmt = Flight::get('db')->prepare('UPDATE users SET seeds = seeds + :amt WHERE id = :id');
        $stmt->execute(['amt' => $amount, 'id' => $userId]);
    }

    public static function spendSeeds(int $userId, int $amount): bool
    {
        $db = Flight::get('db');
        $stmt = $db->prepare('SELECT seeds FROM users WHERE id = :id FOR UPDATE');
        $stmt->execute(['id' => $userId]);
        $seeds = (int) ($stmt->fetchColumn() ?: 0);

        if ($seeds < $amount) {
            return false;
        }

        $stmt = $db->prepare('UPDATE users SET seeds = seeds - :amt WHERE id = :id');
        $stmt->execute(['amt' => $amount, 'id' => $userId]);
        return true;
    }

    public static function updatePerformanceScore(int $userId): void
    {
        $reviewStats = Review::getStats($userId);
        $completedCount = Order::countCompletedBySeller($userId);
        
        $avgRating = (float)$reviewStats['avg_rating'];
        
        // Example formula: (Rating * 10) + (Missions / 5)
        $score = ($avgRating * 10) + ($completedCount / 5);
        $score = min(100, $score); // Cap at 100

        $stmt = Flight::get('db')->prepare('UPDATE users SET performance_score = :score WHERE id = :id');
        $stmt->execute(['score' => $score, 'id' => $userId]);
    }
}
