<?php

declare(strict_types=1);

namespace App\Models;

use Flight;

final class Job
{
    public static function create(array $data): int
    {
        $db = Flight::get('db');
        $stmt = $db->prepare('
            INSERT INTO jobs (user_id,title,description,budget_eur,deadline_text,category,hero_image,status)
            VALUES (:user_id,:title,:description,:budget,:deadline,:category,:hero_image,"pending")
        ');
        $stmt->execute([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'budget' => $data['budget_eur'],
            'deadline' => $data['deadline_text'],
            'category' => $data['category'],
            'hero_image' => $data['hero_image'] ?? null,
        ]);
        return (int) $db->lastInsertId();
    }

    public static function addAttachment(int $jobId, array $fileData): void
    {
        $stmt = Flight::get('db')->prepare('
            INSERT INTO job_attachments (job_id, file_path, file_name, file_type, file_size)
            VALUES (:job_id, :file_path, :file_name, :file_type, :file_size)
        ');
        $stmt->execute([
            'job_id' => $jobId,
            'file_path' => $fileData['file_path'],
            'file_name' => $fileData['file_name'],
            'file_type' => $fileData['file_type'] ?? null,
            'file_size' => $fileData['file_size'] ?? null,
        ]);
    }

    public static function getAttachments(int $jobId): array
    {
        $stmt = Flight::get('db')->prepare('SELECT * FROM job_attachments WHERE job_id = :job_id');
        $stmt->execute(['job_id' => $jobId]);
        return $stmt->fetchAll();
    }

    public static function byUser(int $userId): array
    {
        $stmt = Flight::get('db')->prepare('SELECT * FROM jobs WHERE user_id = :uid ORDER BY created_at DESC');
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    public static function search(?string $q, ?string $category): array
    {
        $sql = 'SELECT j.*, u.username, u.country FROM jobs j JOIN users u ON u.id = j.user_id WHERE j.status = "approved"';
        $params = [];

        if ($q) {
            $sql .= ' AND (j.title LIKE :q OR j.description LIKE :q2)';
            $params['q'] = "%{$q}%";
            $params['q2'] = "%{$q}%";
        }
        if ($category) {
            $sql .= ' AND j.category = :cat';
            $params['cat'] = $category;
        }

        $sql .= ' ORDER BY j.created_at DESC LIMIT 50';
        $stmt = Flight::get('db')->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function pending(): array
    {
        return Flight::get('db')->query('SELECT j.*, u.username FROM jobs j JOIN users u ON u.id = j.user_id WHERE j.status="pending" ORDER BY j.id DESC')->fetchAll();
    }

    public static function latestApproved(int $limit = 6): array
    {
        $stmt = Flight::get('db')->prepare('
            SELECT j.*, u.username as employer_name, u.profile_photo as employer_photo, u.country as employer_country 
            FROM jobs j 
            JOIN users u ON u.id = j.user_id 
            WHERE j.status = "approved" 
            ORDER BY j.created_at DESC 
            LIMIT :lim
        ');
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function approve(int $id): void
    {
        $stmt = Flight::get('db')->prepare('UPDATE jobs SET status="approved" WHERE id=:id');
        $stmt->execute(['id' => $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = Flight::get('db')->prepare('DELETE FROM jobs WHERE id=:id');
        $stmt->execute(['id' => $id]);
    }
}
