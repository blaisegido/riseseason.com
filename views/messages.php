<div class="grid grid-cols-1 lg:grid-cols-3 gap-5" x-data="{showConvos:false}" x-init="$nextTick(() => { const box = document.getElementById('messages-box'); if (box) box.scrollTop = box.scrollHeight; })">
  <aside class="card p-4 lg:col-span-1">
    <div class="flex items-center justify-between">
      <h2 class="font-bold text-gray-900">Conversations</h2>
      <button type="button" class="text-xs font-semibold text-orange-600 lg:hidden" @click="showConvos=!showConvos" x-text="showConvos ? 'Masquer' : 'Afficher'"></button>
    </div>

    <div class="mt-3 space-y-2" :class="showConvos ? 'block' : 'hidden lg:block'">
      <?php if (empty($conversations)): ?>
        <p class="text-sm text-gray-600">Aucune conversation pour le moment.</p>
      <?php endif; ?>

      <?php foreach ($conversations as $c): ?>
        <?php $isActive = $partner && (int)$partner['id'] === (int)$c['id']; ?>
        <a class="convo-item <?= $isActive ? 'convo-item-active' : '' ?>" href="/messages?user=<?= (int)$c['id'] ?>">
          <span class="convo-avatar"><?= strtoupper(mb_substr((string)$c['first_name'], 0, 1)) ?></span>
          <span class="text-sm font-medium text-gray-800"><?= e($c['first_name'] . ' ' . $c['last_name']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </aside>

  <section class="lg:col-span-2 card p-4 sm:p-5">
    <?php if ($partner): ?>
      <div class="border-b border-gray-100 pb-3 mb-4">
        <h2 class="font-bold text-gray-900">Discussion avec <?= e($partner['first_name']) ?></h2>
        <p class="text-xs text-gray-500">Messagerie MVP</p>
      </div>

      <div id="messages-box" class="space-y-3 max-h-[28rem] overflow-y-auto pr-1">
        <?php foreach ($messages as $m): ?>
          <?php $mine = (int)$m['sender_id'] === (int)$me['id']; ?>
          <div class="flex <?= $mine ? 'justify-end' : 'justify-start' ?>">
            <article class="message-bubble <?= $mine ? 'message-me' : 'message-them' ?>">
              <p><?= e($m['content']) ?></p>
              <p class="mt-1 text-[10px] <?= $mine ? 'text-orange-800/70' : 'text-gray-500' ?>"><?= e(date('d/m H:i', strtotime((string)$m['created_at']))) ?></p>
            </article>
          </div>
        <?php endforeach; ?>
      </div>

      <form method="post" class="mt-4 flex flex-col sm:flex-row gap-2">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="receiver_id" value="<?= (int)$partner['id'] ?>">
        <input name="content" required class="input-ui flex-1" placeholder="Votre message...">
        <button class="btn-primary sm:w-auto">Envoyer</button>
      </form>
    <?php else: ?>
      <div class="h-52 rounded-2xl bg-gradient-to-br from-orange-50 to-gray-50 flex items-center justify-center text-center p-6">
        <p class="text-gray-600">Sélectionnez une conversation pour démarrer l'échange.</p>
      </div>
    <?php endif; ?>
  </section>
</div>
