<?php
$parentMap = [];
foreach ($categoryTree as $parent) {
    $pid = (int) ($parent['id'] ?? 0);
    if ($pid <= 0) {
        continue;
    }
    $parentMap[$pid] = $pid;
    foreach (($parent['children'] ?? []) as $child) {
        $cid = (int) ($child['id'] ?? 0);
        if ($cid > 0) {
            $parentMap[$cid] = $pid;
        }
    }
}

$wizardInit = json_encode([
    'suggestions' => $suggestions,
    'parentMap' => $parentMap,
    'old' => $old,
    'oldFaq' => $oldFaq,
    'oldExtras' => $oldExtras,
    'csrfToken' => csrf_token(),
    'draftSavedAt' => $draftSavedAt ?? null,
    'editing' => $editing ?? false,
    'mainImage' => $mainImage ?? null,
    'gallery' => $gallery ?? [],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>

<script type="application/json" id="gig-create-init"><?= $wizardInit ?: '{}' ?></script>

<div class="max-w-7xl mx-auto space-y-8" x-data="gigWizardFromInit()" x-cloak>
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 px-4 pt-4">
        <div class="space-y-2">
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">
                <template x-if="!editing">Créer un service</template>
                <template x-if="editing">Modifier le service</template>
            </h1>
            <p class="text-slate-500 font-medium italic">Transformez votre expertise en une offre irrésistible.</p>
        </div>
        
        <!-- Premium Progress Tracker -->
        <div class="flex items-center gap-1.5 p-1.5 bg-slate-100 rounded-2xl w-full md:w-auto shadow-inner">
            <template x-for="s in [1,2,3,4]">
                <button type="button" 
                        @click="requestStep(s)"
                        class="flex-1 md:flex-none px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                        :class="step === s ? 'bg-white text-orange-600 shadow-sm' : (step > s ? 'text-slate-900' : 'text-slate-400')">
                    Phase <span x-text="s"></span>
                </button>
            </template>
        </div>
    </div>

    <!-- Error Banner -->
    <?php if (!empty($error)): ?>
        <div class="mx-4 bg-red-50 border border-red-100 p-4 rounded-3xl flex items-center gap-3 text-red-700 text-xs font-black uppercase tracking-tight">
            <i class="fas fa-circle-exclamation text-lg"></i>
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <!-- Main Layout: Form + Preview -->
    <form method="post" enctype="multipart/form-data" 
          class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start px-4"
          @input.debounce.800ms="scheduleDraftSave()" 
          @change.debounce.800ms="scheduleDraftSave()" 
          @submit="handleSubmit($event)">
        
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <!-- Left: Steps Container -->
        <div class="lg:col-span-12 xl:col-span-8 space-y-8">
            
            <!-- Step 1: Positioning -->
            <div x-show="step===1" x-transition:enter="transition ease-out duration-400 transform" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                <div class="bg-white p-8 md:p-12 rounded-[3.5rem] border border-slate-200 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.03)] space-y-10 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-full blur-3xl -mr-32 -mt-32 pointer-events-none opacity-50"></div>
                    
                    <div class="flex items-center justify-between relative">
                        <div>
                            <span class="text-[10px] font-black text-orange-600 uppercase tracking-[0.2em] mb-1 block">Phase 01</span>
                            <h2 class="text-2xl font-black text-slate-900">Positionnement</h2>
                        </div>
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300">
                             <i class="fas fa-compass text-2xl"></i>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-4">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 block ml-1">Domaine d'expertise</label>
                            <div class="relative group">
                                <select name="category_id" class="w-full bg-slate-50 border-2 border-primary-100 hover:border-slate-300 h-16 px-6 rounded-2xl text-sm font-bold text-slate-900 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all appearance-none cursor-pointer" x-model.number="categoryId" x-ref="categoryInput">
                                    <option value="">Choisir la catégorie...</option>
                                    <?php foreach ($categoryTree as $parent): ?>
                                      <?php if (empty($parent['children'])): ?>
                                        <option value="<?= (int) $parent['id'] ?>"><?= e($parent['name']) ?></option>
                                      <?php else: ?>
                                        <optgroup label="<?= e($parent['name']) ?>">
                                          <?php foreach ($parent['children'] as $child): ?>
                                            <option value="<?= (int) $child['id'] ?>"><?= e($child['name']) ?></option>
                                          <?php endforeach; ?>
                                        </optgroup>
                                      <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-300 group-hover:text-orange-500 transition-colors">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            <p class="text-[10px] text-red-500 font-bold uppercase ml-2" x-show="showStep1Errors && !validCategory">Catégorie requise</p>
                        </div>

                        <div class="bg-slate-900 border border-slate-800 p-6 rounded-3xl space-y-3 relative group transition-all hover:bg-slate-800 shadow-xl shadow-slate-200/50">
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center">
                                    <i class="fas fa-magic text-[10px] text-orange-500"></i>
                                </div>
                                <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest leading-none">Aide au copywriting</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-bold text-slate-300 leading-tight" x-text="currentSuggestion.title || 'Choisissez une catégorie...'"></p>
                            </div>
                            <button type="button" @click="applySuggestion()" class="text-[10px] font-black text-white group-hover:text-orange-400 flex items-center gap-2 transition-all mt-2">
                                Appliquer ce titre <i class="fas fa-arrow-right-long opacity-0 group-hover:opacity-100 transition-all"></i>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between ml-1">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400">Titre de votre service</label>
                            <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest" :class="title.length > 70 ? 'text-orange-500' : ''"><span x-text="title.length"></span>/80</span>
                        </div>
                        <input name="title" 
                               class="w-full bg-slate-50 border-2 border-primary-100 h-20 px-8 rounded-3xl text-xl font-black text-slate-900 focus:bg-white focus:border-orange-500 focus:ring-8 focus:ring-orange-500/5 transition-all placeholder:text-slate-200" 
                               x-model="title" maxlength="80" minlength="10" x-ref="titleInput"
                               placeholder="Ex: Je crée votre landing page premium...">
                        <p class="text-[10px] text-red-500 font-bold uppercase ml-2" x-show="showStep1Errors && !validTitle">Entre 10 et 80 caractères</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between ml-1">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400">Votre plaidoirie commerciale</label>
                            <div class="flex items-center gap-3">
                                <button type="button" @click="injectAida()" class="text-[10px] font-black bg-slate-100 text-slate-500 px-4 py-2 rounded-xl uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all">Structure AI-DA</button>
                                <span class="text-[10px] font-black px-4 py-2 rounded-xl uppercase tracking-widest border shadow-sm" :class="wordCount >= 200 ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100'">
                                    <span x-text="wordCount"></span> mots
                                </span>
                            </div>
                        </div>
                        <textarea name="description" 
                                  class="w-full bg-slate-50 border-2 border-primary-100 p-10 rounded-[3rem] text-base font-medium text-slate-700 leading-relaxed focus:bg-white focus:border-orange-500 focus:ring-8 focus:ring-orange-500/5 transition-all min-h-[400px] scrollbar-hide" 
                                  x-model="description" x-ref="descriptionInput"
                                  placeholder="Détaillez votre offre. Soyez précis, rassurant et professionnel..."></textarea>
                        <p class="text-[10px] text-red-500 font-bold uppercase ml-2" x-show="showStep1Errors && !validDescription">Minimum 150 mots</p>
                    </div>

                    <div class="pt-8 flex justify-end">
                        <button type="button" @click="goNext(1,2)" class="px-12 py-5 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-slate-900/20 hover:scale-105 active:scale-95 transition-all">
                            Continuer l'aventure
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Offers -->
            <div x-show="step===2" x-transition:enter="transition ease-out duration-400 transform" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                <div class="bg-white p-8 md:p-12 rounded-[3.5rem] border border-slate-200 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.03)] space-y-10 relative overflow-hidden">
                    <div class="flex items-center justify-between relative">
                        <div>
                            <span class="text-[10px] font-black text-orange-600 uppercase tracking-[0.2em] mb-1 block">Phase 02</span>
                            <h2 class="text-2xl font-black text-slate-900">Tarification & Délais</h2>
                        </div>
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300">
                             <i class="fas fa-euro-sign text-2xl"></i>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-4">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 block ml-1">Prix de base (FCFA)</label>
                            <div class="relative group">
                                <div class="absolute left-6 top-1/2 -translate-y-1/2 flex items-center gap-2 pointer-events-none">
                                     <span class="text-lg font-black text-slate-200 group-focus-within:text-orange-200 group-hover:text-orange-200 transition-colors">F</span>
                                </div>
                                <input name="price_base_xof" type="number" class="w-full bg-slate-50 border-2 border-primary-100 h-20 pl-12 pr-8 rounded-3xl text-2xl font-black text-slate-900 focus:bg-white focus:border-orange-500 focus:ring-8 focus:ring-orange-500/5 transition-all" x-model.number="priceBase" x-ref="priceInput" placeholder="3000">
                            </div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase ml-2 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i> Min. 3 000 FCFA &mdash; <span class="text-orange-400" x-text="priceBase ? '≈ ' + Math.round(priceBase / 655.957) + ' €' : 'équiv. € affiché ici'"></span>
                            </p>
                        </div>

                        <div class="space-y-4">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 block ml-1">Délai estimé (jours)</label>
                            <div class="relative group">
                                <div class="absolute left-6 top-1/2 -translate-y-1/2 flex items-center gap-2 pointer-events-none text-slate-200 group-focus-within:text-orange-200 group-hover:text-orange-200 transition-colors">
                                     <i class="fas fa-clock text-xl"></i>
                                </div>
                                <input name="delivery_days" type="number" class="w-full bg-slate-50 border-2 border-primary-100 h-20 pl-16 pr-8 rounded-3xl text-2xl font-black text-slate-900 focus:bg-white focus:border-orange-500 focus:ring-8 focus:ring-orange-500/5 transition-all" x-model.number="deliveryDays" x-ref="delayInput">
                            </div>
                            <p class="text-[10px] text-red-500 font-bold uppercase ml-2" x-show="showStep2Errors && !validDelay">1 jour minimum</p>
                        </div>
                    </div>

                    <!-- Boosters -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="relative p-8 rounded-3xl border-2 transition-all duration-500" :class="isExpress ? 'border-orange-500 bg-orange-50/20' : 'border-slate-50 bg-slate-50 opacity-40 hover:opacity-100'">
                           <div class="flex items-center justify-between mb-6">
                               <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center" :class="isExpress ? 'text-orange-500' : 'text-slate-300'">
                                   <i class="fas fa-bolt text-xl"></i>
                               </div>
                               <label class="relative inline-flex items-center cursor-pointer scale-125">
                                    <input type="checkbox" name="is_express" value="1" x-model="isExpress" :disabled="!expressEligible" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                                </label>
                           </div>
                           <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Service Express</h4>
                           <p class="text-xs font-medium text-slate-500 leading-relaxed" x-text="expressEligible ? 'Livrez en ' + expressDaysText + ' pour maximiser vos revenus.' : 'Non disponible (délai trop court)'"></p>
                        </div>

                        <div class="relative p-8 rounded-3xl border-2 transition-all duration-500" :class="timezoneAfrica ? 'border-blue-500 bg-blue-50/20' : 'border-slate-50 bg-slate-50 opacity-40 hover:opacity-100'">
                           <div class="flex items-center justify-between mb-6">
                               <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center" :class="timezoneAfrica ? 'text-blue-500' : 'text-slate-300'">
                                   <i class="fas fa-globe-africa text-xl"></i>
                               </div>
                               <label class="relative inline-flex items-center cursor-pointer scale-125">
                                    <input type="checkbox" name="timezone_africa" value="1" x-model="timezoneAfrica" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                           </div>
                           <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Connectivité Locale</h4>
                           <p class="text-xs font-medium text-slate-500 leading-relaxed">Engagement sur le fuseau GMT Afrique de l'Ouest.</p>
                        </div>
                    </div>

                    <!-- Options System -->
                    <div class="space-y-8 pt-4">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 block ml-1">Extras & Options (Power-ups)</label>
                            <button type="button" @click="addExtra()" class="text-[10px] font-black uppercase text-orange-600 flex items-center gap-2 hover:bg-orange-50 px-4 py-2 rounded-xl transition-all" :disabled="extras.length >= 5">
                                <i class="fas fa-plus-circle"></i> Ajouter une option
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(extra, idx) in extras" :key="idx">
                                <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm grid grid-cols-1 md:grid-cols-12 gap-6 items-center group transition-all hover:shadow-xl hover:shadow-slate-100">
                                    <div class="md:col-span-5">
                                        <input class="w-full bg-slate-50 border-2 border-primary-100 rounded-2xl h-14 px-5 text-xs font-black text-slate-900 focus:ring-2 focus:ring-orange-500/20" :name="'extra_name[]'" placeholder="Nom de l'option..." x-model="extra.name">
                                    </div>
                                    <div class="md:col-span-2 relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-200 italic">€</span>
                                        <input class="w-full bg-slate-50 border-2 border-primary-100 rounded-2xl h-14 pl-8 pr-4 text-sm font-black text-slate-900 focus:ring-2 focus:ring-orange-500/20" :name="'extra_price_xof[]'" type="number" min="500" step="10" x-model="extra.price">
                                    </div>
                                    <div class="md:col-span-4">
                                        <input class="w-full bg-slate-50 border-2 border-primary-100 rounded-2xl h-14 px-5 text-[10px] font-medium text-slate-400" :name="'extra_desc[]'" placeholder="Description courte..." x-model="extra.desc">
                                    </div>
                                    <div class="md:col-span-1 flex justify-center">
                                        <button type="button" @click="removeExtra(idx)" class="w-10 h-10 rounded-2xl text-slate-200 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <template x-if="extras.length === 0">
                                <div class="py-16 border-4 border-dashed border-slate-50 rounded-[3rem] flex flex-col items-center justify-center text-slate-200">
                                    <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mb-4">
                                        <i class="fas fa-layer-group text-2xl"></i>
                                    </div>
                                    <p class="text-xs font-black uppercase tracking-[0.2em]">Augmentez votre panier moyen</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="pt-8 flex justify-between gap-4">
                        <button type="button" @click="requestStep(1)" class="px-8 py-4 text-slate-400 font-black text-xs uppercase tracking-widest hover:text-slate-900 transition-colors">Retour</button>
                        <button type="button" @click="goNext(2,3)" class="px-12 py-5 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-slate-900/20 hover:scale-105 active:scale-95 transition-all">
                            Prochaine phase
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Visuals -->
            <div x-show="step===3" x-transition:enter="transition ease-out duration-400 transform" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                <div class="bg-white p-8 md:p-12 rounded-[3.5rem] border border-slate-200 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.03)] space-y-12 relative overflow-hidden">
                    <div class="flex items-center justify-between relative">
                        <div>
                            <span class="text-[10px] font-black text-orange-600 uppercase tracking-[0.2em] mb-1 block">Phase 03</span>
                            <h2 class="text-2xl font-black text-slate-900">Showcase Visuel</h2>
                        </div>
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300">
                             <i class="fas fa-palette text-2xl"></i>
                        </div>
                    </div>

                    <!-- Main Showcase -->
                    <div class="space-y-6">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 block ml-1">Vignette principale (First Impression)</label>
                        <div class="relative h-[28rem] rounded-[4rem] bg-slate-50 border-4 border-dashed border-slate-100 group transition-all duration-700 hover:border-orange-500/30 overflow-hidden shadow-inner">
                            <input type="file" name="main_image" accept=".jpg,.jpeg,.png,.webp" class="absolute inset-0 opacity-0 cursor-pointer z-20" @change="setMainImage($event)" x-ref="mainImageInput">
                            
                            <template x-if="!mainImageUrl">
                                <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-12 pointer-events-none transition-transform duration-700 group-hover:scale-95">
                                    <div class="w-24 h-24 rounded-[2rem] bg-white shadow-xl flex items-center justify-center text-orange-400 mb-8 group-hover:rotate-6 transition-all">
                                        <i class="fas fa-image text-4xl"></i>
                                    </div>
                                    <h3 class="text-base font-black text-slate-900 uppercase tracking-widest mb-2">Cliquez pour téléverser</h3>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] leading-relaxed max-w-xs">Le visuel est l'argument n°1 de vente. Soignez vos contrastes.</p>
                                </div>
                            </template>
                            
                            <template x-if="mainImageUrl">
                                <div class="absolute inset-0 z-10">
                                    <img :src="mainImageUrl" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent flex items-bottom p-12">
                                         <p class="text-white text-[10px] font-black uppercase tracking-[0.3em]">Prévisualisation du rendu final</p>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <p class="text-[10px] text-red-500 font-bold uppercase ml-2 flex items-center gap-2" x-show="showStep3Errors && !validMainImage">
                            <i class="fas fa-warning"></i> Image requise (JPG/PNG/WEBP, 2Mo max)
                        </p>
                    </div>

                    <!-- Extra Views -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between ml-1">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400">Portfolio & Détails (2-5 images)</label>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                            <div class="aspect-square rounded-[2rem] bg-slate-50 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-300 group relative hover:bg-slate-100 hover:border-orange-200 transition-all cursor-pointer">
                                <input type="file" name="gallery[]" multiple accept=".jpg,.jpeg,.png,.webp" class="absolute inset-0 opacity-0 cursor-pointer" @change="setGallery($event)" x-ref="galleryInput">
                                <i class="fas fa-plus text-xl group-hover:scale-110 transition-transform"></i>
                            </div>
                            <template x-for="(url, i) in galleryUrls" :key="i">
                                <div class="aspect-square rounded-[2rem] overflow-hidden shadow-2xl border-4 border-white">
                                    <img :src="url" class="w-full h-full object-cover">
                                </div>
                            </template>
                        </div>
                        <p class="text-[10px] text-red-500 font-bold uppercase ml-2" x-show="showStep3Errors && galleryError" x-text="galleryError"></p>
                    </div>

                    <div class="pt-8 flex justify-between gap-4">
                        <button type="button" @click="requestStep(2)" class="px-8 py-4 text-slate-400 font-black text-xs uppercase tracking-widest hover:text-slate-900 transition-colors">Retour</button>
                        <button type="button" @click="goNext(3,4)" class="px-12 py-5 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-slate-900/20 hover:scale-105 active:scale-95 transition-all">
                            Phase finale
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 4: FAQ & Publish -->
            <div x-show="step===4" x-transition:enter="transition ease-out duration-400 transform" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                <div class="bg-white p-8 md:p-12 rounded-[3.5rem] border border-slate-200 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.03)] space-y-12 relative overflow-hidden">
                    <div class="flex items-center justify-between relative">
                        <div>
                            <span class="text-[10px] font-black text-orange-600 uppercase tracking-[0.2em] mb-1 block">Phase 04</span>
                            <h2 class="text-2xl font-black text-slate-900">FAQ & Publication</h2>
                        </div>
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300">
                             <i class="fas fa-paper-plane text-2xl"></i>
                        </div>
                    </div>

                    <div class="space-y-8" x-ref="faqBlock">
                        <div class="flex items-center justify-between ml-1">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400">Réponses aux doutes clients (3-5)</label>
                            <button type="button" @click="addFaq()" class="text-[10px] font-black uppercase text-orange-600 flex items-center gap-2 hover:bg-orange-50 px-4 py-2 rounded-xl transition-all" :disabled="faq.length >= 5">
                                <i class="fas fa-plus-circle"></i> Ajouter une Q/R
                            </button>
                        </div>

                        <div class="space-y-6">
                            <template x-for="(item, idx) in faq" :key="idx">
                                <div class="bg-slate-50/50 p-8 rounded-[3rem] border border-slate-100 space-y-5 group transition-all hover:bg-white hover:shadow-xl hover:shadow-slate-100/50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-black text-xs shadow-lg shadow-orange-600/20">
                                                Q<span x-text="idx+1"></span>
                                            </div>
                                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Question Client</span>
                                        </div>
                                        <button type="button" @click="removeFaq(idx)" x-show="faq.length > 3" class="text-slate-200 hover:text-red-500 transition-colors">
                                           <i class="fas fa-circle-xmark text-xl"></i>
                                        </button>
                                    </div>
                                    <input class="w-full bg-white border-2 border-primary-100 focus:border-orange-500/20 rounded-2xl h-16 px-6 text-sm font-black text-slate-900 focus:ring-0 transition-all shadow-sm" :name="'faq_q[]'" placeholder="Ex: Livrez-vous les fichiers sources ?" x-model="item.q">
                                    <textarea class="w-full bg-white border-2 border-primary-100 focus:border-orange-500/20 rounded-[2rem] p-8 text-sm font-medium text-slate-500 h-32 focus:ring-0 transition-all shadow-sm" :name="'faq_r[]'" placeholder="Répondez avec transparence..." x-model="item.r"></textarea>
                                </div>
                            </template>
                        </div>
                        <p class="text-[10px] text-red-500 font-bold uppercase ml-2 flex items-center gap-2" x-show="showStep4Errors && !validFaq">
                             <i class="fas fa-warning"></i> Complétez au moins 3 questions.
                        </p>
                    </div>

                    <!-- Final Action Container -->
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 p-12 md:p-16 rounded-[4rem] text-white space-y-8 relative overflow-hidden group">
                        <i class="fas fa-sparkles absolute -top-8 -right-8 text-[12rem] text-white/5 rotate-12 transition-transform duration-1000 group-hover:scale-110"></i>
                        
                        <div class="space-y-3 relative z-10">
                            <h3 class="text-3xl font-black tracking-tight">C'est le moment.</h3>
                            <p class="text-slate-400 font-medium max-w-sm leading-relaxed">Votre service sera examiné par notre équipe de curateurs. Une fois validé, il rejoindra l'élite des services RiseSeason.</p>
                        </div>

                        <!-- Validation Checklist -->
                        <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="flex items-center gap-3 bg-white/5 px-4 py-3 rounded-2xl" :class="step1Valid ? 'border border-emerald-500/30 bg-emerald-500/5' : 'border border-red-500/30 bg-red-500/5'">
                                <i class="fas text-sm" :class="step1Valid ? 'fa-circle-check text-emerald-400' : 'fa-circle-xmark text-red-400'"></i>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest" :class="step1Valid ? 'text-emerald-300' : 'text-red-300'">Phase 1 — Positionnement</p>
                                    <p class="text-[9px] text-white/30 mt-0.5" x-show="!step1Valid">Catégorie, titre (10-80 car.), description (150 mots min.)</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 bg-white/5 px-4 py-3 rounded-2xl" :class="step2Valid ? 'border border-emerald-500/30 bg-emerald-500/5' : 'border border-red-500/30 bg-red-500/5'">
                                <i class="fas text-sm" :class="step2Valid ? 'fa-circle-check text-emerald-400' : 'fa-circle-xmark text-red-400'"></i>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest" :class="step2Valid ? 'text-emerald-300' : 'text-red-300'">Phase 2 — Tarification</p>
                                    <p class="text-[9px] text-white/30 mt-0.5" x-show="!step2Valid">Prix (min. 3 000 FCFA) et délai de livraison requis</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 bg-white/5 px-4 py-3 rounded-2xl" :class="step3Valid ? 'border border-emerald-500/30 bg-emerald-500/5' : 'border border-red-500/30 bg-red-500/5'">
                                <i class="fas text-sm" :class="step3Valid ? 'fa-circle-check text-emerald-400' : 'fa-circle-xmark text-red-400'"></i>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest" :class="step3Valid ? 'text-emerald-300' : 'text-red-300'">Phase 3 — Visuels</p>
                                    <p class="text-[9px] text-white/30 mt-0.5" x-show="!step3Valid">Vignette principale obligatoire (JPG/PNG/WEBP, 2Mo max)</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 bg-white/5 px-4 py-3 rounded-2xl" :class="step4Valid ? 'border border-emerald-500/30 bg-emerald-500/5' : 'border border-red-500/30 bg-red-500/5'">
                                <i class="fas text-sm" :class="step4Valid ? 'fa-circle-check text-emerald-400' : 'fa-circle-xmark text-red-400'"></i>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest" :class="step4Valid ? 'text-emerald-300' : 'text-red-300'">Phase 4 — FAQ</p>
                                    <p class="text-[9px] text-white/30 mt-0.5" x-show="!step4Valid">3 questions/réponses complètes minimum</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row items-center gap-6 pt-2 relative z-10">
                            <button type="button" @click="requestStep(3)" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-300 hover:text-white transition-colors whitespace-nowrap">Vérifier les visuels</button>
                            
                            <!-- When form is NOT valid: show which step to fix -->
                            <template x-if="!formValid">
                                <button type="button" @click="showStep1Errors=true;showStep2Errors=true;showStep3Errors=true;showStep4Errors=true;scrollToFirstInvalid(0)"
                                        class="w-full md:w-64 px-8 py-4 bg-slate-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] flex items-center justify-center gap-3 hover:bg-slate-500 transition-all">
                                    <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                                    <span>Compléter les champs</span>
                                </button>
                            </template>

                            <!-- When form IS valid: submit -->
                            <button type="submit"
                                    x-show="formValid"
                                    class="w-full md:w-64 px-8 py-4 bg-orange-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-[0_20px_40px_-10px_rgba(249,115,22,0.4)] hover:bg-orange-500 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-3">
                                <i class="fas fa-circle-notch fa-spin" x-show="isSubmitting"></i>
                                <span x-text="isSubmitting ? 'Envoi en cours...' : (editing ? 'Mettre à jour l\'offre' : 'Lancer mon service')"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Right: Real-time Seductive Preview -->
        <div class="lg:col-span-12 xl:col-span-4 sticky top-8 space-y-6">
            <div class="bg-white rounded-[3.5rem] border border-slate-200 shadow-[0_48px_96px_-24px_rgba(0,0,0,0.06)] overflow-hidden group relative">
                
                <!-- Animated Progress Stripe -->
                <div class="h-1.5 bg-slate-50 w-full overflow-hidden absolute top-0 left-0">
                    <div class="h-full bg-orange-500 transition-all duration-1000 ease-out" :style="'width: ' + completion + '%'"></div>
                </div>

                <!-- Preview Content -->
                <div class="p-4">
                   <!-- Card Mockup -->
                   <div class="aspect-[4/3] rounded-[3rem] bg-slate-50 overflow-hidden relative shadow-inner">
                       <template x-if="!mainImageUrl">
                           <div class="absolute inset-0 flex flex-col items-center justify-center opacity-20">
                               <i class="fas fa-image text-5xl mb-3"></i>
                               <p class="text-[10px] font-black uppercase tracking-[0.2em]">Visual Mockup</p>
                           </div>
                       </template>
                       <template x-if="mainImageUrl">
                           <img :src="mainImageUrl" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                       </template>
                       
                       <div class="absolute top-6 left-6">
                           <div class="bg-white/90 backdrop-blur-md px-4 py-2 rounded-2xl text-[9px] font-black text-slate-900 border border-white/50 shadow-sm uppercase tracking-widest">
                              RiseSeason Plus
                           </div>
                       </div>
                   </div>
                </div>

                <div class="p-10 pt-4 space-y-8">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                             <div class="h-1.5 w-8 rounded-full bg-orange-500"></div>
                             <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest" x-text="(categoryId ? 'Catégorie n°' + categoryId : 'Secteur indéfini')"></span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 leading-[1.15] group-hover:text-orange-600 transition-colors duration-500" x-text="title || 'Titre de votre futur best-seller...'"></h3>
                    </div>

                    <div class="grid grid-cols-2 gap-6 pt-8 border-t border-slate-50">
                        <div class="space-y-1">
                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.1em]">Expédition</p>
                            <p class="text-xs font-black text-slate-900 uppercase" x-text="(deliveryDays ? deliveryDays + ' jours' : '--')"></p>
                        </div>
                        <div class="space-y-1 text-right">
                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.1em]">Investissement</p>
                            <div class="flex flex-col items-end">
                                <div class="flex items-end justify-end gap-1">
                                    <span class="text-xl font-black text-slate-900" x-text="priceBase ? Number(priceBase).toLocaleString('fr-FR') : '--'"></span>
                                    <span class="text-xs font-black text-slate-300 mb-1">FCFA</span>
                                </div>
                                <span class="text-[9px] text-slate-300 font-bold" x-show="priceBase" x-text="'≈ ' + Math.round(priceBase / 655.957) + ' €'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Readiness Radar -->
                    <div class="bg-slate-50/50 p-6 rounded-[2.5rem] space-y-4 border border-slate-100/50">
                        <div class="flex items-center justify-between">
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Readiness Score</p>
                            <span class="text-xs font-black text-orange-600" x-text="completion + '%'"></span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                             <template x-for="check in ['Titre', 'Bio', 'Image', 'FAQ']">
                                 <div class="h-1.5 flex-1 rounded-full overflow-hidden bg-slate-100">
                                     <div class="h-full bg-emerald-500 transition-all duration-1000" :style="completion >= 25 ? 'width: 100%' : 'width: 0%'"></div>
                                 </div>
                             </template>
                        </div>
                    </div>
                </div>

                <!-- Hidden Floating Badge -->
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-orange-500/5 rounded-full blur-3xl pointer-events-none group-hover:bg-orange-500/10 transition-colors duration-700"></div>
            </div>
            
            <div class="bg-slate-900 p-8 rounded-xl relative overflow-hidden group">
                 <div class="relative z-10 space-y-4">
                     <p class="text-[10px] font-black text-white/30 uppercase tracking-[0.2em]">Statut du projet</p>
                     <div class="flex items-center gap-3">
                         <div class="w-3 h-3 rounded-full animate-pulse" :class="draftState === 'saving' ? 'bg-amber-400' : 'bg-emerald-400'"></div>
                         <p class="text-xs font-black text-white uppercase tracking-widest" x-text="draftState === 'saving' ? 'Synchro en cours...' : (draftSavedAt ? 'Brouillon sécurisé' : 'Session active')"></p>
                     </div>
                     <p x-show="draftSavedAt" class="text-[9px] font-medium text-white/30" x-text="'Dernière sauvegarde: ' + draftSavedAt"></p>
                 </div>
                 <i class="fas fa-shield-halved absolute -right-2 -bottom-2 text-6xl text-white/5 -rotate-12 transition-transform duration-700 group-hover:scale-110"></i>
            </div>
        </div>
    </form>
