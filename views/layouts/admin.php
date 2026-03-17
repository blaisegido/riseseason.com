<?php
use App\Middleware\Auth;
$authUser = Auth::user();
$role = $authUser ? ($authUser['role'] ?? 'freelancer') : 'guest';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$navClass = static function (string $target) use ($path): string {
    $active = str_starts_with($path, $target);
    return $active
        ? 'flex items-center gap-3 px-4 py-3 bg-orange-600/10 text-orange-500 font-bold rounded-xl transition-all border-l-4 border-orange-500'
        : 'flex items-center gap-3 px-4 py-3 text-gray-400 hover:bg-white/5 hover:text-white rounded-xl transition-all group';
};

$iconClass = 'w-5 h-5 transition-transform group-hover:scale-110';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
  <title>Tableau de bord - RiseSeason</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/css/app.css">
  <style>
    body { font-family: var(--font-admin); }
  </style>
  <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    [x-cloak] { display: none !important; }
    .sidebar-gradient { background: linear-gradient(180deg, #0F172A 0%, #1e293b 100%); }
  </style>
</head>
<body class="bg-[#F8F9FA] font-sans antialiased text-slate-900">
  <div class="flex min-h-screen" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" 
         x-cloak
         @click="sidebarOpen = false" 
         class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm md:hidden transition-opacity"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Mobile Sidebar Menu -->
    <aside x-show="sidebarOpen" 
           x-cloak
           class="fixed inset-y-0 left-0 z-50 w-72 sidebar-gradient text-white transform md:hidden flex flex-col transition-transform"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full">
      <div class="p-8 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-600/20">
                <i class="fas fa-bolt text-lg"></i>
            </div>
            <span class="text-xl font-black tracking-tight tracking-tighter">RiseSeason</span>
        </div>
        <button @click="sidebarOpen = false" class="text-white/50 hover:text-white">
            <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <!-- Re-use the same nav content as desktop or a simplified version -->
        <!-- For simplicity and avoiding duplication in this template, we can use a PHP partial or include if needed, but here we just repeat the key items for the demo -->
        <a href="/dashboard" class="<?= $navClass('/dashboard') ?>">
          <i class="fas fa-grid-2 <?= $iconClass ?> fa-th-large"></i>
          <span>Tableau de bord</span>
        </a>
        <a href="/profil" class="<?= $navClass('/profil') ?>">
          <i class="fas fa-user-circle <?= $iconClass ?>"></i>
          <span>Mon Profil</span>
        </a>

        <?php if (in_array($role, ['admin', 'contributeur'])): ?>
            <div class="pt-6 pb-2 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Administration</div>
            <a href="/admin/posts" class="<?= $navClass('/admin/posts') ?>">
                <i class="fas fa-feather-pointed <?= $iconClass ?>"></i>
                <span>Articles (CMS)</span>
            </a>
        <?php endif; ?>

        <?php if ($role === 'employeur' || $role === 'admin'): ?>
            <div class="pt-6 pb-2 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Recrutement</div>
            <a href="/job/publier" class="<?= $navClass('/job/publier') ?>">
                <i class="fas fa-plus-circle <?= $iconClass ?>"></i>
                <span>Publier un job</span>
            </a>
        <?php endif; ?>

        <?php if ($role === 'freelancer' || $role === 'admin'): ?>
            <div class="pt-6 pb-2 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Ma Boutique</div>
            <a href="/gig/creer" class="<?= $navClass('/gig/creer') ?>">
                <i class="fas fa-plus-circle <?= $iconClass ?>"></i>
                <span>Proposer un service</span>
            </a>
            <a href="/mon-compte/plans" class="<?= $navClass('/mon-compte/plans') ?>">
                <i class="fas fa-crown <?= $iconClass ?>"></i>
                <span>Mon Plan & Crédits</span>
            </a>
        <?php endif; ?>

        <div class="pt-6 pb-2 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Portefeuille</div>
        <a href="/portefeuille" class="<?= $navClass('/portefeuille') ?>">
            <i class="fas fa-wallet <?= $iconClass ?>"></i>
            <span>Mes revenus</span>
        </a>
      </nav>

      <div class="p-6 border-t border-white/5 bg-black/20">
        <a href="/deconnexion" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-white/5 rounded-xl transition-colors text-sm font-bold">
          <i class="fas fa-power-off"></i>
          Quitter
        </a>
      </div>
    </aside>

    <!-- Sidebar Desktop -->
    <aside class="hidden md:flex flex-col w-72 sidebar-gradient text-white border-r border-white/5 shadow-2xl">
      <div class="p-8 flex items-center gap-3">
        <div class="w-10 h-10 bg-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-600/20">
            <i class="fas fa-bolt text-lg"></i>
        </div>
        <a href="/" class="text-xl font-black tracking-tight tracking-tighter">RiseSeason <span class="text-[10px] block opacity-50 font-medium uppercase tracking-widest -mt-1">Plateforme Talents</span></a>
      </div>

      <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <!-- Liens Communs -->
        <a href="/dashboard" class="<?= $navClass('/dashboard') ?>">
          <i class="fas fa-grid-2 <?= $iconClass ?> fa-th-large"></i>
          <span>Tableau de bord</span>
        </a>

        <a href="/profil" class="<?= $navClass('/profil') ?>">
          <i class="fas fa-user-circle <?= $iconClass ?>"></i>
          <span>Mon Profil</span>
        </a>

        <!-- Admin / Contributeur Section -->
        <?php if (in_array($role, ['admin', 'contributeur'])): ?>
            <div class="pt-6 pb-2 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Administration</div>
            <a href="/admin/posts" class="<?= $navClass('/admin/posts') ?>">
                <i class="fas fa-feather-pointed <?= $iconClass ?>"></i>
                <span>Articles (CMS)</span>
            </a>
            <?php if ($role === 'admin'): ?>
                <a href="/admin/gigs" class="<?= $navClass('/admin/gigs') ?>">
                    <i class="fas fa-shield-halved <?= $iconClass ?>"></i>
                    <span>Modération Gigs</span>
                </a>
                <a href="/admin/users" class="<?= $navClass('/admin/users') ?>">
                    <i class="fas fa-users-gear <?= $iconClass ?>"></i>
                    <span>Utilisateurs</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Employeur Section -->
        <?php if ($role === 'employeur' || $role === 'admin'): ?>
            <div class="pt-6 pb-2 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Recrutement</div>
            <a href="/job/publier" class="<?= $navClass('/job/publier') ?>">
                <i class="fas fa-plus-circle <?= $iconClass ?>"></i>
                <span>Publier un job</span>
            </a>
            <a href="/profil?tab=work" class="<?= $navClass('/profil?tab=work') ?>">
                <i class="fas fa-briefcase <?= $iconClass ?>"></i>
                <span>Mes missions</span>
            </a>
        <?php endif; ?>

        <!-- Freelancer Section -->
        <?php if ($role === 'freelancer' || $role === 'admin'): ?>
            <div class="pt-6 pb-2 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Ma Boutique</div>
            <a href="/gig/creer" class="<?= $navClass('/gig/creer') ?>">
                <i class="fas fa-plus-circle <?= $iconClass ?>"></i>
                <span>Proposer un service</span>
            </a>
            <a href="/mes-services" class="<?= $navClass('/mes-services') ?>">
                <i class="fas fa-store <?= $iconClass ?>"></i>
                <span>Mes services</span>
            </a>
            <a href="/mon-compte/plans" class="<?= $navClass('/mon-compte/plans') ?>">
                <i class="fas fa-crown <?= $iconClass ?>"></i>
                <span>Mon Plan & Crédits</span>
            </a>
        <?php endif; ?>

        <!-- Section Finance -->
        <div class="pt-6 pb-2 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Portefeuille</div>
        <a href="/portefeuille" class="<?= $navClass('/portefeuille') ?>">
            <i class="fas fa-wallet <?= $iconClass ?>"></i>
            <span>Mes revenus</span>
        </a>
      </nav>

      <div class="p-6 mt-auto border-t border-white/5 bg-black/20">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center font-bold text-orange-500 uppercase">
                <?= mb_substr($authUser['username'], 0, 1) ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold truncate"><?= e($authUser['username']) ?></p>
                <p class="text-[10px] text-gray-500 uppercase truncate"><?= e($role) ?></p>
            </div>
        </div>
        <a href="/deconnexion" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-white/5 rounded-xl transition-colors text-sm font-bold">
          <i class="fas fa-power-off"></i>
          Quitter
        </a>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
      <!-- Header -->
      <header class="bg-white border-b border-slate-200 h-20 shrink-0">
        <div class="px-8 h-full flex items-center justify-between">
          <button @click="sidebarOpen = true" class="md:hidden text-slate-500 hover:text-slate-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
          </button>

          <div class="hidden sm:flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest">
                <i class="fas fa-calendar-alt"></i>
                <?= format_date_fr(date('Y-m-d H:i:s'), 'd F Y') ?>
          </div>

          <div class="flex items-center gap-4">
            <!-- Currency Toggle -->
            <button onclick="toggleCurrency()" 
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-slate-200 hover:border-orange-300 hover:bg-orange-50 text-xs font-bold text-slate-500 hover:text-orange-600 transition-all cursor-pointer select-none"
                    title="Changer la devise">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1"/></svg>
                <span id="currencyLabelDesktop">FCFA</span>
            </button>
            <div class="relative group">
                <button class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-orange-600 transition-colors">
                    <i class="fas fa-bell"></i>
                </button>
                <span class="absolute top-2 right-2 w-2 h-2 bg-orange-600 rounded-full border-2 border-white"></span>
            </div>
            <div class="hidden md:flex flex-col text-right">
                <span class="text-xs font-bold text-slate-900"><?= e($authUser['username']) ?></span>
                <span class="text-[10px] font-bold text-orange-500 uppercase"><?= e($role) ?></span>
            </div>
            <img src="<?= e($authUser['profile_photo'] ?? 'https://ui-avatars.com/api/?name='.urlencode($authUser['username']).'&background=f97316&color=fff') ?>" 
                 class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover">
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-8 overflow-y-auto bg-[#F8F9FA]">
        <div class="animate-fade-in">
            <?= $content ?>
        </div>
      </main>
    </div>
  </div>
<script>
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
    var labels = document.querySelectorAll('#currencyLabelDesktop, #currencyLabelMobile');
    for (i = 0; i < labels.length; i++) labels[i].textContent = currency;
}
function toggleCurrency() {
    var current = localStorage.getItem('rs_currency') || 'FCFA';
    var next = current === 'FCFA' ? 'EUR' : 'FCFA';
    localStorage.setItem('rs_currency', next);
    applyCurrency(next);
}
document.addEventListener('DOMContentLoaded', function() {
    applyCurrency(localStorage.getItem('rs_currency') || 'FCFA');
});
</script>
</body>
</html>
