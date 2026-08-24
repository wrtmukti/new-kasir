<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Masuk ke Sistem POS — Nexora Cashier</title>

  {{-- Fonts & Icons --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('nexora-assets/css/main.css') }}">

  <style>
    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--bg-base);
      font-family: 'Inter', sans-serif;
      position: relative;
      overflow-x: hidden;
    }
    .auth-bg-blob-1 {
      position: absolute;
      top: -10%;
      left: -10%;
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(59, 130, 246, 0.18) 0%, rgba(0,0,0,0) 70%);
      pointer-events: none;
      z-index: 0;
    }
    .auth-bg-blob-2 {
      position: absolute;
      bottom: -10%;
      right: -10%;
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(0,0,0,0) 70%);
      pointer-events: none;
      z-index: 0;
    }
    .auth-card {
      width: 100%;
      max-width: 440px;
      background: var(--bg-elevated);
      border: 1px solid var(--border-subtle);
      border-radius: 1.25rem;
      padding: 2.25rem;
      box-shadow: 0 20px 40px -15px rgba(0,0,0,0.3);
      position: relative;
      z-index: 1;
      backdrop-filter: blur(16px);
    }
    .brand-logo-wrap {
      width: 48px;
      height: 48px;
      background: linear-gradient(135deg, #3b82f6, #10b981);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 1.5rem;
      font-weight: 700;
      box-shadow: 0 8px 16px -4px rgba(59, 130, 246, 0.4);
    }
    .loading-shimmer {
      position: relative;
      overflow: hidden;
    }
    .loading-shimmer::after {
      content: "";
      position: absolute;
      top: 0; right: 0; bottom: 0; left: 0;
      transform: translateX(-100%);
      background-image: linear-gradient(90deg, rgba(255,255,255,0) 0, rgba(255,255,255,0.08) 20%, rgba(255,255,255,0.15) 60%, rgba(255,255,255,0));
      animation: shimmer 1.5s infinite;
    }
    @keyframes shimmer {
      100% { transform: translateX(100%); }
    }
  </style>
</head>
<body>

  {{-- Background Glow --}}
  <div class="auth-bg-blob-1"></div>
  <div class="auth-bg-blob-2"></div>

  {{-- Theme Toggle Top Right --}}
  <div class="position-absolute top-0 end-0 p-3" style="z-index: 10;">
    <button class="btn btn-sm btn-outline-soft rounded-pill px-3 py-1.5" id="themeToggleBtn" aria-label="Ganti Tema">
      <i class="bi bi-sun me-1"></i> <span id="themeText">Tema</span>
    </button>
  </div>

  <div class="auth-card">
    <div class="d-flex align-items-center gap-3 mb-4">
      <div class="brand-logo-wrap">
        <i class="bi bi-cart-check-fill"></i>
      </div>
      <div>
        <h5 class="fw-bold mb-0" style="color:var(--text-primary);">Nexora POS</h5>
        <small class="text-muted-c">Point of Sales & Cashier System</small>
      </div>
    </div>

    <h4 class="fw-bold mb-1" style="color:var(--text-primary); font-size:1.35rem;">Masuk ke Panel Kasir</h4>
    <p class="text-muted-c mb-4" style="font-size:0.85rem;">Silakan masukkan akun staff atau owner untuk mulai bertransaksi.</p>

    {{-- Error Banner --}}
    <div id="alertBox" class="alert alert-danger d-none py-2.5 px-3 rounded-3 mb-3" style="font-size:0.82rem;" role="alert">
      <i class="bi bi-exclamation-circle-fill me-1.5"></i> <span id="alertMessage"></span>
    </div>

    @if(session('success'))
      <div class="alert alert-success py-2.5 px-3 rounded-3 mb-3" style="font-size:0.82rem;">
        <i class="bi bi-check-circle-fill me-1.5"></i> {{ session('success') }}
      </div>
    @endif

    <form id="loginForm" action="{{ route('login.post') }}" method="POST">
      @csrf

      <div class="mb-3 input-skeleton">
        <label for="email" class="form-label-modern fw-semibold" style="font-size:0.82rem;">Email Akun <span class="text-danger">*</span></label>
        <div class="input-group">
          <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);">
            <i class="bi bi-envelope"></i>
          </span>
          <input type="email" name="email" id="email" class="form-control form-control-modern" value="{{ old('email', 'admin@gmail.com') }}" placeholder="nama@resto.com" required autocomplete="email" autofocus>
        </div>
        <span class="text-danger d-block mt-1 field-error" id="error-email" style="font-size:0.78rem;">
          {{ (isset($errors) && $errors->has('email')) ? $errors->first('email') : '' }}
        </span>
      </div>

      <div class="mb-3 input-skeleton">
        <div class="d-flex align-items-center justify-content-between mb-1">
          <label for="password" class="form-label-modern fw-semibold mb-0" style="font-size:0.82rem;">Kata Sandi <span class="text-danger">*</span></label>
        </div>
        <div class="input-group">
          <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);">
            <i class="bi bi-lock"></i>
          </span>
          <input type="password" name="password" id="password" class="form-control form-control-modern" value="password" placeholder="••••••••" required autocomplete="current-password">
          <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" style="border-color: var(--border-subtle); background: var(--bg-elevated-2); color: var(--text-secondary);">
            <i class="bi bi-eye" id="togglePasswordIcon"></i>
          </button>
        </div>
        <span class="text-danger d-block mt-1 field-error" id="error-password" style="font-size:0.78rem;">
          {{ (isset($errors) && $errors->has('password')) ? $errors->first('password') : '' }}
        </span>
      </div>

      <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="form-check form-check-inline mb-0">
          <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked>
          <label class="form-check-label text-secondary-c" for="rememberMe" style="font-size:0.82rem;">
            Ingat saya di perangkat ini
          </label>
        </div>
      </div>

      <button type="submit" class="btn btn-primary-grad w-100 py-2.5 fw-semibold rounded-3 btn-loading shadow-sm" id="btnSubmit">
        <i class="bi bi-box-arrow-in-right me-1.5"></i>Masuk Sekarang
      </button>
    </form>

    <div class="mt-4 pt-3 border-top text-center" style="border-color: var(--border-subtle) !important;">
      <small class="text-muted-c" style="font-size:0.78rem;">
        Login untuk Platform Super Admin? <a href="{{ route('sys_admin.login') }}" class="text-primary fw-semibold text-decoration-none">Masuk di sini</a>
      </small>
    </div>
  </div>

  {{-- Toast Container for NexoraToast --}}
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="liveToast" class="toast align-items-center text-white border-0 rounded-3 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body" id="toastMessage" style="font-size:0.85rem;"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Theme Switcher Logic
    const htmlEl = document.documentElement;
    const themeBtn = document.getElementById('themeToggleBtn');
    const savedTheme = localStorage.getItem('nexora_theme') || 'dark';
    htmlEl.setAttribute('data-theme', savedTheme);

    themeBtn.addEventListener('click', () => {
      const current = htmlEl.getAttribute('data-theme');
      const next = current === 'dark' ? 'light' : 'dark';
      htmlEl.setAttribute('data-theme', next);
      localStorage.setItem('nexora_theme', next);
    });

    // Password Toggle
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePasswordIcon');

    toggleBtn.addEventListener('click', () => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      toggleIcon.className = type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
    });

    // Toast Alert Helper
    function NexoraToast(message, type = 'success') {
      const toastEl = document.getElementById('liveToast');
      const msgEl = document.getElementById('toastMessage');
      msgEl.innerHTML = (type === 'success' ? '<i class="bi bi-check-circle-fill me-1.5"></i> ' : '<i class="bi bi-exclamation-triangle-fill me-1.5"></i> ') + message;
      toastEl.className = 'toast align-items-center text-white border-0 rounded-3 shadow-lg ' + (type === 'success' ? 'bg-success' : 'bg-danger');
      const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
      toast.show();
    }

    // AJAX Form Handling with 400ms feedback latency
    const loginForm = document.getElementById('loginForm');
    const btnSubmit = document.getElementById('btnSubmit');
    const alertBox = document.getElementById('alertBox');
    const alertMessage = document.getElementById('alertMessage');

    loginForm.addEventListener('submit', function(e) {
      e.preventDefault();

      // Reset state
      alertBox.classList.add('d-none');
      document.querySelectorAll('.field-error').forEach(el => el.textContent = '');

      btnSubmit.disabled = true;
      btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memverifikasi Kredensial...';
      document.querySelectorAll('.input-skeleton').forEach(el => el.classList.add('loading-shimmer'));

      const formData = new FormData(loginForm);

      setTimeout(() => {
        fetch(loginForm.action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          },
          body: formData
        })
        .then(async response => {
          const data = await response.json();
          btnSubmit.disabled = false;
          btnSubmit.innerHTML = '<i class="bi bi-box-arrow-in-right me-1.5"></i>Masuk Sekarang';
          document.querySelectorAll('.input-skeleton').forEach(el => el.classList.remove('loading-shimmer'));

          if (response.ok && data.success) {
            NexoraToast(data.message, 'success');
            btnSubmit.innerHTML = '<i class="bi bi-check2-circle me-1.5"></i>Mengalihkan...';
            btnSubmit.disabled = true;
            setTimeout(() => {
              window.location.href = data.redirect_url;
            }, 600);
          } else {
            if (response.status === 422 && data.errors) {
              for (const [key, msgs] of Object.entries(data.errors)) {
                const errEl = document.getElementById(`error-${key}`);
                if (errEl) errEl.textContent = msgs[0];
              }
            } else {
              alertMessage.textContent = data.message || 'Login gagal. Silakan periksa kembali email dan kata sandi Anda.';
              alertBox.classList.remove('d-none');
              NexoraToast(data.message || 'Login gagal.', 'error');
            }
          }
        })
        .catch(err => {
          btnSubmit.disabled = false;
          btnSubmit.innerHTML = '<i class="bi bi-box-arrow-in-right me-1.5"></i>Masuk Sekarang';
          document.querySelectorAll('.input-skeleton').forEach(el => el.classList.remove('loading-shimmer'));
          alertMessage.textContent = 'Koneksi terputus. Silakan coba beberapa saat lagi.';
          alertBox.classList.remove('d-none');
        });
      }, 400); // 400ms latency feedback requirement
    });
  </script>
</body>
</html>
