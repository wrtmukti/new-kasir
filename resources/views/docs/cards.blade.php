@extends('docs.layouts.app')

@section('title', 'Cards')

@php $activeMenu = 'cards' @endphp

@section('content')
      <div class="page-header">
        <div>
          <h1>Cards</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><a href="{{ url('docs/components') }}">UI Components</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Cards</span></div>
        </div>
      </div>

      <!-- Standard Cards -->
      <div class="row g-3 mb-3">
        <div class="col-md-4">
          <div class="card card-glow h-100">
            <div class="card-inner card-body">
              <div class="stat-icon mb-2" style="width:40px;height:40px;border-radius:var(--radius-sm);background:var(--info-bg);color:var(--accent-cyan);display:flex;align-items:center;justify-content:center;"><i class="bi bi-cpu-fill"></i></div>
              <h6 class="mb-1">Card Glow</h6>
              <p class="text-muted-c mb-0" style="font-size:0.82rem;">Border gradient menyala saat hover — signature visual Nexora.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card card-hover-lift h-100">
            <div class="card-body">
              <div class="stat-icon mb-2" style="width:40px;height:40px;border-radius:var(--radius-sm);background:var(--success-bg);color:var(--success);display:flex;align-items:center;justify-content:center;"><i class="bi bi-arrow-up"></i></div>
              <h6 class="mb-1">Card Hover Lift</h6>
              <p class="text-muted-c mb-0" style="font-size:0.82rem;">Bergeser naik saat hover dengan shadow efek kedalaman.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="stat-icon mb-2" style="width:40px;height:40px;border-radius:var(--radius-sm);background:var(--bg-elevated-2);color:var(--text-muted);display:flex;align-items:center;justify-content:center;"><i class="bi bi-file-text"></i></div>
              <h6 class="mb-1">Card Standar</h6>
              <p class="text-muted-c mb-0" style="font-size:0.82rem;">Kartu dasar tanpa efek hover — untuk konten statis.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3">
          <div class="card card-glow h-100">
            <div class="card-inner card-body stat-card">
              <div class="stat-icon" style="background:var(--info-bg);color:var(--accent-cyan);"><i class="bi bi-cpu-fill"></i></div>
              <div class="stat-value"><span data-counter="48230">0</span></div>
              <div class="stat-label">API Requests</div>
              <span class="stat-trend up mt-2"><i class="bi bi-arrow-up-short"></i>12.4%</span>
            </div>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card card-glow h-100">
            <div class="card-inner card-body stat-card">
              <div class="stat-icon" style="background:var(--success-bg);color:var(--success);"><i class="bi bi-people-fill"></i></div>
              <div class="stat-value"><span data-counter="3127">0</span></div>
              <div class="stat-label">Active Users</div>
              <span class="stat-trend up mt-2"><i class="bi bi-arrow-up-short"></i>4.8%</span>
            </div>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card card-glow h-100">
            <div class="card-inner card-body stat-card">
              <div class="stat-icon" style="background:rgba(99,102,241,0.12);color:var(--accent-1);"><i class="bi bi-currency-dollar"></i></div>
              <div class="stat-value">$<span data-counter="98450">0</span></div>
              <div class="stat-label">Revenue</div>
              <span class="stat-trend up mt-2"><i class="bi bi-arrow-up-short"></i>8.1%</span>
            </div>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card card-glow h-100">
            <div class="card-inner card-body stat-card">
              <div class="stat-icon" style="background:var(--danger-bg);color:var(--danger);"><i class="bi bi-exclamation-triangle-fill"></i></div>
              <div class="stat-value"><span data-counter="0.8">0</span>%</div>
              <div class="stat-label">Error Rate</div>
              <span class="stat-trend down mt-2"><i class="bi bi-arrow-down-short"></i>0.3%</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Card Horizontal -->
      <div class="card card-horizontal mb-3">
        <div class="card-img-side"><i class="bi bi-image-fill"></i></div>
        <div class="card-body">
          <h6 class="mb-1">Card Horizontal</h6>
          <p class="text-muted-c mb-2" style="font-size:0.82rem;">Layout horizontal — gambar di samping, konten di kanan.</p>
          <div class="d-flex gap-2">
            <span class="badge badge-primary">Desain</span>
            <span class="badge badge-info">UI/UX</span>
          </div>
        </div>
      </div>

      <!-- Metric Cards -->
      <div class="card mb-3">
        <div class="card-header-flex"><h6>Metric Cards</h6></div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="metric-card">
                <div class="metric-icon" style="background:var(--info-bg);color:var(--accent-cyan);"><i class="bi bi-cpu-fill"></i></div>
                <div class="metric-info">
                  <div class="metric-value">48,230</div>
                  <div class="metric-label">API Requests</div>
                </div>
                <span class="metric-change up">+12.4%</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="metric-card">
                <div class="metric-icon" style="background:var(--success-bg);color:var(--success);"><i class="bi bi-people-fill"></i></div>
                <div class="metric-info">
                  <div class="metric-value">3,127</div>
                  <div class="metric-label">Active Users</div>
                </div>
                <span class="metric-change up">+4.8%</span>
              </div>
            </div>
          </div>
        </div>
      </div>
@endsection
