<!-- Hero Section -->
<section class="relative overflow-hidden pt-16 pb-24 sm:pt-20 sm:pb-32 bg-slate-900 rounded-b-[1.25rem] shadow-2xl mt-0 -mt-px">
  <div class="relative z-10 max-w-4xl mx-auto text-center px-6">
    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-orange-500 text-[10px] font-black uppercase tracking-[0.2em] mb-8 animate-fade-in-up">
      <span class="flex h-2 w-2 rounded-full bg-orange-500 animate-pulse"></span>
      L'Élite du Freelancing Africain
    </div>
    <h1 class="text-4xl sm:text-6xl font-display font-black text-white leading-[1.05] animate-fade-in-up">
      Recrutez des <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-amber-400">talents d'exception</span> pour vos projets.
    </h1>
    <p class="mt-8 text-xl text-gray-400 animate-fade-in-up animate-delay-100 leading-relaxed max-w-2xl mx-auto">
      RiseSeason est la passerelle entre les visions audacieuses et l'expertise technique de haut niveau du continent.
    </p>

    <!-- Search Bar -->
    <div class="mt-12 animate-fade-in-up animate-delay-200">
      <form action="/recherche" method="GET" class="flex flex-col sm:flex-row gap-3 bg-white/5 border border-white/10 backdrop-blur-xl p-2 rounded-[2rem] shadow-2xl max-w-2xl mx-auto">
        <div class="flex-1 flex items-center px-5 border-r border-white/10">
          <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input type="text" name="q" placeholder="Quel talent recherchez-vous ?" class="w-full px-4 py-3.5 outline-none border-none text-white placeholder-gray-500 bg-transparent text-base font-medium focus:ring-0">
        </div>
        <button type="submit" class="bg-primary-600 text-white py-3.5 px-10 rounded-xl font-bold transition-all hover:bg-primary-500 hover:scale-[1.02] active:scale-95 shadow-xl shadow-primary-900/20">
          Rechercher
        </button>
      </form>
    </div>
  </div>

  <!-- Decorative Elements -->
  <div class="absolute -bottom-24 -left-24 w-[40rem] h-[40rem] bg-primary-600/5 rounded-full blur-[120px]"></div>
</section>

<div class="max-w-6xl mx-auto px-4 py-16 sm:py-24">

<!-- Categories Section -->
<section class="mb-24 reveal px-4">
  <div class="flex flex-col md:flex-row items-baseline justify-between mb-12 gap-4">
    <div class="space-y-2">
      <h2 class="text-4xl font-display font-black text-gray-900">Expertises disponibles</h2>
      <p class="text-lg text-gray-500 leading-relaxed max-w-xl">Une sélection rigoureuse de talents dans les domaines les plus demandés.</p>
    </div>
    <a href="/recherche" class="group flex items-center text-primary-600 font-bold tracking-tight">
      Explorer toutes les catégories 
      <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7"/></svg>
    </a>
  </div>

  <!-- Category Slider (Horizontal Scroll) -->
  <div class="relative group">
    <div class="flex gap-6 overflow-x-auto no-scrollbar pb-8 snap-x snap-mandatory">
      <?php 
      $catIcons = [
          'Développement web' => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6" />',
          'Marketing digital' => '<path d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />',
          'Design graphique' => '<path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />',
          'Rédaction & Traduction' => '<path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
          'Montage vidéo' => '<path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />',
          'Community management' => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 00-3-3.87" /><path d="M16 3.13a4 4 0 010 7.75" />',
          'Assistance virtuelle' => '<path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />',
      ];
      foreach ($categories as $cat): 
        if ($cat === 'Autre') continue;
      ?>
        <a href="/recherche?category=<?= urlencode((string)$cat) ?>" class="flex-none w-64 snap-start">
          <div class="glass-card flex flex-col items-center text-center group h-full">
            <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-orange-50 text-orange-600 group-hover:bg-primary-600 group-hover:text-white transition-all duration-500 shadow-sm">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <?= $catIcons[$cat] ?? '<path d="M13 10V3L4 14h7v7l9-11h-7z" />' ?>
              </svg>
            </div>
            <h3 class="mt-6 text-lg font-display font-bold text-gray-900 group-hover:text-primary-600 transition-colors"><?= e($cat) ?></h3>
            <p class="mt-2 text-xs font-bold text-gray-400 uppercase tracking-widest">Voir les services</p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <!-- Scroll Indicators / Buttons could go here -->
  </div>
