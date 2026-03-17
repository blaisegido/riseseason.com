<div class="max-w-4xl mx-auto" x-data="{ 
    step: 1, 
    title: '', 
    category: '<?php echo $categories[0] ?? 'Développement web'; ?>',
    description: '',
    budget: '',
    deadline: '',
    heroImagePreview: null,
    attachments: [],
    validateStep(n) {
        if (n === 1) return this.title.length > 5 && this.category !== '';
        if (n === 2) return this.description.length > 50;
        if (n === 3) return true; // Les visuels sont optionnels mais recommandés
        if (n === 4) return this.budget > 0 && this.deadline !== '';
        return true;
    },
    handleHeroChange(e) {
        const file = e.target.files[0];
        if (file) {
            this.heroImagePreview = URL.createObjectURL(file);
        }
    },
    handleAttachmentsChange(e) {
        const files = Array.from(e.target.files);
        files.forEach(file => {
            this.attachments.push({
                name: file.name,
                size: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                type: file.type
            });
        });
    },
    removeAttachment(index) {
        this.attachments.splice(index, 1);
        // Note: For real file removal from the input, it's more complex, 
        // but for now, we'll just show the UI list.
    }
}">
  <div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Publier une mission</h1>
        <span class="rounded-full bg-orange-100 px-4 py-1.5 text-xs font-bold text-orange-700 uppercase tracking-widest">Recruteur</span>
    </div>
    
    <!-- Progress Stepper -->
    <div class="flex items-center gap-1">
        <template x-for="i in 5">
            <div class="flex-1 h-2 rounded-full transition-all duration-500" 
                 :class="i <= step ? 'bg-orange-500' : 'bg-gray-200'"></div>
        </template>
    </div>
    <div class="flex justify-between mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
        <span>L'Essentiel</span>
        <span>Le Brief</span>
        <span>Visuels</span>
        <span>Conditions</span>
        <span>Validation</span>
    </div>
  </div>

  <form method="post" action="/job/publier" enctype="multipart/form-data" class="space-y-6">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <!-- Phase 1 : L'Essentiel -->
    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="card p-8 space-y-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 font-bold">1</div>
                <div class="space-y-4 flex-1">
                    <h2 class="text-xl font-bold text-gray-900">Commençons par le titre</h2>
                    <p class="text-sm text-gray-500 italic">Soyez précis pour attirer les freelances qualifiés.</p>
                    <input name="title" x-model="title" required placeholder="Ex: Création d'une identité visuelle pour une startup Foodtech" 
                           class="w-full text-xl font-semibold border-b-2 border-primary-100 focus:border-orange-500 outline-none pb-2 transition-colors bg-transparent">
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 font-bold text-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h7" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <div class="space-y-4 flex-1">
                    <h3 class="text-lg font-bold text-gray-900">Dans quelle catégorie ?</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <?php foreach ($categories as $cat): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="category" value="<?= e($cat) ?>" x-model="category" class="hidden">
                                <div class="px-4 py-3 border-2 rounded-xl text-sm font-medium transition-all text-center"
                                     :class="category === '<?= e($cat) ?>' ? 'border-orange-500 bg-orange-50 text-orange-700 shadow-sm' : 'border-gray-50 bg-white text-gray-600 hover:border-gray-200'">
                                    <?= e($cat) ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phase 2 : Le Brief -->
    <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card p-8 space-y-6">
                <h2 class="text-xl font-bold text-gray-900">Décrivez votre projet</h2>
                <textarea name="description" x-model="description" required 
                          placeholder="Listez vos objectifs, les livrables attendus et tout ce qui aidera le freelance à chiffrer..." 
                          class="w-full min-h-[300px] p-4 rounded-xl border-2 border-primary-100 focus:border-orange-500 outline-none transition-all"></textarea>
                <div class="flex justify-between items-center text-xs font-semibold uppercase tracking-wider">
                    <span :class="description.length < 50 ? 'text-red-400' : 'text-green-500'">Min. 50 caractères</span>
                    <span x-text="`${description.length} caractères`" class="text-gray-400"></span>
                </div>
            </div>
            <div class="space-y-4">
                <div class="card p-5 bg-orange-50 border-orange-100 border text-sm">
                    <h4 class="font-bold text-orange-800 mb-2">💡 Conseil d'expert</h4>
                    <p class="text-orange-700 leading-relaxed">
                        Un brief clair vous fera gagner des heures d'échanges inutiles. Précisez si vous avez déjà des actifs (logo, textes) ou s'il faut tout créer.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Phase 3 : Visuels & Documents -->
    <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Hero Image -->
            <div class="card p-8 space-y-4">
                <h2 class="text-xl font-bold text-gray-900">Image descriptive</h2>
                <p class="text-sm text-gray-500 italic">Une image vélaborée attire 3x plus de candidats.</p>
                
                <div class="relative group border-2 border-dashed border-gray-200 rounded-2xl h-64 flex flex-col items-center justify-center overflow-hidden transition-all hover:border-orange-400 hover:bg-orange-50/30">
                    <template x-if="!heroImagePreview">
                        <div class="text-center space-y-2">
                             <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                             </div>
                             <p class="text-sm font-bold text-gray-700">Cliquez ou glissez l'image</p>
                             <p class="text-xs text-gray-400 font-medium uppercase tracking-tighter">JPG, PNG, WEBP · Max 2 Mo</p>
                        </div>
                    </template>
                    <template x-if="heroImagePreview">
                        <img :src="heroImagePreview" class="absolute inset-0 w-full h-full object-cover">
                    </template>
                    <input type="file" name="hero_image" @change="handleHeroChange" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                   
                    <template x-if="heroImagePreview">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="text-white text-sm font-bold flex items-center gap-2 bg-black/50 px-4 py-2 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Changer l'image
                            </span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Attachments -->
            <div class="card p-8 space-y-4">
                <h2 class="text-xl font-bold text-gray-900">Pièces jointes</h2>
                <p class="text-sm text-gray-500 italic">Cahier des charges, maquettes, exemples...</p>
                
                <div class="relative group border-2 border-dashed border-gray-200 rounded-2xl p-6 flex flex-col items-center justify-center transition-all hover:border-blue-400 hover:bg-blue-50/30">
                    <div class="text-center space-y-2">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.414a4 4 0 00-5.656-5.656l-6.415 6.415a6 6 0 108.486 8.486L20.5 13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <p class="text-sm font-bold text-gray-700">Ajouter des fichiers</p>
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-tighter">PDF, ZIP, DOCX · Max 10 Mo</p>
                    </div>
                    <input type="file" name="attachments[]" multiple @change="handleAttachmentsChange" class="absolute inset-0 opacity-0 cursor-pointer">
                </div>

                <!-- List of selected files -->
                <div class="space-y-2 mt-4" x-show="attachments.length > 0">
                    <template x-for="(file, index) in attachments" :key="index">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100 group">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="w-8 h-8 rounded-lg bg-white border flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="truncate">
                                    <p class="text-xs font-bold text-gray-900 truncate" x-text="file.name"></p>
                                    <p class="text-[10px] text-gray-400 font-medium" x-text="file.size"></p>
                                </div>
                            </div>
                            <button type="button" @click="removeAttachment(index)" class="p-1 hover:text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Phase 4 : Conditions -->
    <div x-show="step === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0">
        <div class="card p-8 max-w-2xl mx-auto space-y-8">
            <h2 class="text-xl font-bold text-gray-900 border-b pb-4">Budget et Délai</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Budget estimé (FCFA)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-gray-400">FCFA</span>
                        <input type="number" name="budget_xof" x-model="budget" step="500" required 
                               class="w-full pl-16 pr-4 py-4 rounded-xl border-2 border-primary-100 focus:border-orange-500 outline-none text-xl font-bold">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Délai souhaité</label>
                    <input type="text" name="deadline_text" x-model="deadline" required placeholder="Ex: 2 semaines / Fin Mars"
                           class="w-full px-4 py-4 rounded-xl border-2 border-primary-100 focus:border-orange-500 outline-none text-xl font-bold">
                </div>
            </div>
        </div>
    </div>

    <!-- Phase 5 : Révision -->
    <div x-show="step === 5" x-transition:enter="transition scale-95 duration-300">
        <div class="card p-8 space-y-8 bg-white border-2 border-orange-200">
            <div class="text-center space-y-2">
                <div class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-bold uppercase tracking-widest mb-2">Prêt pour envoi</div>
                <h2 class="text-2xl font-black text-gray-900" x-text="title"></h2>
                <div class="text-orange-600 font-bold" x-text="`${budget} FCFA · ${deadline}`"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                 <div class="bg-gray-50 rounded-2xl p-6 text-sm text-gray-600 leading-relaxed whitespace-pre-wrap" x-text="description"></div>
                 <template x-if="heroImagePreview">
                     <div class="rounded-2xl overflow-hidden shadow-sm border border-gray-100">
                        <img :src="heroImagePreview" class="w-full h-auto">
                        <div class="p-3 bg-white text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Aperçu de l'image descriptive</div>
                     </div>
                 </template>
            </div>

            <div x-show="attachments.length > 0" class="space-y-3">
                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">Documents joints (<span x-text="attachments.length"></span>)</h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    <template x-for="file in attachments">
                        <div class="flex items-center gap-2 p-2 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-bold">
                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.414a4 4 0 00-5.656-5.656l-6.415 6.415a6 6 0 108.486 8.486L20.5 13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span class="truncate" x-text="file.name"></span>
                        </div>
                    </template>
                </div>
            </div>
            
            <div class="flex items-center gap-2 p-4 bg-blue-50 text-blue-700 rounded-xl text-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>En cliquant sur publier, votre mission sera envoyée en modération. Elle sera visible sous 24h.</span>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex justify-between items-center pt-8 border-t border-gray-100">
        <button type="button" @click="step--" x-show="step > 1" 
                class="px-6 py-3 font-bold text-gray-500 hover:text-orange-600 transition-colors">Retour</button>
        <div x-show="step === 1" class="text-gray-400 text-sm italic">Phase 1 sur 5</div>
        
        <button type="button" @click="step++" x-show="step < 5" :disabled="!validateStep(step)"
                class="ml-auto px-8 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-orange-600 transition-all disabled:opacity-30 disabled:hover:bg-gray-900">
            Continuer
        </button>
        
        <button type="submit" x-show="step === 5" 
                class="ml-auto px-10 py-4 bg-orange-600 text-white font-black rounded-xl hover:bg-orange-700 transform hover:scale-105 transition-all shadow-lg shadow-orange-200">
            Publier ma mission
        </button>
    </div>
  </form>
</div>

