<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Articles</h1>
        <a href="/admin/posts/creer" class="btn-primary">Créer un article</a>
    </div>

    <div class="card bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Titre</th>
                        <th class="px-6 py-4">Auteur</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm border-t border-gray-100">
                    <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">Aucun article trouvé.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900"><?= e($post['title']) ?></div>
                                <div class="text-xs text-gray-500">/<?= e($post['slug']) ?></div>
                            </td>
                            <td class="px-6 py-4 text-gray-600"><?= e($post['author_name']) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $post['status'] === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' ?>">
                                    <?= $post['status'] === 'published' ? 'Publié' : 'Brouillon' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500"><?= date('d/m/Y', strtotime($post['created_at'])) ?></td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="/admin/posts/<?= (int)$post['id'] ?>/modifier" class="text-orange-600 hover:text-orange-700 font-semibold">Modifier</a>
                                <form method="post" action="/admin/posts/<?= (int)$post['id'] ?>/supprimer" class="inline" onsubmit="return confirm('Supprimer cet article ?')">
                                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                    <button class="text-red-600 hover:text-red-700 font-semibold">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
