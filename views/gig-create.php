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

// DEBUG
// echo "<!-- DEBUG: mainImage = " . var_export($mainImage ?? 'undefined', true) . " -->";
// echo "<!-- DEBUG: gallery = " . var_export($gallery ?? 'undefined', true) . " -->";
// echo "<!-- DEBUG: editing = " . var_export($editing ?? false, true) . " -->";

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

<div class="max-w-6xl mx-auto" x-data="gigWizardFromInit()">
  <section class="card p-6 sm:p-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
          <template x-if="!editing">Creation de service</template>
          <template x-if="editing">Modification du service</template>
        </h1>
        <p class="mt-1 text-sm text-gray-600">
          <template x-if="!editing">Processus guide type marketplace: clair, rapide, et orienté conversion.</template>
          <template x-if="editing">Modifiez les details de votre service en attente de validation.</template>
        </p>
      </div>
      <div class="rounded-xl border border-orange-100 bg-orange-50 px-4 py-2 text-xs font-semibold text-orange-700">
        Progression: <span x-text="completion + '%' "></span>
      </div>
    </div>
    <p class="mt-2 text-xs" :class="draftState === 'error' ? 'text-red-600' : 'text-gray-500'">
      <template x-if="draftState === 'saving'"><span>Sauvegarde du brouillon...</span></template>
      <template x-if="draftState === 'saved'"><span>Brouillon sauvegarde <span x-text="draftSavedAt || ''"></span></span></template>
      <template x-if="draftState === 'idle'"><span>Auto-save actif (session)</span></template>
      <template x-if="draftState === 'error'"><span>Echec de sauvegarde brouillon</span></template>
    </p>

    <?php if (!empty($error)): ?>
      <div class="mt-4 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        <?= e($error) ?>
      </div>
    <?php endif; ?>

    <div class="mt-6 flex flex-wrap gap-2">
      <button type="button" class="step-pill" :class="step >= 1 ? 'step-pill-active' : ''" @click="requestStep(1)">1. Positionnement</button>
      <button type="button" class="step-pill" :class="step >= 2 ? 'step-pill-active' : ''" @click="requestStep(2)">2. Offre</button>
      <button type="button" class="step-pill" :class="step >= 3 ? 'step-pill-active' : ''" @click="requestStep(3)">3. Medias</button>
      <button type="button" class="step-pill" :class="step >= 4 ? 'step-pill-active' : ''" @click="requestStep(4)">4. FAQ + Publication</button>
    </div>
  </section>

  <form method="post" enctype="multipart/form-data" class="mt-5 grid grid-cols-1 xl:grid-cols-3 gap-5" @input.debounce.800ms="scheduleDraftSave()" @change.debounce.800ms="scheduleDraftSave()" @submit="handleSubmit($event)">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <section class="xl:col-span-2 space-y-5">
      <div class="card p-6 space-y-4" x-show="step===1" x-transition>
        <h2 class="text-lg font-bold text-gray-900">Positionnement du service</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Sous-categorie</label>
            <select name="category_id" class="input-ui" x-model.number="categoryId" required x-ref="categoryInput">
              <option value="">Choisir...</option>
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
            <p class="mt-1 text-xs text-red-600" x-show="showStep1Errors && !validCategory">Selectionne une sous-categorie.</p>
          </div>
          <div class="rounded-xl border border-orange-100 bg-orange-50 p-3 text-sm text-gray-700">
            <p class="font-semibold text-orange-700">Suggestion de copywriting</p>
            <p class="mt-1"><strong>Titre:</strong> <span x-text="currentSuggestion.title || 'Choisis une categorie.'"></span></p>
            <p class="mt-1"><strong>Angle:</strong> <span x-text="currentSuggestion.desc || '-' "></span></p>
            <button type="button" class="mt-2 text-xs font-semibold text-orange-700 hover:underline" @click="applySuggestion()">Appliquer</button>
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Titre accrocheur (10-80)</label>
          <input name="title" class="input-ui" x-model="title" maxlength="80" minlength="10" required x-ref="titleInput">
          <p class="mt-1 text-xs text-gray-500"><span x-text="title.length"></span>/80 caracteres</p>
          <p class="mt-1 text-xs text-red-600" x-show="showStep1Errors && !validTitle">Le titre doit faire entre 10 et 80 caracteres.</p>
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="block text-sm font-semibold text-gray-700">Description detaillee</label>
            <span class="text-xs rounded-full px-2.5 py-1" :class="wordCount >= 200 ? 'bg-green-50 text-green-700' : 'bg-orange-50 text-orange-700'">
              <span x-text="wordCount"></span> mots
            </span>
          </div>
          <textarea name="description" class="input-ui min-h-56" x-model="description" required x-ref="descriptionInput"></textarea>
          <div class="mt-2 flex gap-2">
            <button type="button" class="btn-secondary !py-2 !px-3 text-xs" @click="injectAida()">Inserer structure AIDA</button>
            <p class="text-xs text-gray-500 self-center">Minimum 150 mots, recommande 200+.</p>
          </div>
          <p class="mt-1 text-xs text-red-600" x-show="showStep1Errors && !validDescription">La description doit contenir au moins 150 mots.</p>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" class="btn-primary sm:w-auto" :disabled="!step1Valid" :class="!step1Valid ? 'opacity-60 cursor-not-allowed' : ''" @click="goNext(1,2)">Continuer</button>
        </div>
      </div>

      <div class="card p-6 space-y-4" x-show="step===2" x-transition>
        <h2 class="text-lg font-bold text-gray-900">Offre commerciale</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Prix de base (EUR)</label>
            <input name="price_base" type="number" min="5" step="0.01" class="input-ui" x-model.number="priceBase" required x-ref="priceInput">
            <p class="mt-1 text-xs text-red-600" x-show="showStep2Errors && !validPrice">Le prix minimum est de 5 EUR.</p>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Delai (en jours)</label>
            <input name="delivery_days" type="number" min="1" class="input-ui" x-model.number="deliveryDays" required x-ref="delayInput">
            <p class="mt-1 text-xs text-red-600" x-show="showStep2Errors && !validDelay">Le delai doit etre superieur ou egal a 1 jour.</p>
          </div>
          <div class="rounded-xl bg-gray-50 border border-gray-200 p-3 text-xs text-gray-600">
            <p class="font-semibold text-gray-700">Service express (formule dynamique)</p>
            <p x-show="!expressEligible">Non disponible si delai <= 2 jours.</p>
            <p x-show="expressEligible" x-text="expressRuleText"></p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div class="rounded-xl border border-gray-200 p-4" :class="!expressEligible ? 'opacity-60 bg-gray-50' : 'bg-white'">
            <div class="flex items-center justify-between mb-2">
              <span class="font-semibold text-gray-900">Offre Express</span>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_express" value="1" x-model="isExpress" :disabled="!expressEligible" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
              </label>
            </div>
            <div x-show="expressEligible" class="text-xs text-gray-600">
              <span>Délai normal: <span x-text="deliveryDays"></span> jours</span> |
              <span>Express: <span x-text="expressDaysText"></span></span>
            </div>
            <div x-show="!expressEligible" class="text-xs text-gray-500">
              Service express indisponible pour ce délai
            </div>
          </div>
          <label class="flex items-center gap-2 rounded-xl border border-gray-200 p-3 text-sm">
            <input type="checkbox" name="timezone_africa" value="1" x-model="timezoneAfrica">
            <span>Disponible fuseau Afrique de l'Ouest</span>
          </label>
        </div>

        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Extras payants</h3>
            <button type="button" class="text-xs font-semibold text-orange-700" @click="addExtra()" :disabled="extras.length>=5">+ Ajouter un extra</button>
          </div>

          <template x-for="(extra, idx) in extras" :key="idx">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 rounded-xl border border-gray-200 p-3">
              <input class="input-ui md:col-span-4" :name="'extra_name[]'" placeholder="Nom" x-model="extra.name">
              <input class="input-ui md:col-span-3" :name="'extra_price[]'" type="number" min="1" step="0.01" placeholder="Prix" x-model="extra.price">
              <input class="input-ui md:col-span-4" :name="'extra_desc[]'" placeholder="Description" x-model="extra.desc">
              <button type="button" class="btn-secondary md:col-span-1 !px-2" @click="removeExtra(idx)">X</button>
            </div>
          </template>

          <p class="text-xs text-gray-500">Max 5 extras pour augmenter le panier moyen.</p>
        </div>

        <div class="flex justify-between gap-2">
          <button type="button" class="btn-secondary sm:w-auto" @click="requestStep(1)">Retour</button>
          <button type="button" class="btn-primary sm:w-auto" :disabled="!step2Valid" :class="!step2Valid ? 'opacity-60 cursor-not-allowed' : ''" @click="goNext(2,3)">Continuer</button>
        </div>
      </div>

      <div class="card p-6 space-y-4" x-show="step===3" x-transition>
        <h2 class="text-lg font-bold text-gray-900">Medias du service</h2>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Vignette principale (obligatoire, 2Mo max)</label>
          <input type="file" name="main_image" accept=".jpg,.jpeg,.png,.webp" class="input-ui" :required="!editing" @change="setMainImage($event)" x-ref="mainImageInput">
          <div class="mt-3" x-show="mainImageUrl">
            <img :src="mainImageUrl" alt="Apercu vignette" class="h-44 w-full sm:w-72 object-cover rounded-xl border border-gray-200">
          </div>
          <template x-if="editing">
            <p class="mt-2 text-xs text-gray-600">💡 Laisser vide pour garder l'image actuelle</p>
          </template>
          <p class="mt-1 text-xs text-red-600" x-show="showStep3Errors && !validMainImage">La vignette est obligatoire (jpg/png/webp, 2Mo max).</p>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Galerie (optionnel: 2-5 images)</label>
          <input type="file" name="gallery[]" multiple accept=".jpg,.jpeg,.png,.webp" class="input-ui" @change="setGallery($event)" x-ref="galleryInput">
          <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-2" x-show="galleryUrls.length">
            <template x-for="(url, i) in galleryUrls" :key="i">
              <img :src="url" class="h-24 w-full object-cover rounded-lg border border-gray-200" alt="Apercu galerie">
            </template>
          </div>
          <p class="mt-1 text-xs text-red-600" x-show="showStep3Errors && galleryError" x-text="galleryError"></p>
        </div>

        <div class="flex justify-between gap-2">
          <button type="button" class="btn-secondary sm:w-auto" @click="requestStep(2)">Retour</button>
          <button type="button" class="btn-primary sm:w-auto" :disabled="!step3Valid" :class="!step3Valid ? 'opacity-60 cursor-not-allowed' : ''" @click="goNext(3,4)">Continuer</button>
        </div>
      </div>

      <div class="card p-6 space-y-4" x-show="step===4" x-transition>
        <h2 class="text-lg font-bold text-gray-900">FAQ + Publication</h2>

        <div class="space-y-3" x-ref="faqBlock">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">FAQ (3 a 5)</h3>
            <button type="button" class="text-xs font-semibold text-orange-700" @click="addFaq()" :disabled="faq.length>=5">+ Ajouter</button>
          </div>

          <template x-for="(item, idx) in faq" :key="idx">
            <div class="rounded-xl border border-gray-200 p-3 space-y-2">
              <input class="input-ui" :name="'faq_q[]'" placeholder="Question" x-model="item.q">
              <textarea class="input-ui min-h-20" :name="'faq_r[]'" placeholder="Reponse" x-model="item.r"></textarea>
              <div class="text-right" x-show="faq.length>3">
                <button type="button" class="text-xs font-semibold text-orange-700" @click="removeFaq(idx)">Retirer</button>
              </div>
            </div>
          </template>
          <p class="text-xs text-red-600" x-show="showStep4Errors && !validFaq">Complete entre 3 et 5 questions/reponses.</p>
        </div>

        <div class="rounded-xl border border-orange-100 bg-orange-50 p-3 text-sm text-gray-700">
          <p class="font-semibold text-orange-700">Rappel moderation</p>
          <p>Toute nouvelle publication passe en <strong>pending</strong> et sera validee par un admin.</p>
          <p class="text-xs mt-1">// TODO: notification email admin automatique</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
          <h3 class="font-semibold text-gray-900">Recapitulatif avant publication</h3>
          <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <p><span class="text-gray-500">Titre:</span> <span class="font-medium" x-text="title || '-'"></span></p>
            <p><span class="text-gray-500">Categorie:</span> <span class="font-medium" x-text="categoryId || '-'"></span></p>
            <p><span class="text-gray-500">Prix:</span> <span class="font-medium" x-text="priceBase > 0 ? priceBase + ' EUR' : '-'"></span></p>
            <p><span class="text-gray-500">Delai:</span> <span class="font-medium" x-text="deliveryDays ? deliveryDays + ' jours' : '-'"></span></p>
            <p><span class="text-gray-500">FAQ complete:</span> <span class="font-medium" x-text="checkFaq ? 'Oui' : 'Non'"></span></p>
            <p><span class="text-gray-500">Extras:</span> <span class="font-medium" x-text="extras.length"></span></p>
          </div>
          <p class="mt-3 text-xs text-gray-500">Verification qualite: <span class="font-semibold" x-text="completion + '%'"></span></p>
        </div>

        <div class="flex justify-between gap-2">
          <button type="button" class="btn-secondary sm:w-auto" @click="requestStep(3)">Retour</button>
          <button class="btn-primary" :disabled="!formValid" :class="!formValid ? 'opacity-60 cursor-not-allowed' : ''" @click="if(!formValid){showStep1Errors=true;showStep2Errors=true;showStep3Errors=true;showStep4Errors=true;scrollToFirstInvalid(0)}">
            <template x-if="!editing">Publier mon service</template>
            <template x-if="editing">Modifier le service</template>
          </button>
        </div>
      </div>
    </section>

    <aside class="space-y-4">
      <div class="card p-5 xl:sticky xl:top-24">
        <h3 class="font-bold text-gray-900">Checklist qualite</h3>
        <ul class="mt-3 space-y-2 text-sm">
          <li :class="checkTitle ? 'text-green-700' : 'text-gray-600'">• Titre 10-80 caracteres</li>
          <li :class="checkDescription ? 'text-green-700' : 'text-gray-600'">• Description >= 150 mots</li>
          <li :class="checkPrice ? 'text-green-700' : 'text-gray-600'">• Prix >= 5 EUR</li>
          <li :class="checkDelay ? 'text-green-700' : 'text-gray-600'">• Delai >= 1 jour</li>
          <li :class="checkMainImage ? 'text-green-700' : 'text-gray-600'">• Vignette principale</li>
          <li :class="checkFaq ? 'text-green-700' : 'text-gray-600'">• FAQ 3-5 complete</li>
          <li :class="isExpress ? 'text-orange-700' : 'text-gray-500'">• Service express (<span x-text="expressEligible ? expressDaysText : 'indisponible'"></span>)</li>
          <li :class="timezoneAfrica ? 'text-orange-700' : 'text-gray-500'">• Fuseau Afrique Ouest (option)</li>
        </ul>

        <div class="mt-4 rounded-xl bg-gray-100 p-3">
          <p class="text-xs text-gray-500">Badge profil optimise</p>
          <p class="text-sm font-semibold" :class="completion >= 90 ? 'text-green-700' : 'text-gray-600'" x-text="completion >= 90 ? 'Eligible' : 'A completer'"></p>
        </div>
      </div>
    </aside>
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
    get checkPrice() { return Number(this.priceBase) >= 5; },
    get checkDelay() { return Number(this.deliveryDays) >= 1; },
    get checkMainImage() { return this.validMainImage; },
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
      const band = Math.ceil((d - 1) / 5); // 3-5 =>1, 6-10=>2, 11-15=>3...
      return { min: (2 * band) - 1, max: 2 * band };
    },
    get expressRuleText() {
      const r = this.expressReductionRange;
      return `Regle: delai - ${r.min} ou ${r.max} jours.`;
    },
    get expressDaysText() {
      if (!this.expressEligible) return '-';
      const d = Number(this.deliveryDays);
      const r = this.expressReductionRange;
      const fastMax = Math.max(1, d - r.min);
      const fastMin = Math.max(1, d - r.max);
      return `${fastMin} a ${fastMax} jours`;
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
      this.description += (this.description ? '\n\n' : '') + 'Attention:\nInteret:\nDesir:\nAction:';
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
          this.galleryError = 'Chaque image galerie doit etre jpg/png/webp et <= 2Mo.';
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
      if (this.formValid) return;
      e.preventDefault();
      this.showStep1Errors = true;
      this.showStep2Errors = true;
      this.showStep3Errors = true;
      this.showStep4Errors = true;
      this.scrollToFirstInvalid(0);
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
        formData.append('price_base', String(this.priceBase || ''));
        formData.append('delivery_days', String(this.deliveryDays || ''));
        if (this.isExpress && this.expressEligible) formData.append('is_express', '1');
        if (this.timezoneAfrica) formData.append('timezone_africa', '1');
        this.faq.forEach(item => {
          formData.append('faq_q[]', item.q || '');
          formData.append('faq_r[]', item.r || '');
        });
        this.extras.forEach(item => {
          formData.append('extra_name[]', item.name || '');
          formData.append('extra_price[]', String(item.price || ''));
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

