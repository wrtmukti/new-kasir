@extends('admin.layouts.app')

@section('title', 'Pusat Laporan POS')

@php $activeMenu = 'reports-dashboard' @endphp

@section('content')
<!-- Header Page & Filter Tanggal -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
  <div>
    <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary);">
      <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Pusat Laporan & Keuangan POS
    </h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.order.index') }}" class="text-muted-c text-decoration-none">Beranda</a>
      <i class="bi bi-chevron-right text-muted-c" style="font-size:0.6rem;"></i>
      <span style="color: var(--text-primary); font-weight: 600;">Laporan</span>
    </div>
  </div>

  <form action="{{ route('admin.reports.dashboard') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
    <div class="input-group input-group-sm" style="width: auto;">
      <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);">
        <i class="bi bi-calendar3"></i>
      </span>
      <input type="date" name="start_date" class="form-control form-control-sm form-control-modern" value="{{ $startDate }}" style="width:135px;">
    </div>
    <span class="text-muted-c" style="font-size: 0.85rem;">s.d</span>
    <div class="input-group input-group-sm" style="width: auto;">
      <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);">
        <i class="bi bi-calendar3"></i>
      </span>
      <input type="date" name="end_date" class="form-control form-control-sm form-control-modern" value="{{ $endDate }}" style="width:135px;">
    </div>
    <button type="submit" class="btn btn-primary-grad btn-sm px-3 rounded-3 fw-semibold">
      <i class="bi bi-filter me-1"></i>Filter
    </button>
  </form>
</div>

<!-- 4 Clean Bento KPI Cards -->
<div class="row g-3 mb-4">
  {{-- Card 1: Gross Sales --}}
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100 p-4 rounded-4 border-0 shadow-sm kpi-card-hover" 
         style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="text-muted-c fw-semibold" style="font-size: 0.76rem; letter-spacing: 0.5px;">TOTAL OMZET KASIR</span>
        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" 
             style="width: 40px; height: 40px; background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
          <i class="bi bi-cash-stack fs-5"></i>
        </div>
      </div>
      <h3 class="fw-bold mb-2" style="color: var(--text-primary); font-size: 1.6rem; letter-spacing: -0.5px;">
        Rp {{ number_format($grossSales, 0, ',', '.') }}
      </h3>
      <div class="d-flex align-items-center justify-content-between pt-1">
        <span class="text-muted-c" style="font-size: 0.76rem;">Penjualan Kotor (Gross)</span>
        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">Bruto POS</span>
      </div>
    </div>
  </div>

  {{-- Card 2: Net Revenue --}}
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100 p-4 rounded-4 border-0 shadow-sm kpi-card-hover" 
         style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="text-muted-c fw-semibold" style="font-size: 0.76rem; letter-spacing: 0.5px;">NET REVENUE</span>
        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" 
             style="width: 40px; height: 40px; background: rgba(16, 185, 129, 0.12); color: #10b981;">
          <i class="bi bi-graph-up-arrow fs-5"></i>
        </div>
      </div>
      <h3 class="fw-bold mb-2" style="color: var(--text-primary); font-size: 1.6rem; letter-spacing: -0.5px;">
        Rp {{ number_format($netSales, 0, ',', '.') }}
      </h3>
      <div class="d-flex align-items-center justify-content-between pt-1">
        <span class="text-muted-c" style="font-size: 0.76rem;">Pendapatan Bersih</span>
        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">Tanpa Pajak</span>
      </div>
    </div>
  </div>

  {{-- Card 3: Tax & Service Charge --}}
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100 p-4 rounded-4 border-0 shadow-sm kpi-card-hover" 
         style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="text-muted-c fw-semibold" style="font-size: 0.76rem; letter-spacing: 0.5px;">PAJAK & SERVICE</span>
        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" 
             style="width: 40px; height: 40px; background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
          <i class="bi bi-bank fs-5"></i>
        </div>
      </div>
      <h3 class="fw-bold mb-2" style="color: var(--text-primary); font-size: 1.6rem; letter-spacing: -0.5px;">
        Rp {{ number_format($totalTax + $totalService, 0, ',', '.') }}
      </h3>
      <div class="d-flex align-items-center justify-content-between pt-1">
        <span class="text-muted-c" style="font-size: 0.76rem;">PB1: Rp {{ number_format($totalTax, 0, ',', '.') }}</span>
        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">PB1 + Service</span>
      </div>
    </div>
  </div>

  {{-- Card 4: Waste Cost Loss --}}
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100 p-4 rounded-4 border-0 shadow-sm kpi-card-hover" 
         style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="text-muted-c fw-semibold" style="font-size: 0.76rem; letter-spacing: 0.5px;">KERUGIAN WASTE</span>
        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" 
             style="width: 40px; height: 40px; background: rgba(239, 68, 68, 0.12); color: #ef4444;">
          <i class="bi bi-trash3-fill fs-5"></i>
        </div>
      </div>
      <h3 class="fw-bold mb-2" style="color: var(--text-primary); font-size: 1.6rem; letter-spacing: -0.5px;">
        Rp {{ number_format($wasteLoss, 0, ',', '.') }}
      </h3>
      <div class="d-flex align-items-center justify-content-between pt-1">
        <span class="text-muted-c" style="font-size: 0.76rem;">Bahan Rusak / Kadaluarsa</span>
        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">Waste Loss</span>
      </div>
    </div>
  </div>
