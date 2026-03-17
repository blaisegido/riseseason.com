<div class="max-w-5xl mx-auto">
    <form action="/admin/posts/sauvegarder" method="post" class="space-y-6">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= $post['id'] ?? '' ?>">

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900"><?= e($title) ?></h1>
            <div class="flex gap-3">
                <a href="/admin/posts" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content Area -->
            <div class="lg:col-span-2 space-y-4">
                <div class="card p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Titre de l'article</label>
                        <input type="text" name="title" value="<?= e($post['title'] ?? '') ?>" 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-lg font-bold" 
                               placeholder="Saisissez votre titre ici..." required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Contenu</label>
                        <textarea name="content" rows="20" 
                                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 font-mono text-sm" 
                                  placeholder="Écrivez votre article..." required><?= e($post['content'] ?? '') ?></textarea>
                        <p class="mt-2 text-xs text-gray-500 italic">Astuce : Utilisez du code HTML ou Markdown pour formater votre texte.</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar Settings -->
            <div class="space-y-6">
                <div class="card p-6 space-y-4">
                    <h2 class="font-bold text-gray-900 border-b pb-2">Réglages</h2>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Statut</label>
                        <select name="status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="draft" <?= ($post['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Brouillon</option>
                            <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publié</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Slug (URL)</label>
                        <input type="text" name="slug" value="<?= e($post['slug'] ?? '') ?>" 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" 
                               placeholder="mon-titre-article">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Extrait (Excerpt)</label>
                        <textarea name="excerpt" rows="4" 
                                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" 
                                  placeholder="Bref résumé de l'article..."><?= e($post['excerpt'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Image à la une (URL)</label>
                        <input type="text" name="featured_image" value="<?= e($post['featured_image'] ?? '') ?>" 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" 
                               placeholder="https://...">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
