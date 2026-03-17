<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center p-4 sm:p-8">
  <div class="w-full max-w-6xl bg-white rounded-[2rem] sm:rounded-[3rem] shadow-2xl overflow-hidden flex flex-col md:flex-row">
    
    <!-- Left Side: Login Form -->
    <div class="w-full md:w-1/2 p-8 sm:p-12 lg:p-16 xl:p-20 flex flex-col justify-center">
      <div class="w-full max-w-sm mx-auto">
        <div class="mb-10 text-center md:text-left">
          <h1 class="text-3xl sm:text-4xl font-display font-black text-gray-900 tracking-tight">Bon retour 👋</h1>
          <p class="mt-3 text-base text-gray-500">Connectez-vous pour accéder à votre espace RiseSeason.</p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100 flex items-center gap-3 animate-fade-in">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm font-medium text-red-700"><?= e($error) ?></p>
          </div>
        <?php endif; ?>

        <form method="post" class="space-y-5">
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
          
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
            <div class="flex items-center justify-between pl-1">
              <label class="block text-sm font-bold text-gray-700">Mot de passe</label>
            </div>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              </div>
              <input name="password" id="login-password" type="password" required placeholder="••••••••" class="w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-primary-100 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all font-medium">
              <button type="button" onclick="const p = document.getElementById('login-password'); const isPwd = p.type === 'password'; p.type = isPwd ? 'text' : 'password'; this.innerHTML = isPwd ? '&lt;svg class=\'h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'&gt;&lt;path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18\'/&gt;&lt;/svg&gt;' : '&lt;svg class=\'h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'&gt;&lt;path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M15 12a3 3 0 11-6 0 3 3 0 016 0z\'/&gt;&lt;path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z\'/&gt;&lt;/svg&gt;';" class="absolute inset-y-0 right-0 pr-4 flex items-center z-10">
                <svg class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              </button>
            </div>
          </div>

          <button class="w-full bg-primary-600 text-white rounded-2xl py-4 font-bold text-lg hover:bg-primary-500 hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-primary-600/20 mt-4">
            Se connecter
          </button>
        </form>

        <p class="mt-10 text-center text-sm text-gray-600 font-medium">
          Nouveau sur RiseSeason ? 
          <a href="/inscription" class="text-primary-600 font-bold hover:text-primary-500 hover:underline transition-colors">Créer un compte</a>
        </p>
      </div>
    </div>

    <!-- Right Side: Branding/Visuals -->
    <div class="hidden md:block w-1/2 relative p-12 bg-slate-900 overflow-hidden">
      <!-- Decorative Gradients -->
      <div class="absolute top-0 right-0 w-[40rem] h-[40rem] bg-primary-600/30 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/3"></div>
      <div class="absolute bottom-0 left-0 w-[30rem] h-[30rem] bg-orange-400/20 rounded-full blur-[80px] translate-y-1/3 -translate-x-1/3"></div>
      
      <div class="relative h-full flex flex-col justify-between z-10">
        <div>
          <a href="/" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-white font-bold hover:bg-white/10 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour au site
          </a>
        </div>
        
        <div class="mb-12">
          <div class="inline-block p-4 bg-white/10 rounded-3xl backdrop-blur-xl border border-white/20 mb-8 shadow-2xl">
            <svg class="w-12 h-12 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </div>
          <h2 class="text-4xl lg:text-5xl font-display font-black text-white leading-[1.1] mb-6">
            L'excellence <br/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-amber-300">à portée de main.</span>
          </h2>
          <p class="text-lg text-gray-400 leading-relaxed max-w-md">
            Connectez-vous pour commencer à collaborer avec les meilleurs talents ou proposer vos services sur la plateforme.
          </p>
          
          <div class="mt-12 flex items-center gap-4">
            <div class="flex -space-x-3">
              <img class="w-10 h-10 rounded-full border-2 border-slate-900 object-cover" src="https://ui-avatars.com/api/?name=Alice&background=FFEDD5&color=C2410C" alt="">
              <img class="w-10 h-10 rounded-full border-2 border-slate-900 object-cover" src="https://ui-avatars.com/api/?name=Bob&background=FFEDD5&color=C2410C" alt="">
              <img class="w-10 h-10 rounded-full border-2 border-slate-900 object-cover" src="https://ui-avatars.com/api/?name=Charlie&background=FFEDD5&color=C2410C" alt="">
            </div>
            <div class="text-sm font-medium text-gray-400">
              Rejoignez <span class="text-white font-bold">1.2k+</span> membres
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
