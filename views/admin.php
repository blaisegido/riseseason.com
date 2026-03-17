<div class="space-y-8" x-data="{usersOpen:true, gigsOpen:true, jobsOpen:true}">
    
    <!-- Header Admin -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-display font-black text-gray-900 tracking-tight">Vue d'ensemble</h1>
            <p class="text-gray-500 mt-1">Gérez l'activité et les statistiques de la plateforme RiseSeason.</p>
        </div>
        <a href="/admin/gigs" class="inline-flex items-center justify-center rounded-xl bg-orange-50 text-orange-600 px-4 py-2 text-sm font-bold shadow-sm hover:bg-orange-100 transition-colors gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Modération Gigs
        </a>
    </div>

    <!-- Stats de base -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-full -translate-y-8 translate-x-8 blur-xl group-hover:bg-blue-500/10 transition-colors"></div>
            <div class="flex items-center gap-4 mb-4 relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <div>
                    <div class="text-[10px] uppercase font-black text-gray-400 tracking-wider">Membres</div>
                    <div class="text-3xl font-black text-gray-900 leading-none mt-1"><?= count($users) ?></div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm relative overflow-hidden group cursor-pointer" @click="gigsOpen = true; $el.scrollIntoView({behavior: 'smooth'})">
            <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/5 rounded-full -translate-y-8 translate-x-8 blur-xl group-hover:bg-orange-500/10 transition-colors"></div>
            <div class="flex items-center gap-4 mb-4 relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <div class="text-[10px] uppercase font-black text-gray-400 tracking-wider">Services à valider</div>
                    <div class="text-3xl font-black text-gray-900 leading-none mt-1"><?= count($pendingGigs) ?></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm relative overflow-hidden group cursor-pointer" @click="jobsOpen = true; $el.scrollIntoView({behavior: 'smooth'})">
            <div class="absolute top-0 right-0 w-24 h-24 bg-purple-500/5 rounded-full -translate-y-8 translate-x-8 blur-xl group-hover:bg-purple-500/10 transition-colors"></div>
            <div class="flex items-center gap-4 mb-4 relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-500 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <div class="text-[10px] uppercase font-black text-gray-400 tracking-wider">Missions à valider</div>
                    <div class="text-3xl font-black text-gray-900 leading-none mt-1"><?= count($pendingJobs) ?></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-gray-300 transition-colors">
            <a href="/admin/posts" class="absolute inset-0 z-20"></a>
            <div class="absolute top-0 right-0 w-24 h-24 bg-green-500/5 rounded-full -translate-y-8 translate-x-8 blur-xl group-hover:bg-green-500/10 transition-colors"></div>
            <div class="flex items-center gap-4 mb-4 relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-500 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                </div>
                <div>
                    <div class="text-[10px] uppercase font-black text-gray-400 tracking-wider">Articles Blog</div>
                    <div class="text-3xl font-black text-gray-900 leading-none mt-1"><?= $postsCount ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Financières -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-[2rem] p-8 border border-emerald-100 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-full -translate-y-8 translate-x-8 blur-2xl group-hover:bg-emerald-500/20 transition-colors"></div>
            <div class="flex items-center gap-5 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-white text-emerald-600 flex items-center justify-center shadow-sm">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <div class="text-xs uppercase font-black text-emerald-700/70 tracking-wider">Commissions générées (≈ 655 FCFA / vente)</div>
                    <div class="text-4xl font-black text-emerald-900 mt-1"><?= format_price((float)$commissions, true) ?></div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-[2rem] p-8 border border-indigo-100 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full -translate-y-8 translate-x-8 blur-2xl group-hover:bg-indigo-500/20 transition-colors"></div>
            <div class="flex items-center gap-5 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-white text-indigo-600 flex items-center justify-center shadow-sm">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </div>
                <div>
                    <div class="text-xs uppercase font-black text-indigo-700/70 tracking-wider">Volume d'affaires global (GMV)</div>
                    <div class="text-4xl font-black text-indigo-900 mt-1"><?= format_price((float)$gmv, true) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <!-- Moderation Gigs -->
        <section class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                    Services en attente <span class="text-sm font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full ml-1"><?= count($pendingGigs) ?></span>
                </h2>
                <button type="button" class="text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors" @click="gigsOpen=!gigsOpen" x-text="gigsOpen ? 'Masquer' : 'Afficher'"></button>
            </div>

            <div x-show="gigsOpen" x-transition.opacity class="space-y-4">
            <?php if (empty($pendingGigs)): ?>
                <div class="text-center py-10 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <p class="text-sm text-gray-500 font-medium tracking-wide">La file d'attente est vide. 🎉</p>
                </div>
            <?php else: ?>
                <?php foreach ($pendingGigs as $g): ?>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-gray-50 hover:bg-gray-100/80 rounded-2xl transition-colors border border-gray-100 gap-4">
                        <div>
                            <p class="text-base font-bold text-gray-900 mb-1"><?= e($g['title']) ?></p>
                            <p class="text-xs font-semibold text-gray-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <?= e($g['username']) ?>
                            </p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <form method="post" action="/admin/gig/<?= (int)$g['id'] ?>/approve">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <button class="btn-primary !px-4 !py-2 !text-xs !rounded-xl !shadow-none">Approuver</button>
                            </form>
                            <form method="post" action="/admin/gig/<?= (int)$g['id'] ?>/delete">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <button class="btn-secondary !px-4 !py-2 !text-xs !rounded-xl !bg-white">Rejeter</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </section>

        <!-- Moderation Jobs -->
        <section class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                    Missions en attente <span class="text-sm font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full ml-1"><?= count($pendingJobs) ?></span>
                </h2>
                <button type="button" class="text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors" @click="jobsOpen=!jobsOpen" x-text="jobsOpen ? 'Masquer' : 'Afficher'"></button>
            </div>

            <div x-show="jobsOpen" x-transition.opacity class="space-y-4">
            <?php if (empty($pendingJobs)): ?>
                <div class="text-center py-10 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <p class="text-sm text-gray-500 font-medium tracking-wide">La file d'attente est vide. 🎉</p>
                </div>
            <?php else: ?>
                <?php foreach ($pendingJobs as $j): ?>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-gray-50 hover:bg-gray-100/80 rounded-2xl transition-colors border border-gray-100 gap-4">
                        <div>
                            <p class="text-base font-bold text-gray-900 mb-1"><?= e($j['title']) ?></p>
                            <p class="text-xs font-semibold text-gray-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <?= e($j['username']) ?>
                            </p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <form method="post" action="/admin/job/<?= (int)$j['id'] ?>/approve">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <button class="bg-purple-600 text-white hover:bg-purple-500 font-bold !px-4 !py-2 !text-xs !rounded-xl transition-colors active:scale-95 shadow-sm">Approuver</button>
                            </form>
                            <form method="post" action="/admin/job/<?= (int)$j['id'] ?>/delete">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <button class="btn-secondary !px-4 !py-2 !text-xs !rounded-xl !bg-white">Supprimer</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Utilisateurs (Pleine largeur) -->
    <section class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm mt-8">
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                Derniers Membres Inscrits
            </h2>
            <button type="button" class="text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors" @click="usersOpen=!usersOpen" x-text="usersOpen ? 'Masquer' : 'Afficher'"></button>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-gray-100" x-show="usersOpen" x-transition.opacity>
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-2xl">Utilisateur</th>
                        <th class="px-6 py-4">Rôle</th>
                        <th class="px-6 py-4 rounded-tr-2xl">Pays</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <?php 
                        // Afficher les 10 derniers utilisateurs
                        $latestUsers = array_slice($users, 0, 10);
                        foreach ($latestUsers as $u): 
                    ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900"><?= e($u['username'] ?? $u['email']) ?></div>
                                <div class="text-xs text-gray-500 mt-0.5"><?= e($u['email']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                    $roleColor = match ($u['role']) {
                                        'admin' => 'bg-red-100 text-red-700',
                                        'freelancer' => 'bg-green-100 text-green-700',
                                        'employeur' => 'bg-blue-100 text-blue-700',
                                        'contributeur' => 'bg-purple-100 text-purple-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                ?>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full <?= $roleColor ?>">
                                    <?= ucfirst(e($u['role'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-600">
                                <?= e($u['country']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

</div>
