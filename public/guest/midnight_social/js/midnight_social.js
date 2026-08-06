/* ==========================================
   Midnight Social Guest Theme Script
   ========================================== */

function NexoraToast(message, type = 'default') {
  let container = document.querySelector('.ms-toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'ms-toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `ms-toast ms-toast-${type}`;

  const icon = type === 'success' ? 'check_circle' : (type === 'danger' ? 'error' : 'info');
  toast.innerHTML = `<span class="material-symbols-outlined">${icon}</span> <span>${message}</span>`;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(-10px) scale(0.95)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Global helper for rupiah formatting
function formatRupiah(amount) {
  return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
}