</div>

<!-- 7 Navigasi Kartu Laporan Dedicated (Clickable) -->
<h5 class="fw-bold mb-3" style="color: var(--text-primary);">
  <i class="bi bi-folder-symlink me-2 text-primary"></i>Pilih Detail Laporan Dedicated
</h5>

<div class="row g-3 mb-4">
  <!-- Card 1: Penjualan -->
  <div class="col-md-4">
    <a href="{{ route('admin.reports.sales', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-decoration-none">
      <div class="card h-100 p-4 rounded-4 border-0 shadow-sm card-report-hover" 
           style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3 flex-shrink-0" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
            <i class="bi bi-credit-card-2-front fs-3"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Laporan Penjualan & Bayar</h6>
            <small class="text-muted-c">Rekap Omzet & Breakdown Pembayaran (Cash, QRIS, EDC)</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 2: Performa Menu -->
  <div class="col-md-4">
    <a href="{{ route('admin.reports.products', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-decoration-none">
      <div class="card h-100 p-4 rounded-4 border-0 shadow-sm card-report-hover" 
           style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3 flex-shrink-0" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
            <i class="bi bi-cup-hot fs-3"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Laporan Performa Menu</h6>
            <small class="text-muted-c">Ranking Top Seller, Slow Moving & Qty Terjual</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 3: Arus Kas -->
  <div class="col-md-4">
    <a href="{{ route('admin.reports.cashflow', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-decoration-none">
      <div class="card h-100 p-4 rounded-4 border-0 shadow-sm card-report-hover" 
           style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3 flex-shrink-0" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
            <i class="bi bi-cash-stack fs-3"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Laporan Arus Kas (Cash Flow)</h6>
            <small class="text-muted-c">Uang Masuk (POS) vs Uang Keluar (PO & Operational)</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 4: Pajak & Service -->
  <div class="col-md-4">
    <a href="{{ route('admin.reports.tax-service', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-decoration-none">
      <div class="card h-100 p-4 rounded-4 border-0 shadow-sm card-report-hover" 
           style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3 flex-shrink-0" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
            <i class="bi bi-bank fs-3"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Laporan Pajak (PB1) & Service</h6>
            <small class="text-muted-c">Setoran PB1 Pemda & Pool Service Charge Karyawan</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 5: Inventory & Waste -->
  <div class="col-md-4">
    <a href="{{ route('admin.reports.inventory', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-decoration-none">
      <div class="card h-100 p-4 rounded-4 border-0 shadow-sm card-report-hover" 
           style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3 flex-shrink-0" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
            <i class="bi bi-box-seam fs-3"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Laporan Stok & Waste Log</h6>
            <small class="text-muted-c">Aset Gudang Bahan Mentah & Belanja PO Supplier</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 6: Audit Shift Closing -->
  <div class="col-md-4">
    <a href="{{ route('admin.reports.shifts', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-decoration-none">
      <div class="card h-100 p-4 rounded-4 border-0 shadow-sm card-report-hover" 
           style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3 flex-shrink-0" style="background: rgba(6, 182, 212, 0.12); color: #06b6d4;">
            <i class="bi bi-shield-lock fs-3"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Audit Shift Closing Kasir</h6>
            <small class="text-muted-c">Histori Cut-Off, Modal Awal, & Selisih Tekor/Lebih Kas</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 7: HPP & Laba Rugi -->
  <div class="col-md-4">
    <a href="{{ route('admin.keuangan.hpp-report.index') }}" class="text-decoration-none">
      <div class="card h-100 p-4 rounded-4 border-0 shadow-sm card-report-hover" 
           style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3 flex-shrink-0" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
            <i class="bi bi-graph-up-arrow fs-3"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Laporan HPP & Laba Rugi</h6>
            <small class="text-muted-c">Kalkulasi COGS Modal Menu, Gaji/Overhead, & Net Profit</small>
          </div>
        </div>
      </div>
    </a>
  </div>
</div>
@endsection

@push('styles')
<style>
  .kpi-card-hover {
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s ease, border-color 0.2s ease;
  }
  .kpi-card-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
  }
  .card-report-hover {
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s ease, border-color 0.2s ease;
  }
  .card-report-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    border-color: #3b82f6 !important;
  }
</style>
@endpush
