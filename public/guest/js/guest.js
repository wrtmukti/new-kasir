/* =========================================================
   Guest Ordering UI — Kasir POS
   Toast + utilitas guest (standalone, gak butuh nexora).
   ========================================================= */

(function () {
  // ——— Toast ———
  function ensureToastStack() {
    let stack = document.querySelector('.guest-toast-stack');
    if (!stack) {
      stack = document.createElement('div');
      stack.className = 'guest-toast-stack';
      document.body.appendChild(stack);
    }
    return stack;
  }

  function guestToast(message, type) {
    type = type || 'default';
    const stack = ensureToastStack();
    const toast = document.createElement('div');
    toast.className = 'guest-toast guest-toast-' + type;

    const iconMap = {
      success: 'bi-check-circle-fill',
      danger: 'bi-exclamation-circle-fill',
      default: 'bi-info-circle-fill',
    };
    const titleMap = {
      success: 'Berhasil',
      danger: 'Error',
      default: 'Informasi',
    };

    toast.innerHTML =
      '<i class="bi ' + (iconMap[type] || iconMap.default) + '"></i>' +
      '<div><strong>' + (titleMap[type] || titleMap.default) + '</strong>' + message + '</div>';

    stack.appendChild(toast);
    setTimeout(function () {
      toast.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(-8px)';
      setTimeout(function () { toast.remove(); }, 250);
    }, 3200);
  }

  window.NexoraGuestToast = guestToast;
})();