</section>

<!-- Stats Section -->
<section class="mb-24 reveal px-4">
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 bg-slate-900 rounded-[3.5rem] p-12 sm:p-20 relative overflow-hidden shadow-2xl">
    <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 to-transparent"></div>
    <div class="relative space-y-2">
      <div class="text-5xl font-display font-black text-white">1.2k+</div>
      <div class="text-sm font-bold text-orange-400 uppercase tracking-widest">Freelances Experts</div>
    </div>
    <div class="relative space-y-2 border-l border-white/10 pl-8">
      <div class="text-5xl font-display font-black text-white">450+</div>
      <div class="text-sm font-bold text-orange-400 uppercase tracking-widest">Missions Réussies</div>
    </div>
    <div class="relative space-y-2 border-l border-white/10 pl-8">
      <div class="text-5xl font-display font-black text-white">98%</div>
      <div class="text-sm font-bold text-orange-400 uppercase tracking-widest">Client Satisfaits</div>
    </div>
    <div class="relative space-y-2 border-l border-white/10 pl-8">
      <div class="text-5xl font-display font-black text-white">24h</div>
      <div class="text-sm font-bold text-orange-400 uppercase tracking-widest">Paiement Garanti</div>
    </div>
  </div>
</section>

<!-- Gig Grid -->
<section class="mb-24 reveal px-4">
  <div class="flex flex-col gap-6 mb-12">
    <div class="flex items-baseline justify-between">
      <h2 class="text-4xl font-display font-black text-gray-900">Services à la une</h2>
      <a href="/recherche" class="text-orange-600 font-bold hover:underline">Voir tout</a>
    </div>
    
    <!-- Category Filter Carousel -->
    <div class="relative group flex items-center gap-4">
      <button onclick="document.getElementById('gigs-category-slider').scrollBy({left: -200, behavior: 'smooth'})" class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full bg-white border border-gray-100 shadow-sm text-gray-400 hover:text-primary-600 hover:border-primary-100 hover:shadow-md transition-all z-10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </button>
      
      <div id="gigs-category-slider" class="flex-1 flex gap-3 overflow-x-auto no-scrollbar scroll-smooth snap-x">
        <a href="/?category=" class="flex-none snap-start px-5 py-2 rounded-full text-sm font-bold whitespace-nowrap transition-colors <?= empty($_GET['category']) ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20' : 'bg-white border border-gray-200 text-gray-600 hover:border-primary-600 hover:text-primary-600' ?>">
          Tous
        </a>
        <?php foreach ($categories as $cat): if ($cat === 'Autre') continue; ?>
          <a href="/?category=<?= urlencode((string)$cat) ?>#gigs" class="flex-none snap-start px-5 py-2 rounded-full text-sm font-bold whitespace-nowrap transition-colors <?= ($_GET['category'] ?? '') === $cat ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20' : 'bg-white border border-gray-200 text-gray-600 hover:border-primary-600 hover:text-primary-600' ?>">
            <?= e($cat) ?>
          </a>
        <?php endforeach; ?>
      </div>

      <button onclick="document.getElementById('gigs-category-slider').scrollBy({left: 200, behavior: 'smooth'})" class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full bg-white border border-gray-100 shadow-sm text-gray-400 hover:text-primary-600 hover:border-primary-100 hover:shadow-md transition-all z-10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </button>
    </div>
  </div>
  
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php 
    $top6Gigs = array_slice($gigs, 0, 6);
    foreach ($top6Gigs as $gig): 
    ?>
      <article class="group bg-white rounded-[2rem] overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-1">
        <div class="relative h-40 overflow-hidden group/carousel">
          <!-- Visibility Badges -->
          <div class="absolute top-4 left-4 z-20 flex flex-wrap gap-2">
            <?php if ($gig['is_sponsored']): ?>
              <span class="px-2.5 py-1 bg-orange-600 text-white text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg flex items-center gap-1.5">
                <i class="fas fa-rocket"></i> Sponsorisé
              </span>
            <?php endif; ?>
            <?php if (($gig['subscription_status'] ?? 'free') === 'premium'): ?>
              <span class="px-2.5 py-1 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg flex items-center gap-1.5 border border-white/10">
                <i class="fas fa-crown text-orange-400"></i> Premium
              </span>
            <?php endif; ?>
          </div>
          <?php 
            // Combine main image with gallery items
            $sliderImages = [$gig['main_image']];
            if (!empty($gig['gallery_items'])) {
                $sliderImages = array_merge($sliderImages, $gig['gallery_items']);
            }
          ?>
          
          <!-- Images Container -->
          <div class="flex h-full w-full overflow-x-auto snap-x snap-mandatory no-scrollbar scroll-smooth slider-container">
            <?php foreach ($sliderImages as $index => $imgUrl): ?>
              <div class="flex-none w-full h-full snap-center relative">
                <img src="<?= e($imgUrl) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy">
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Carousel Controls (Show only if multiple images) -->
          <?php if (count($sliderImages) > 1): ?>
            <button onclick="event.preventDefault(); this.previousElementSibling.scrollBy({left: -this.previousElementSibling.offsetWidth, behavior: 'smooth'})" class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-white/80 backdrop-blur text-gray-800 opacity-0 group-hover/carousel:opacity-100 transition-opacity hover:bg-white shadow-sm z-10">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button onclick="event.preventDefault(); this.previousElementSibling.previousElementSibling.scrollBy({left: this.previousElementSibling.previousElementSibling.offsetWidth, behavior: 'smooth'})" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-white/80 backdrop-blur text-gray-800 opacity-0 group-hover/carousel:opacity-100 transition-opacity hover:bg-white shadow-sm z-10">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
            
            <!-- Indicators -->
            <div class="absolute bottom-3 inset-x-0 flex justify-center gap-1.5 z-10">
              <?php foreach ($sliderImages as $index => $imgUrl): ?>
                <div class="w-1.5 h-1.5 rounded-full bg-white/60 <?= $index === 0 ? 'bg-white' : '' ?>"></div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-4 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
            <span class="text-white text-xs font-bold"><?= e($gig['category']) ?></span>
          </div>
          
          <!-- Save Button -->
          <button onclick="toggleSave(this, <?= (int)$gig['id'] ?>); event.preventDefault();" class="absolute top-4 right-4 p-2.5 rounded-full bg-white/90 backdrop-blur shadow-sm hover:bg-white transition-colors z-20">
            <?php if (in_array((int)$gig['id'], $savedIds ?? [], true)): ?>
              <svg class="w-5 h-5 text-orange-500 fill-current" viewBox="0 0 24 24"><path d="M5 3h14a1 1 0 0 1 1 1v17l-8-4-8 4V4a1 1 0 0 1 1-1z"/></svg>
            <?php else: ?>
              <svg class="w-5 h-5 text-gray-400 stroke-current fill-none" stroke-width="2" viewBox="0 0 24 24"><path d="M5 3h14a1 1 0 0 1 1 1v17l-8-4-8 4V4a1 1 0 0 1 1-1z"/></svg>
            <?php endif; ?>
          </button>
        </div>
        <div class="p-5">
             <div class="flex items-center gap-2 mb-3">
                 <div class="w-6 h-6 rounded-full bg-gray-100 overflow-hidden">
                     <?php $avatarUrl = !empty($gig['profile_photo']) ? $gig['profile_photo'] : 'https://ui-avatars.com/api/?name=' . urlencode($gig['username'] ?? 'User') . '&background=random'; ?>
                     <img src="<?= e($avatarUrl) ?>" class="w-full h-full object-cover">
                 </div>
                 <span class="text-xs font-bold text-gray-500 hover:text-orange-600"><?= e($gig['username']) ?></span>
                 <?php
                 $levelMedalMap = [
                     'nouveau' => 'text-slate-300',
                     'confirmé' => 'text-blue-400',
                     'expert' => 'text-orange-400',
                     'elite' => 'text-yellow-400',
                 ];
                 $medClass = $levelMedalMap[$gig['level'] ?? 'nouveau'] ?? $levelMedalMap['nouveau'];
                 ?>
                 <i class="fas fa-medal <?= $medClass ?> text-[10px]" title="Level <?= e($gig['level'] ?? 'nouveau') ?>"></i>
             </div>
          <h3 class="font-bold text-gray-900 group-hover:text-orange-600 transition-colors line-clamp-2 min-h-[3rem]">
            <a href="/gig/<?= e($gig['slug']) ?>"><?= e($gig['title']) ?></a>
          </h3>
          <div class="mt-4 flex items-center justify-between border-t border-gray-50 pt-4">
            <span class="text-[10px] uppercase tracking-widest font-black text-gray-400"><?= e($gig['country']) ?></span>
            <div class="flex flex-col items-end">
                <span class="text-xs text-gray-400 font-medium">À partir de</span>
                <span class="text-lg font-black text-slate-900"><?= format_price((float)$gig['price_base']) ?></span>
            </div>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<!-- Featured Freelancers -->
