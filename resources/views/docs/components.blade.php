@extends('docs.layouts.app')

@section('title', 'UI Components')

@php $activeMenu = 'components' @endphp

@section('content')
      <div class="page-header">
        <div>
          <h1>UI Components</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>UI Components</span></div>
        </div>
      </div>

      <!-- Category navigation cards -->
      <div class="row g-3 mb-4">
        <div class="col-md-4 col-6">
          <a href="{{ url('docs/buttons') }}" class="card card-glow h-100 text-decoration-none">
            <div class="card-inner card-body text-center" style="padding:2rem 1rem;">
              <div class="stat-icon" style="margin:0 auto 0.75rem;background:var(--info-bg);color:var(--accent-cyan);"><i class="bi bi-mouse2-fill"></i></div>
              <h6>Buttons</h6>
              <p class="text-muted-c mb-0" style="font-size:0.78rem;">Warna, ukuran, state, grup</p>
            </div>
          </a>
        </div>
        <div class="col-md-4 col-6">
          <a href="{{ url('docs/cards') }}" class="card card-glow h-100 text-decoration-none">
            <div class="card-inner card-body text-center" style="padding:2rem 1rem;">
              <div class="stat-icon" style="margin:0 auto 0.75rem;background:var(--success-bg);color:var(--success);"><i class="bi bi-credit-card-2-front-fill"></i></div>
              <h6>Cards</h6>
              <p class="text-muted-c mb-0" style="font-size:0.78rem;">Glow, stat, horizontal, metric</p>
            </div>
          </a>
        </div>
        <div class="col-md-4 col-6">
          <a href="{{ url('docs/forms') }}" class="card card-glow h-100 text-decoration-none">
            <div class="card-inner card-body text-center" style="padding:2rem 1rem;">
              <div class="stat-icon" style="margin:0 auto 0.75rem;background:var(--warning-bg);color:var(--warning);"><i class="bi bi-ui-checks"></i></div>
              <h6>Forms</h6>
              <p class="text-muted-c mb-0" style="font-size:0.78rem;">Input, select, switch, radio</p>
            </div>
          </a>
        </div>
        <div class="col-md-4 col-6">
          <a href="{{ url('docs/tables') }}" class="card card-glow h-100 text-decoration-none">
            <div class="card-inner card-body text-center" style="padding:2rem 1rem;">
              <div class="stat-icon" style="margin:0 auto 0.75rem;background:var(--bg-elevated-2);color:var(--accent-1);"><i class="bi bi-table"></i></div>
              <h6>Tables</h6>
              <p class="text-muted-c mb-0" style="font-size:0.78rem;">Stripe, compact, DataTable</p>
            </div>
          </a>
        </div>
        <div class="col-md-4 col-6">
          <a href="{{ url('docs/charts') }}" class="card card-glow h-100 text-decoration-none">
            <div class="card-inner card-body text-center" style="padding:2rem 1rem;">
              <div class="stat-icon" style="margin:0 auto 0.75rem;background:var(--info-bg);color:var(--accent-cyan);"><i class="bi bi-bar-chart-fill"></i></div>
              <h6>Charts</h6>
              <p class="text-muted-c mb-0" style="font-size:0.78rem;">10 jenis chart Chart.js</p>
            </div>
          </a>
        </div>
        <div class="col-md-4 col-6">
          <a href="{{ url('docs/icons') }}" class="card card-glow h-100 text-decoration-none">
            <div class="card-inner card-body text-center" style="padding:2rem 1rem;">
              <div class="stat-icon" style="margin:0 auto 0.75rem;background:rgba(99,102,241,0.12);color:var(--accent-1);"><i class="bi bi-star-fill"></i></div>
              <h6>Icons</h6>
              <p class="text-muted-c mb-0" style="font-size:0.78rem;">Bootstrap Icons gallery</p>
            </div>
          </a>
        </div>
      </div>

      <!-- Component quick preview -->
      <div class="card mb-3">
        <div class="card-header-flex"><h6>Quick Preview — Pills &amp; Status</h6></div>
        <div class="card-body d-flex flex-wrap gap-2">
          <span class="pill pill-success"><i class="bi bi-check-circle"></i> Sukses</span>
          <span class="pill pill-danger"><i class="bi bi-x-circle"></i> Gagal</span>
          <span class="pill pill-warning"><i class="bi bi-clock"></i> Pending</span>
          <span class="pill pill-info"><i class="bi bi-info-circle"></i> Info</span>
          <span class="pill pill-neutral">Netral</span>
          <span class="chip-tag">#frontend</span>
          <span class="d-inline-flex align-items-center gap-2"><span class="status-dot live"></span> Live</span>
          <span class="d-inline-flex align-items-center gap-2"><span class="status-dot" style="background:var(--text-muted);"></span> Offline</span>
        </div>
      </div>

      <!-- More components quick preview -->
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-header-flex"><h6>Avatars</h6><a href="{{ url('docs/cards') }}" class="btn btn-ghost btn-sm">Lihat</a></div>
            <div class="card-body d-flex align-items-center gap-3 flex-wrap">
              <span class="avatar avatar-xs">RA</span>
              <span class="avatar avatar-sm">RA</span>
              <span class="avatar avatar-md">RA</span>
              <span class="avatar avatar-lg">RA</span>
              <div class="avatar-group">
                <span class="avatar avatar-sm" style="background:#6366F1;">AD</span>
                <span class="avatar avatar-sm" style="background:#34D399;">MS</span>
                <span class="avatar-more">+3</span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-header-flex"><h6>Alerts</h6><a href="{{ url('docs/buttons') }}" class="btn btn-ghost btn-sm">Lihat</a></div>
            <div class="card-body d-flex flex-column gap-2">
              <div class="alert alert-success mb-0" style="padding:0.5rem 0.75rem;"><i class="bi bi-check-circle-fill"></i><div class="alert-content">Data berhasil disimpan.</div></div>
              <div class="alert alert-danger mb-0" style="padding:0.5rem 0.75rem;"><i class="bi bi-exclamation-circle-fill"></i><div class="alert-content">Gagal memuat data.</div></div>
            </div>
          </div>
        </div>
      </div>
@endsection
