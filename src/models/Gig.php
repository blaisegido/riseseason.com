<?php

declare(strict_types=1);

namespace App\Models;

use Flight;

final class Gig
{
    public static function categoryTree(): array
    {
        $rows = Flight::get('db')->query('SELECT id, name, parent_id FROM categories WHERE name != "Autre" ORDER BY parent_id IS NULL DESC, name ASC')->fetchAll();

        $parents = [];
        $children = [];

        foreach ($rows as $row) {
            if ($row['parent_id'] === null) {
                $parents[(int)$row['id']] = [
                    'id' => (int)$row['id'],
                    'name' => $row['name'],
                    'children' => [],
                ];
                continue;
            }
            $children[] = $row;
        }

        foreach ($children as $child) {
            $pid = (int)$child['parent_id'];
            if (!isset($parents[$pid])) {
                continue;
            }
            $parents[$pid]['children'][] = [
                'id' => (int)$child['id'],
                'name' => $child['name'],
            ];
        }

        return array_values($parents);
    }

    public static function create(array $data): int
    {
        $db = Flight::get('db');
        $slug = self::uniqueSlug($data['title']);

        $stmt = $db->prepare('
            INSERT INTO gigs (
                user_id, title, slug, category_id, description, price_base, delivery_days,
                status, main_image, gallery, faq, extras, is_express, timezone_africa
            ) VALUES (
                :user_id, :title, :slug, :category_id, :description, :price_base, :delivery_days,
                "pending", "", :gallery, :faq, :extras, :is_express, :timezone_africa
            )
        ');

        $stmt->execute([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'slug' => $slug,
            'category_id' => $data['category_id'],
            'description' => $data['description'],
            'price_base' => $data['price_base'],
            'delivery_days' => $data['delivery_days'],
            'gallery' => json_encode([], JSON_UNESCAPED_UNICODE),
            'faq' => json_encode($data['faq'], JSON_UNESCAPED_UNICODE),
            'extras' => json_encode($data['extras'], JSON_UNESCAPED_UNICODE),
            'is_express' => $data['is_express'],
            'timezone_africa' => $data['timezone_africa'],
        ]);

        return (int) $db->lastInsertId();
    }

    public static function updateMedia(int $gigId, string $mainImage, array $gallery): void
    {
        $stmt = Flight::get('db')->prepare('UPDATE gigs SET main_image = :main_image, gallery = :gallery WHERE id = :id');
        $stmt->execute([
            'id' => $gigId,
            'main_image' => $mainImage,
            'gallery' => json_encode($gallery, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public static function latestApproved(int $limit = 12): array
    {
        $stmt = Flight::get('db')->prepare('
            SELECT g.*, g.price_base AS price_eur, c.name AS category,
                   u.username, u.country, u.profile_photo, u.level, u.subscription_status
            FROM gigs g
            JOIN users u ON u.id = g.user_id
            LEFT JOIN categories c ON c.id = g.category_id
            WHERE g.status = "approved" AND g.deleted_at IS NULL
            ORDER BY 
                g.is_sponsored DESC,
                (u.subscription_status = "premium") DESC,
                u.performance_score DESC,
                g.created_at DESC
            LIMIT :lim
        ');
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        foreach ($rows as &$row) {
            $row['gallery_items'] = self::decodeJsonArray($row['gallery']);
        }

        return $rows;
    }

    public static function byUser(int $userId): array
    {
        $stmt = Flight::get('db')->prepare('
            SELECT g.*, c.name AS category,
            CASE
              WHEN COALESCE(g.main_image, "") <> ""
               AND JSON_VALID(g.faq)
               AND JSON_LENGTH(g.faq) >= 3
               AND g.price_base > 0
               AND g.delivery_days >= 1
              THEN 1 ELSE 0 END AS is_optimized
            FROM gigs g
            LEFT JOIN categories c ON c.id = g.category_id
            WHERE g.user_id = :uid
              AND g.deleted_at IS NULL
            ORDER BY g.created_at DESC
        ');
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    public static function search(?string $q, ?string $category, ?string $country): array
    {
        $sql = '
            SELECT g.*, g.price_base AS price_eur, c.name AS category, u.username, u.country, u.level, u.subscription_status
            FROM gigs g
            JOIN users u ON u.id = g.user_id
            LEFT JOIN categories c ON c.id = g.category_id
            WHERE g.status = "approved" AND g.deleted_at IS NULL
        ';
        $params = [];

        if ($q) {
            $sql .= ' AND (g.title LIKE :q OR g.description LIKE :q2)';
            $params['q'] = "%{$q}%";
            $params['q2'] = "%{$q}%";
        }
        if ($category) {
            $sql .= ' AND c.name = :cat';
            $params['cat'] = $category;
        }
        if ($country) {
            $sql .= ' AND u.country = :country';
            $params['country'] = $country;
        }

        $sql .= ' 
            ORDER BY 
                g.is_sponsored DESC,
                (u.subscription_status = "premium") DESC,
                u.performance_score DESC,
                g.created_at DESC 
            LIMIT 50';
        $stmt = Flight::get('db')->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        foreach ($rows as &$row) {
            $row['gallery_items'] = self::decodeJsonArray($row['gallery']);
        }

        return $rows;
    }

    public static function findApprovedBySlug(string $slug): ?array
    {
        $stmt = Flight::get('db')->prepare('
            SELECT g.*, c.name AS category, p.name AS parent_category,
                   u.username, u.first_name, u.last_name, u.country
            FROM gigs g
            JOIN users u ON u.id = g.user_id
            LEFT JOIN categories c ON c.id = g.category_id
            LEFT JOIN categories p ON p.id = c.parent_id
            WHERE g.slug = :slug AND g.status = "approved" AND g.deleted_at IS NULL
            LIMIT 1
        ');
        $stmt->execute(['slug' => $slug]);
        $gig = $stmt->fetch();
        if (!$gig) {
            return null;
        }

        $gig['gallery_items'] = self::decodeJsonArray($gig['gallery']);
        $gig['faq_items'] = self::decodeJsonArray($gig['faq']);
        $gig['extras_items'] = self::decodeJsonArray($gig['extras']);

        return $gig;
    }

    public static function pendingForAdmin(): array
    {
        return Flight::get('db')->query('
            SELECT g.id, g.title, g.slug, g.price_base, g.delivery_days, g.created_at,
                   c.name AS category, u.username, u.email
            FROM gigs g
            JOIN users u ON u.id = g.user_id
            LEFT JOIN categories c ON c.id = g.category_id
            WHERE g.status = "pending" AND g.deleted_at IS NULL
            ORDER BY g.id DESC
        ')->fetchAll();
    }

    public static function approve(int $id, ?int $moderatorId = null): void
    {
        $db = Flight::get('db');
        $stmt = $db->prepare('
            UPDATE gigs SET
                status = "approved",
                moderated_by = :moderator,
                moderated_at = CURRENT_TIMESTAMP,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $id,
            'moderator' => $moderatorId,
        ]);
    }

    public static function reject(int $id, ?string $reason = null, ?string $feedback = null, ?int $moderatorId = null): void
    {
        $db = Flight::get('db');
        $stmt = $db->prepare('
            UPDATE gigs SET
                status = "rejected",
                rejection_reason = :reason,
                rejection_feedback = :feedback,
                moderated_by = :moderator,
                moderated_at = CURRENT_TIMESTAMP,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $id,
            'reason' => $reason,
            'feedback' => $feedback,
            'moderator' => $moderatorId,
        ]);
    }

    public static function findById(int $id): ?array
    {
        $stmt = Flight::get('db')->prepare('
            SELECT g.*, u.username, u.email, c.name as category_name, p.name as parent_category_name
            FROM gigs g
            LEFT JOIN users u ON g.user_id = u.id
            LEFT JOIN categories c ON g.category_id = c.id
            LEFT JOIN categories p ON c.parent_id = p.id
            WHERE g.id = :id AND g.deleted_at IS NULL
            LIMIT 1
        ');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return [
            'id' => (int)$row['id'],
            'user_id' => (int)$row['user_id'],
            'username' => $row['username'],
            'email' => $row['email'],
            'title' => $row['title'],
            'slug' => $row['slug'],
            'category_id' => (int)$row['category_id'],
            'category' => $row['parent_category_name'] ? $row['parent_category_name'] . ' > ' . $row['category_name'] : $row['category_name'],
            'description' => $row['description'],
            'price_base' => (float)$row['price_base'],
            'delivery_days' => (int)$row['delivery_days'],
            'is_express' => (int)$row['is_express'],
            'timezone_africa' => (int)$row['timezone_africa'],
            'faq' => self::decodeJsonArray($row['faq']),
            'extras' => self::decodeJsonArray($row['extras']),
            'main_image' => $row['main_image'],
            'gallery' => self::decodeJsonArray($row['gallery']),
            'status' => $row['status'],
            'rejection_reason' => $row['rejection_reason'],
            'rejection_feedback' => $row['rejection_feedback'],
            'moderated_by' => $row['moderated_by'] ? (int)$row['moderated_by'] : null,
            'moderated_at' => $row['moderated_at'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ];
    }

    public static function update(int $id, array $data): void
    {
        $db = Flight::get('db');
        $stmt = $db->prepare('
            UPDATE gigs SET
                title = :title,
                category_id = :category_id,
                description = :description,
                price_base = :price_base,
                delivery_days = :delivery_days,
                faq = :faq,
                extras = :extras,
                is_express = :is_express,
                timezone_africa = :timezone_africa,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'category_id' => $data['category_id'],
            'description' => $data['description'],
            'price_base' => $data['price_base'],
            'delivery_days' => $data['delivery_days'],
            'faq' => json_encode($data['faq']),
            'extras' => json_encode($data['extras']),
            'is_express' => $data['is_express'],
            'timezone_africa' => $data['timezone_africa'],
        ]);
    }

    public static function pending(): array
    {
        return self::pendingForAdmin();
    }

    public static function countByUser(int $userId): int
    {
        $stmt = Flight::get('db')->prepare('SELECT COUNT(*) FROM gigs WHERE user_id = :uid AND deleted_at IS NULL');
        $stmt->execute(['uid' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function sponsor(int $gigId, int $days): bool
    {
        $stmt = Flight::get('db')->prepare("
            UPDATE gigs SET 
                is_sponsored = 1, 
                sponsorship_expires_at = DATE_ADD(COALESCE(sponsorship_expires_at, CURRENT_TIMESTAMP), INTERVAL :days DAY)
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $gigId, 'days' => $days]);
    }

    private static function decodeJsonArray(?string $value): array
    {
        if (!$value) {
            return [];
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private static function uniqueSlug(string $title): string
    {
        $base = strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
        $base = mb_substr($base ?: 'service', 0, 70);

        $slug = $base;
        $i = 2;

        $db = Flight::get('db');
        while (true) {
            $stmt = $db->prepare('SELECT id FROM gigs WHERE slug = :slug LIMIT 1');
            $stmt->execute(['slug' => $slug]);
            if (!$stmt->fetch()) {
                return $slug;
            }
            $slug = $base . '-' . $i;
            $i++;
        }
    }
}