<?php if (!empty($featuredFreelancers)): ?>
<section class="mb-24 reveal px-4">
  <div class="flex flex-col md:flex-row items-baseline justify-between mb-12 gap-4">
    <div class="space-y-2">
      <h2 class="text-4xl font-display font-black text-gray-900">Freelances à la une</h2>
      <p class="text-lg text-gray-500 leading-relaxed max-w-xl">Les talents les plus actifs et qualifiés du moment.</p>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <?php foreach ($featuredFreelancers as $freelancer): ?>
      <a href="/profil/<?= e($freelancer['username']) ?>" class="group glass-card hover:bg-white hover:shadow-2xl hover:border-orange-100 transition-all duration-500">
        <div class="flex flex-col items-center text-center space-y-4">
          <div class="relative">
            <div class="w-24 h-24 rounded-3xl bg-orange-100 p-1 shadow-xl group-hover:rotate-3 transition-transform duration-500 relative">
              <img src="<?= e($freelancer['profile_photo'] ?: 'https://ui-avatars.com/api/?name='.urlencode($freelancer['username']).'&background=f97316&color=fff') ?>" 
                   class="w-full h-full rounded-2xl object-cover border-4 border-white shadow-inner">
              
              <!-- Availability Indicator -->
              <?php
              $availColor = 'bg-emerald-500';
              if (($freelancer['availability'] ?? '') === 'soon') $availColor = 'bg-orange-500';
              if (($freelancer['availability'] ?? '') === 'unavailable') $availColor = 'bg-red-500';
              ?>
              <div class="absolute -top-1 -right-1 w-4 h-4 <?= $availColor ?> border-2 border-white rounded-full shadow-sm animate-pulse"></div>
            </div>
            <?php
            $levelMedalMap = [
                'nouveau' => 'text-slate-300',
                'confirmé' => 'text-blue-400',
                'expert' => 'text-orange-400',
                'elite' => 'text-yellow-400',
            ];
            $medClass = $levelMedalMap[$freelancer['level'] ?? 'nouveau'] ?? $levelMedalMap['nouveau'];
            ?>
            <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-white rounded-xl shadow-lg border border-slate-50 flex items-center justify-center <?= $medClass ?>">
              <i class="fas fa-medal text-sm"></i>
            </div>
          </div>
          
          <div class="space-y-1">
            <h3 class="font-black text-gray-900 group-hover:text-orange-600 transition-colors">
              <?= e($freelancer['first_name'] . ' ' . $freelancer['last_name'] ?: $freelancer['username']) ?>
            </h3>
            <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest"><?= e($freelancer['country']) ?></p>
          </div>
          
          <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed h-10 italic">
            "<?= e(mb_substr($freelancer['bio'] ?? '', 0, 80)) ?>..."
          </p>
          
          <div class="pt-2 w-full">
            <span class="inline-flex w-full items-center justify-center px-4 py-2 rounded-xl bg-orange-50 text-orange-600 text-[10px] font-black uppercase tracking-widest group-hover:bg-orange-600 group-hover:text-white transition-all">
              Voir le profil
            </span>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- Job Offers Section -->
