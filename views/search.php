<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col lg:flex-row gap-8">
      <!-- Sidebar Filters -->
      <aside class="w-full lg:w-72 space-y-6">
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
              <h2 class="text-lg font-bold text-gray-900 mb-4">Filtres</h2>
              
              <form action="/recherche" method="GET" class="space-y-6">
                  <div>
                      <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Recherche</label>
                      <input type="text" name="q" value="<?= e($q ?? '') ?>" placeholder="Ex: Logo, Site web..." class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 outline-none transition-all">
                  </div>

                  <div>
                      <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Catégorie</label>
                      <select name="category" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 outline-none transition-all">
                          <option value="">Toutes les catégories</option>
                          <?php foreach (Flight::get('db')->query('SELECT name FROM categories WHERE parent_id IS NULL ORDER BY name ASC')->fetchAll() as $cat): ?>
                              <option value="<?= e($cat['name']) ?>" <?= ($category ?? '') === $cat['name'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                          <?php endforeach; ?>
                      </select>
                  </div>

                  <button type="submit" class="w-full bg-slate-900 text-white py-3 rounded-xl font-bold text-sm hover:bg-slate-800 transition-all shadow-lg shadow-slate-200">
                      Appliquer les filtres
                  </button>
              </form>
          </div>
      </aside>

      <!-- Results Grid -->
      <main class="flex-1">
          <div class="flex items-center justify-between mb-8">
              <h1 class="text-2xl font-black text-gray-900">
                  <?php if ($q || $category): ?>
                      Résultats pour "<?= e($q ?: ($category ?: 'Tout')) ?>"
                  <?php else: ?>
                      Tous les services
                  <?php endif; ?>
              </h1>
              <span class="text-sm font-bold text-gray-400"><?= count($gigs) ?> services trouvés</span>
          </div>

          <?php if (empty($gigs)): ?>
              <div class="bg-white rounded-3xl p-12 text-center border-2 border-dashed border-gray-100">
                  <div class="w-20 h-20 bg-orange-50 text-orange-500 rounded-full flex items-center justify-center mx-auto mb-6">
                      <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                  </div>
                  <h2 class="text-xl font-bold text-gray-900 mb-2">Aucun résultat trouvé</h2>
                  <p class="text-gray-500 mb-8">Essayez d'élargir vos critères de recherche ou explorez une nouveau catégorie.</p>
                  <a href="/recherche" class="inline-flex items-center text-primary-600 font-bold">Réinitialiser la recherche</a>
              </div>
          <?php else: ?>
              <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                  <?php foreach ($gigs as $gig): ?>
                      <article class="group bg-white rounded-[2rem] overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-1">
                          <a href="/gig/<?= e($gig['slug']) ?>" class="block h-48 overflow-hidden relative">
                              <!-- Visibility Badges -->
                              <div class="absolute top-4 left-4 z-20 flex flex-wrap gap-2">
                                <?php if ($gig['is_sponsored']): ?>
                                  <span class="px-2.5 py-1 bg-orange-600 text-white text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg flex items-center gap-1.5">
                                    <i class="fas fa-rocket"></i> Sponsorisé
                                  </span>
                                <?php endif; ?>
                                <?php if (($gig['subscription_status'] ?? 'free') === 'premium'): ?>
                                  <span class="px-2.5 py-1 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg flex items-center gap-1.5 border border-white/10">
                                    <i class="fas fa-crown text-orange-400"></i> Premium
                                  </span>
                                <?php endif; ?>
                              </div>
                              <img src="<?= e($gig['main_image']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                          </a>
                          <div class="p-6">
                              <div class="flex items-center gap-2 mb-3">
                                  <span class="text-[9px] font-black uppercase text-orange-500 tracking-[0.2em] px-2 py-0.5 bg-orange-50 rounded-md"><?= e($gig['category']) ?></span>
                              </div>
                              <h3 class="font-bold text-gray-900 line-clamp-2 hover:text-orange-600 transition-colors h-12">
                                  <a href="/gig/<?= e($gig['slug']) ?>"><?= e($gig['title']) ?></a>
                              </h3>
                              <div class="mt-6 flex items-center justify-between border-t border-gray-50 pt-4">
                                  <span class="flex items-center gap-2 text-xs font-bold text-gray-400">
                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/></svg>
                                      <?= e($gig['country'] ?? 'Afrik') ?>
                                  </span>
                                  <span class="text-lg font-black text-gray-900"><?= format_price((float)$gig['price_base']) ?></span>
                              </div>
                          </div>
                      </article>
                  <?php endforeach; ?>
              </div>
          <?php endif; ?>
      </main>
    </div>
</div>
