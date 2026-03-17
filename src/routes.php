<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\GigController;
use App\Controllers\JobController;
use App\Controllers\MessageController;
use App\Controllers\ProfileController;
use App\Controllers\SavedGigController;
use App\Middleware\Auth;
use App\Models\Gig;
use App\Models\Job;
use App\Models\SavedGig;
use App\Models\User;

try {
    $rows = Flight::get('db')->query('SELECT name FROM categories WHERE name != "Autre" ORDER BY parent_id IS NULL DESC, name ASC')->fetchAll();
} catch (\Throwable $e) {
    $rows = Flight::get('db')->query('SELECT name FROM categories WHERE name != "Autre" ORDER BY name ASC')->fetchAll();
}
$categories = array_map(static fn(array $row): string => (string) $row['name'], $rows);

// --- Système Économique (ComeUp Style) ---
Flight::route('POST /paiement/creer', [App\Controllers\PaymentController::class, 'createCheckout']);
Flight::route('GET /paiement/simulation-success', [App\Controllers\PaymentController::class, 'handleSuccess']);
Flight::route('GET /commande/@id:[0-9]+', [App\Controllers\PaymentController::class, 'showOrder']);
Flight::route('POST /commande/@id:[0-9]+/valider', [App\Controllers\PaymentController::class, 'approveOrder']);
Flight::route('GET /portefeuille', [App\Controllers\PaymentController::class, 'showWallet']);
Flight::route('POST /portefeuille/retrait', [App\Controllers\PaymentController::class, 'withdraw']);

// --- Gestion financière Freelance ---
Flight::route('GET /mon-compte/plans', [App\Controllers\PaymentController::class, 'showPlans']);
Flight::route('POST /mon-compte/abonnement', [App\Controllers\PaymentController::class, 'buyPremium']);
Flight::route('POST /mon-compte/seeds', [App\Controllers\PaymentController::class, 'buySeeds']);
Flight::route('POST /gig/@id:[0-9]+/sponsor', [App\Controllers\PaymentController::class, 'sponsorGig']);

// --- Dashboards & Administration ---
Flight::route('GET /dashboard', [App\Controllers\AdminController::class, 'dashboard']);
Flight::route('GET /admin', [App\Controllers\AdminController::class, 'index']);
Flight::route('GET /admin/posts', [App\Controllers\AdminController::class, 'listPosts']);
Flight::route('GET /admin/posts/creer', [App\Controllers\AdminController::class, 'showCreatePost']);
Flight::route('GET /admin/posts/modifier/@id:[0-9]+', [App\Controllers\AdminController::class, 'showEditPost']);
Flight::route('POST /admin/posts/enregistrer', [App\Controllers\AdminController::class, 'savePost']);
Flight::route('POST /admin/posts/supprimer/@id:[0-9]+', [App\Controllers\AdminController::class, 'deletePost']);

// Initialisation globale si nécessaire...

Flight::route('GET /', function () use ($categories) {
    $authUser = Auth::user();
    $savedIds = $authUser ? SavedGig::savedIdsByUser((int) $authUser['id']) : [];
    
    // Récupérer quelques freelances pour la section "Featured"
    $db = Flight::get('db');
    $featuredFreelancers = $db->query('SELECT username, first_name, last_name, profile_photo, country, bio, level FROM users WHERE role = "freelancer" AND profile_photo IS NOT NULL LIMIT 4')->fetchAll();

    $categoryFilter = trim((string) (Flight::request()->query->category ?? ''));
    $gigs = $categoryFilter ? Gig::search(null, $categoryFilter, null) : Gig::latestApproved(12);

    Flight::renderView('home', [
        'gigs'                => $gigs,
        'jobs'                => Job::latestApproved(6),
        'savedIds'            => $savedIds,
        'categories'          => $categories,
        'featuredFreelancers' => $featuredFreelancers,
    ]);
});

Flight::route('GET /mes-services', [GigController::class, 'myServices']);

