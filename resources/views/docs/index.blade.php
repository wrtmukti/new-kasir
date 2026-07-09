@extends('docs.layouts.app')

@section('title', 'Dashboard')

@php $activeMenu = 'dashboard' @endphp

@section('content')
      <div class="page-header">
        <div>
          <h1>Dashboard</h1>
          <div class="breadcrumb-trail">
            <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Overview</span>
          </div>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-soft"><i class="bi bi-download me-1"></i>Ekspor</button>
          <button class="btn btn-primary-grad"><i class="bi bi-plus-lg me-1"></i>Proyek Baru</button>
        </div>
      </div>

      <!-- STAT CARDS -->
      <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3">
          <div class="card card-glow h-100">
            <div class="card-inner card-body stat-card">
              <div class="stat-icon" style="background:var(--info-bg); color:var(--accent-cyan);"><i class="bi bi-cpu-fill"></i></div>
              <div class="stat-value"><span data-counter="48230">0</span></div>
              <div class="stat-label">Permintaan API Bulan Ini</div>
              <span class="stat-trend up mt-2"><i class="bi bi-arrow-up-short"></i>12.4%</span>
            </div>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card card-glow h-100">
            <div class="card-inner card-body stat-card">
              <div class="stat-icon" style="background:var(--success-bg); color:var(--success);"><i class="bi bi-people-fill"></i></div>
              <div class="stat-value"><span data-counter="3127">0</span></div>
              <div class="stat-label">Pengguna Aktif</div>
              <span class="stat-trend up mt-2"><i class="bi bi-arrow-up-short"></i>4.8%</span>
            </div>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card card-glow h-100">
            <div class="card-inner card-body stat-card">
              <div class="stat-icon" style="background:rgba(99,102,241,0.12); color:var(--accent-1);"><i class="bi bi-currency-dollar"></i></div>
              <div class="stat-value">$<span data-counter="98450">0</span></div>
              <div class="stat-label">Pendapatan Bulanan</div>
              <span class="stat-trend up mt-2"><i class="bi bi-arrow-up-short"></i>8.1%</span>
            </div>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card card-glow h-100">
            <div class="card-inner card-body stat-card">
              <div class="stat-icon" style="background:var(--danger-bg); color:var(--danger);"><i class="bi bi-exclamation-triangle-fill"></i></div>
              <div class="stat-value"><span data-counter="0.8">0</span>%</div>
              <div class="stat-label">Tingkat Error</div>
              <span class="stat-trend down mt-2"><i class="bi bi-arrow-down-short"></i>0.3%</span>
            </div>
          </div>
        </div>
      </div>

      <!-- CHARTS ROW -->
      <div class="row g-3 mb-3">
        <div class="col-lg-8">
          <div class="card h-100">
            <div class="card-header-flex">
              <div>
                <h6>Pendapatan &amp; Proyeksi</h6>
                <span class="text-muted-c" style="font-size:0.78rem;">Performa 12 bulan terakhir</span>
              </div>
              <div class="d-flex align-items-center gap-2">
                <span class="status-dot live"></span>
                <span class="text-muted-c" style="font-size:0.78rem;">Live</span>
              </div>
            </div>
            <div class="card-body" style="height:300px;">
              <canvas id="chartRevenue"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card h-100">
            <div class="card-header-flex">
              <h6>Penggunaan Model AI</h6>
            </div>
            <div class="card-body" style="height:300px;">
              <canvas id="chartUsage"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- TABLE + ACTIVITY ROW -->
      <div class="row g-3">
        <div class="col-lg-7">
          <div class="card">
            <div class="card-header-flex">
              <h6>Transaksi Terbaru</h6>
              <a href="{{ url('docs/tables') }}" class="btn btn-ghost btn-sm">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table-modern">
                  <thead>
                    <tr><th>Pengguna</th><th>Paket</th><th>Jumlah</th><th>Status</th><th>Tanggal</th></tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="cell-primary"><span class="avatar-sm">SA</span>Sinta Amalia</td>
                      <td>Pro Plan</td>
                      <td class="text-mono">$49.00</td>
                      <td><span class="pill pill-success"><i class="bi bi-check-circle"></i> Sukses</span></td>
                      <td class="text-muted-c">18 Jun 2026</td>
                    </tr>
                    <tr>
                      <td class="cell-primary"><span class="avatar-sm">BP</span>Budi Pratama</td>
                      <td>Team Plan</td>
                      <td class="text-mono">$129.00</td>
                      <td><span class="pill pill-warning"><i class="bi bi-clock"></i> Pending</span></td>
                      <td class="text-muted-c">17 Jun 2026</td>
                    </tr>
                    <tr>
                      <td class="cell-primary"><span class="avatar-sm">DK</span>Dewi Kartika</td>
                      <td>Starter</td>
                      <td class="text-mono">$19.00</td>
                      <td><span class="pill pill-danger"><i class="bi bi-x-circle"></i> Gagal</span></td>
                      <td class="text-muted-c">16 Jun 2026</td>
                    </tr>
                    <tr>
                      <td class="cell-primary"><span class="avatar-sm">RH</span>Rian Hidayat</td>
                      <td>Pro Plan</td>
                      <td class="text-mono">$49.00</td>
                      <td><span class="pill pill-success"><i class="bi bi-check-circle"></i> Sukses</span></td>
                      <td class="text-muted-c">15 Jun 2026</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card h-100">
            <div class="card-header-flex">
              <h6>Aktivitas Sistem</h6>
            </div>
            <div class="card-body" style="height:300px;">
              <canvas id="chartBar"></canvas>
            </div>
          </div>
        </div>
      </div>
@endsection
