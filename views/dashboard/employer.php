<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Espace Recruteur</h1>
            <p class="text-slate-500 mt-1">Gérez vos missions et trouvez les meilleurs talents pour vos projets.</p>
        </div>
        <a href="/job/publier" class="inline-flex items-center gap-2 px-6 py-3 bg-orange-600 text-white font-bold rounded-xl hover:bg-orange-700 transition-all shadow-lg shadow-orange-600/20">
            <i class="fas fa-plus"></i>
            Publier une mission
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card p-6 bg-white border border-slate-200">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-briefcase text-xl"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Actives</span>
            </div>
            <div class="text-3xl font-black text-slate-900"><?= count($activeJobs) ?></div>
            <div class="text-sm font-medium text-slate-500 mt-1">Missions en cours</div>
        </div>

        <div class="card p-6 bg-white border border-slate-200">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-handshake text-xl"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">En cours</span>
            </div>
            <div class="text-3xl font-black text-slate-900"><?= count($activeOrders) ?></div>
            <div class="text-sm font-medium text-slate-500 mt-1">Contrats actifs</div>
        </div>

        <div class="card p-6 bg-white border border-slate-200">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-euro-sign text-xl"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Investissement</span>
            </div>
            <div class="text-3xl font-black text-slate-900"><?= format_price((float)$totalSpent, true) ?></div>
            <div class="text-sm font-medium text-slate-500 mt-1">Total engagé</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Missions Récentes -->
        <section class="card p-8 bg-white border border-slate-200">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-900">Mes dernières missions</h2>
                <a href="/profil?tab=work" class="text-xs font-bold text-orange-600 hover:underline uppercase tracking-widest">Voir tout</a>
            </div>
            <div class="space-y-4">
                <?php if (empty($activeJobs)): ?>
                    <div class="py-12 text-center text-slate-400 italic">
                        Aucune mission publiée pour le moment.
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($activeJobs, 0, 3) as $job): ?>
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                            <div>
                                <h3 class="font-bold text-slate-900"><?= e($job['title']) ?></h3>
                                <p class="text-xs text-slate-500"><?= e($job['category']) ?> • <?= date('d/m/Y', strtotime($job['created_at'])) ?></p>
                            </div>
                            <span class="px-3 py-1 bg-white text-[10px] font-bold text-slate-600 rounded-full border border-slate-100 uppercase"><?= e(format_status_fr($job['status'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Commandes en cours -->
        <section class="card p-8 bg-white border border-slate-200">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-900">Commandes de services</h2>
                <a href="/dashboard" class="text-xs font-bold text-orange-600 hover:underline uppercase tracking-widest">Gérer</a>
            </div>
            <div class="space-y-4">
                <?php if (empty($activeOrders)): ?>
                    <div class="py-12 text-center text-slate-400 italic">
                        Aucune commande de service active.
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($activeOrders, 0, 3) as $order): ?>
                        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl">
                            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-slate-900 text-sm">Commande #<?= $order['id'] ?></h3>
                                <p class="text-xs text-slate-500"><?= format_price((float)$order['amount'], true) ?> • <?= e(format_status_fr($order['status'])) ?></p>
                            </div>
                            <a href="/commande/<?= $order['id'] ?>" class="p-2 text-slate-400 hover:text-orange-600"><i class="fas fa-chevron-right"></i></a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
