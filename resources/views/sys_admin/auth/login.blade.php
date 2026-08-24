@extends('sys_admin.layouts.auth')

@section('title', 'Masuk System Admin Control Panel')

@section('content')
<div class="auth-wrap d-flex align-items-center justify-content-center min-vh-100 p-3" style="background: radial-gradient(circle at 50% 20%, rgba(59, 130, 246, 0.12), transparent 70%), var(--bg-base);">
  <div class="auth-card p-4 p-md-5 rounded-4 shadow-lg" style="max-width: 440px; width: 100%; background: var(--bg-surface); border: 1px solid var(--border-subtle);">
    
    {{-- Brand Logo --}}
    <div class="auth-brand d-flex align-items-center justify-content-center gap-2 mb-3">
      <div class="brand-mark rounded-3 d-flex align-items-center justify-content-center fw-bold fs-4 text-white shadow-sm" style="width:44px; height:44px; background: linear-gradient(135deg, #3b82f6, #6366f1);">
        <i class="bi bi-shield-lock-fill"></i>
      </div>
      <span class="brand-name fw-bold" style="font-size:1.35rem; color:var(--text-primary); letter-spacing:-0.02em;">
        Nexora <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill ms-1" style="font-size:0.65rem; vertical-align:middle;">SYSTEM ADMIN</span>
      </span>
    </div>

    <h1 class="text-center fw-bold mb-1" style="font-size: 1.35rem; color: var(--text-primary);">
      Platform Control Panel
    </h1>
    <p class="text-muted-c text-center mb-4" style="font-size: 0.85rem;">
      Masuk untuk mengelola client, database, dan infrastruktur SaaS.
    </p>

    {{-- Flash Warning / Error --}}
    @if(session('error'))
      <div class="alert alert-danger py-2 px-3 rounded-3 d-flex align-items-center gap-2 mb-3" style="font-size:0.85rem;">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>{{ session('error') }}</div>
      </div>
    @endif
    @if(session('warning'))
      <div class="alert alert-warning py-2 px-3 rounded-3 d-flex align-items-center gap-2 mb-3" style="font-size:0.85rem;">
        <i class="bi bi-info-circle-fill"></i>
        <div>{{ session('warning') }}</div>
      </div>
    @endif
    @if(session('success'))
      <div class="alert alert-success py-2 px-3 rounded-3 d-flex align-items-center gap-2 mb-3" style="font-size:0.85rem;">
        <i class="bi bi-check-circle-fill"></i>
        <div>{{ session('success') }}</div>
      </div>
    @endif

    <form id="formLoginSysAdmin" action="{{ route('sys_admin.login.post') }}" method="POST">
      @csrf

      <div class="mb-3 input-skeleton">
        <label for="login" class="form-label-modern fw-semibold" style="font-size:0.85rem; color:var(--text-secondary);">
          Email atau Username <span class="text-danger">*</span>
        </label>
        <div class="input-group">
          <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);">
            <i class="bi bi-person-fill"></i>
          </span>
          <input type="text" name="login" id="login"
                 class="form-control form-control-modern @error('login') is-invalid @enderror"
                 placeholder="admin@system.local atau superadmin"
                 value="{{ old('login') }}" required autofocus>
        </div>
        <span class="text-danger d-block mt-1 field-error" id="error-login" style="font-size:0.8rem;">
          @error('login') {{ $message }} @enderror
        </span>
      </div>

      <div class="mb-3 input-skeleton">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <label for="password" class="form-label-modern fw-semibold mb-0" style="font-size:0.85rem; color:var(--text-secondary);">
            Kata Sandi <span class="text-danger">*</span>
          </label>
        </div>
        <div class="input-group">
          <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);">
            <i class="bi bi-key-fill"></i>
          </span>
          <input type="password" name="password" id="password"
                 class="form-control form-control-modern @error('password') is-invalid @enderror"
                 placeholder="••••••••" required>
        </div>
        <span class="text-danger d-block mt-1 field-error" id="error-password" style="font-size:0.8rem;">
          @error('password') {{ $message }} @enderror
        </span>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" style="cursor: pointer;">
          <label class="form-check-label" for="remember" style="font-size:0.82rem; color:var(--text-secondary); cursor: pointer;">
            Ingat sesi masuk
          </label>
        </div>
        <span class="text-muted-c" style="font-size:0.78rem;">v1.0 Multi-Client</span>
      </div>

      <button type="submit" class="btn btn-primary-grad w-100 py-2.5 rounded-3 fw-bold btn-loading shadow-sm" id="btnSubmitLogin">
        <i class="bi bi-box-arrow-in-right me-1.5"></i>Masuk ke Platform
      </button>
    </form>

    <div class="mt-4 pt-3 border-top text-center text-muted-c" style="border-color: var(--border-subtle) !important; font-size:0.75rem;">
      <i class="bi bi-shield-check me-1 text-success"></i>Koneksi terenkripsi & Terisolasi oleh Nexora Guard.
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('formLoginSysAdmin');
  const btn = document.getElementById('btnSubmitLogin');

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Clear previous errors
    document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // Loading Shimmer
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memverifikasi...';
    form.querySelectorAll('.input-skeleton').forEach(el => el.classList.add('loading-shimmer'));

    const formData = new FormData(form);

    setTimeout(() => {
      fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: formData
      })
      .then(async response => {
        const data = await response.json();
        if (response.ok && data.success) {
          if (typeof NexoraToast === 'function') {
            NexoraToast(data.message, 'success');
          }
          setTimeout(() => {
            window.location.href = data.redirect_url;
          }, 300);
        } else {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-box-arrow-in-right me-1.5"></i>Masuk ke Platform';
          form.querySelectorAll('.input-skeleton').forEach(el => el.classList.remove('loading-shimmer'));

          if (response.status === 422 && data.errors) {
            for (const [key, msgs] of Object.entries(data.errors)) {
              const errSpan = document.getElementById(`error-${key}`);
              const inputEl = document.getElementById(key);
              if (errSpan) errSpan.textContent = msgs[0];
              if (inputEl) inputEl.classList.add('is-invalid');
            }
          } else {
            const errSpan = document.getElementById('error-login');
            if (errSpan) errSpan.textContent = data.message || 'Terjadi kesalahan saat masuk.';
          }
        }
      })
      .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-box-arrow-in-right me-1.5"></i>Masuk ke Platform';
        form.querySelectorAll('.input-skeleton').forEach(el => el.classList.remove('loading-shimmer'));
        const errSpan = document.getElementById('error-login');
        if (errSpan) errSpan.textContent = 'Koneksi ke server bermasalah.';
      });
    }, 400); // 400ms feedback latency as per rule_ai.md
  });
});
</script>
@endpush