<?php if (!empty($jobs)): ?>
<section class="mb-24 reveal px-4">
  <div class="flex items-baseline justify-between mb-12">
    <div>
        <h2 class="text-4xl font-display font-black text-gray-900">Missions récentes</h2>
        <p class="mt-2 text-lg text-gray-500">Des opportunités publiées par des entreprises cherchant votre expertise.</p>
    </div>
    <a href="/recherche" class="text-orange-600 font-bold hover:underline whitespace-nowrap ml-4">Voir tout</a>
  </div>
  
  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <?php foreach ($jobs as $job): ?>
      <article class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-orange-100 transition-all duration-300 overflow-hidden flex flex-row">
        <!-- Left accent bar -->
        <div class="w-1.5 bg-gradient-to-b from-orange-400 to-orange-600 shrink-0 group-hover:from-orange-500 group-hover:to-orange-700 transition-colors"></div>

        <div class="flex-1 p-5 flex flex-col gap-3 min-w-0">
          <!-- Top row: category + date -->
          <div class="flex items-center justify-between gap-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-orange-50 text-orange-600 text-[11px] font-bold truncate">
              <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              <?= e($job['category']) ?>
            </span>
            <span class="text-[11px] font-semibold text-gray-400 whitespace-nowrap"><?= $job['created_at'] ? format_date_fr($job['created_at']) : '' ?></span>
          </div>

          <!-- Title -->
          <h3 class="text-base font-bold text-gray-900 group-hover:text-orange-600 transition-colors leading-snug line-clamp-1">
            <?= e($job['title']) ?>
          </h3>

          <!-- Description -->
          <p class="text-gray-500 text-sm leading-relaxed line-clamp-2">
            <?= e($job['description']) ?>
          </p>

          <!-- Bottom row: employer + budget -->
          <div class="flex items-center justify-between pt-2 border-t border-gray-50 mt-auto">
            <div class="flex items-center gap-2.5 min-w-0">
              <div class="w-7 h-7 rounded-full bg-gray-100 overflow-hidden shrink-0 border border-white shadow-sm">
                <?php $employerAvatar = !empty($job['employer_photo']) ? $job['employer_photo'] : 'https://ui-avatars.com/api/?name=' . urlencode($job['employer_name'] ?? 'Employer') . '&background=random'; ?>
                <img src="<?= e($employerAvatar) ?>" alt="<?= e($job['employer_name']) ?>" class="w-full h-full object-cover">
              </div>
              <div class="min-w-0">
                <div class="text-xs font-bold text-gray-900 truncate"><?= e($job['employer_name']) ?></div>
                <div class="text-[10px] uppercase font-black tracking-wider text-gray-400"><?= e($job['employer_country']) ?></div>
              </div>
            </div>

            <div class="flex items-center gap-1.5 bg-slate-50 px-3 py-1.5 rounded-lg shrink-0">
              <span class="text-[10px] uppercase font-black text-gray-400 tracking-wider">Budget</span>
              <span class="text-sm font-black text-slate-900"><?= format_price((float)$job['budget_eur']) ?></span>
            </div>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- Call to Action -->
<section class="mb-24 reveal px-4">
    <div class="bg-primary-600 rounded-[3.5rem] p-12 sm:p-20 text-center relative overflow-hidden shadow-2xl shadow-orange-200">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-black/10 rounded-full translate-y-1/2 -translate-x-1/2 blur-3xl"></div>
        
        <div class="relative z-10 max-w-2xl mx-auto">
            <h2 class="text-4xl sm:text-5xl font-display font-black text-white mb-8">Prêt à donner vie à vos idées ?</h2>
            <p class="text-xl text-orange-100 mb-12 opacity-90">Rejoignez des milliers d'entreprises qui font confiance à l'excellence africaine.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/inscription" class="bg-white text-primary-600 px-10 py-5 rounded-2xl font-black text-lg hover:bg-orange-50 transition-all shadow-xl hover:-translate-y-1">Commencer maintenant</a>
                <a href="/recherche" class="bg-primary-700 text-white px-10 py-5 rounded-2xl font-black text-lg hover:bg-primary-800 transition-all shadow-xl hover:-translate-y-1">Explorer les services</a>
            </div>
        </div>
    </div>
</section>

</div>
