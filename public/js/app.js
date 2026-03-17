document.addEventListener('alpine:init', () => {
  // Alpine prêt pour dropdowns/modals/toggles futurs
});

// Scroll Reveal
document.addEventListener('DOMContentLoaded', () => {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fade-in-up');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
});

/**
 * Toggle la sauvegarde d'un gig.
 * @param {HTMLElement} btn        - Le bouton cliqué
 * @param {number}      gigId      - L'ID du gig
 * @param {boolean}     removeCard - Si true, retire la carte de la page au désauvegarder (page /sauvegardes)
 */
async function toggleSave(btn, gigId, removeCard = false) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

  let res;
  try {
    res = await fetch('/gig/' + gigId + '/save', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'csrf=' + encodeURIComponent(csrf),
    });
  } catch (_) {
    return; // Erreur réseau silencieuse
  }

  if (res.status === 401) {
    const data = await res.json();
    if (data.redirect) window.location.href = data.redirect;
    return;
  }
  if (!res.ok) return;

  const data = await res.json();
  if (!data.ok) return;

  btn.dataset.saved = data.saved ? '1' : '0';

  const svgFull = '<svg class="w-5 h-5 text-orange-500 fill-current" viewBox="0 0 24 24"><path d="M5 3h14a1 1 0 0 1 1 1v17l-8-4-8 4V4a1 1 0 0 1 1-1z"/></svg>';
  const svgEmpty = '<svg class="w-5 h-5 text-gray-400 stroke-current fill-none" stroke-width="2" viewBox="0 0 24 24"><path d="M5 3h14a1 1 0 0 1 1 1v17l-8-4-8 4V4a1 1 0 0 1 1-1z"/></svg>';
  const svgSmFull = '<svg class="w-4 h-4 text-orange-500 fill-current" viewBox="0 0 24 24"><path d="M5 3h14a1 1 0 0 1 1 1v17l-8-4-8 4V4a1 1 0 0 1 1-1z"/></svg>';
  const svgSmEmpty = '<svg class="w-4 h-4 text-gray-400 stroke-current fill-none" stroke-width="2" viewBox="0 0 24 24"><path d="M5 3h14a1 1 0 0 1 1 1v17l-8-4-8 4V4a1 1 0 0 1 1-1z"/></svg>';

  const isSmall = btn.querySelector('.w-4');
  btn.innerHTML = data.saved
    ? (isSmall ? svgSmFull : svgFull)
    : (isSmall ? svgSmEmpty : svgEmpty);

  // Sur la page /sauvegardes : retirer la carte si désauvegardé
  if (removeCard && !data.saved) {
    const card = document.getElementById('saved-card-' + gigId);
    if (card) {
      card.style.transition = 'opacity 0.3s';
      card.style.opacity = '0';
      setTimeout(() => card.remove(), 310);
    }
  }
}
