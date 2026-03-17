<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="mb-12">
        <h1 class="text-3xl font-black text-slate-900 mb-2">Mon Plan & Crédits</h1>
        <p class="text-slate-500">Gérez votre visibilité et votre abonnement pour maximiser vos opportunités.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Subscription Card -->
        <div class="bg-white rounded-[2.5rem] p-8 border border-slate-200 shadow-premium flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center">
                    <i class="fas fa-crown text-xl"></i>
                </div>
                <?php if ($isPremium): ?>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase rounded-full">Actif</span>
                <?php else: ?>
                    <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[10px] font-black uppercase rounded-full">Gratuit</span>
                <?php endif; ?>
            </div>

            <h2 class="text-xl font-black text-slate-900 mb-4">Abonnement Premium</h2>
            <ul class="space-y-3 mb-8 flex-1">
                <li class="flex items-center gap-3 text-sm text-slate-600">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    Services illimités (vs 3 max)
                </li>
                <li class="flex items-center gap-3 text-sm text-slate-600">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    Boost de visibilité "Premium"
                </li>
                <li class="flex items-center gap-3 text-sm text-slate-600">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    Positionnement prioritaire
                </li>
            </ul>

            <div class="pt-6 border-t border-slate-100">
                <div class="text-2xl font-black text-slate-900 mb-6">15 000F <span class="text-xs text-slate-400 font-bold uppercase">/ an</span></div>
                <form action="/mon-compte/abonnement" method="POST">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <button type="submit" class="w-full py-4 bg-orange-600 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-orange-500 transition-all shadow-lg shadow-orange-600/20">
                        <?= $isPremium ? 'Renouveler' : 'Passer au Premium' ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Seeds Card -->
        <div class="bg-white rounded-[2.5rem] p-8 border border-slate-200 shadow-premium flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <i class="fas fa-seedling text-xl"></i>
                </div>
                <span class="text-lg font-black text-slate-900"><?= $user['seeds'] ?? 0 ?> <span class="text-[10px] text-slate-400 uppercase tracking-widest">Seeds</span></span>
            </div>

            <h2 class="text-xl font-black text-slate-900 mb-4">Acheter des Seeds</h2>
            <p class="text-sm text-slate-500 mb-8 flex-1">
                Utilisez vos Seeds pour sponsoriser vos services. 500 Seeds valent 500F CFA et permettent de booster votre visibilité.
            </p>

            <div class="pt-6 border-t border-slate-100">
                <form action="/mon-compte/seeds" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Montant à recharger (Min 500F)</label>
                            <input type="number" name="amount" min="500" step="500" value="500" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 text-slate-900 font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none" required>
                    </div>
                    <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg">
                        Acheter des Seeds
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
