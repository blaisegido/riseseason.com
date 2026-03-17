<?php
/** @var array $order */
/** @var array $user */
?>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">
                <i class="fas fa-receipt"></i>
                Commande #<?= e($order['id']) ?>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Détails de la mission</h1>
        </div>
        <div class="flex items-center gap-3">
            <?php
            $statusClass = match($order['status']) {
                'pending' => 'bg-amber-50 text-amber-600 border-amber-200',
                'paid' => 'bg-blue-50 text-blue-600 border-blue-200',
                'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                default => 'bg-slate-50 text-slate-600 border-slate-200'
            };
            $statusLabel = match($order['status']) {
                'pending' => 'En attente',
                'paid' => 'Payé (En séquestre)',
                'completed' => 'Terminé',
                default => e(format_status_fr($order['status']))
            };
            ?>
            <span class="px-4 py-1.5 rounded-full border text-xs font-bold uppercase tracking-wide <?= $statusClass ?>">
                <?= $statusLabel ?>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Gig Details -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="aspect-video relative">
                    <img src="/<?= e($order['gig_image']) ?>" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-6">
                        <h2 class="text-xl font-bold text-white"><?= e($order['gig_title']) ?></h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6 pb-6 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                                <i class="fas fa-handshake text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase">Vendeur</p>
                                <p class="font-bold text-slate-900"><?= e($order['seller_username']) ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-400 uppercase">Acheteur</p>
                            <p class="font-bold text-slate-900"><?= e($order['buyer_username']) ?></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2">
                            <span class="text-slate-500 font-medium">Prix du service</span>
                            <span class="font-bold text-slate-900"><?= format_price((float)$order['amount']) ?></span>
                        </div>
                        <?php if ((int)$user['id'] === (int)$order['seller_id']): ?>
                            <div class="flex items-center justify-between py-2 text-slate-500">
                                <span>Commission plateforme</span>
                                <span class="font-medium">- <?= format_price((float)$order['commission']) ?></span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-t border-slate-100 mt-2">
                                <span class="text-slate-900 font-bold uppercase text-xs tracking-widest">Net à recevoir</span>
                                <span class="text-xl font-black text-orange-600"><?= format_price((float)$order['net_to_seller']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Discussion / Actions -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm text-center">
                <i class="fas fa-comments text-4xl text-slate-200 mb-4"></i>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Besoin d'échanger sur la mission ?</h3>
                <p class="text-slate-500 text-sm mb-6">Utilisez la messagerie interne pour envoyer vos fichiers ou poser des questions.</p>
                <?php 
                $chatPartner = ((int)$user['id'] === (int)$order['buyer_id']) ? $order['seller_id'] : $order['buyer_id'];
                ?>
                <a href="/messages?user=<?= $chatPartner ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 transition-all">
                    <i class="fas fa-paper-plane text-xs"></i>
                    Ouvrir la conversation
                </a>
            </div>

            <!-- Review Section -->
            <?php if ($order['status'] === 'completed' && (int)$user['id'] === (int)$order['buyer_id']): ?>
                <?php if ($hasReview): ?>
                    <div class="bg-emerald-50 p-6 rounded-2xl border border-emerald-100 shadow-sm text-center">
                        <i class="fas fa-star text-4xl text-emerald-200 mb-4"></i>
                        <h3 class="text-lg font-bold text-emerald-900 mb-1">Avis envoyé !</h3>
                        <p class="text-emerald-700 text-sm">Merci d'avoir partagé votre expérience avec <?= e($order['seller_username']) ?>.</p>
                    </div>
                <?php else: ?>
                    <div class="bg-white p-8 rounded-2xl border-2 border-orange-100 shadow-sm animate-fade-in" x-data="{ rating: 5 }">
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Comment s'est passée votre mission ?</h3>
                        <p class="text-slate-500 text-sm mb-6">Votre avis aide les autres membres de la communauté et récompense le travail de <?= e($order['seller_username']) ?>.</p>
                        
                        <form action="/commande/<?= $order['id'] ?>/avis" method="POST" class="space-y-4">
                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                            <input type="hidden" name="rating" :value="rating">
                            
                            <div class="flex flex-col items-center gap-4 py-4 bg-slate-50 rounded-2xl">
                                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Votre note</p>
                                <div class="flex gap-2">
                                    <template x-for="i in [1,2,3,4,5]">
                                        <button type="button" @click="rating = i" class="text-3xl focus:outline-none transition-all transform hover:scale-110" :class="rating >= i ? 'text-orange-500' : 'text-slate-200'">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest">Votre commentaire</label>
                                <textarea name="comment" required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-orange-500 min-h-[100px] transition-all" placeholder="Qu'avez-vous particulièrement apprécié ?"></textarea>
                            </div>

                            <button type="submit" class="w-full py-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold transition-all shadow-lg shadow-slate-900/10">
                                Publier mon avis
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <!-- Summary Info -->
            <div class="bg-slate-900 text-white p-6 rounded-2xl shadow-xl shadow-slate-900/20">
                <h3 class="font-bold text-white/50 uppercase text-[10px] tracking-widest mb-4">Rappel du paiement</h3>
                <div class="text-3xl font-black mb-1"><?= format_price((float)$order['amount']) ?></div>
                <p class="text-xs text-white/40 mb-6 font-medium leading-relaxed">
                    <?php if ($order['status'] === 'paid'): ?>
                        Les fonds sont actuellement en séquestre sur RiseSeason. Ils seront libérés dès que l'acheteur validera la livraison.
                    <?php elseif ($order['status'] === 'completed'): ?>
                        Cette commande est terminée. Le vendeur a reçu ses fonds.
                    <?php else: ?>
                        Le paiement n'a pas encore été finalisé pour cette commande.
                    <?php endif; ?>
                </p>

                <?php if ($order['status'] === 'paid' && (int)$user['id'] === (int)$order['buyer_id']): ?>
                    <form action="/commande/<?= $order['id'] ?>/valider" method="POST">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <button type="submit" class="w-full py-4 bg-orange-600 hover:bg-orange-500 text-white rounded-xl font-bold transition-all shadow-lg shadow-orange-600/30 flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            Valider la livraison
                        </button>
                    </form>
                    <p class="text-[10px] text-center mt-4 text-white/30 font-bold uppercase tracking-tighter">Action irréversible</p>
                <?php endif; ?>
            </div>

            <!-- Timeline -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-clock text-orange-500"></i>
                    Historique
                </h3>
                <div class="space-y-6">
                    <div class="flex gap-4 relative">
                        <div class="absolute left-[7px] top-6 bottom-[-24px] w-[2px] bg-slate-100"></div>
                        <div class="w-4 h-4 rounded-full bg-orange-500 border-4 border-orange-50 shrink-0 z-10"></div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Commande créée</p>
                            <p class="text-[10px] text-slate-400"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                        </div>
                    </div>
                    <?php if ($order['status'] !== 'pending'): ?>
                    <div class="flex gap-4 relative">
                        <div class="w-4 h-4 rounded-full bg-blue-500 border-4 border-blue-50 shrink-0 z-10"></div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Paiement reçu</p>
                            <p class="text-[10px] text-slate-400">Transaction sécurisée</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($order['status'] === 'completed'): ?>
                    <div class="flex gap-4">
                        <div class="w-4 h-4 rounded-full bg-emerald-500 border-4 border-emerald-50 shrink-0 z-10"></div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Mission terminée</p>
                            <p class="text-[10px] text-slate-400">Fonds libérés</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
