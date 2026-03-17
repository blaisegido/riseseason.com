<?php $r = $_GET['role'] ?? 'freelancer'; ?>
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center p-4 sm:p-8">
  <div class="w-full max-w-6xl bg-white rounded-[2rem] sm:rounded-[3rem] shadow-2xl overflow-hidden flex flex-col md:flex-row">
    
    <!-- Left Side: Register Form -->
    <div class="w-full md:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center">
      <div class="w-full max-w-md mx-auto">
        <div class="mb-10 text-center md:text-left">
          <h1 class="text-3xl sm:text-4xl font-display font-black text-gray-900 tracking-tight">Inscription 🚀</h1>
          <p class="mt-3 text-base text-gray-500">Créez votre compte RiseSeason en moins de 2 minutes.</p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100 flex items-center gap-3 animate-fade-in">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm font-medium text-red-700"><?= e($error) ?></p>
          </div>
        <?php endif; ?>

        <form method="post" class="space-y-5">
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="space-y-1.5">
              <label class="block text-sm font-bold text-gray-700 pl-1">Prénom</label>
              <input name="first_name" required placeholder="John" class="w-full px-4 py-3.5 bg-gray-50 border border-primary-100 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all font-medium">
            </div>
            <div class="space-y-1.5">
              <label class="block text-sm font-bold text-gray-700 pl-1">Nom</label>
              <input name="last_name" required placeholder="Doe" class="w-full px-4 py-3.5 bg-gray-50 border border-primary-100 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all font-medium">
            </div>
          </div>

          <div class="space-y-1.5">
            <label class="block text-sm font-bold text-gray-700 pl-1">Adresse email</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
              </div>
              <input name="email" type="email" required placeholder="vous@exemple.com" class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-primary-100 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all font-medium">
            </div>
          </div>

          <div class="space-y-1.5">
            <label class="block text-sm font-bold text-gray-700 pl-1">Mot de passe (8+ caractères)</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              </div>
              <input name="password" id="reg-password" type="password" required minlength="8" placeholder="••••••••" class="w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-primary-100 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all font-medium">
              <button type="button" onclick="const p = document.getElementById('reg-password'); const isPwd = p.type === 'password'; p.type = isPwd ? 'text' : 'password'; this.innerHTML = isPwd ? '&lt;svg class=\'h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'&gt;&lt;path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18\'/&gt;&lt;/svg&gt;' : '&lt;svg class=\'h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'&gt;&lt;path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M15 12a3 3 0 11-6 0 3 3 0 016 0z\'/&gt;&lt;path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z\'/&gt;&lt;/svg&gt;';" class="absolute inset-y-0 right-0 pr-4 flex items-center z-10">
                <svg class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="space-y-1.5">
              <label class="block text-sm font-bold text-gray-700 pl-1">Pays</label>
              <select name="country" required class="w-full px-4 py-3.5 bg-gray-50 border border-primary-100 rounded-2xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all font-medium appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2224%22%20height%3D%2224%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpath%20d%3D%22M6%209l6%206%206-6%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.5rem_1.5rem] bg-[right_0.5rem_center] bg-no-repeat pr-10">
                <option>Sénégal</option><option>Côte d'Ivoire</option><option>Cameroun</option><option>Burkina Faso</option>
                <option>Mali</option><option>Bénin</option><option>Togo</option><option>Guinée</option>
                <option>France</option><option>Belgique</option><option>Canada</option>
              </select>
            </div>
            <div class="space-y-1.5">
              <label class="block text-sm font-bold text-gray-700 pl-1">Je suis un...</label>
              <select name="role" class="w-full px-4 py-3.5 bg-gray-50 border border-primary-100 rounded-2xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all font-medium appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2224%22%20height%3D%2224%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpath%20d%3D%22M6%209l6%206%206-6%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.5rem_1.5rem] bg-[right_0.5rem_center] bg-no-repeat pr-10">
                <option value="freelancer" <?= $r === 'freelancer' ? 'selected' : '' ?>>Freelancer</option>
                <option value="employeur" <?= $r === 'employeur' ? 'selected' : '' ?>>Employeur</option>
                <option value="contributeur">Contributeur</option>
              </select>
            </div>
          </div>

          <button class="w-full bg-primary-600 text-white rounded-2xl py-4 font-bold text-lg hover:bg-primary-500 hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-primary-600/20 mt-6">
            Créer mon compte
          </button>
        </form>

        <p class="mt-10 text-center text-sm text-gray-600 font-medium">
          Déjà un compte ? 
          <a href="/connexion" class="text-primary-600 font-bold hover:text-primary-500 hover:underline transition-colors">Se connecter</a>
        </p>
      </div>
    </div>

    <!-- Right Side: Branding/Visuals -->
    <div class="hidden md:block w-1/2 relative p-12 bg-slate-900 overflow-hidden">
      <!-- Decorative Gradients -->
      <div class="absolute top-0 right-0 w-[40rem] h-[40rem] bg-orange-500/30 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/3"></div>
      <div class="absolute bottom-0 left-0 w-[30rem] h-[30rem] bg-amber-400/20 rounded-full blur-[80px] translate-y-1/3 -translate-x-1/3"></div>
      
      <div class="relative h-full flex flex-col justify-between z-10">
        <div class="flex justify-end">
          <a href="/" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-white font-bold hover:bg-white/10 transition-colors">
            Retour au site
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </a>
        </div>
        
        <div class="mb-12">
          <div class="inline-block p-4 bg-white/10 rounded-3xl backdrop-blur-xl border border-white/20 mb-8 shadow-2xl">
            <svg class="w-12 h-12 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          </div>
          <h2 class="text-4xl lg:text-5xl font-display font-black text-white leading-[1.1] mb-6">
            Propulsez <br/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-300 to-orange-400">votre carrière.</span>
          </h2>
          <ul class="text-lg text-gray-400 space-y-4 max-w-sm">
             <li class="flex items-center gap-3">
                 <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                 Accès à des milliers de clients
             </li>
             <li class="flex items-center gap-3">
                 <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                 Paiements garantis et sécurisés
             </li>
             <li class="flex items-center gap-3">
                 <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                 Support client dédié
             </li>
          </ul>
        </div>
      </div>
    </div>

  </div>
</div>
