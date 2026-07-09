@extends('docs.layouts.app')

@section('title', 'Analytics')

@php $activeMenu = 'analytics' @endphp

@section('content')
      <div class="page-header">
        <div>
          <h1>Analytics</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Analytics</span></div>
        </div>
        <select class="form-select-modern" style="width:auto;">
          <option>7 Hari Terakhir</option>
          <option>30 Hari Terakhir</option>
          <option>90 Hari Terakhir</option>
        </select>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3">
          <div class="card card-glow h-100"><div class="card-inner card-body stat-card">
            <div class="stat-icon" style="background:var(--info-bg); color:var(--accent-cyan);"><i class="bi bi-eye-fill"></i></div>
            <div class="stat-value"><span data-counter="182400">0</span></div>
            <div class="stat-label">Total Kunjungan</div>
          </div></div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card card-glow h-100"><div class="card-inner card-body stat-card">
            <div class="stat-icon" style="background:var(--success-bg); color:var(--success);"><i class="bi bi-clock-history"></i></div>
            <div class="stat-value"><span data-counter="4.2">0</span>m</div>
            <div class="stat-label">Rata-rata Durasi Sesi</div>
          </div></div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card card-glow h-100"><div class="card-inner card-body stat-card">
            <div class="stat-icon" style="background:rgba(99,102,241,0.12); color:var(--accent-1);"><i class="bi bi-arrow-repeat"></i></div>
            <div class="stat-value"><span data-counter="38.6">0</span>%</div>
            <div class="stat-label">Tingkat Konversi</div>
          </div></div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card card-glow h-100"><div class="card-inner card-body stat-card">
            <div class="stat-icon" style="background:var(--danger-bg); color:var(--danger);"><i class="bi bi-box-arrow-right"></i></div>
            <div class="stat-value"><span data-counter="22.1">0</span>%</div>
            <div class="stat-label">Bounce Rate</div>
          </div></div>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header-flex"><h6>Tren Permintaan API</h6></div>
            <div class="card-body" style="height:320px;"><canvas id="chartRevenue"></canvas></div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header-flex"><h6>Distribusi Model</h6></div>
            <div class="card-body" style="height:320px;"><canvas id="chartUsage"></canvas></div>
          </div>
        </div>
      </div>
@endsection
