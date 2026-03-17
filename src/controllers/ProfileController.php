<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Auth;
use App\Models\Gig;
use App\Models\Job;
use App\Models\User;
use App\Models\Review;
use Flight;

final class ProfileController
{
    public static function show(?string $username = null): void
    {
        $current = Auth::user();
        if (!$current && !$username) {
            Flight::redirect('/connexion');
            return;
        }

        $user = $username ? User::findByUsername($username) : $current;

        if ($current) {
            User::updateLastSeen((int)$current['id']);
        }

        if ($user) {
            User::refreshLevel((int)$user['id']);
            User::updatePerformanceScore((int)$user['id']);
            // Re-fetch user to get the new level, last_seen and performance_score
            $user = User::findById((int)$user['id']);
            // Check if availability should expire
            $user = User::checkAvailabilityExpiration((int)$user['id'], $user);
        }

        if (!$user) {
            Flight::halt(404, 'Profil introuvable.');
        }

        $gigs = $user['role'] === 'freelancer' ? Gig::byUser((int)$user['id']) : [];
        $jobs = $user['role'] === 'employeur' ? Job::byUser((int)$user['id']) : [];

        $trustSignals = [];
        if ($user['role'] === 'freelancer') {
            $completedCount = \App\Models\Order::countCompletedBySeller((int)$user['id']);
            $trustSignals = [
                'completed_missions' => $completedCount,
                'member_since' => date('M Y', strtotime($user['created_at'] ?? 'now')),
                'response_time' => ' < 1h', // Mocked for now, or based on logic if available
                'profile_score' => 0
            ];
            
            // Re-use logic from AdminController for consistency or local calculation
            $gigsApproved = count(array_filter($gigs, fn($g) => ($g['status'] ?? '') === 'approved'));
            $score = 0;
            if (!empty($user['bio'])) $score += 25;
            if (!empty($user['skills'])) $score += 25;
            if (!empty($user['profile_photo'])) $score += 25;
            if ($gigsApproved > 0) $score += 25;
            $trustSignals['profile_score'] = $score;
        }

        $stmt = Flight::get('db')->prepare('SELECT * FROM portfolio_files WHERE user_id = :id ORDER BY id DESC');
        $stmt->execute(['id' => (int)$user['id']]);
        $portfolio = $stmt->fetchAll();

        $reviews = Review::byFreelancer((int)$user['id']);
        $reviewStats = Review::getStats((int)$user['id']);

        Flight::renderView('profile', [
            'profile' => $user,
            'currentUser' => $current,
            'gigs' => $gigs,
            'jobs' => $jobs,
            'portfolio' => $portfolio,
            'trustSignals' => $trustSignals,
            'reviews' => $reviews,
            'reviewStats' => $reviewStats,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null,
        ], 'main');
        unset($_SESSION['success'], $_SESSION['error']);
    }

    public static function update(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $r = Flight::request()->data;

        if (!check_csrf($r->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        User::updateProfile((int)$user['id'], [
            'bio' => trim((string)$r->bio),
            'skills' => trim((string)$r->skills),
            'title' => trim((string)$r->title),
            'daily_rate' => $r->daily_rate ? (float)$r->daily_rate : null,
            'languages' => trim((string)$r->languages),
            'availability' => $r->availability ?: 'available',
            'website' => trim((string)$r->website),
            'linkedin' => trim((string)$r->linkedin),
            'github' => trim((string)$r->github),
        ]);

        $_SESSION['success'] = 'Profil mis à jour.';
        Flight::redirect('/profil');
    }

    public static function uploadPortfolio(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $r = Flight::request()->data;

        if (!check_csrf($r->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        $file = $_FILES['portfolio_file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Erreur lors de l\'envoi du fichier.';
            Flight::redirect('/profil');
            return;
        }

        $allowed = ['image/jpeg','image/png','image/webp','application/pdf'];
        if (!in_array($file['type'], $allowed, true)) {
            $_SESSION['error'] = 'Format de fichier non supporté (JPG, PNG, WebP, PDF uniquement).';
            Flight::redirect('/profil');
            return;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safe = bin2hex(random_bytes(16)) . '.' . strtolower($ext);

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $safe)) {
            $stmt = Flight::get('db')->prepare('
                INSERT INTO portfolio_files (user_id, title, description, file_name, original_name) 
                VALUES (:u, :t, :d, :f, :o)
            ');
            $stmt->execute([
                'u' => (int)$user['id'],
                't' => trim((string)$r->title),
                'd' => trim((string)$r->description),
                'f' => $safe,
                'o' => $file['name']
            ]);
            $_SESSION['success'] = 'Élément ajouté au portfolio.';
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'enregistrement du fichier.';
        }

        Flight::redirect('/profil');
    }
}
