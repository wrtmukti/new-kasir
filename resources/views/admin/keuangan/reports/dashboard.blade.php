@extends('admin.layouts.app')

@section('title', 'Pusat Dashboard Laporan POS')

@php $activeMenu = 'reports-dashboard' @endphp

@section('content')
<!-- Header Page & Filter Tanggal -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
  <div>
    <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary);">📊 Pusat Dashboard Laporan</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Keuangan</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Dashboard Laporan</span>
    </div>
  </div>

  <form action="{{ route('admin.reports.dashboard') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" style="width:140px;">
    <span class="text-muted">s.d</span>
    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" style="width:140px;">
    <button type="submit" class="btn btn-primary-grad btn-sm px-3">
      <i class="bi bi-filter me-1"></i>Filter
    </button>
  </form>
</div>

<!-- 4 KPI Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card h-100 p-3" style="background: var(--bg-surface); border: 1.5px solid #3b82f6; border-radius: 14px; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.1);">
      <div class="text-muted-c text-uppercase fw-semibold mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">TOTAL OMZET KASIR (GROSS)</div>
      <h3 class="fw-bold mb-0" style="color: #3b82f6;">Rp {{ number_format($grossSales, 0, ',', '.') }}</h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card h-100 p-3" style="background: var(--bg-surface); border: 1.5px solid #10b981; border-radius: 14px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.1);">
      <div class="text-muted-c text-uppercase fw-semibold mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">NET REVENUE (TANPA PAJAK)</div>
      <h3 class="fw-bold mb-0" style="color: #10b981;">Rp {{ number_format($netSales, 0, ',', '.') }}</h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card h-100 p-3" style="background: var(--bg-surface); border: 1.5px solid #8b5cf6; border-radius: 14px; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.1);">
      <div class="text-muted-c text-uppercase fw-semibold mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">PAJAK PB1 + SERVICE CHARGE</div>
      <h3 class="fw-bold mb-0" style="color: #8b5cf6;">Rp {{ number_format($totalTax + $totalService, 0, ',', '.') }}</h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card h-100 p-3" style="background: var(--bg-surface); border: 1.5px solid #ef4444; border-radius: 14px; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.1);">
      <div class="text-muted-c text-uppercase fw-semibold mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">KERUGIAN BAHAN RUSAK (WASTE)</div>
      <h3 class="fw-bold mb-0" style="color: #ef4444;">Rp {{ number_format($wasteLoss, 0, ',', '.') }}</h3>
    </div>
  </div>
</div>

<!-- 6 Navigasi Kartu Laporan Dedicated (Clickable) -->
<h5 class="fw-bold mb-3" style="color: var(--text-primary);"><i class="bi bi-folder-symlink me-2" style="color:#3b82f6;"></i>Pilih Detail Laporan Dedicated</h5>

<div class="row g-3">
  <!-- Card 1: Penjualan -->
  <div class="col-md-4">
    <a href="{{ route('admin.reports.sales', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-decoration-none">
      <div class="card h-100 p-4 card-report-hover" style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 14px; transition: all 0.2s ease;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
            <i class="bi bi-credit-card-2-front fs-3"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Laporan Penjualan & Bayar</h6>
            <small class="text-muted">Rekap Omzet & Breakdown Pembayaran (Cash, QRIS, EDC)</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 2: Performa Menu -->
  <div class="col-md-4">
    <a href="{{ route('admin.reports.products', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-decoration-none">
      <div class="card h-100 p-4 card-report-hover" style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 14px; transition: all 0.2s ease;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
            <i class="bi bi-cup-hot fs-3"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Laporan Performa Menu</h6>
            <small class="text-muted">Ranking Top Seller, Slow Moving & Qty Terjual</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 3: Arus Kas -->
  <div class="col-md-4">
    <a href="{{ route('admin.reports.cashflow', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-decoration-none">
      <div class="card h-100 p-4 card-report-hover" style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 14px; transition: all 0.2s ease;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
            <i class="bi bi-cash-stack fs-3"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Laporan Arus Kas (Cash Flow)</h6>
            <small class="text-muted">Uang Masuk (POS) vs Uang Keluar (PO & Operational)</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 4: Pajak & Service -->
  <div class="col-md-4">
    <a href="{{ route('admin.reports.tax-service', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-decoration-none">
      <div class="card h-100 p-4 card-report-hover" style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 14px; transition: all 0.2s ease;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
            <i class="bi bi-bank fs-3"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Laporan Pajak (PB1) & Service</h6>
            <small class="text-muted">Setoran PB1 Pemda & Pool Service Charge Karyawan</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 5: Inventory & Waste -->
  <div class="col-md-4">
    <a href="{{ route('admin.reports.inventory', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-decoration-none">
      <div class="card h-100 p-4 card-report-hover" style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 14px; transition: all 0.2s ease;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
            <i class="bi bi-box-seam fs-3"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Laporan Stok & Waste Log</h6>
            <small class="text-muted">Aset Gudang Bahan Mentah & Belanja PO Supplier</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 6: Audit Shift Closing -->
  <div class="col-md-4">
    <a href="{{ route('admin.reports.shifts', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-decoration-none">
      <div class="card h-100 p-4 card-report-hover" style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 14px; transition: all 0.2s ease;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3" style="background: rgba(6, 182, 212, 0.12); color: #06b6d4;">
            <i class="bi bi-shield-lock fs-3"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Audit Shift Closing Kasir</h6>
            <small class="text-muted">Histori Cut-Off, Modal Awal, & Selisih Tekor/Lebih Kas</small>
          </div>
        </div>
      </div>
    </a>
  </div>
</div>
@endsection

@push('styles')
<style>
  .card-report-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    border-color: #3b82f6 !important;
  }
</style>
@endpush
