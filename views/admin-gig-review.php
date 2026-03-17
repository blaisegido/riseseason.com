<div class="max-w-6xl mx-auto space-y-6">
  <section class="card p-6">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Révision du service</h1>
        <p class="text-sm text-gray-600 mt-1">Vérifiez le contenu et prenez une décision de modération</p>
      </div>
      <a href="/admin/gigs" class="btn-secondary">← Retour à la liste</a>
    </div>
  </section>

  <section class="card p-6 sm:p-8">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
      <div>
        <p class="text-xs text-orange-700 font-semibold uppercase tracking-wide">
          <?= e($gig['category'] ?: 'Catégorie non définie') ?>
        </p>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1"><?= e($gig['title']) ?></h1>
        <p class="text-sm text-gray-600 mt-2">
          Par <span class="font-semibold">@<?= e($gig['username']) ?></span>
          · <span class="text-gray-500"><?= e($gig['email']) ?></span>
          · Soumis le <?= date('d/m/Y à H:i', strtotime($gig['created_at'])) ?>
        </p>
      </div>
      <div class="rounded-2xl border border-orange-100 bg-orange-50 px-4 py-3 text-sm">
        <p class="font-bold text-orange-700"><?= format_price((float)$gig['price_base']) ?></p>
        <p class="text-gray-700">Délai: <?= (int)$gig['delivery_days'] ?> jours</p>
        <?php if ((int)$gig['is_express'] === 1): ?>
          <p class="text-xs text-orange-700 font-semibold mt-1">Service express disponible</p>
        <?php endif; ?>
        <?php if ((int)$gig['timezone_africa'] === 1): ?>
          <p class="text-xs text-orange-700 font-semibold mt-1">Disponible fuseau Afrique</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="mt-5 grid grid-cols-1 lg:grid-cols-2 gap-4">
      <?php if ($gig['main_image']): ?>
        <img src="/<?= e($gig['main_image']) ?>" alt="Vignette principale" class="w-full h-72 object-cover rounded-2xl border border-gray-200">
      <?php else: ?>
        <div class="w-full h-72 rounded-2xl border border-dashed border-gray-300 flex items-center justify-center text-gray-500">
          Aucune image principale
        </div>
      <?php endif; ?>

      <div class="grid grid-cols-2 gap-2">
        <?php if (!empty($gig['gallery'])): ?>
          <?php foreach ($gig['gallery'] as $img): ?>
            <img src="/<?= e($img) ?>" alt="Galerie" class="w-full h-36 object-cover rounded-xl border border-gray-200">
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-span-2 rounded-xl border border-dashed border-gray-300 h-36 flex items-center justify-center text-sm text-gray-500">
            Pas de galerie supplémentaire
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="card p-6 sm:p-8">
    <h2 class="text-xl font-bold text-gray-900">Description du service</h2>
    <div class="mt-3 text-gray-700 leading-relaxed whitespace-pre-line bg-gray-50 rounded-lg p-4 border">
      <?= e($gig['description']) ?>
    </div>
  </section>

  <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <article class="card p-6">
      <h3 class="text-lg font-bold text-gray-900">Extras payants</h3>
      <?php if (empty($gig['extras'])): ?>
        <p class="mt-2 text-sm text-gray-600">Aucun extra défini.</p>
      <?php else: ?>
        <div class="mt-3 space-y-2">
          <?php foreach ($gig['extras'] as $extra): ?>
            <div class="rounded-xl border border-gray-200 p-3">
              <div class="flex items-start justify-between gap-2">
                <p class="font-semibold text-gray-900"><?= e($extra['name'] ?? '') ?></p>
                <span class="text-orange-700 font-semibold">+<?= format_price((float)($extra['price'] ?? 0)) ?></span>
              </div>
              <p class="text-sm text-gray-600 mt-1"><?= e($extra['desc'] ?? '') ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </article>

    <article class="card p-6">
      <h3 class="text-lg font-bold text-gray-900">Questions fréquentes</h3>
      <?php if (empty($gig['faq'])): ?>
        <p class="mt-2 text-sm text-gray-600">Aucune FAQ définie.</p>
      <?php else: ?>
        <div class="mt-3 space-y-3">
          <?php foreach ($gig['faq'] as $item): ?>
            <div class="border-l-4 border-orange-200 pl-4">
              <p class="font-semibold text-gray-900">Q: <?= e($item['q'] ?? '') ?></p>
              <p class="text-gray-700 mt-1">R: <?= e($item['r'] ?? '') ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </article>
  </section>

  <section class="card p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Décision de modération</h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Formulaire d'approbation -->
      <div class="rounded-xl border border-green-200 bg-green-50 p-4">
        <h3 class="font-semibold text-green-800 mb-3">✅ Approuver le service</h3>
        <p class="text-sm text-green-700 mb-4">
          Le service respecte nos standards de qualité et peut être publié.
        </p>
        <form method="post" action="/admin/gigs/<?= (int)$gig['id'] ?>/approve">
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
          <button type="submit" class="btn-primary w-full">Approuver et publier</button>
        </form>
      </div>

      <!-- Formulaire de rejet -->
      <div class="rounded-xl border border-red-200 bg-red-50 p-4">
        <h3 class="font-semibold text-red-800 mb-3">❌ Rejeter le service</h3>
        <p class="text-sm text-red-700 mb-4">
          Le service nécessite des corrections avant publication.
        </p>
        <form method="post" action="/admin/gigs/<?= (int)$gig['id'] ?>/reject">
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

          <div class="space-y-3">
            <div>
              <label class="block text-sm font-semibold text-red-800 mb-1">Raison du rejet *</label>
              <select name="rejection_reason" required class="input-ui w-full">
                <option value="">Sélectionnez une raison</option>
                <option value="description_incomplete">Description incomplète ou peu claire</option>
                <option value="prix_non_competitif">Prix non compétitif ou inadapté</option>
                <option value="delai_trop_long">Délai de livraison trop long</option>
                <option value="images_qualite">Images de mauvaise qualité</option>
                <option value="faq_insuffisante">FAQ insuffisante ou manquante</option>
                <option value="extras_manquants">Extras manquants ou inadaptés</option>
                <option value="categorie_inappropriee">Catégorie inappropriée</option>
                <option value="contenu_inapproprie">Contenu inapproprié ou non professionnel</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-red-800 mb-1">Commentaires et axes d'amélioration</label>
              <textarea name="rejection_feedback" rows="4" class="input-ui w-full" placeholder="Expliquez précisément ce qui doit être corrigé et donnez des conseils constructifs pour améliorer le service..."></textarea>
            </div>

            <button type="submit" class="btn-secondary w-full">Rejeter avec commentaires</button>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>