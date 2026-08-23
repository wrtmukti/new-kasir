@extends('admin.layouts.app')

@section('title', 'Laporan Pemasukan, Pengeluaran & Arus Kas')

@php $activeMenu = 'reports-cashflow' @endphp

@section('content')
<!-- Header Page & Action Buttons -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 no-print">
  <div>
    <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary);">💵 Laporan Pemasukan, Pengeluaran & Arus Kas (Cash Flow)</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.reports.dashboard') }}">Dashboard Laporan</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Arus Kas</span>
    </div>
  </div>

  <div class="d-flex flex-wrap align-items-center gap-2">
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm px-3">
      <i class="bi bi-printer me-1"></i>Cetak Laporan
    </button>
    <a href="{{ route('admin.reports.cashflow.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success btn-sm px-3">
      <i class="bi bi-file-earmark-excel me-1"></i>Export Excel / CSV
    </a>
  </div>
</div>

<!-- Form Filter Tanggal (No Print) -->
<div class="card p-3 mb-4 no-print" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 14px;">
  <form action="{{ route('admin.reports.cashflow') }}" method="GET" class="row g-3 align-items-center">
    <div class="col-md-6">
      <label class="text-muted small mb-1 fw-semibold">Rentang Tanggal:</label>
      <div class="d-flex align-items-center gap-2">
        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" style="background: var(--bg-surface); border-color: var(--border-subtle); color: var(--text-primary);">
        <span class="text-muted">s.d</span>
        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" style="background: var(--bg-surface); border-color: var(--border-subtle); color: var(--text-primary);">
      </div>
    </div>

    <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
      <button type="submit" class="btn btn-primary-grad btn-sm px-4">
        <i class="bi bi-filter me-1"></i>Terapkan Filter
      </button>
      <a href="{{ route('admin.reports.cashflow') }}" class="btn btn-soft-secondary btn-sm px-3">Reset</a>
    </div>
  </form>
</div>

<!-- Title Print Header -->
<div class="print-only mb-4 text-center">
  <h2 class="fw-bold">LAPORAN ARUS KAS (CASH FLOW)</h2>
  <p class="text-muted">Periode: {{ date('d/m/Y', strtotime($startDate)) }} s.d {{ date('d/m/Y', strtotime($endDate)) }} | Dicetak: {{ date('d F Y, H:i') }} WIB</p>
  <hr>
</div>

<!-- KPI Summary Cards Theme Adapted -->
<div class="row g-4 mb-4">
  <!-- Pemasukan -->
  <div class="col-md-4">
    <div class="card p-4 h-100" style="background: var(--bg-surface); border: 1.5px solid #10b981; border-radius: 16px;">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="badge bg-soft-success text-success fs-6 px-3 py-2">INFLOW (UANG MASUK)</span>
        <i class="bi bi-arrow-down-left-circle fs-2 text-success"></i>
      </div>
      <h6 class="text-muted mb-1" style="color: var(--text-muted);">Total Omzet Penjualan POS</h6>
      <h3 class="fw-bold text-success mb-0">Rp {{ number_format($totalCashIn, 0, ',', '.') }}</h3>
    </div>
  </div>

  <!-- Pengeluaran -->
  <div class="col-md-4">
    <div class="card p-4 h-100" style="background: var(--bg-surface); border: 1.5px solid #ef4444; border-radius: 16px;">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="badge bg-soft-danger text-danger fs-6 px-3 py-2">OUTFLOW (PENGELUARAN)</span>
        <i class="bi bi-arrow-up-right-circle fs-2 text-danger"></i>
      </div>
      <h6 class="text-muted mb-1" style="color: var(--text-muted);">PO Belanja Supplier + Waste Log</h6>
      <h3 class="fw-bold text-danger mb-0">Rp {{ number_format($totalPoOut + $totalWasteOut, 0, ',', '.') }}</h3>
      <small class="text-muted mt-2 d-block" style="color: var(--text-secondary);">PO: Rp {{ number_format($totalPoOut, 0, ',', '.') }} | Waste: Rp {{ number_format($totalWasteOut, 0, ',', '.') }}</small>
    </div>
  </div>

  <!-- Net Cash Flow -->
  <div class="col-md-4">
    <div class="card p-4 h-100" style="background: var(--bg-surface); border: 1.5px solid #3b82f6; border-radius: 16px;">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="badge bg-soft-primary text-primary fs-6 px-3 py-2">NET CASH FLOW</span>
        <i class="bi bi-wallet2 fs-2 text-primary"></i>
      </div>
      <h6 class="text-muted mb-1" style="color: var(--text-muted);">Surplus / (Defisit) Bersih Kas</h6>
      <h3 class="fw-bold text-primary mb-0">Rp {{ number_format($netCashFlow, 0, ',', '.') }}</h3>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .print-only { display: none !important; }
  @media print {
    .no-print, .sidebar, .topbar, .sidebar-backdrop { display: none !important; }
    .print-only { display: block !important; }
    .main-col, .app-shell, body { margin: 0 !important; padding: 0 !important; background: #ffffff !important; color: #000000 !important; }
    .card { border: 1px solid #ccc !important; background: #ffffff !important; box-shadow: none !important; }
  }
</style>
@endpush
