<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Auth;
use App\Models\Gig;
use App\Models\Job;
use App\Models\Post;
use App\Models\User;
use Flight;

class AdminController
{
    /**
     * Point d'entrée universel du dashboard chargé de la redirection par rôle.
     */
    public static function dashboard(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        
        switch ($user['role']) {
            case 'admin':
                self::index();
                break;
            case 'contributeur':
                self::listPosts();
                break;
            case 'employeur':
                self::employerDashboard();
                break;
            case 'freelancer':
            default:
                self::freelancerDashboard();
                break;
        }
    }

    public static function index(): void
    {
        Auth::requireRole('admin');
        
        $db = Flight::get('db');
        $commissions = $db->query('SELECT SUM(commission) as total FROM orders WHERE status = "completed"')->fetch()['total'] ?? 0;
        $volume = $db->query('SELECT SUM(amount) as total FROM orders WHERE status = "completed"')->fetch()['total'] ?? 0;

        Flight::renderView('admin', [
            'users' => User::all(),
            'pendingGigs' => Gig::pendingForAdmin(),
            'pendingJobs' => Job::pending(),
            'postsCount' => count(Post::all()),
            'commissions' => (float)$commissions,
            'gmv' => (float)$volume,
        ], 'admin');
    }

    public static function employerDashboard(): void
    {
        Auth::requireRole('employeur');
        $user = Auth::user();
        $db = Flight::get('db');

        // Missions publiées par ce recruteur
        $stmt = $db->prepare('SELECT * FROM jobs WHERE user_id = :uid ORDER BY created_at DESC');
        $stmt->execute(['uid' => $user['id']]);
        $activeJobs = $stmt->fetchAll();

        // Commandes de services passées par ce recruteur
        $stmt = $db->prepare('SELECT * FROM orders WHERE buyer_id = :uid ORDER BY created_at DESC');
        $stmt->execute(['uid' => $user['id']]);
        $activeOrders = $stmt->fetchAll();

        // Total investi (somme des commandes complétées)
        $stmt = $db->prepare('SELECT SUM(amount) FROM orders WHERE buyer_id = :uid AND status = "completed"');
        $stmt->execute(['uid' => $user['id']]);
        $totalSpent = (float)$stmt->fetchColumn();

        Flight::renderView('dashboard/employer', [
            'activeJobs' => $activeJobs,
            'activeOrders' => $activeOrders,
            'totalSpent' => $totalSpent
        ], 'admin');
    }

    public static function freelancerDashboard(): void
    {
        Auth::requireRole('freelancer');
        $user = Auth::user();
        $db = Flight::get('db');

        // Services (Gigs) de ce freelance
        $stmt = $db->prepare('SELECT * FROM gigs WHERE user_id = :uid ORDER BY created_at DESC');
        $stmt->execute(['uid' => $user['id']]);
        $myGigs = $stmt->fetchAll();

        // Compteurs gigs par statut
        $gigsApproved = 0;
        $gigsPending = 0;
        $gigsRejected = 0;
        foreach ($myGigs as $g) {
            if (($g['status'] ?? '') === 'approved') $gigsApproved++;
            elseif (($g['status'] ?? '') === 'pending') $gigsPending++;
            elseif (($g['status'] ?? '') === 'rejected') $gigsRejected++;
        }

        // Ventes (Commandes reçues)
        $stmt = $db->prepare('
            SELECT o.*, u.username as buyer_username 
            FROM orders o 
            JOIN users u ON u.id = o.buyer_id 
            WHERE o.seller_id = :uid 
            ORDER BY o.created_at DESC
        ');
        $stmt->execute(['uid' => $user['id']]);
        $allSales = $stmt->fetchAll();

        // Séparer les ventes actives (en cours) et les ventes complétées
        $activeSales = [];
        $completedSalesCount = 0;
        $totalSalesCount = count($allSales);
        foreach ($allSales as $sale) {
            $status = $sale['status'] ?? '';
            if ($status === 'completed') {
                $completedSalesCount++;
            }
            if (in_array($status, ['paid', 'in_progress', 'delivered', 'pending'])) {
                $activeSales[] = $sale;
            }
        }

        // Portefeuille
        $stmt = $db->prepare('SELECT * FROM user_wallets WHERE user_id = :uid');
        $stmt->execute(['uid' => $user['id']]);
        $wallet = $stmt->fetch() ?: ['balance' => 0, 'pending_balance' => 0];

        // Total gagné
        $stmt = $db->prepare('SELECT SUM(net_to_seller) FROM orders WHERE seller_id = :uid AND status = "completed"');
        $stmt->execute(['uid' => $user['id']]);
        $totalEarned = (float)$stmt->fetchColumn();

        // Profil completeness
        $profileScore = 0;
        if (!empty($user['bio'])) $profileScore += 25;
        if (!empty($user['skills'])) $profileScore += 25;
        if (!empty($user['profile_photo'])) $profileScore += 25;
        if ($gigsApproved > 0) $profileScore += 25;

        Flight::renderView('dashboard/freelancer', [
            'user' => $user,
            'myGigs' => $myGigs,
            'activeSales' => $activeSales,
            'allSales' => $allSales,
            'wallet' => $wallet,
            'totalEarned' => $totalEarned,
            'completedSalesCount' => $completedSalesCount,
            'totalSalesCount' => $totalSalesCount,
            'gigsApproved' => $gigsApproved,
            'gigsPending' => $gigsPending,
            'gigsRejected' => $gigsRejected,
            'profileScore' => $profileScore,
        ], 'admin');
    }

    public static function listPosts(): void
    {
        Auth::requireAnyRole(['admin', 'contributeur']);
        
        Flight::renderView('admin/posts/index', [
            'posts' => Post::all(),
        ], 'admin');
    }

    public static function showCreatePost(): void
    {
        Auth::requireAnyRole(['admin', 'contributeur']);
        
        Flight::renderView('admin/posts/editor', [
            'post' => null,
            'title' => 'Créer un article'
        ], 'admin');
    }

    public static function showEditPost(int $id): void
    {
        Auth::requireAnyRole(['admin', 'contributeur']);
        
        $post = Post::findById($id);
        if (!$post) {
            Flight::halt(404, 'Article non trouvé');
        }

        Flight::renderView('admin/posts/editor', [
            'post' => $post,
            'title' => 'Modifier l\'article'
        ], 'admin');
    }

    public static function savePost(): void
    {
        Auth::requireAnyRole(['admin', 'contributeur']);
        $user = Auth::user();
        $data = Flight::request()->data;

        if (!check_csrf($data->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        $id = $data->id ? (int)$data->id : null;
        $postData = [
            'user_id' => $user['id'],
            'title' => $data->title,
            'slug' => $data->slug ?: self::slugify($data->title),
            'content' => $data->content,
            'excerpt' => $data->excerpt,
            'status' => $data->status,
        ];

        if ($id) {
            Post::update($id, $postData);
        } else {
            Post::create($postData);
        }

        Flight::redirect('/admin/posts');
    }

    public static function deletePost(int $id): void
    {
        Auth::requireRole('admin');
        if (!check_csrf(Flight::request()->data->csrf ?? null)) {
            Flight::halt(419, 'CSRF invalide.');
        }

        Post::delete($id);
        Flight::redirect('/admin/posts');
    }

    private static function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        if (empty($text)) return 'n-a';
        return $text;
    }
}