Flight::route('GET /inscription', [AuthController::class, 'showRegister']);
Flight::route('POST /inscription', [AuthController::class, 'register']);
Flight::route('GET /connexion', [AuthController::class, 'showLogin']);
Flight::route('POST /connexion', [AuthController::class, 'login']);
Flight::route('GET /deconnexion', [AuthController::class, 'logout']);

Flight::route('GET /profil', fn() => ProfileController::show(null));
Flight::route('GET /profil/@username', fn(string $username) => ProfileController::show($username));
Flight::route('POST /profil', [ProfileController::class, 'update']);
Flight::route('POST /profil/upload', [ProfileController::class, 'uploadPortfolio']);

Flight::route('POST /gig/@id/save', [SavedGigController::class, 'toggle']);
Flight::route('GET /sauvegardes', [SavedGigController::class, 'index']);

Flight::route('GET /gig/creer', [GigController::class, 'create']);
Flight::route('POST /gig/creer', [GigController::class, 'create']);
Flight::route('GET /gig/@id/modifier', [GigController::class, 'edit']);
Flight::route('POST /gig/@id/modifier', [GigController::class, 'edit']);
Flight::route('GET /gig/@slug/statut', [GigController::class, 'status']);
Flight::route('POST /gig/brouillon', [GigController::class, 'saveDraft']);
Flight::route('POST /gig/@id:[0-9]+/supprimer', [GigController::class, 'delete']);
Flight::route('POST /gig/@id:[0-9]+/toggle-pause', [GigController::class, 'togglePause']);
Flight::route('GET /gig/@slug', [GigController::class, 'show']);

Flight::route('GET /job/publier', [JobController::class, 'showCreate']);
Flight::route('POST /job/publier', [JobController::class, 'create']);

Flight::route('GET /recherche', function () use ($categories) {
    $q = trim((string) (Flight::request()->query->q ?? ''));
    $cat = trim((string) (Flight::request()->query->category ?? ''));
    $country = trim((string) (Flight::request()->query->country ?? ''));

    Flight::renderView('search', [
        'q' => $q,
        'category' => $cat,
        'country' => $country,
        'categories' => $categories,
        'gigs' => Gig::search($q ?: null, $cat ?: null, $country ?: null),
        'jobs' => Job::search($q ?: null, $cat ?: null),
    ]);
});

Flight::route('GET /messages', [MessageController::class, 'index']);
Flight::route('POST /messages', [MessageController::class, 'send']);

use App\Controllers\AdminController;

Flight::route('GET /admin', [AdminController::class, 'index']);
Flight::route('GET /admin/posts', [AdminController::class, 'listPosts']);
Flight::route('GET /admin/posts/creer', [AdminController::class, 'showCreatePost']);
Flight::route('GET /admin/posts/@id/modifier', [AdminController::class, 'showEditPost']);
Flight::route('POST /admin/posts/sauvegarder', [AdminController::class, 'savePost']);
Flight::route('POST /admin/posts/@id/supprimer', [AdminController::class, 'deletePost']);

Flight::route('GET /admin/gigs', [GigController::class, 'adminList']);
Flight::route('GET /admin/gigs/@id/review', [GigController::class, 'adminReview']);
Flight::route('POST /admin/gigs/@id/approve', [GigController::class, 'adminApprove']);
Flight::route('POST /admin/gigs/@id/reject', [GigController::class, 'adminReject']);

// Compat routes legacy
Flight::route('POST /admin/gig/@id/approve', [GigController::class, 'adminApprove']);
Flight::route('POST /admin/gig/@id/delete', [GigController::class, 'adminReject']);

Flight::route('POST /admin/job/@id/approve', function (int $id) {
    Auth::requireRole('admin');
    if (!check_csrf(Flight::request()->data->csrf ?? null)) Flight::halt(419, 'CSRF invalide.');
    Job::approve($id);
    Flight::redirect('/admin');
});

Flight::route('POST /admin/job/@id/delete', function (int $id) {
    Auth::requireRole('admin');
    if (!check_csrf(Flight::request()->data->csrf ?? null)) Flight::halt(419, 'CSRF invalide.');
    Job::delete($id);
    Flight::redirect('/admin');
});
