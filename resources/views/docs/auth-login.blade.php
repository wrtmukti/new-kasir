@extends('docs.layouts.auth')

@section('title', 'Masuk')

@section('content')
  <div class="auth-wrap">
    <div class="auth-card">
      <div class="auth-brand">
        <div class="brand-mark">N</div>
        <span class="brand-name" style="font-size:1.25rem;">Nexora</span>
      </div>

      <h1 style="font-size:1.3rem; text-align:center; margin-bottom:0.35rem;">Selamat Datang Kembali</h1>
      <p class="text-muted-c text-center mb-4" style="font-size:0.85rem;">Masuk untuk mengakses dashboard Anda</p>

      <form onsubmit="event.preventDefault(); window.location.href='{{ url('docs/index') }}';">
        <div class="mb-3">
          <label class="form-label-modern">Email</label>
          <input type="email" class="form-control-modern" placeholder="nama@perusahaan.com" required>
        </div>
        <div class="mb-3">
          <label class="form-label-modern">Kata Sandi</label>
          <input type="password" class="form-control-modern" placeholder="••••••••" required>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="form-check-modern">
            <input type="checkbox" id="rememberMe">
            <label for="rememberMe" class="text-secondary-c" style="font-size:0.82rem;">Ingat saya</label>
          </div>
          <a href="#" class="text-secondary-c" style="font-size:0.82rem;">Lupa sandi?</a>
        </div>
        <button type="submit" class="btn btn-primary-grad w-100 mb-3">Masuk</button>
      </form>

      <div class="d-flex align-items-center gap-2 mb-3">
        <hr class="divider-line flex-grow-1 m-0">
        <span class="text-muted-c" style="font-size:0.75rem;">atau</span>
        <hr class="divider-line flex-grow-1 m-0">
      </div>

      <button class="btn btn-outline-soft w-100 mb-2"><i class="bi bi-google me-2"></i>Masuk dengan Google</button>
      <button class="btn btn-outline-soft w-100"><i class="bi bi-github me-2"></i>Masuk dengan GitHub</button>

      <p class="text-center text-muted-c mt-4 mb-0" style="font-size:0.83rem;">
        Belum punya akun? <a href="#" style="color: var(--accent-cyan); font-weight:600;">Daftar di sini</a>
      </p>
    </div>
  </div>
@endsection
