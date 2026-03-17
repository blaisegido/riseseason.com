<div class="max-w-5xl mx-auto space-y-6">
  <section class="card p-6 sm:p-8">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
      <div>
        <p class="text-xs text-orange-700 font-semibold uppercase tracking-wide"><?= e($gig['parent_category'] ?: $gig['category']) ?></p>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1"><?= e($gig['title']) ?></h1>
        <p class="text-sm text-gray-600 mt-2">
          Par <a href="/profil/<?= e($gig['username']) ?>" class="text-orange-700 hover:underline">@<?= e($gig['username']) ?></a>
          · <?= e($gig['country']) ?>
        </p>
      </div>
      <div class="rounded-2xl border border-orange-100 bg-orange-50 px-4 py-3 text-sm">
        <p class="font-bold text-orange-700"><?= format_price((float)$gig['price_base']) ?></p>
        <p class="text-gray-700">Délai: <?= (int)$gig['delivery_days'] ?> jours</p>
        <?php if ((int)$gig['is_express'] === 1): ?><p class="text-xs text-orange-700 font-semibold mt-1">Service express</p><?php endif; ?>
      </div>
    </div>

    <div class="mt-5 grid grid-cols-1 lg:grid-cols-2 gap-4">
      <img src="/<?= e($gig['main_image']) ?>" alt="Vignette gig" class="w-full h-72 object-cover rounded-2xl border border-gray-200">
      <div class="grid grid-cols-2 gap-2">
        <?php foreach ($gig['gallery_items'] as $img): ?>
          <img src="/<?= e($img) ?>" alt="Galerie" class="w-full h-36 object-cover rounded-xl border border-gray-200">
        <?php endforeach; ?>
        <?php if (empty($gig['gallery_items'])): ?>
          <div class="col-span-2 rounded-xl border border-dashed border-gray-300 h-36 flex items-center justify-center text-sm text-gray-500">Pas de galerie supplémentaire</div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="card p-6 sm:p-8">
    <h2 class="text-xl font-bold text-gray-900">Description</h2>
    <div class="mt-3 text-gray-700 leading-relaxed whitespace-pre-line"><?= e($gig['description']) ?></div>
  </section>

  <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <article class="card p-6">
      <h3 class="text-lg font-bold text-gray-900">Extras</h3>
      <?php if (empty($gig['extras_items'])): ?>
        <p class="mt-2 text-sm text-gray-600">Aucun extra défini.</p>
      <?php endif; ?>
      <div class="mt-3 space-y-2">
        <?php foreach ($gig['extras_items'] as $extra): ?>
          <div class="rounded-xl border border-gray-200 p-3">
            <div class="flex items-start justify-between gap-2">
              <p class="font-semibold text-gray-900"><?= e($extra['name'] ?? '') ?></p>
              <span class="text-orange-700 font-semibold"><?= format_price((float)($extra['price'] ?? 0)) ?></span>
            </div>
            <p class="text-sm text-gray-600 mt-1"><?= e($extra['desc'] ?? '') ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </article>

    <article class="card p-6">
      <h3 class="text-lg font-bold text-gray-900">FAQ</h3>
      <?php if (empty($gig['faq_items'])): ?>
        <p class="mt-2 text-sm text-gray-600">Aucune FAQ.</p>
      <?php endif; ?>
      <div class="mt-3 space-y-3">
        <?php foreach ($gig['faq_items'] as $item): ?>
          <div class="rounded-xl border border-gray-200 p-3">
            <p class="font-semibold text-gray-900"><?= e($item['q'] ?? '') ?></p>
            <p class="text-sm text-gray-600 mt-1"><?= e($item['r'] ?? '') ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </article>
  </section>

  <section class="card p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <p class="text-sm text-gray-600">Paiements bientôt disponibles</p>
      <p class="text-xs text-gray-500">TODO: Wave / Orange Money / PayPal</p>
    </div>
    <button class="btn-primary">Commander (bientôt)</button>
  </section>
</div>
