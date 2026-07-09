@extends('docs.layouts.app')

@section('title', 'Pricing')

@php $activeMenu = 'pricing' @endphp

@section('content')
      <div class="page-header">
        <div>
          <h1>Paket Harga</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Pricing</span></div>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body text-center">
              <h5>Basic</h5>
              <div class="stat-value my-2">$0</div>
              <p class="text-muted-c" style="font-size:0.85rem;">Gratis, cocok untuk mencoba. Akses fitur dasar dan 1 proyek.</p>
              <ul class="list-unstyled mt-3 mb-3 text-start" style="font-size:0.85rem; color:var(--text-secondary);">
                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--success);"></i>1 Proyek</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--success);"></i>100 API Calls/hari</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--success);"></i>Community Support</li>
              </ul>
              <button class="btn btn-outline-soft w-100">Pilih Basic</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card card-glow h-100">
            <div class="card-inner card-body text-center">
              <span class="badge badge-primary badge-pill mb-2">POPULER</span>
              <h5>Pro</h5>
              <div class="stat-value my-2">$49</div>
              <p class="text-muted-c" style="font-size:0.85rem;">Untuk pengguna intensif, termasuk akses API penuh dan prioritas.</p>
              <ul class="list-unstyled mt-3 mb-3 text-start" style="font-size:0.85rem; color:var(--text-secondary);">
                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--success);"></i>10 Proyek</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--success);"></i>10.000 API Calls/hari</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--success);"></i>Priority Support</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--success);"></i>Analytics Lanjutan</li>
              </ul>
              <button class="btn btn-primary-grad w-100">Pilih Pro</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body text-center">
              <h5>Enterprise</h5>
              <div class="stat-value my-2">$129</div>
              <p class="text-muted-c" style="font-size:0.85rem;">Dukungan SLA &amp; tim khusus untuk kebutuhan perusahaan Anda.</p>
              <ul class="list-unstyled mt-3 mb-3 text-start" style="font-size:0.85rem; color:var(--text-secondary);">
                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--success);"></i>Unlimited Proyek</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--success);"></i>Unlimited API Calls</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--success);"></i>SLA 99.9%</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--success);"></i>Account Manager</li>
              </ul>
              <button class="btn btn-outline-soft w-100">Kontak Sales</button>
            </div>
          </div>
        </div>
      </div>
@endsection
