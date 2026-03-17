<div class="space-y-6">
  <section class="card p-6">
    <div class="flex items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Modération des Gigs</h1>
        <p class="mt-1 text-sm text-gray-600">Tous les nouveaux services arrivent ici en statut pending.</p>
      </div>
      <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">En attente: <?= count($pendingGigs) ?></span>
    </div>
  </section>

  <section class="card p-6">
    <?php if (empty($pendingGigs)): ?>
      <p class="text-sm text-gray-600">Aucun gig en attente de validation.</p>
    <?php endif; ?>

    <div class="space-y-3">
      <?php foreach ($pendingGigs as $g): ?>
        <div class="admin-row cursor-pointer hover:bg-orange-50 transition" onclick="window.location.href='/admin/gigs/<?= (int)$g['id'] ?>/review'">
          <div>
            <p class="font-semibold text-gray-900 hover:text-orange-600">
              <?= e($g['title']) ?>
            </p>
            <p class="text-xs text-gray-500">@<?= e($g['username']) ?> · <?= e($g['email']) ?></p>
            <p class="text-xs text-gray-500 mt-1">
              <?= e($g['category'] ?: 'Catégorie non définie') ?> · <span class="inline-flex"><?= format_price((float)$g['price_base']) ?></span> · <?= (int)$g['delivery_days'] ?> j
            </p>
          </div>
          <div class="flex items-center gap-2 text-orange-600 font-semibold text-sm">
            Ouvrir →
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>
