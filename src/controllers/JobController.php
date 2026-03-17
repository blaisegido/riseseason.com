<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Auth;
use App\Models\Job;
use Flight;

final class JobController
{
    public static function showCreate(): void
    {
        Auth::requireRole('employeur');
        
        // Récupérer les catégories pour la cohérence avec les Gigs
        $db = Flight::get('db');
        try {
            $rows = $db->query('SELECT name FROM categories ORDER BY parent_id IS NULL DESC, name ASC')->fetchAll();
        } catch (\Throwable $e) {
            $rows = $db->query('SELECT name FROM categories ORDER BY name ASC')->fetchAll();
        }
        $categories = array_map(static fn(array $row): string => (string) $row['name'], $rows);

        Flight::renderView('job-create', [
            'error' => null,
            'categories' => $categories
        ], 'admin');
    }

    public static function create(): void
    {
        Auth::requireRole('employeur');
        $user = Auth::user();
        $r = Flight::request()->data;

        if (!check_csrf($r->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        $title = trim((string)$r->title);
        $desc = trim((string)$r->description);
        $budgetXof = (float)$r->budget_xof;
        $budget = $budgetXof / get_eur_to_xof_rate();
        $deadline = trim((string)$r->deadline_text);
        $category = trim((string)$r->category);

        if ($title === '' || $desc === '' || $budget <= 0 || $deadline === '' || $category === '') {
            Flight::renderView('job-create', ['error' => 'Tous les champs sont obligatoires.', 'categories' => self::getCategories()]);
            return;
        }

        $db = Flight::get('db');
        try {
            $db->beginTransaction();

            $jobId = Job::create([
                'user_id' => (int)$user['id'],
                'title' => $title,
                'description' => $desc,
                'budget_eur' => $budget,
                'deadline_text' => $deadline,
                'category' => $category,
            ]);

            $targetDir = __DIR__ . '/../../public/uploads/jobs/' . $jobId . '/';
            if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                throw new \RuntimeException('Impossible de créer le dossier des uploads.');
            }

            // Gestion de l'image descriptive
            $heroImage = '';
            if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
                $heroImage = self::storeFile($_FILES['hero_image'], $targetDir, 'hero_' . $jobId);
                $stmt = $db->prepare('UPDATE jobs SET hero_image = :img WHERE id = :id');
                $stmt->execute(['img' => $heroImage, 'id' => $jobId]);
            }

            // Gestion des pièces jointes
            if (isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
                foreach ($_FILES['attachments']['name'] as $i => $name) {
                    if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_OK) {
                        $fileData = [
                            'name' => $name,
                            'type' => $_FILES['attachments']['type'][$i],
                            'tmp_name' => $_FILES['attachments']['tmp_name'][$i],
                            'error' => $_FILES['attachments']['error'][$i],
                            'size' => $_FILES['attachments']['size'][$i],
                        ];
                        $path = self::storeFile($fileData, $targetDir, 'attach_' . bin2hex(random_bytes(4)));
                        Job::addAttachment($jobId, [
                            'file_path' => $path,
                            'file_name' => $name,
                            'file_type' => $fileData['type'],
                            'file_size' => $fileData['size'],
                        ]);
                    }
                }
            }

            $db->commit();
            $_SESSION['success'] = 'Job publié. En attente de validation admin.';
            Flight::redirect('/profil');

        } catch (\Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log('Erreur création job: ' . $e->getMessage());
            Flight::renderView('job-create', [
                'error' => 'Une erreur est survenue lors de la publication. ' . $e->getMessage(),
                'categories' => self::getCategories()
            ]);
        }
    }

    private static function getCategories(): array
    {
        $db = Flight::get('db');
        try {
            $rows = $db->query('SELECT name FROM categories ORDER BY parent_id IS NULL DESC, name ASC')->fetchAll();
        } catch (\Throwable $e) {
            $rows = $db->query('SELECT name FROM categories ORDER BY name ASC')->fetchAll();
        }
        return array_map(static fn(array $row): string => (string) $row['name'], $rows);
    }

    private static function storeFile(array $file, string $targetDir, string $prefix): string
    {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeName = $prefix . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $absolute = $targetDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $absolute)) {
            throw new \RuntimeException('Erreur lors du déplacement du fichier.');
        }

        $jobId = basename(dirname($absolute));
        return 'uploads/jobs/' . $jobId . '/' . $safeName;
    }
}
