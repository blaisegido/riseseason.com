<?php
/** @var array $gigs */
/** @var array $stats */
/** @var array $user */
?>

<div class="space-y-8" x-data="{ filter: 'all' }">
    <!-- Premium Header -->
    <div class="relative overflow-hidden bg-slate-900 rounded-3xl p-8 md:p-12 text-white shadow-2xl">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-orange-500/20 to-transparent pointer-events-none"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-4 mb-4">
                <h1 class="text-3xl md:text-4xl font-black tracking-tight">Mes Services</h1>
                <?php
                $levelMap = [
                    'nouveau' => ['label' => 'Nouveau', 'class' => 'bg-slate-700 text-slate-300 border-slate-600'],
                    'confirmé' => ['label' => 'Confirmé', 'class' => 'bg-blue-900/40 text-blue-300 border-blue-500/30'],
                    'expert' => ['label' => 'Expert', 'class' => 'bg-orange-900/40 text-orange-300 border-orange-500/30'],
                    'elite' => ['label' => 'Élite', 'class' => 'bg-yellow-900/40 text-yellow-300 border-yellow-500/30'],
                ];
                $lvl = $user['level'] ?? 'nouveau';
                $lvlData = $levelMap[$lvl] ?? $levelMap['nouveau'];
                ?>
                <span class="mt-1 px-3 py-1 border <?= $lvlData['class'] ?> text-[10px] font-black uppercase tracking-widest rounded-full flex items-center gap-2 backdrop-blur-sm shadow-xl">
                    <i class="fas fa-medal text-[9px] opacity-70"></i>
                    Level <?= $lvlData['label'] ?>
                </span>
            </div>
            <p class="text-slate-400 text-lg max-w-xl font-medium">Gérez vos offres, suivez leurs performances et optimisez votre visibilité sur RiseSeason.</p>
            
            <div class="mt-8 flex flex-wrap gap-4">
                <a href="/gig/creer" class="px-8 py-4 bg-orange-600 hover:bg-orange-700 text-white rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-xl shadow-orange-600/20 flex items-center gap-3">
                    <i class="fas fa-plus"></i>
                    Créer un nouveau service
                </a>
                <a href="/mon-compte/plans" class="px-8 py-4 bg-white hover:bg-slate-100 text-slate-900 rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-xl flex items-center gap-3">
                    <i class="fas fa-crown text-orange-500"></i>
                    Mon Plan & Crédits
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div @click="filter = 'all'" :class="filter === 'all' ? 'ring-2 ring-orange-500 bg-white' : 'bg-white/50 hover:bg-white'" class="p-6 rounded-3xl border border-slate-200 cursor-pointer transition-all shadow-sm">
            <p class="text-[10px] font-black uppercase text-slate-400 mb-1 tracking-widest">Total</p>
            <p class="text-2xl font-black text-slate-900"><?= $stats['total'] ?></p>
        </div>
        <div @click="filter = 'approved'" :class="filter === 'approved' ? 'ring-2 ring-emerald-500 bg-white' : 'bg-white/50 hover:bg-white'" class="p-6 rounded-3xl border border-slate-200 cursor-pointer transition-all shadow-sm">
            <p class="text-[10px] font-black uppercase text-slate-400 mb-1 tracking-widest">En ligne</p>
            <p class="text-2xl font-black text-emerald-600"><?= $stats['approved'] ?></p>
        </div>
        <div @click="filter = 'pending'" :class="filter === 'pending' ? 'ring-2 ring-amber-500 bg-white' : 'bg-white/50 hover:bg-white'" class="p-6 rounded-3xl border border-slate-200 cursor-pointer transition-all shadow-sm">
            <p class="text-[10px] font-black uppercase text-slate-400 mb-1 tracking-widest">En relecture</p>
            <p class="text-2xl font-black text-amber-600"><?= $stats['pending'] ?></p>
        </div>
        <div class="hidden md:block p-6 rounded-3xl border border-slate-200 bg-gradient-to-br from-orange-50 to-white transition-all shadow-sm">
            <p class="text-[10px] font-black uppercase text-orange-400 mb-1 tracking-widest">Seeds</p>
            <p class="text-2xl font-black text-orange-600"><?= $user['seeds'] ?? 0 ?></p>
        </div>
        <div class="p-6 rounded-3xl border border-slate-200 bg-slate-900 text-white transition-all shadow-sm">
            <p class="text-[10px] font-black uppercase text-slate-500 mb-1 tracking-widest">Plan</p>
            <p class="text-lg font-black <?= ($user['subscription_status'] ?? 'free') === 'premium' ? 'text-orange-400' : 'text-white' ?>">
                <?= ($user['subscription_status'] ?? 'free') === 'premium' ? 'PREMIUM' : 'GRATUIT' ?>
            </p>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden min-h-[400px]">
        <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Liste de vos services</h2>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest mr-2">Filtrer par :</span>
                <select x-model="filter" class="bg-white border text-[10px] font-black uppercase tracking-widest rounded-xl px-4 py-2 outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                    <option value="all">Tous les services</option>
                    <option value="approved">Publiés</option>
                    <option value="pending">En attente</option>
                    <option value="paused">Inactifs</option>
                    <option value="rejected">Rejetés</option>
                </select>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 gap-6">
                <?php if (empty($gigs)): ?>
                    <div class="py-20 text-center">
                        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-rocket text-4xl text-slate-200"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Lancez votre premier service !</h3>
                        <p class="text-slate-500 mb-8 max-w-sm mx-auto font-medium">Partagez votre expertise et commencez à recevoir des commandes dès aujourd'hui.</p>
                        <a href="/gig/creer" class="inline-flex items-center gap-3 px-8 py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-slate-800 transition-all">
                            <i class="fas fa-plus"></i>
                            Créer mon premier service
                        </a>
                    </div>
                <?php endif; ?>

                <?php foreach ($gigs as $gig): ?>
                    <div x-show="filter === 'all' || filter === '<?= $gig['status'] ?>'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-4"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="group relative bg-white rounded-3xl border border-slate-100 p-6 hover:shadow-2xl hover:border-orange-100 transition-all duration-300 flex flex-col md:flex-row gap-8 items-center">
                        
                        <!-- Thumbnail -->
                        <div class="w-full md:w-48 aspect-video md:aspect-[4/3] rounded-2xl overflow-hidden bg-slate-100 relative shadow-inner shrink-0">
                            <img src="<?= e($gig['main_image'] ?: 'img/placeholder-gig.jpg') ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute top-3 left-3">
                                <?php
                                $statusMap = [
                                    'approved' => ['bg-emerald-500', 'En ligne'],
                                    'pending' => ['bg-amber-500', 'Relecture'],
                                    'paused' => ['bg-slate-500', 'Inactif'],
                                    'rejected' => ['bg-rose-500', 'Rejeté'],
                                ];
                                $s = $statusMap[$gig['status']] ?? ['bg-gray-400', $gig['status']];
                                ?>
                                <span class="px-2.5 py-1 <?= $s[0] ?> text-white text-[9px] font-black uppercase tracking-wider rounded-lg shadow-lg"><?= $s[1] ?></span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0 space-y-3">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-black uppercase text-orange-600 tracking-widest bg-orange-50 px-2 py-0.5 rounded-md"><?= e($gig['category'] ?: 'Général') ?></span>
                                <span class="text-[10px] font-bold text-slate-400">• Créé le <?= date('d/m/Y', strtotime($gig['created_at'])) ?></span>
                            </div>
                            <h3 class="text-xl font-black text-slate-900 group-hover:text-orange-600 transition-colors leading-tight"><?= e($gig['title']) ?></h3>
                            <div class="flex flex-wrap items-center gap-6 text-sm font-bold text-slate-500">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-tags text-slate-300"></i>
                                    Partir de <?= format_price((float)$gig['price_base']) ?>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock text-slate-300"></i>
                                    <?= $gig['delivery_days'] ?> jours
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex md:flex-col gap-3 w-full md:w-auto shrink-0">
                            <a href="/gig/<?= $gig['id'] ?>/modifier" class="flex-1 md:w-48 px-6 py-3 bg-slate-50 hover:bg-slate-100 text-slate-900 rounded-xl font-black text-[10px] uppercase tracking-widest text-center transition-all border border-slate-100 flex items-center justify-center gap-2">
                                <i class="fas fa-pen-to-square text-xs opacity-40"></i>
                                Modifier
                            </a>

                            <?php if ($gig['status'] === 'approved'): ?>
                            <form method="POST" action="/gig/<?= $gig['id'] ?>/sponsor" class="flex-1 md:w-48">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="days" value="5">
                                <button class="w-full px-6 py-3 bg-orange-600 hover:bg-orange-500 text-white rounded-xl font-black text-[10px] uppercase tracking-widest text-center transition-all shadow-lg shadow-orange-600/20 flex items-center justify-center gap-2">
                                    <i class="fas fa-rocket text-xs"></i>
                                    Sponsoriser (5j)
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <form method="POST" action="/gig/<?= $gig['id'] ?>/toggle-pause" class="flex-1 md:w-48">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <button class="w-full px-6 py-3 bg-white hover:bg-slate-50 text-slate-600 rounded-xl font-black text-[10px] uppercase tracking-widest text-center transition-all border border-slate-100 flex items-center justify-center gap-2">
                                    <?php if ($gig['status'] === 'paused'): ?>
                                        <i class="fas fa-play text-xs opacity-40"></i>
                                        Reprendre
                                    <?php else: ?>
                                        <i class="fas fa-pause text-xs opacity-40"></i>
                                        Désactiver
                                    <?php endif; ?>
                                </button>
                            </form>

                            <?php if ($gig['status'] === 'rejected'): ?>
                                <a href="/gig/<?= $gig['slug'] ?>/statut" class="flex-1 md:w-48 px-6 py-3 bg-rose-50 text-rose-600 rounded-xl font-black text-[10px] uppercase tracking-widest text-center transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-circle-info text-xs"></i>
                                    Motif du rejet
                                </a>
                            <?php endif; ?>

                            <form method="POST" action="/gig/<?= $gig['id'] ?>/supprimer" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce service ? Cette action est irréversible.');" class="flex-1 md:w-48">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <button class="w-full px-6 py-3 bg-white hover:bg-rose-50 text-rose-600 rounded-xl font-black text-[10px] uppercase tracking-widest text-center transition-all border border-rose-50 flex items-center justify-center gap-2">
                                    <i class="fas fa-trash-can text-xs opacity-40"></i>
                                    Supprimer
                                </button>
                            </form>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