</div>

<script>
function gigWizardFromInit() {
  const node = document.getElementById('gig-create-init');
  let init = {};
  try {
    init = JSON.parse(node ? node.textContent : '{}');
  } catch (e) {
    init = {};
  }
  return gigWizard(
    init.suggestions || {},
    init.parentMap || {},
    init.old || {},
    init.oldFaq || [],
    init.oldExtras || [],
    init.csrfToken || '',
    init.draftSavedAt || null,
    init.editing || false,
    init.gigId || null,
    init.mainImage || null,
    init.gallery || []
  );
}

function gigWizard(suggestions, parentMap, old, oldFaq, oldExtras, csrfToken, draftSavedAt, editing, gigId, mainImage, gallery) {
  return {
    step: 1,
    suggestions,
    parentMap,
    csrfToken,
    editing,
    title: old.title || '',
    description: old.description || '',
    categoryId: Number(old.category_id || 0),
    priceBase: Number(old.price_base || 0),
    deliveryDays: Number(old.delivery_days || 3),
    isExpress: Number(old.is_express || 0) === 1,
    timezoneAfrica: Number(old.timezone_africa || 0) === 1,
    faq: Array.isArray(oldFaq) && oldFaq.length ? oldFaq : [{q:'',r:''},{q:'',r:''},{q:'',r:''}],
    extras: Array.isArray(oldExtras) ? oldExtras : [],
    mainImageUrl: mainImage ? `/${mainImage}` : '',
    hasMainImage: !!mainImage,
    mainImageError: '',
    galleryError: '',
    galleryUrls: gallery && Array.isArray(gallery) && gallery.length > 0 ? gallery.map(g => `/${g}`) : [],
    draftState: draftSavedAt ? 'saved' : 'idle',
    draftSavedAt: draftSavedAt || '',
    saveTimer: null,
    showStep1Errors: false,
    showStep2Errors: false,
    showStep3Errors: false,
    showStep4Errors: false,
    isSubmitting: false,

    init() {
      const schedule = () => this.scheduleDraftSave();
      this.$watch('title', schedule);
      this.$watch('description', schedule);
      this.$watch('categoryId', schedule);
      this.$watch('priceBase', schedule);
      this.$watch('deliveryDays', () => { this.syncExpressEligibility(); this.scheduleDraftSave(); });
      this.$watch('isExpress', schedule);
      this.$watch('timezoneAfrica', schedule);
      this.$watch('faq', schedule);
      this.$watch('extras', schedule);
      this.syncExpressEligibility();
    },

    get parentId() {
      return this.parentMap[this.categoryId] || 0;
    },
    get currentSuggestion() {
      return this.suggestions[this.parentId] || {};
    },
    get wordCount() {
      const m = (this.description || '').trim().match(/\p{L}+/gu);
      return m ? m.length : 0;
    },
    get checkTitle() { return this.title.trim().length >= 10 && this.title.trim().length <= 80; },
    get checkDescription() { return this.wordCount >= 150; },
    get checkPrice() { return Number(this.priceBase) >= 3000; }, 
    get checkDelay() { return Number(this.deliveryDays) >= 1; },
    get checkMainImage() { return this.hasMainImage; },
    get checkFaq() {
      const completed = this.faq.filter(i => (i.q || '').trim() && (i.r || '').trim()).length;
      return completed >= 3 && completed <= 5;
    },
    get completion() {
      const checks = [this.checkTitle, this.checkDescription, this.checkPrice, this.checkDelay, this.checkMainImage, this.checkFaq];
      const done = checks.filter(Boolean).length;
      return Math.round((done / checks.length) * 100);
    },
    get validTitle() { return this.checkTitle; },
    get validDescription() { return this.checkDescription; },
    get validCategory() { return Number(this.categoryId) > 0; },
    get validPrice() { return this.checkPrice; },
    get validDelay() { return this.checkDelay; },
    get validMainImage() { return this.hasMainImage && !this.mainImageError; },
    get validGallery() { return !this.galleryError; },
    get validFaq() { return this.checkFaq; },
    get step1Valid() { return this.validCategory && this.validTitle && this.validDescription; },
    get step2Valid() { return this.validPrice && this.validDelay; },
    get step3Valid() { return this.validMainImage && this.validGallery; },
    get step4Valid() { return this.validFaq; },
    get formValid() { return this.step1Valid && this.step2Valid && this.step3Valid && this.step4Valid; },
    get expressEligible() { return Number(this.deliveryDays) > 2; },
    get expressReductionRange() {
      const d = Number(this.deliveryDays);
      if (d <= 2) return { min: 0, max: 0 };
      const band = Math.ceil((d - 1) / 5);
      return { min: (2 * band) - 1, max: 2 * band };
    },
    get expressDaysText() {
      if (!this.expressEligible) return '-';
      const d = Number(this.deliveryDays);
      const r = this.expressReductionRange;
      const fastMax = Math.max(1, d - r.min);
      const fastMin = Math.max(1, d - r.max);
      return `${fastMin} à ${fastMax} jours`;
    },

    applySuggestion() {
      if (!this.currentSuggestion.title) return;
      if (!this.title.trim()) this.title = this.currentSuggestion.title;
      if (!this.description.trim()) this.description = this.currentSuggestion.desc;
      this.scheduleDraftSave();
    },
    syncExpressEligibility() {
      if (!this.expressEligible) {
        this.isExpress = false;
      }
    },
    injectAida() {
      if (this.description.includes('Attention:')) return;
      this.description += (this.description ? '\n\n' : '') + 'Attention:\nIntérêt:\nDésir:\nAction:';
    },
    addFaq() {
      if (this.faq.length >= 5) return;
      this.faq.push({q:'', r:''});
      this.scheduleDraftSave();
    },
    removeFaq(idx) {
      if (this.faq.length <= 3) return;
      this.faq.splice(idx, 1);
      this.scheduleDraftSave();
    },
    addExtra() {
      if (this.extras.length >= 5) return;
      this.extras.push({name:'', price:'', desc:''});
      this.scheduleDraftSave();
    },
    removeExtra(idx) {
      this.extras.splice(idx, 1);
      this.scheduleDraftSave();
    },
    setMainImage(e) {
      const f = e.target.files && e.target.files[0];
      this.mainImageError = '';
      this.hasMainImage = !!f;
      if (!f) {
        this.mainImageUrl = '';
        return;
      }
      const okType = ['image/jpeg', 'image/png', 'image/webp'].includes(f.type);
      const okSize = f.size <= (2 * 1024 * 1024);
      if (!okType || !okSize) {
        this.mainImageError = 'Format invalide ou taille > 2Mo.';
        this.hasMainImage = false;
        this.mainImageUrl = '';
        return;
      }
      this.mainImageUrl = URL.createObjectURL(f);
    },
    setGallery(e) {
      this.galleryError = '';
      this.galleryUrls = [];
      const files = e.target.files || [];
      if (files.length > 0 && (files.length < 2 || files.length > 5)) {
        this.galleryError = 'La galerie doit contenir entre 2 et 5 images.';
      }
      for (let i = 0; i < files.length; i++) {
        const f = files[i];
        const okType = ['image/jpeg', 'image/png', 'image/webp'].includes(f.type);
        const okSize = f.size <= (2 * 1024 * 1024);
        if (!okType || !okSize) {
          this.galleryError = 'Chaque image galerie doit être jpg/png/webp et <= 2Mo.';
          continue;
        }
        this.galleryUrls.push(URL.createObjectURL(files[i]));
      }
    },
    requestStep(target) {
      if (target <= this.step) {
        this.step = target;
        return;
      }
      if (target > 1 && !this.step1Valid) {
        this.showStep1Errors = true;
        this.step = 1;
        return;
      }
      if (target > 2 && !this.step2Valid) {
        this.showStep2Errors = true;
        this.step = 2;
        return;
      }
      if (target > 3 && !this.step3Valid) {
        this.showStep3Errors = true;
        this.step = 3;
        return;
      }
      this.step = target;
    },
    goNext(from, to) {
      if (from === 1 && !this.step1Valid) {
        this.showStep1Errors = true;
        this.scrollToFirstInvalid(1);
        return;
      }
      if (from === 2 && !this.step2Valid) {
        this.showStep2Errors = true;
        this.scrollToFirstInvalid(2);
        return;
      }
      if (from === 3 && !this.step3Valid) {
        this.showStep3Errors = true;
        this.scrollToFirstInvalid(3);
        return;
      }
      this.step = to;
    },
    handleSubmit(e) {
      if (this.isSubmitting) {
        e.preventDefault();
        return false;
      }
      if (this.formValid) {
        this.isSubmitting = true;
        // Native submission continues
        return true;
      }
      e.preventDefault();
      this.showStep1Errors = true;
      this.showStep2Errors = true;
      this.showStep3Errors = true;
      this.showStep4Errors = true;
      this.scrollToFirstInvalid(0);
      return false;
    },
    scrollToFirstInvalid(scopeStep = 0) {
      const scrollToRef = (refName) => {
        const el = this.$refs[refName];
        if (!el) return false;
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        if (typeof el.focus === 'function') el.focus();
        return true;
      };

      const checkStep1 = () => {
        if (!this.validCategory) return scrollToRef('categoryInput');
        if (!this.validTitle) return scrollToRef('titleInput');
        if (!this.validDescription) return scrollToRef('descriptionInput');
        return false;
      };
      const checkStep2 = () => {
        if (!this.validPrice) return scrollToRef('priceInput');
        if (!this.validDelay) return scrollToRef('delayInput');
        return false;
      };
      const checkStep3 = () => {
        if (!this.validMainImage) return scrollToRef('mainImageInput');
        if (!this.validGallery) return scrollToRef('galleryInput');
        return false;
      };
      const checkStep4 = () => {
        if (!this.validFaq) return scrollToRef('faqBlock');
        return false;
      };

      if (scopeStep === 1) { checkStep1(); return; }
      if (scopeStep === 2) { checkStep2(); return; }
      if (scopeStep === 3) { checkStep3(); return; }
      if (scopeStep === 4) { checkStep4(); return; }

      if (checkStep1()) { this.step = 1; return; }
      if (checkStep2()) { this.step = 2; return; }
      if (checkStep3()) { this.step = 3; return; }
      if (checkStep4()) { this.step = 4; return; }
    },
    scheduleDraftSave() {
      clearTimeout(this.saveTimer);
      this.draftState = 'saving';
      this.saveTimer = setTimeout(() => this.saveDraft(), 700);
    },
    async saveDraft() {
      try {
        const formData = new URLSearchParams();
        formData.append('csrf', this.csrfToken);
        formData.append('title', this.title || '');
        formData.append('description', this.description || '');
        formData.append('category_id', String(this.categoryId || ''));
        formData.append('price_base_xof', String(this.priceBase || ''));
        formData.append('delivery_days', String(this.deliveryDays || ''));
        if (this.isExpress && this.expressEligible) formData.append('is_express', '1');
        if (this.timezoneAfrica) formData.append('timezone_africa', '1');
        this.faq.forEach(item => {
          formData.append('faq_q[]', item.q || '');
          formData.append('faq_r[]', item.r || '');
        });
        this.extras.forEach(item => {
          formData.append('extra_name[]', item.name || '');
          formData.append('extra_price_xof[]', String(item.price || ''));
          formData.append('extra_desc[]', item.desc || '');
        });

        const res = await fetch('/gig/brouillon', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: formData.toString()
        });
        const data = await res.json();
        if (!res.ok || !data.ok) {
          throw new Error('save failed');
        }
        this.draftState = 'saved';
        this.draftSavedAt = data.saved_at || '';
      } catch (e) {
        this.draftState = 'error';
      }
    }
  }
}
</script>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
[x-cloak] { display: none !important; }
</style>
