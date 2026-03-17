<section class="mt-0">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Mes sauvegardes</h1>
    <a href="/recherche" class="text-sm font-semibold text-orange-600 hover:text-orange-700">Explorer les gigs</a>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php if (empty($gigs)): ?>
      <div class="card p-8 md:col-span-2 lg:col-span-3 text-center text-gray-500">
        <svg class="mx-auto mb-3 w-10 h-10 text-gray-300 stroke-current fill-none" stroke-width="1.5" viewBox="0 0 24 24"><path d="M5 3h14a1 1 0 0 1 1 1v17l-8-4-8 4V4a1 1 0 0 1 1-1z"/></svg>
        <p class="font-medium">Aucune sauvegarde pour le moment.</p>
        <p class="mt-1 text-sm">Clique sur l'icône 🔖 sur n'importe quel gig pour le retrouver ici.</p>
      </div>
    <?php endif; ?>

    <?php foreach ($gigs as $gig): ?>
      <article class="card overflow-hidden flex flex-col relative" id="saved-card-<?= (int)$gig['id'] ?>">
        <?php if (!empty($gig['main_image'])): ?>
          <a href="/gig/<?= e($gig['slug']) ?>">
            <img src="<?= e($gig['main_image']) ?>" alt="<?= e($gig['title']) ?>" class="w-full h-44 object-cover">
          </a>
          <button
            type="button"
            onclick="toggleSave(this, <?= (int)$gig['id'] ?>, true)"
            data-saved="1"
            class="absolute top-2 right-2 rounded-full bg-white/80 backdrop-blur p-1.5 shadow hover:bg-white transition"
            title="Retirer des sauvegardes"
          >
            <svg class="w-5 h-5 text-orange-500 fill-current" viewBox="0 0 24 24"><path d="M5 3h14a1 1 0 0 1 1 1v17l-8-4-8 4V4a1 1 0 0 1 1-1z"/></svg>
          </button>
        <?php else: ?>
          <div class="w-full h-44 bg-gray-100 flex items-center justify-center text-gray-400 text-sm relative">
            Pas d'image
            <button
              type="button"
              onclick="toggleSave(this, <?= (int)$gig['id'] ?>, true)"
              data-saved="1"
              class="absolute top-2 right-2 rounded-full bg-white/80 p-1.5 shadow hover:bg-white transition"
              title="Retirer des sauvegardes"
            >
              <svg class="w-5 h-5 text-orange-500 fill-current" viewBox="0 0 24 24"><path d="M5 3h14a1 1 0 0 1 1 1v17l-8-4-8 4V4a1 1 0 0 1 1-1z"/></svg>
            </button>
          </div>
        <?php endif; ?>

        <!-- Freelancer info -->
        <div class="flex items-center gap-2 px-4 py-2 border-b border-gray-100 bg-white">
          <?php
            $avatar = !empty($gig['profile_photo'])
              ? e($gig['profile_photo'])
              : 'https://ui-avatars.com/api/?name=' . urlencode((string)$gig['username']) . '&background=f97316&color=fff&size=64&bold=true';
          ?>
          <img src="<?= $avatar ?>" alt="<?= e($gig['username']) ?>" class="w-7 h-7 rounded-full object-cover flex-shrink-0 border border-orange-200">
          <span class="text-xs font-semibold text-gray-700 truncate">@<?= e($gig['username']) ?></span>
        </div>

        <div class="px-5 pb-5 pt-1 flex flex-col flex-1">
          <h3 class="text-gray-900 line-clamp-3">
            <a class="hover:text-orange-700" href="/gig/<?= e($gig['slug']) ?>"><?= e($gig['title']) ?></a>
          </h3>
          <div class="mt-auto pt-3 flex flex-wrap items-center gap-2 text-xs">
            <span class="rounded-full bg-orange-50 px-2.5 py-1 font-semibold text-orange-700"><?= format_price((float)$gig['price_base']) ?></span>
            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-gray-700"><?= e($gig['category']) ?></span>
            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-gray-700"><?= e($gig['country']) ?></span>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
