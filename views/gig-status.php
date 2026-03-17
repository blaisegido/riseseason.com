<?php
/** @var array $gig */
$isPending  = $gig['status'] === 'pending';
$isRejected = $gig['status'] === 'rejected';
$createdAt  = !empty($gig['created_at'])
    ? (new \DateTime($gig['created_at']))->format('d/m/Y à H\hi')
    : null;
$updatedAt  = !empty($gig['updated_at'])
    ? (new \DateTime($gig['updated_at']))->format('d/m/Y à H\hi')
    : null;
?>

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-50 flex items-center justify-center p-6">
    <div class="max-w-2xl w-full space-y-6">

        <?php if ($isPending): ?>
        <!-- ===== PENDING STATE ===== -->
        <div class="relative bg-white rounded-[3rem] border border-amber-100 shadow-[0_48px_96px_-24px_rgba(0,0,0,0.06)] overflow-hidden p-12 md:p-16">
            <!-- Decorative glow -->
            <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-amber-100/40 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full bg-orange-50/60 blur-3xl pointer-events-none"></div>

            <!-- Animated icon -->
            <div class="relative z-10 flex flex-col items-center text-center space-y-8">
                <div class="relative">
                    <div class="w-24 h-24 bg-amber-50 border-2 border-amber-100 rounded-[2rem] flex items-center justify-center shadow-lg shadow-amber-100">
                        <i class="fas fa-hourglass-half text-4xl text-amber-500" style="animation: spin-slow 4s linear infinite;"></i>
                    </div>
                    <div class="absolute -top-1 -right-1 w-6 h-6 bg-amber-400 rounded-full border-2 border-white flex items-center justify-center">
                        <i class="fas fa-clock text-white text-[8px]"></i>
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-500">En cours de traitement</p>
                    <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-tight">
                        Votre service est<br>en bonne voie.
                    </h1>
                    <p class="text-slate-500 font-medium leading-relaxed max-w-sm mx-auto">
                        Notre équipe de curateurs examine votre service avec attention. Vous recevrez une notification dès qu'une décision sera prise.
                    </p>
                </div>

                <!-- Service recap card -->
                <div class="w-full bg-slate-50 rounded-2xl p-6 border border-slate-100 text-left space-y-3">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Service soumis</p>
                    <p class="text-base font-black text-slate-900"><?= e($gig['title']) ?></p>
                    <?php if ($createdAt): ?>
                        <p class="text-[10px] text-slate-400 font-bold flex items-center gap-2">
                            <i class="fas fa-calendar-alt"></i>
                            Soumis le <?= $createdAt ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Timeline -->
                <div class="w-full space-y-3">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fas fa-check text-emerald-600 text-xs"></i>
                        </div>
                        <div class="text-left flex-1">
                            <p class="text-sm font-black text-slate-900">Service soumis</p>
                            <p class="text-[10px] text-slate-400 font-bold">Votre service a bien été reçu</p>
                        </div>
                        <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Fait</span>
                    </div>
                    <div class="ml-4 border-l-2 border-dashed border-slate-100 h-4"></div>
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center shrink-0 animate-pulse">
                            <i class="fas fa-magnifying-glass text-amber-600 text-xs"></i>
                        </div>
                        <div class="text-left flex-1">
                            <p class="text-sm font-black text-slate-900">Examen curation</p>
                            <p class="text-[10px] text-slate-400 font-bold">Notre équipe vérifie la qualité de votre offre</p>
                        </div>
                        <span class="text-[9px] font-black text-amber-600 uppercase tracking-widest">En cours</span>
                    </div>
                    <div class="ml-4 border-l-2 border-dashed border-slate-100 h-4"></div>
                    <div class="flex items-center gap-4 opacity-40">
                        <div class="w-8 h-8 bg-slate-100 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fas fa-rocket text-slate-400 text-xs"></i>
                        </div>
                        <div class="text-left flex-1">
                            <p class="text-sm font-black text-slate-700">Publication</p>
                            <p class="text-[10px] text-slate-400 font-bold">Votre service rejoint l'élite RiseSeason</p>
                        </div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">En attente</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 w-full pt-2">
                    <a href="/gig/<?= (int)$gig['id'] ?>/modifier"
                       class="flex-1 flex items-center justify-center gap-2 px-6 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-slate-800 transition-all">
                        <i class="fas fa-pen text-xs"></i>
                        Modifier le service
                    </a>
                    <a href="/profil"
                       class="flex-1 flex items-center justify-center gap-2 px-6 py-4 bg-white text-slate-600 border border-slate-200 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-slate-50 transition-all">
                        <i class="fas fa-arrow-left text-xs"></i>
                        Mon profil
                    </a>
                </div>
            </div>
        </div>

        <?php elseif ($isRejected): ?>
        <!-- ===== REJECTED STATE ===== -->
        <div class="relative bg-white rounded-[3rem] border border-red-100 shadow-[0_48px_96px_-24px_rgba(0,0,0,0.06)] overflow-hidden p-12 md:p-16">
            <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-red-50/60 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col items-center text-center space-y-8">
                <div class="w-24 h-24 bg-red-50 border-2 border-red-100 rounded-[2rem] flex items-center justify-center shadow-lg shadow-red-100">
                    <i class="fas fa-triangle-exclamation text-4xl text-red-500"></i>
                </div>

                <div class="space-y-3">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-red-500">Corrections requises</p>
                    <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-tight">
                        Votre service a besoin<br>d'ajustements.
                    </h1>
                    <p class="text-slate-500 font-medium leading-relaxed max-w-sm mx-auto">
                        Notre équipe a examiné votre service et recommande des améliorations avant sa mise en ligne.
                    </p>
                </div>

                <?php if (!empty($gig['rejection_reason']) || !empty($gig['rejection_feedback'])): ?>
                <div class="w-full bg-red-50 rounded-2xl p-6 border border-red-100 text-left space-y-3">
                    <p class="text-[9px] font-black uppercase tracking-widest text-red-400">Motif du retour</p>
                    <?php if (!empty($gig['rejection_reason'])): ?>
                        <p class="text-sm font-black text-red-800"><?= e($gig['rejection_reason']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($gig['rejection_feedback'])): ?>
                        <p class="text-sm text-red-700 font-medium leading-relaxed"><?= nl2br(e($gig['rejection_feedback'])) ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Service recap -->
                <div class="w-full bg-slate-50 rounded-2xl p-6 border border-slate-100 text-left">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2">Service concerné</p>
                    <p class="text-base font-black text-slate-900"><?= e($gig['title']) ?></p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full pt-2">
                    <a href="/gig/<?= (int)$gig['id'] ?>/modifier"
                       class="flex-1 flex items-center justify-center gap-2 px-6 py-4 bg-orange-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-[0_20px_40px_-10px_rgba(249,115,22,0.3)] hover:bg-orange-500 hover:scale-105 transition-all">
                        <i class="fas fa-pen text-xs"></i>
                        Corriger et soumettre
                    </a>
                    <a href="/profil"
                       class="flex-1 flex items-center justify-center gap-2 px-6 py-4 bg-white text-slate-600 border border-slate-200 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-slate-50 transition-all">
                        <i class="fas fa-arrow-left text-xs"></i>
                        Mon profil
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<style>
@keyframes spin-slow {
    0%   { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
