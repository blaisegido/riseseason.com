<?php

declare(strict_types=1);

namespace App\Models;

use Flight;
use PDO;

class Post
{
    public static function all(): array
    {
        return Flight::get('db')->query('SELECT p.*, u.username as author_name FROM posts p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC')->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $stmt = Flight::get('db')->prepare('SELECT * FROM posts WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = Flight::get('db')->prepare('SELECT p.*, u.username as author_name FROM posts p LEFT JOIN users u ON p.user_id = u.id WHERE p.slug = ? AND p.status = "published"');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): bool
    {
        $stmt = Flight::get('db')->prepare('
            INSERT INTO posts (user_id, title, slug, content, excerpt, featured_image, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        return $stmt->execute([
            $data['user_id'],
            $data['title'],
            $data['slug'],
            $data['content'],
            $data['excerpt'] ?? null,
            $data['featured_image'] ?? null,
            $data['status'] ?? 'draft'
        ]);
    }

    public static function update(int $id, array $data): bool
    {
        $stmt = Flight::get('db')->prepare('
            UPDATE posts 
            SET title = ?, slug = ?, content = ?, excerpt = ?, featured_image = ?, status = ?
            WHERE id = ?
        ');
        return $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['content'],
            $data['excerpt'] ?? null,
            $data['featured_image'] ?? null,
            $data['status'],
            $id
        ]);
    }

    public static function delete(int $id): bool
    {
        $stmt = Flight::get('db')->prepare('DELETE FROM posts WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public static function latest(int $limit = 10): array
    {
        $stmt = Flight::get('db')->prepare('SELECT p.*, u.username as author_name FROM posts p LEFT JOIN users u ON p.user_id = u.id WHERE p.status = "published" ORDER BY p.created_at DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
