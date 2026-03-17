<?php
use App\Middleware\Auth;
$authUser = Auth::user();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$navClass = static function (string $target) use ($path): string {
    return str_starts_with($path, $target)
        ? 'nav-link nav-link-active'
        : 'nav-link';
};
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
  <title>RiseSeason</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/app.css">
  <style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    @keyframes slide-in-right {
        from { transform: scaleX(0); transform-origin: left; }
        to { transform: scaleX(1); transform-origin: left; }
    }
    .animate-slide-in-right {
        animation: slide-in-right 0.3s ease-out forwards;
    }
  </style>
  <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script defer src="/js/app.js"></script>
</head>
<body class="app-shell text-gray-800">
<?php
$db = Flight::get('db');
$parentCategories = [];
if ($db) {
    try {
        $parentCategoriesStmt = $db->query('SELECT id, name FROM categories WHERE parent_id IS NULL AND name != "Autre" ORDER BY name ASC');
        $parentCategories = $parentCategoriesStmt ? $parentCategoriesStmt->fetchAll() : [];
    } catch (\Throwable $e) {
        // Silently fail to keep the layout rendering
    }
}
?>
<header class="sticky top-0 z-40 bg-white shadow-sm border-b border-gray-100" x-data="{ mobileOpen: false, userOpen: false, catOpen: false }">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 gap-4">
            <!-- Left side: Logo + Categories -->
            <div class="flex items-center gap-6">
                <a href="/" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 bg-orange-500 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-lg shadow-orange-500/20 group-hover:scale-105 transition-transform">R</div>
                    <span class="text-xl font-display font-black tracking-tight text-slate-900">RiseSeason</span>
                </a>

                <div class="hidden lg:block relative" @mouseenter="catOpen = true" @mouseleave="catOpen = false">
                    <button class="flex items-center gap-1.5 text-sm font-semibold text-gray-600 hover:text-orange-600 transition-colors py-5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                        Catégories
                    </button>
                    <!-- Categories Dropdown -->
                    <div x-show="catOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="absolute top-full left-0 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 p-2 z-50">
                        <?php foreach ($parentCategories as $cat): ?>
                            <a href="/recherche?category=<?= urlencode((string)$cat['name']) ?>" class="flex items-center px-4 py-2.5 text-sm text-gray-600 hover:bg-orange-50 hover:text-orange-600 rounded-xl transition-all">
                                <?= e($cat['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Center/Search (Flexible) -->
            <div class="flex-1 max-w-md hidden sm:block">
                <form action="/recherche" method="GET" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400 group-focus-within:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="q" placeholder="Trouver un service..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-full text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500/10 focus:border-orange-500 transition-all bg-gray-50/50">
                </form>
            </div>

            <!-- Right side: Notifications, Profile, Seller CTA -->
            <div class="flex items-center gap-2 sm:gap-4">
                <!-- Currency Toggle -->
                <button onclick="toggleCurrency()" id="currencyToggleDesktop"
                        class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-gray-200 hover:border-orange-300 hover:bg-orange-50 text-xs font-bold text-gray-600 hover:text-orange-600 transition-all cursor-pointer select-none"
                        title="Changer la devise">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1"/></svg>
                    <span id="currencyLabelDesktop">FCFA</span>
                </button>
                <div class="hidden md:flex items-center gap-1">
                    <?php if ($authUser): ?>
                        <a href="/gig/creer" class="mr-2 px-4 py-2 text-sm font-bold text-slate-700 hover:text-orange-600 hover:bg-orange-50 rounded-xl transition-all flex items-center gap-2">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                             Vendre
                        </a>
                        <a href="/messages" class="p-2 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded-xl transition-all relative group" title="Messages">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            <span class="absolute top-2 right-2 w-2 h-2 bg-orange-500 rounded-full border-2 border-white"></span>
                        </a>
                        <a href="/sauvegardes" class="p-2 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded-xl transition-all group" title="Sauvegardes">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </a>
                        
                        <!-- User Dropdown -->
                        <div class="relative ml-2" @click.away="userOpen = false">
                            <button @click="userOpen = !userOpen" class="flex items-center gap-2 p-0.5 rounded-full hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100">
                                <div class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                                    <?= strtoupper(substr($authUser['first_name'] ?? $authUser['username'] ?? 'U', 0, 1)) ?>
                                </div>
                            </button>
                            <div x-show="userOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="absolute top-full right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 z-50">
                                <div class="px-4 py-3 border-b border-gray-50 mb-1">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Compte</p>
                                    <p class="text-sm font-bold text-slate-900 truncate"><?= e($authUser['first_name'] ?? $authUser['username']) ?></p>
                                </div>
                                <a href="/profil" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 hover:bg-orange-50 hover:text-orange-600 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Mon Profil
                                </a>
                                <a href="/dashboard" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 hover:bg-orange-50 hover:text-orange-600 transition-all">
                                    <i class="fas fa-grid-2 w-4 text-center"></i>
                                    Tableau de bord
                                </a>
                                <?php if ($authUser['role'] === 'admin'): ?>
                                    <a href="/admin" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        Administration
                                    </a>
                                <?php endif; ?>
                                 <a href="/mon-compte/plans" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 hover:bg-orange-50 hover:text-orange-600 transition-all">
                                    <i class="fas fa-crown w-4 text-center"></i>
                                    Mon Plan & Crédits
                                </a>
                                <div class="h-px bg-gray-50 my-1"></div>
                                <a href="/deconnexion" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Déconnexion
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/connexion" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-slate-900 transition-all">Connexion</a>
                        <a href="/inscription" class="btn-primary px-5 py-2.5 text-sm font-bold shadow-lg shadow-orange-500/20">S'inscrire</a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Content -->
    <div x-show="mobileOpen" x-transition x-cloak class="md:hidden border-t border-gray-100 bg-white p-4 space-y-3">
        <?php if ($authUser): ?>
            <div class="flex items-center gap-3 px-2 py-3 mb-2 border-b border-gray-50">
                <div class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold">
                    <?= strtoupper(substr($authUser['first_name'] ?? $authUser['username'] ?? 'U', 0, 1)) ?>
                </div>
                <div>
                    <p class="font-bold text-slate-900"><?= e($authUser['first_name'] ?? $authUser['username']) ?></p>
                    <p class="text-xs text-gray-500"><?= e($authUser['email']) ?></p>
                </div>
            </div>
            <button onclick="toggleCurrency()" class="flex items-center gap-2 w-full px-3 py-2 rounded-xl text-gray-600 hover:bg-orange-50 hover:text-orange-600 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1"/></svg>
                Devise : <span id="currencyLabelMobile" class="font-bold">FCFA</span>
            </button>
            <a href="/messages" class="block px-3 py-2 rounded-xl text-gray-600 hover:bg-orange-50 hover:text-orange-600 transition-all">Messages</a>
            <a href="/sauvegardes" class="block px-3 py-2 rounded-xl text-gray-600 hover:bg-orange-50 hover:text-orange-600 transition-all">Sauvegardes</a>
            <a href="/dashboard" class="block px-3 py-2 rounded-xl text-gray-600 hover:bg-orange-50 hover:text-orange-600 transition-all">Mon Tableau de bord</a>
            <a href="/mon-compte/plans" class="block px-3 py-2 rounded-xl text-gray-600 hover:bg-orange-50 hover:text-orange-600 transition-all">Mon Plan & Crédits</a>
            <a href="/profil" class="block px-3 py-2 rounded-xl text-gray-600 hover:bg-orange-50 hover:text-orange-600 transition-all">Mon Profil</a>
            <a href="/deconnexion" class="block px-3 py-2 rounded-xl text-red-600 hover:bg-red-50 transition-all">Déconnexion</a>
        <?php else: ?>
            <a href="/connexion" class="block px-3 py-2 rounded-xl text-gray-600 hover:bg-gray-100 transition-all">Connexion</a>
            <a href="/inscription" class="block btn-primary text-center">Inscription</a>
        <?php endif; ?>
    </div>
</header>

<main>
    <?= $content ?>
</main>

<footer class="bg-slate-900 pt-20 pb-10 rounded-t-[1.5rem] mt-24 relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-1/4 w-64 h-64 bg-primary-600/5 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-primary-900/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            <!-- Brand & Description -->
            <div class="space-y-6">
                <a href="/" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-lg shadow-orange-500/20 group-hover:scale-105 transition-transform">R</div>
                    <span class="text-2xl font-display font-black tracking-tight text-white">RiseSeason</span>
                </a>
                <p class="text-gray-400 text-sm leading-relaxed max-w-xs">
                    La plateforme d'élite pour connecter les meilleurs talents africains avec des projets d'exception à travers le monde.
                </p>
                <div class="flex items-center gap-4">
                    <a href="#" class="w-9 h-9 flex items-center justify-center rounded-lg bg-white/5 border border-white/10 text-gray-400 hover:text-orange-500 hover:border-orange-500/50 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 flex items-center justify-center rounded-lg bg-white/5 border border-white/10 text-gray-400 hover:text-orange-500 hover:border-orange-500/50 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.058-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Platform Links -->
            <div>
                <h4 class="text-white font-bold mb-6">Plateforme</h4>
                <ul class="space-y-4">
                    <li><a href="/" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">Accueil</a></li>
                    <li><a href="/recherche" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">Trouver un talent</a></li>
                    <li><a href="/inscription" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">Devenir Freelance</a></li>
                    <li><a href="/connexion" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">Connexion</a></li>
                </ul>
            </div>

            <!-- Freelance Links -->
            <div>
                <h4 class="text-white font-bold mb-6">Freelances</h4>
                <ul class="space-y-4">
                    <li><a href="/gig/creer" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">Proposer un service</a></li>
                    <li><a href="/dashboard" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">Tableau de bord</a></li>
                    <li><a href="/messages" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">Messages</a></li>
                    <li><a href="/sauvegardes" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">Mes favoris</a></li>
                </ul>
            </div>

            <!-- Legal & Support -->
            <div>
                <h4 class="text-white font-bold mb-6">Support & Légal</h4>
                <ul class="space-y-4">
                    <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">Centre d'aide</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">Conditions d'utilisation</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">Confidentialité</a></li>
                    <li><a href="mailto:contact@riseseason.com" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">Nous contacter</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Line -->
        <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-gray-500 text-xs font-medium">
                © <?= date('Y') ?> RiseSeason. Fièrement développé en Afrique.
            </div>
            
            <!-- Payment Methods -->
            <div class="flex items-center gap-4 opacity-50 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-500">
                <span class="text-[10px] uppercase tracking-widest font-black text-gray-600 mr-2">Paiements sécurisés</span>
                <div class="flex gap-3">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/Wave_Logo_Blue.svg" alt="Wave" class="h-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/c8/Orange_logo.svg" alt="Orange Money" class="h-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" class="h-4">
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
// ─── Currency Toggle ───────────────────────────────────
function applyCurrency(currency) {
    var xofEls = document.querySelectorAll('.price-xof');
    var eurEls = document.querySelectorAll('.price-eur');
    var i;
    if (currency === 'EUR') {
        for (i = 0; i < xofEls.length; i++) xofEls[i].style.display = 'none';
        for (i = 0; i < eurEls.length; i++) eurEls[i].style.display = '';
    } else {
        for (i = 0; i < xofEls.length; i++) xofEls[i].style.display = '';
        for (i = 0; i < eurEls.length; i++) eurEls[i].style.display = 'none';
    }
    // Update labels
    var desktopLabel = document.getElementById('currencyLabelDesktop');
    var mobileLabel = document.getElementById('currencyLabelMobile');
    if (desktopLabel) desktopLabel.textContent = currency;
    if (mobileLabel) mobileLabel.textContent = currency;
}

function toggleCurrency() {
    var current = localStorage.getItem('rs_currency') || 'FCFA';
    var next = current === 'FCFA' ? 'EUR' : 'FCFA';
    localStorage.setItem('rs_currency', next);
    applyCurrency(next);
}

// Apply on page load
document.addEventListener('DOMContentLoaded', function() {
    var saved = localStorage.getItem('rs_currency') || 'FCFA';
    applyCurrency(saved);
});
</script>
</body>
</html>
