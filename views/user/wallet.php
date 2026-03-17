<div class="max-w-5xl mx-auto space-y-8" x-data="{ withdrawalAmount: 0 }">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-black text-gray-900">Mon Portefeuille</h1>
        <div class="flex gap-2">
            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs font-bold text-gray-600 uppercase tracking-widest">
                Monnaie : <?= e($wallet['currency']) ?>
            </span>
        </div>
    </div>

    <!-- Balance Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card p-8 bg-gray-900 text-white shadow-xl shadow-gray-200">
            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Solde disponible</div>
            <div class="text-5xl font-black"><?= format_price((float)$wallet['balance'], true) ?></div>
            <p class="text-gray-400 text-sm mt-4 italic">Fonds utilisables immédiatement pour vos achats ou retraits.</p>
        </div>

        <div class="card p-8 border-2 border-orange-100 bg-orange-50/30">
            <div class="text-xs font-bold text-orange-600 uppercase tracking-widest mb-2">Fonds en séquestre</div>
            <div class="text-5xl font-black text-gray-900"><?= format_price((float)$wallet['pending_balance'], true) ?></div>
            <p class="text-gray-500 text-sm mt-4 italic">En attente de validation par vos clients.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-white px-2">
        <!-- Retrait -->
        <div class="card p-8 bg-white border border-gray-100 shadow-sm lg:col-span-1 border-t-4 border-t-orange-500">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Demander un retrait</h2>
            <form action="/portefeuille/retrait" method="POST" class="space-y-4">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-2">Montant à retirer (FCFA)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-gray-400">FCFA</span>
                        <input type="number" name="amount_xof" x-model="withdrawalAmount" step="500" min="2000" max="<?= round((float)$wallet['balance'] * get_eur_to_xof_rate()) ?>" required
                               class="w-full pl-16 pr-4 py-3 rounded-xl border-2 border-gray-100 focus:border-orange-500 outline-none text-xl font-bold text-gray-900">
                    </div>
                </div>
                <button type="submit" 
                        class="w-full py-4 bg-orange-600 text-white font-black rounded-xl hover:bg-orange-700 transition-all disabled:opacity-50"
                        :disabled="withdrawalAmount < 2000 || withdrawalAmount > <?= round((float)$wallet['balance'] * get_eur_to_xof_rate()) ?>">
                    Retirer mes fonds
                </button>
                <p class="text-[10px] text-gray-400 text-center uppercase font-bold">Virement bancaire sécurisé</p>
            </form>
        </div>

        <!-- Transactions -->
        <div class="lg:col-span-2 card p-8 bg-white border border-gray-100 shadow-sm">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Dernières transactions</h2>
            <div class="space-y-4 text-black">
                <?php if (empty($transactions)): ?>
                    <div class="py-12 text-center text-gray-400 italic">
                        <svg class="w-12 h-12 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" stroke-width="2" stroke-linecap="round"/></svg>
                        Aucun mouvement récent sur votre compte.
                    </div>
                <?php else: ?>
                    <!-- Liste des transactions ici -->
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Escrow Logic Explanation (ComeUp Style) -->
    <div class="card p-6 bg-blue-50 border border-blue-100 flex items-start gap-4">
        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round"/></svg>
        </div>
        <div>
            <h3 class="font-bold text-blue-900">Fonctionnement du modèle RiseSeason</h3>
            <p class="text-sm text-blue-800 leading-relaxed mt-1">
                La commission est fixe : **environ 655 FCFA par commande complète**, quel que soit le montant. Vos fonds sont sécurisés pendant la réalisation de la mission et débloqués dès validation par l'acheteur. Vous pouvez retirer votre solde à tout moment vers votre compte bancaire.
            </p>
        </div>
    </div>
</div>
