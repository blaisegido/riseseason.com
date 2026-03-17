<?php
/** @var array $profile */
/** @var array $currentUser */
/** @var array $gigs */
/** @var array $jobs */
/** @var array $portfolio */
/** @var array $trustSignals */
/** @var string|null $success */
?>

<div class="space-y-8 animate-fade-in" x-data="{ tab: 'work', editOpen: false, filesOpen: false }">
    <!-- Ultra-Premium Profile Header (Flat Design) -->
    <div class="relative overflow-hidden bg-slate-900 shadow-2xl transition-all duration-700 hover:shadow-orange-500/5 group/header">
        <!-- Dynamic Background Layer -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-900 to-orange-900/40 opacity-90"></div>
        <div class="absolute top-0 right-0 w-2/3 h-full bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-orange-500/10 via-transparent to-transparent pointer-events-none"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-600/10 rounded-full blur-[100px] pointer-events-none animate-pulse-slow"></div>

        <div class="relative p-6 md:p-10 flex flex-col md:flex-row gap-10 items-center md:items-end text-center md:text-left z-10">
            <!-- Avatar with Aura -->
            <div class="relative shrink-0">
                <div class="w-36 h-36 md:w-48 md:h-48 rounded-[3rem] bg-gradient-to-br from-orange-400 to-orange-600 p-1 shadow-2xl shadow-orange-500/20 group-hover/header:scale-105 transition-transform duration-700">
                    <img src="<?= e($profile['profile_photo'] ?? 'https://ui-avatars.com/api/?name='.urlencode($profile['username']).'&background=f97316&color=fff&size=200') ?>" 
                         class="w-full h-full rounded-[2.8rem] object-cover border-4 border-slate-900 shadow-inner">
                </div>
                <!-- Premium Status Badge -->
                <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-white rounded-2xl shadow-2xl border border-slate-100 flex items-center justify-center text-orange-500 transform hover:rotate-12 transition-transform">
                    <i class="fas fa-check-double text-2xl"></i>
                </div>
            </div>

            <!-- Header Info -->
            <div class="flex-1 space-y-6">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-4">
                        <h1 class="text-4xl md:text-6xl font-black text-white tracking-tighter leading-none">
                            <?= e($profile['first_name'] . ' ' . $profile['last_name'] ?: $profile['username']) ?>
                        </h1>
                        <?php if ($trustSignals['profile_score'] === 100): ?>
                            <div class="flex items-center gap-1.5 px-4 py-1.5 bg-orange-500/10 border border-orange-500/20 text-orange-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-full backdrop-blur-md">
                                <i class="fas fa-shield-check text-[10px]"></i>
                                Certifié
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-4">
                        <?php if (!empty($profile['title'])): ?>
                            <p class="text-xl md:text-2xl font-bold text-slate-300 italic opacity-90"><?= e($profile['title']) ?></p>
                        <?php endif; ?>
                        
                        <!-- Mini Level Pill -->
                        <?php
                        $levelMap = [
                            'nouveau' => ['label' => 'Nouveau', 'color' => 'bg-slate-500'],
                            'confirmé' => ['label' => 'Confirmé', 'color' => 'bg-blue-500'],
                            'expert' => ['label' => 'Expert', 'color' => 'bg-orange-500'],
                            'elite' => ['label' => 'Élite', 'color' => 'bg-yellow-500'],
                        ];
                        $lvl = $profile['level'] ?? 'nouveau';
                        $lvlData = $levelMap[$lvl] ?? $levelMap['nouveau'];
                        ?>
                         <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-400">
                            <span class="w-1.5 h-1.5 rounded-full <?= $lvlData['color'] ?>"></span>
                            Level <?= $lvlData['label'] ?>
                         </div>

                         <!-- Availability Pill -->
                         <?php
                         $availMap = [
                             'available' => ['label' => 'Disponible', 'color' => 'bg-emerald-500', 'text' => 'text-emerald-400'],
                             'soon' => ['label' => 'Bientôt libre', 'color' => 'bg-orange-500', 'text' => 'text-orange-400'],
                             'unavailable' => ['label' => 'Occupé', 'color' => 'bg-red-500', 'text' => 'text-red-400'],
                         ];
                         $avail = $profile['availability'] ?? 'available';
                         $availData = $availMap[$avail] ?? $availMap['available'];
                         ?>
                         <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest <?= $availData['text'] ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $availData['color'] ?> animate-pulse"></span>
                            <?= $availData['label'] ?>
                         </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-center md:justify-start gap-8 text-slate-400 text-sm font-semibold tracking-wide">
                    <div class="flex items-center gap-2.5 group/meta">
                        <i class="fas fa-location-dot text-orange-500/50 group-hover/meta:text-orange-500 transition-colors"></i>
                        <?= e($profile['country'] ?: 'Monde') ?>
                    </div>
                    <div class="flex items-center gap-2.5 group/meta">
                        <i class="fas fa-bolt text-orange-500/50 group-hover/meta:text-orange-500 transition-colors"></i>
                        Réponse en <?= $trustSignals['response_time'] ?>
                    </div>
                    <div class="flex items-center gap-2.5 group/meta">
                        <i class="fas fa-award text-orange-500/50 group-hover/meta:text-orange-500 transition-colors"></i>
                        <?= $trustSignals['completed_missions'] ?> missions
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="pt-4 flex flex-wrap items-center justify-center md:justify-start gap-4">
                    <?php if ($currentUser && (int)$currentUser['id'] === (int)$profile['id']): ?>
                        <button @click="editOpen = !editOpen" class="px-8 py-4 bg-white text-slate-900 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-500 hover:text-white transition-all shadow-xl hover:-translate-y-1">
                            Modifier mon profil
                        </button>
                        <button @click="filesOpen = !filesOpen" class="px-8 py-4 bg-white/5 text-white border border-white/10 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-white/10 transition-all backdrop-blur-md">
                            Portfolio
                        </button>
                    <?php else: ?>
                        <a href="/messages?user=<?= $profile['id'] ?>" class="px-10 py-4 bg-orange-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-500 transition-all shadow-2xl shadow-orange-600/30 hover:-translate-y-1 flex items-center gap-3">
                            <i class="fas fa-paper-plane text-[10px]"></i>
                            Démarrer un projet
                        </a>
                        <button class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-white hover:bg-white/10 transition-all backdrop-blur-md">
                            <i class="fas fa-share-nodes"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Floating Rating (Desktop Only) -->
            <?php if ($reviewStats['total_reviews'] > 0): ?>
                <div class="hidden lg:flex flex-col items-center bg-white/5 border border-white/10 p-6 rounded-[2rem] backdrop-blur-xl shrink-0 group-hover/header:bg-white/10 transition-colors">
                    <span class="text-4xl font-black text-white"><?= $reviewStats['avg_rating'] ?></span>
                    <div class="flex text-orange-400 text-[10px] my-2">
                        <?php for ($i=1; $i<=5; $i++): ?>
                            <i class="fas fa-star <?= $i <= round($reviewStats['avg_rating']) ? '' : 'text-white/10' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400"><?= $reviewStats['total_reviews'] ?> avis</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($success)): ?>
      <div class="animate-bounce-subtle bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl font-bold text-sm flex items-center gap-3">
          <i class="fas fa-circle-check"></i>
          <?= e($success) ?>
      </div>
    <?php endif; ?>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <!-- Left Column: Bio & Trusts (Glass Sidebar) -->
        <div class="lg:col-span-1 space-y-10">
            <!-- Trust Signals -->
            <?php if ($profile['role'] === 'freelancer'): ?>
            <div class="bg-white p-10 rounded-[2.5rem] border border-slate-200 shadow-premium space-y-8 group/trust hover:border-orange-200 transition-colors">
                <div class="flex items-center justify-between">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Fiabilité & Confiance</h2>
                    <i class="fas fa-shield-heart text-orange-500/20 group-hover/trust:scale-110 transition-transform"></i>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <p class="text-2xl font-black text-slate-900 tracking-tighter"><?= $trustSignals['completed_missions'] ?></p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Missions OK</p>
                    </div>
                    <div class="space-y-1 text-right">
                        <p class="text-2xl font-black text-slate-900 tracking-tighter"><?= $trustSignals['response_time'] ?></p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Réactivité</p>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-3 text-[10px] font-black uppercase tracking-widest">
                        <span class="text-slate-400">Qualité Profil</span>
                        <span class="text-orange-600"><?= $trustSignals['profile_score'] ?>%</span>
                    </div>
                    <div class="w-full h-1.5 bg-slate-50 rounded-full overflow-hidden">
                        <div class="h-full bg-slate-900 rounded-full transition-all duration-1000" style="width: <?= $trustSignals['profile_score'] ?>%"></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Bio Card -->
            <div class="bg-white p-10 rounded-[2.5rem] border border-slate-200 shadow-premium space-y-8">
                <div class="flex items-center justify-between">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">À propos de moi</h2>
                    <i class="fas fa-fingerprint text-slate-100 text-2xl"></i>
                </div>
                
                <?php if (!empty($profile['daily_rate'])): ?>
                    <div class="relative p-6 bg-slate-50 rounded-[2rem] border border-slate-100 overflow-hidden group/rate">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover/rate:opacity-10 transition-opacity">
                            <i class="fas fa-coins text-4xl"></i>
                        </div>
                        <p class="text-[10px] font-black uppercase text-slate-400 mb-1 tracking-widest">TJM / Tarif de base</p>
                        <p class="text-2xl font-black text-slate-900"><?= format_price_raw((float)$profile['daily_rate']) ?></p>
                    </div>
                <?php endif; ?>

                <div class="relative">
                    <p class="text-slate-600 leading-[1.8] font-medium text-sm">
                        <?= !empty($profile['bio']) ? nl2br(e($profile['bio'])) : '<i class="text-slate-300">Aucune présentation disponible.</i>' ?>
                    </p>
                </div>

                <?php if (!empty($profile['languages'])): ?>
                    <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                        <h3 class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Langues</h3>
                        <p class="text-xs font-black text-slate-900 uppercase tracking-tighter"><?= e($profile['languages']) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($profile['website']) || !empty($profile['linkedin']) || !empty($profile['github'])): ?>
                    <div class="pt-6 border-t border-slate-100 space-y-4">
                        <h3 class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-4">Canaux officiels</h3>
                        <div class="grid grid-cols-3 gap-3">
                            <?php if ($profile['website']): ?>
                                <a href="<?= e($profile['website']) ?>" target="_blank" class="flex flex-col items-center justify-center p-4 bg-slate-50 hover:bg-orange-50 hover:text-orange-600 rounded-2xl transition-all group/link" title="Site web">
                                    <i class="fas fa-globe text-lg mb-1 opacity-40 group-hover/link:opacity-100 transition-opacity"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($profile['linkedin']): ?>
                                <a href="<?= e($profile['linkedin']) ?>" target="_blank" class="flex flex-col items-center justify-center p-4 bg-slate-50 hover:bg-blue-50 hover:text-blue-600 rounded-2xl transition-all group/link" title="LinkedIn">
                                    <i class="fab fa-linkedin-in text-lg mb-1 opacity-40 group-hover/link:opacity-100 transition-opacity"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($profile['github']): ?>
                                <a href="<?= e($profile['github']) ?>" target="_blank" class="flex flex-col items-center justify-center p-4 bg-slate-50 hover:bg-slate-900 hover:text-white rounded-2xl transition-all group/link" title="GitHub">
                                    <i class="fab fa-github text-lg mb-1 opacity-40 group-hover/link:opacity-100 transition-opacity"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($profile['skills'])): ?>
                <div class="pt-6 border-t border-slate-100 space-y-5">
                    <h3 class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Expertises Validées</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach (array_filter(array_map('trim', explode(',', (string)$profile['skills']))) as $skill): ?>
                            <span class="px-4 py-2 bg-slate-50 text-slate-600 border border-slate-100 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-orange-500 hover:text-orange-600 transition-all cursor-default">
                                <?= e($skill) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Services & Activity -->
        <div class="lg:col-span-2 space-y-10">
            
            <!-- Management Controls (only for owner) -->
            <?php if ($currentUser && (int)$currentUser['id'] === (int)$profile['id']): ?>
                <div x-show="editOpen" x-collapse>
                    <div class="bg-white p-10 rounded-[3rem] border-4 border-slate-900 shadow-2xl mb-10">
                        <div class="flex items-center gap-4 mb-10">
                            <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center">
                                <i class="fas fa-fingerprint"></i>
                            </div>
                            <h2 class="text-2xl font-black text-slate-900">Édition Premium de votre identité</h2>
                        </div>
                        <form method="post" action="/profil" class="space-y-8">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Poste / Titre pro</label>
                                    <input name="title" class="w-full bg-slate-50 border-2 border-primary-100 rounded-2xl p-5 text-sm font-bold focus:border-orange-500 focus:bg-white focus:ring-0 transition-all outline-none" value="<?= e($profile['title'] ?? '') ?>" placeholder="Ex: Designer UX/UI Senior">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Tarif Journalier Moyen (€)</label>
                                    <input type="number" step="0.01" name="daily_rate" class="w-full bg-slate-50 border-2 border-primary-100 rounded-2xl p-5 text-sm font-bold focus:border-orange-500 focus:bg-white focus:ring-0 transition-all outline-none" value="<?= (float)($profile['daily_rate'] ?? 0) ?>">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Langues</label>
                                    <input name="languages" class="w-full bg-slate-50 border-2 border-primary-100 rounded-2xl p-5 text-sm font-bold focus:border-orange-500 focus:bg-white focus:ring-0 transition-all outline-none" value="<?= e($profile['languages'] ?? '') ?>">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Disponibilité</label>
                                    <select name="availability" class="w-full bg-slate-50 border-2 border-primary-100 rounded-2xl p-5 text-sm font-bold focus:border-orange-500 focus:bg-white focus:ring-0 transition-all outline-none appearance-none">
                                        <option value="available" <?= ($profile['availability'] ?? 'available') === 'available' ? 'selected' : '' ?>>Disponible 🟢</option>
                                        <option value="soon" <?= ($profile['availability'] ?? '') === 'soon' ? 'selected' : '' ?>>Bientôt libre 🟠</option>
                                        <option value="unavailable" <?= ($profile['availability'] ?? '') === 'unavailable' ? 'selected' : '' ?>>Occupé 🔴</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Réseaux Sociaux / Portfolio</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <input name="website" class="bg-slate-50 border-2 border-primary-100 rounded-2xl p-5 text-sm font-bold focus:border-orange-500 focus:bg-white focus:ring-0 transition-all outline-none" value="<?= e($profile['website'] ?? '') ?>" placeholder="Site web">
                                    <input name="linkedin" class="bg-slate-50 border-2 border-primary-100 rounded-2xl p-5 text-sm font-bold focus:border-orange-500 focus:bg-white focus:ring-0 transition-all outline-none" value="<?= e($profile['linkedin'] ?? '') ?>" placeholder="LinkedIn">
                                    <input name="github" class="bg-slate-50 border-2 border-primary-100 rounded-2xl p-5 text-sm font-bold focus:border-orange-500 focus:bg-white focus:ring-0 transition-all outline-none" value="<?= e($profile['github'] ?? '') ?>" placeholder="GitHub">
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Votre histoire professionnelle</label>
                                <textarea name="bio" class="w-full bg-slate-50 border-2 border-primary-100 rounded-[2rem] p-6 text-sm font-medium focus:border-orange-500 focus:bg-white focus:ring-0 transition-all outline-none min-h-[160px] leading-relaxed"><?= e($profile['bio']) ?></textarea>
                            </div>

                            <div class="flex justify-end gap-4 pt-6">
                                <button type="button" @click="editOpen = false" class="px-8 py-4 text-slate-400 font-black text-xs uppercase tracking-widest hover:text-slate-900 transition-colors">Abandonner</button>
                                <button class="px-10 py-5 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-orange-600 transition-all shadow-xl shadow-slate-900/10">Appliquer les mises à jour</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div x-show="filesOpen" x-collapse>
                    <div class="bg-white p-10 rounded-[3rem] border-4 border-orange-500 shadow-2xl mb-10 overflow-hidden relative">
                        <div class="absolute top-0 right-0 p-8 opacity-5">
                            <i class="fas fa-images text-8xl"></i>
                        </div>
                        <h2 class="text-2xl font-black text-slate-900 mb-10 flex items-center gap-4">
                            <span class="w-10 h-10 bg-orange-500 text-white rounded-xl flex items-center justify-center text-sm"><i class="fas fa-plus"></i></span>
                            Nouveau projet Portfolio
                        </h2>
                        <form method="post" action="/profil/upload" enctype="multipart/form-data" class="space-y-8 relative z-10">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-6">
                                    <div>
                                        <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Nom du projet</label>
                                        <input name="title" required class="w-full bg-slate-50 border-2 border-primary-100 rounded-2xl p-5 text-sm font-bold focus:border-orange-500 focus:bg-white transition-all outline-none" placeholder="Ex: RiseSeason 2.0 Webdesign">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Rôle & Context</label>
                                        <textarea name="description" class="w-full bg-slate-50 border-2 border-primary-100 rounded-2xl p-5 text-sm font-medium focus:border-orange-500 focus:bg-white transition-all outline-none min-h-[120px]" placeholder="Optionnel : bref résumé du projet..."></textarea>
                                    </div>
                                </div>
                                
                                <div class="relative group h-full">
                                    <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Fichier démonstration</label>
                                    <div class="border-2 border-dashed border-slate-200 rounded-[2.5rem] p-10 text-center hover:border-orange-500 hover:bg-orange-50 transition-all relative h-[calc(100%-1.5rem)] flex flex-col items-center justify-center cursor-pointer">
                                        <input type="file" name="portfolio_file" required class="absolute inset-0 opacity-0 cursor-pointer">
                                        <div class="w-16 h-16 bg-white rounded-2xl shadow-premium mb-4 flex items-center justify-center text-slate-200 group-hover:text-orange-500 transition-all">
                                            <i class="fas fa-cloud-arrow-up text-2xl"></i>
                                        </div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-orange-600 transition-colors">JPG, PNG ou PDF</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-4 pt-6">
                                <button type="button" @click="filesOpen = false" class="px-8 py-4 text-slate-400 font-black text-xs uppercase tracking-widest hover:text-slate-900 transition-colors">Fermer</button>
                                <button class="px-10 py-5 bg-orange-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-900 transition-all shadow-xl shadow-orange-600/20">Ajouter à la vitrine</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Interactive Content Tabs -->
            <div class="bg-white rounded-[3rem] border border-slate-100 shadow-premium overflow-hidden">
                <nav class="flex items-center gap-4 p-4 bg-slate-50/50 border-b border-slate-100">
                    <button @click="tab = 'work'" 
                            :class="tab === 'work' ? 'bg-orange-600 text-white shadow-lg shadow-orange-600/20 scale-105' : 'bg-orange-50 text-orange-600 border border-orange-100 hover:bg-orange-100/50'" 
                            class="flex-1 py-3.5 px-6 text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all duration-300">
                        Offres
                    </button>
                    
                    <button @click="tab = 'overview'" 
                            :class="tab === 'overview' ? 'bg-orange-600 text-white shadow-lg shadow-orange-600/20 scale-105' : 'bg-orange-50 text-orange-600 border border-orange-100 hover:bg-orange-100/50'" 
                            class="flex-1 py-3.5 px-6 text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all duration-300">
                        Portfolio
                    </button>

                    <button @click="tab = 'reviews'" 
                            :class="tab === 'reviews' ? 'bg-orange-600 text-white shadow-lg shadow-orange-600/20 scale-105' : 'bg-orange-50 text-orange-600 border border-orange-100 hover:bg-orange-100/50'" 
                            class="flex-1 py-3.5 px-6 text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all duration-300">
                        Retours Clients
                    </button>
                </nav>

                <div class="p-10">
                     <!-- Services/Offres Tab -->
                    <div x-show="tab === 'work'" x-transition:enter="duration-500 ease-out" x-transition:enter-start="opacity-0 translate-y-4" class="space-y-6">
                        <?php if (empty($gigs) && empty($jobs)): ?>
                            <div class="py-24 text-center space-y-4">
                                <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto text-slate-200">
                                    <i class="fas fa-box-open text-3xl"></i>
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-300">Aucune activité publique identifiée</p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="grid grid-cols-1 gap-4">
                        <?php foreach ($gigs as $g): ?>
                            <a href="/gig/<?= e($g['slug']) ?>" class="group/gig relative flex flex-col md:flex-row gap-6 p-6 bg-white hover:bg-slate-50 border border-slate-100 hover:border-orange-200 rounded-3xl transition-all duration-300">
                                <div class="w-full md:w-32 aspect-[4/3] rounded-2xl overflow-hidden shadow-sm shrink-0">
                                    <img src="/<?= e($g['main_image'] ?: 'img/placeholder.jpg') ?>" class="w-full h-full object-cover group-hover/gig:scale-110 transition-transform duration-700">
                                </div>
                                <div class="flex-1 min-w-0 flex flex-col justify-center gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-orange-500 bg-orange-50 px-2 py-0.5 rounded-lg"><?= e($g['category']) ?></span>
                                    </div>
                                    <h4 class="text-lg font-black text-slate-900 group-hover/gig:text-orange-600 transition-colors truncate"><?= e($g['title']) ?></h4>
                                    <div class="flex items-center gap-6 mt-1 text-[10px] font-bold text-slate-400">
                                        <span class="flex items-center gap-2"><i class="fas fa-bolt text-xs opacity-30"></i> Dès <?= format_price((float)$g['price_base']) ?></span>
                                        <span class="flex items-center gap-2"><?= format_status_fr($g['status']) ?></span>
                                    </div>
                                </div>
                                <div class="absolute top-6 right-6 opacity-0 group-hover/gig:opacity-40 -translate-x-2 group-hover/gig:translate-x-0 transition-all">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                        <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Portfolio Tab (Visual Grid) -->
                    <div x-show="tab === 'overview'" x-transition:enter="duration-500 ease-out" x-transition:enter-start="opacity-0 translate-y-4" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <?php if (empty($portfolio)): ?>
                            <div class="col-span-full py-24 text-center space-y-4">
                                <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto text-slate-200">
                                    <i class="fas fa-shapes text-3xl"></i>
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-300">Aucun projet en vitrine pour l'instant</p>
                            </div>
                        <?php endif; ?>
                        <?php foreach ($portfolio as $f): ?>
                            <div class="group/port relative bg-slate-950 rounded-[2.5rem] aspect-[4/3] overflow-hidden shadow-2xl">
                                <?php if (str_ends_with($f['file_name'], '.pdf')): ?>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-white/20">
                                        <i class="fas fa-file-pdf text-7xl mb-4"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest opacity-50">Document Archive</span>
                                    </div>
                                <?php else: ?>
                                    <img src="/uploads/<?= e($f['file_name']) ?>" class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover/port:opacity-30 group-hover/port:scale-110 transition-all duration-700">
                                <?php endif; ?>
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent p-8 flex flex-col justify-end transform translate-y-4 group-hover/port:translate-y-0 transition-transform duration-500">
                                    <h3 class="text-white font-black text-xl mb-1 opacity-0 group-hover/port:opacity-100 transition-opacity duration-500 delay-100"><?= e($f['title'] ?: $f['original_name']) ?></h3>
                                    <?php if (!empty($f['description'])): ?>
                                        <p class="text-white/60 text-xs font-medium line-clamp-2 opacity-0 group-hover/port:opacity-100 transition-opacity duration-500 delay-200"><?= e($f['description']) ?></p>
                                    <?php endif; ?>
                                    <div class="mt-6 flex gap-3 opacity-0 group-hover/port:opacity-100 transition-opacity duration-500 delay-300">
                                        <a href="/uploads/<?= e($f['file_name']) ?>" target="_blank" class="px-6 py-2.5 bg-white text-slate-950 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-orange-500 hover:text-white transition-colors">Voir le projet</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Reviews Tab (Premium Layout) -->
                    <div x-show="tab === 'reviews'" x-transition:enter="duration-500 ease-out" x-transition:enter-start="opacity-0 translate-y-4" class="space-y-8">
                        <?php if (empty($reviews)): ?>
                            <div class="py-24 text-center space-y-4">
                                <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto text-slate-200">
                                    <i class="fas fa-comment-dots text-3xl"></i>
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-300">Premier avis en attente</p>
                            </div>
                        <?php endif; ?>

                        <?php foreach ($reviews as $rv): ?>
                            <div class="relative p-10 bg-slate-50 rounded-[2.5rem] border border-slate-100 group/rv hover:bg-white hover:border-orange-100 transition-all duration-500">
                                <div class="absolute -top-4 -right-4 w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-xl text-orange-500/10 group-hover/rv:rotate-12 group-hover/rv:text-orange-500 transition-all duration-700">
                                    <i class="fas fa-quote-right text-xl"></i>
                                </div>
                                <div class="flex items-center gap-4 mb-6">
                                    <img src="<?= e($rv['buyer_photo'] ?? 'https://ui-avatars.com/api/?name='.urlencode($rv['buyer_username']).'&background=random') ?>" class="w-12 h-12 rounded-2xl object-cover shadow-sm">
                                    <div>
                                        <p class="font-black text-slate-900 leading-none">@<?= e($rv['buyer_username']) ?></p>
                                        <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest"><?= date('F Y', strtotime($rv['created_at'])) ?></p>
                                    </div>
                                    <div class="ml-auto flex text-orange-400 text-[9px] gap-0.5">
                                        <?php for ($i=1; $i<=5; $i++): ?>
                                            <i class="fas fa-star <?= $i <= (int)$rv['rating'] ? '' : 'text-slate-200' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="text-slate-600 text-sm leading-relaxed font-medium italic">"<?= e($rv['comment']) ?>"</p>
                                <div class="mt-8 pt-6 border-t border-slate-100/50 flex items-center justify-between">
                                    <span class="text-[9px] font-black uppercase text-slate-400 tracking-[0.1em]">Prestation : <?= e($rv['gig_title']) ?></span>
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulse-slow {
    0%, 100% { transform: scale(1); opacity: 0.1; }
    50% { transform: scale(1.1); opacity: 0.2; }
}
.animate-pulse-slow {
    animation: pulse-slow 8s infinite ease-in-out;
}
.shadow-premium {
    box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.03), 0 4px 10px -5px rgba(0, 0, 0, 0.01);
}
.animate-fade-in {
    animation: fadeIn 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
[x-cloak] { display: none !important; }
</style>
