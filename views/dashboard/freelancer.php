<div class="space-y-6">
    <!-- Header / Welcome -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Bonjour, <?= e($user['first_name'] ?? $user['username'] ?? 'Freelancer') ?> 👋</h1>
            <p class="text-sm text-slate-500 mt-1">Voici un aperçu de votre activité sur RiseSeason.</p>
        </div>
        <a href="/gig/creer" class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 text-white text-sm font-bold rounded-xl hover:bg-orange-700 transition-all shadow-lg shadow-orange-600/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau Service
        </a>
    </div>

    <!-- Profile Completeness Alert -->
    <?php if ($profileScore < 100): ?>
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/60 rounded-2xl p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-amber-900">Complétez votre profil pour attirer plus de clients</p>
            <div class="flex items-center gap-3 mt-2">
                <div class="flex-1 h-2 bg-amber-200/50 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-amber-400 to-orange-500 rounded-full transition-all" style="width: <?= $profileScore ?>%"></div>
                </div>
                <span class="text-xs font-black text-amber-700"><?= $profileScore ?>%</span>
            </div>
            <div class="flex flex-wrap gap-2 mt-2">
                <?php if (empty($user['bio'])): ?><span class="text-[10px] px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full font-bold">+ Bio</span><?php endif; ?>
                <?php if (empty($user['skills'])): ?><span class="text-[10px] px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full font-bold">+ Compétences</span><?php endif; ?>
                <?php if (empty($user['profile_photo'])): ?><span class="text-[10px] px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full font-bold">+ Photo</span><?php endif; ?>
                <?php if ($gigsApproved === 0): ?><span class="text-[10px] px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full font-bold">+ 1er service</span><?php endif; ?>
            </div>
        </div>
        <a href="/profil" class="px-4 py-2 bg-amber-600 text-white text-xs font-bold rounded-lg hover:bg-amber-700 transition-colors shrink-0">Compléter</a>
    </div>
    <?php endif; ?>

    <!-- KPI Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Solde Dispo -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-5 text-white relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-orange-500/10 rounded-full -translate-y-8 translate-x-8 blur-xl group-hover:bg-orange-500/20 transition-colors"></div>
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Disponible</span>
            </div>
            <div class="text-2xl font-black"><?= format_price((float)$wallet['balance'], true) ?></div>
            <a href="/portefeuille" class="mt-3 inline-flex items-center gap-1 text-[10px] font-bold text-orange-400 hover:text-orange-300 uppercase tracking-widest">
                Retirer <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <!-- En attente -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 group hover:border-orange-100 transition-colors">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">En attente</span>
            </div>
            <div class="text-2xl font-black text-slate-900"><?= format_price((float)$wallet['pending_balance'], true) ?></div>
            <p class="text-[11px] text-slate-400 mt-3 font-medium">Libérés après validation</p>
        </div>

        <!-- Total gagné -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 group hover:border-green-100 transition-colors">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total gagné</span>
            </div>
            <div class="text-2xl font-black text-slate-900"><?= format_price((float)$totalEarned, true) ?></div>
            <p class="text-[11px] text-green-600 mt-3 font-bold"><?= $completedSalesCount ?> vente<?= $completedSalesCount > 1 ? 's' : '' ?> terminée<?= $completedSalesCount > 1 ? 's' : '' ?></p>
        </div>

        <!-- Commandes actives -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 group hover:border-blue-100 transition-colors">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Actives</span>
            </div>
            <div class="text-2xl font-black text-slate-900"><?= count($activeSales) ?></div>
            <p class="text-[11px] text-slate-400 mt-3 font-medium"><?= $totalSalesCount ?> commande<?= $totalSalesCount > 1 ? 's' : '' ?> au total</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- Ventes à traiter (2/3) -->
        <section class="xl:col-span-2 bg-white rounded-2xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h2 class="text-base font-bold text-slate-900">Ventes à traiter</h2>
                    <?php if (!empty($activeSales)): ?>
                        <span class="px-2 py-0.5 bg-orange-100 text-orange-600 text-[10px] font-black rounded-full"><?= count($activeSales) ?></span>
                    <?php endif; ?>
                </div>
                <a href="/dashboard" class="text-[11px] font-bold text-orange-600 hover:underline uppercase tracking-widest">Tout voir</a>
            </div>
            <div class="divide-y divide-slate-50">
                <?php if (empty($activeSales)): ?>
                    <div class="py-16 text-center">
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <p class="text-sm text-slate-400 font-medium">Aucune vente en cours</p>
                        <p class="text-xs text-slate-300 mt-1">Les nouvelles commandes apparaîtront ici.</p>
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($activeSales, 0, 6) as $sale): ?>
                        <?php
                            $statusColor = match($sale['status'] ?? '') {
                                'paid' => 'bg-blue-50 text-blue-600',
                                'in_progress' => 'bg-violet-50 text-violet-600',
                                'delivered' => 'bg-green-50 text-green-600',
                                default => 'bg-slate-100 text-slate-600',
                            };
                            $statusLabel = match($sale['status'] ?? '') {
                                'paid' => 'Payé',
                                'in_progress' => 'En cours',
                                'delivered' => 'Livré',
                                'pending' => 'En attente',
                                default => e(format_status_fr($sale['status'] ?? '-')),
                            };
                        ?>
                        <div class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50/50 transition-colors group">
                            <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-xs font-black uppercase shrink-0">
                                <?= strtoupper(substr($sale['buyer_username'] ?? 'C', 0, 2)) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-slate-900 truncate">@<?= e($sale['buyer_username'] ?? 'Client') ?></span>
                                    <span class="px-2 py-0.5 <?= $statusColor ?> text-[10px] font-bold rounded-full uppercase"><?= $statusLabel ?></span>
                                </div>
                                <span class="text-[11px] text-slate-400 font-medium"><?= date('d/m/Y à H:i', strtotime($sale['created_at'])) ?></span>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-sm font-black text-slate-900"><?= format_price((float)$sale['amount'], true) ?></div>
                            </div>
                            <a href="/commande/<?= $sale['id'] ?>" class="p-2 text-slate-300 hover:text-orange-600 transition-colors opacity-0 group-hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Sidebar (1/3) -->
        <div class="space-y-6">
            <!-- Mes Services -->
            <section class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Mes Services</h2>
                    <div class="flex items-center gap-2">
                        <?php if ($gigsPending > 0): ?>
                            <span class="px-2 py-0.5 bg-amber-50 text-amber-600 text-[10px] font-bold rounded-full"><?= $gigsPending ?> en attente</span>
                        <?php endif; ?>
                        <a href="/profil?tab=work" class="text-[11px] font-bold text-orange-600 hover:underline">Gérer</a>
                    </div>
                </div>
                <div class="divide-y divide-slate-50">
                    <?php if (empty($myGigs)): ?>
                        <div class="py-10 text-center">
                            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500">Créez votre 1er service</p>
                            <p class="text-xs text-slate-400 mt-1">Commencez à vendre vos compétences.</p>
                            <a href="/gig/creer" class="mt-3 inline-block px-4 py-2 bg-orange-600 text-white text-xs font-bold rounded-lg hover:bg-orange-700 transition-colors">Créer</a>
                        </div>
                    <?php else: ?>
                        <?php foreach (array_slice($myGigs, 0, 5) as $gig): ?>
                            <?php
                                $gigStatusColor = match($gig['status'] ?? '') {
                                    'approved' => 'bg-green-400',
                                    'pending' => 'bg-amber-400',
                                    'rejected' => 'bg-red-400',
                                    default => 'bg-slate-300',
                                };
                                $mainImg = !empty($gig['main_image']) ? '/' . $gig['main_image'] : '/images/placeholders/gig.jpg';
                            ?>
                            <div class="flex items-center gap-3 px-5 py-3 hover:bg-slate-50/50 transition-colors group">
                                <div class="w-10 h-10 bg-slate-100 rounded-lg overflow-hidden shrink-0 relative">
                                    <img src="<?= e($mainImg) ?>" class="w-full h-full object-cover" alt="">
                                    <div class="absolute bottom-0.5 right-0.5 w-2.5 h-2.5 <?= $gigStatusColor ?> rounded-full border-2 border-white"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xs font-bold text-slate-900 truncate group-hover:text-orange-600 transition-colors"><?= e($gig['title']) ?></h3>
                                    <p class="text-[11px] text-orange-600 font-bold"><?= format_price((float)$gig['price_base'], true) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Statistiques rapides -->
            <section class="bg-white rounded-2xl border border-slate-100 p-5">
                <h2 class="text-sm font-bold text-slate-900 mb-4">Statistiques</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500">Services actifs</span>
                        <span class="text-xs font-black text-green-600"><?= $gigsApproved ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500">En modération</span>
                        <span class="text-xs font-black text-amber-500"><?= $gigsPending ?></span>
                    </div>
                    <?php if ($gigsRejected > 0): ?>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500">Refusés</span>
                        <span class="text-xs font-black text-red-500"><?= $gigsRejected ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="h-px bg-slate-100 my-1"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500">Ventes totales</span>
                        <span class="text-xs font-black text-slate-900"><?= $totalSalesCount ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500">Taux réussite</span>
                        <span class="text-xs font-black text-slate-900">
                            <?= $totalSalesCount > 0 ? round(($completedSalesCount / $totalSalesCount) * 100) : 0 ?>%
                        </span>
                    </div>
                </div>
            </section>

            <!-- Raccourcis -->
            <section class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-5 text-white">
                <h2 class="text-sm font-bold mb-4">Raccourcis</h2>
                <div class="grid grid-cols-2 gap-2">
                    <a href="/gig/creer" class="flex flex-col items-center gap-1.5 p-3 bg-white/5 hover:bg-white/10 rounded-xl transition-colors text-center">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span class="text-[10px] font-bold uppercase tracking-wider">Créer</span>
                    </a>
                    <a href="/portefeuille" class="flex flex-col items-center gap-1.5 p-3 bg-white/5 hover:bg-white/10 rounded-xl transition-colors text-center">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span class="text-[10px] font-bold uppercase tracking-wider">Portefeuille</span>
                    </a>
                    <a href="/profil" class="flex flex-col items-center gap-1.5 p-3 bg-white/5 hover:bg-white/10 rounded-xl transition-colors text-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="text-[10px] font-bold uppercase tracking-wider">Profil</span>
                    </a>
                    <a href="/messages" class="flex flex-col items-center gap-1.5 p-3 bg-white/5 hover:bg-white/10 rounded-xl transition-colors text-center">
                        <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span class="text-[10px] font-bold uppercase tracking-wider">Messages</span>
                    </a>
                </div>
            </section>
        </div>
    </div>
</div>
