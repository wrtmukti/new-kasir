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
      <span>Arus Kas (Plan B)</span>
    </div>
  </div>

  <div class="d-flex flex-wrap align-items-center gap-2">
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm px-3">
      <i class="bi bi-printer me-1"></i>Cetak Laporan
    </button>
    <a href="{{ route('admin.keuangan.cashflow-report.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success btn-sm px-3">
      <i class="bi bi-file-earmark-excel me-1"></i>Export Excel / CSV
    </a>
  </div>
</div>

<!-- Form Filter Tanggal (No Print) -->
<div class="card p-3 mb-4 no-print filter-panel">
  <form action="{{ route('admin.keuangan.cashflow-report.index') }}" method="GET" class="row g-3 align-items-center">
    <div class="col-md-6">
      <label class="text-muted-sub small mb-1 fw-semibold"><i class="bi bi-calendar-range me-1"></i>Rentang Tanggal Periode Kas:</label>
      <div class="d-flex align-items-center gap-2">
        <input type="date" name="start_date" class="form-control form-control-sm date-input" value="{{ $startDate }}">
        <span class="text-muted-sub fw-semibold">s.d</span>
        <input type="date" name="end_date" class="form-control form-control-sm date-input" value="{{ $endDate }}">
      </div>
    </div>

    <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
      <button type="submit" class="btn btn-primary-grad btn-sm px-4">
        <i class="bi bi-filter me-1"></i>Terapkan Filter
      </button>
      <a href="{{ route('admin.keuangan.cashflow-report.index') }}" class="btn btn-soft-secondary btn-sm px-3">Reset</a>
    </div>
  </form>
</div>

<!-- Title Print Header -->
<div class="print-only mb-4 text-center">
  <h2 class="fw-bold">LAPORAN ARUS KAS & BUKU KAS HARIAN (CASH FLOW)</h2>
  <p class="text-muted">Periode: {{ date('d/m/Y', strtotime($startDate)) }} s.d {{ date('d/m/Y', strtotime($endDate)) }} | Dicetak: {{ date('d F Y, H:i') }} WIB</p>
  <hr>
</div>

<!-- KPI Summary Cards Theme-Adapted -->
<div class="row g-4 mb-4">
  <!-- 1. TOTAL CASH INFLOW -->
  <div class="col-lg-4 col-md-6">
    <div class="card p-4 h-100 cf-card cf-inflow">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="badge cf-badge-inflow px-3 py-2 fw-bold">
          <i class="bi bi-arrow-down-left-circle-fill me-1"></i>TOTAL CASH INFLOW
        </span>
        <div class="cf-icon-wrap icon-inflow">
          <i class="bi bi-arrow-down-left"></i>
        </div>
      </div>
      <div class="cf-sub-header">Pemasukan Kas & Non-Kas Masuk</div>
      <h2 class="fw-bold cf-amount-inflow mb-3">Rp {{ number_format($totalCashIn, 0, ',', '.') }}</h2>
      
      <div class="cf-breakdown-box">
        <div class="cf-row">
          <span class="cf-label"><i class="bi bi-cash-stack me-1.5 text-success"></i>Tunai Kasir:</span>
          <strong class="cf-val">Rp {{ number_format($totalSalesCash, 0, ',', '.') }}</strong>
        </div>
        <div class="cf-row">
          <span class="cf-label"><i class="bi bi-qr-code-scan me-1.5 text-info"></i>Non-Tunai (QRIS/Bank):</span>
          <strong class="cf-val">Rp {{ number_format($totalSalesNonCash, 0, ',', '.') }}</strong>
        </div>
        <div class="cf-row">
          <span class="cf-label"><i class="bi bi-box-arrow-in-down me-1.5 text-primary"></i>Top-Up Modal Laci:</span>
          <strong class="cf-val">Rp {{ number_format($totalDrawerCashIn, 0, ',', '.') }}</strong>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. TOTAL OPERATING OUTFLOW -->
  <div class="col-lg-4 col-md-6">
    <div class="card p-4 h-100 cf-card cf-outflow">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="badge cf-badge-outflow px-3 py-2 fw-bold">
          <i class="bi bi-arrow-up-right-circle-fill me-1"></i>TOTAL OPERATING OUTFLOW
        </span>
        <div class="cf-icon-wrap icon-outflow">
          <i class="bi bi-arrow-up-right"></i>
        </div>
      </div>
      <div class="cf-sub-header">Pengeluaran Kas Nyata Keluar</div>
      <h2 class="fw-bold cf-amount-outflow mb-3">Rp {{ number_format($totalOperatingOutflow, 0, ',', '.') }}</h2>
      
      <div class="cf-breakdown-box">
        <div class="cf-row">
          <span class="cf-label"><i class="bi bi-cart-check-fill me-1.5 text-danger"></i>PO Bahan Mentah Lunas:</span>
          <strong class="cf-val">Rp {{ number_format($totalPoPaid, 0, ',', '.') }}</strong>
        </div>
        <div class="cf-row">
          <span class="cf-label"><i class="bi bi-wallet2 me-1.5 text-warning"></i>Petty Cash Laci (Es/Gas):</span>
          <strong class="cf-val">Rp {{ number_format($totalDrawerCashOut, 0, ',', '.') }}</strong>
        </div>
        <div class="cf-row">
          <span class="cf-label"><i class="bi bi-people-fill me-1.5 text-info"></i>Gaji & Tenaga Kerja:</span>
          <strong class="cf-val">Rp {{ number_format($totalLaborCost, 0, ',', '.') }}</strong>
        </div>
        <div class="cf-row">
          <span class="cf-label"><i class="bi bi-lightning-charge-fill me-1.5 text-warning"></i>Listrik/Air/Overhead:</span>
          <strong class="cf-val">Rp {{ number_format($totalOverheadCost, 0, ',', '.') }}</strong>
        </div>
      </div>
    </div>
  </div>

  <!-- 3. NET CASH FLOW BERSIH -->
  <div class="col-lg-4 col-md-12">
    <div class="card p-4 h-100 cf-card {{ $netCashFlow >= 0 ? 'cf-net-surplus' : 'cf-net-deficit' }}">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="badge {{ $netCashFlow >= 0 ? 'cf-badge-surplus' : 'cf-badge-deficit' }} px-3 py-2 fw-bold">
          <i class="bi {{ $netCashFlow >= 0 ? 'bi-shield-check' : 'bi-exclamation-triangle-fill' }} me-1"></i>NET CASH FLOW
        </span>
        <div class="cf-icon-wrap {{ $netCashFlow >= 0 ? 'icon-surplus' : 'icon-deficit' }}">
          <i class="bi bi-wallet2"></i>
        </div>
      </div>
      <div class="cf-sub-header">Surplus / (Defisit) Bersih Kas</div>
      <h2 class="fw-bold {{ $netCashFlow >= 0 ? 'cf-amount-surplus' : 'cf-amount-deficit' }} mb-3">
        Rp {{ number_format($netCashFlow, 0, ',', '.') }}
      </h2>
      
      <div class="cf-breakdown-box">
        <div class="cf-row align-items-center">
          <span class="cf-label">Status Likuiditas:</span>
          <span class="badge {{ $netCashFlow >= 0 ? 'cf-status-pill-surplus' : 'cf-status-pill-deficit' }} px-2.5 py-1.5 fw-bold">
            {{ $netCashFlow >= 0 ? 'SURPLUS KAS POSITIF' : 'DEFISIT KAS' }}
          </span>
        </div>
        <div class="cf-row pt-2">
          <span class="cf-label"><i class="bi bi-trash3-fill me-1.5 text-muted-sub"></i>Waste Log (Non-Kas):</span>
          <strong class="cf-val text-muted-sub">Rp {{ number_format($totalWasteOut, 0, ',', '.') }}</strong>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Pemantauan Hutang PO Tempo (Unpaid Commitments) -->
<div class="card p-4 mb-4 cf-unpaid-card">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
      <div class="cf-icon-wrap icon-warning">
        <i class="bi bi-hourglass-split fs-4"></i>
      </div>
      <div>
        <h5 class="fw-bold mb-1 cf-title">⏳ Komitmen Kas Keluar Masa Depan (Hutang PO Tempo Belum Lunas)</h5>
        <p class="cf-desc mb-0">Barang bahan mentah sudah diterima fisik namun kas belum dibayarkan ke supplier (masih dalam masa jatuh tempo).</p>
      </div>
    </div>
    <div class="text-end">
      <span class="badge cf-badge-warning px-3 py-1.5 fw-bold">HUTANG PO BELUM LUNAS</span>
      <h3 class="fw-bold text-warning mb-0 mt-1">Rp {{ number_format($totalPoUnpaid, 0, ',', '.') }}</h3>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
  /* =======================================================
     CASH FLOW STYLES — DARK & LIGHT THEME AUTO ADAPTATION
     ======================================================= */
  
  /* Filter Panel */
  .filter-panel {
    background: var(--bg-surface, #1A1E27);
    border: 1px solid var(--border-subtle, #2F3748);
    border-radius: 14px;
  }
  .text-muted-sub {
    color: var(--text-secondary, #94a3b8) !important;
  }
  .date-input {
    background: var(--bg-surface, #1A1E27) !important;
    border-color: var(--border-subtle, #2F3748) !important;
    color: var(--text-primary, #E8EDF5) !important;
  }

  /* Base Cash Flow Cards */
  .cf-card {
    background: var(--bg-surface, #1A1E27);
    border-radius: 18px;
    transition: all 0.25s ease-in-out;
  }
  .cf-card:hover {
    transform: translateY(-3px);
  }

  .cf-sub-header {
    font-size: 0.85rem;
    color: var(--text-secondary, #94a3b8);
    font-weight: 500;
    margin-bottom: 0.35rem;
  }

  .cf-icon-wrap {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
  }

  /* Breakdown List Inside Cards */
  .cf-breakdown-box {
    background: rgba(0, 0, 0, 0.15);
    border-radius: 12px;
    padding: 0.85rem 1rem;
    border: 1px solid rgba(255, 255, 255, 0.05);
    margin-top: auto;
  }
  .cf-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.32rem 0;
    font-size: 0.86rem;
  }
  .cf-row:not(:last-child) {
    border-bottom: 1px dashed rgba(255, 255, 255, 0.06);
  }
  .cf-label {
    color: var(--text-secondary, #94a3b8);
    font-weight: 500;
  }
  .cf-val {
    color: var(--text-primary, #ffffff);
    font-weight: 600;
  }

  /* --- Inflow Styling --- */
  .cf-inflow {
    border: 1.5px solid #10b981;
    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.12);
  }
  .cf-badge-inflow {
    background: rgba(16, 185, 129, 0.18);
    color: #34d399;
    border: 1px solid rgba(16, 185, 129, 0.4);
    font-size: 0.78rem;
    letter-spacing: 0.5px;
  }
  .icon-inflow {
    background: rgba(16, 185, 129, 0.15);
    color: #34d399;
  }
  .cf-amount-inflow {
    color: #10b981;
    font-size: 1.85rem;
    letter-spacing: -0.5px;
  }

  /* --- Outflow Styling --- */
  .cf-outflow {
    border: 1.5px solid #ef4444;
    box-shadow: 0 4px 20px rgba(239, 68, 68, 0.12);
  }
  .cf-badge-outflow {
    background: rgba(239, 68, 68, 0.18);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.4);
    font-size: 0.78rem;
    letter-spacing: 0.5px;
  }
  .icon-outflow {
    background: rgba(239, 68, 68, 0.15);
    color: #f87171;
  }
  .cf-amount-outflow {
    color: #ef4444;
    font-size: 1.85rem;
    letter-spacing: -0.5px;
  }

  /* --- Net Cash Flow: Surplus Styling --- */
  .cf-net-surplus {
    border: 1.5px solid #3b82f6;
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.15);
  }
  .cf-badge-surplus {
    background: rgba(59, 130, 246, 0.18);
    color: #60a5fa;
    border: 1px solid rgba(59, 130, 246, 0.4);
    font-size: 0.78rem;
    letter-spacing: 0.5px;
  }
  .icon-surplus {
    background: rgba(59, 130, 246, 0.15);
    color: #60a5fa;
  }
  .cf-amount-surplus {
    color: #3b82f6;
    font-size: 1.85rem;
    letter-spacing: -0.5px;
  }
  .cf-status-pill-surplus {
    background: #10b981;
    color: #ffffff;
    font-size: 0.75rem;
    letter-spacing: 0.3px;
  }

  /* --- Net Cash Flow: Deficit Styling --- */
  .cf-net-deficit {
    border: 1.5px solid #ef4444;
    box-shadow: 0 4px 25px rgba(239, 68, 68, 0.2);
  }
  .cf-badge-deficit {
    background: rgba(239, 68, 68, 0.2);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.5);
    font-size: 0.78rem;
    letter-spacing: 0.5px;
  }
  .icon-deficit {
    background: rgba(239, 68, 68, 0.18);
    color: #f87171;
  }
  .cf-amount-deficit {
    color: #f87171;
    font-size: 1.85rem;
    letter-spacing: -0.5px;
  }
  .cf-status-pill-deficit {
    background: #dc2626;
    color: #ffffff;
    font-size: 0.75rem;
    letter-spacing: 0.3px;
    box-shadow: 0 0 10px rgba(220, 38, 38, 0.4);
  }

  /* --- Unpaid PO Card --- */
  .cf-unpaid-card {
    background: var(--bg-surface, #1A1E27);
    border: 1.5px dashed rgba(245, 158, 11, 0.5);
    border-radius: 18px;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.08);
  }
  .cf-badge-warning {
    background: rgba(245, 158, 11, 0.18);
    color: #fbbf24;
    border: 1px solid rgba(245, 158, 11, 0.4);
    font-size: 0.76rem;
  }
  .icon-warning {
    background: rgba(245, 158, 11, 0.15);
    color: #fbbf24;
  }
  .cf-title {
    color: var(--text-primary, #E8EDF5);
    font-size: 1.05rem;
  }
  .cf-desc {
    color: var(--text-secondary, #94a3b8);
    font-size: 0.84rem;
  }

  /* =======================================================
     LIGHT THEME EXPLICIT OVERRIDES
     ======================================================= */
  [data-theme="light"] .filter-panel,
  [data-theme="light"] .cf-card,
  [data-theme="light"] .cf-unpaid-card {
    background: #ffffff !important;
    border-color: #e2e8f0;
  }
  [data-theme="light"] .cf-breakdown-box {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
  }
  [data-theme="light"] .cf-row:not(:last-child) {
    border-bottom: 1px dashed #e2e8f0 !important;
  }
  [data-theme="light"] .cf-label {
    color: #64748b !important;
  }
  [data-theme="light"] .cf-val {
    color: #0f172a !important;
  }
  [data-theme="light"] .cf-sub-header,
  [data-theme="light"] .cf-desc {
    color: #64748b !important;
  }
  [data-theme="light"] .cf-title {
    color: #0f172a !important;
  }
  [data-theme="light"] .date-input {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
  }

  /* Print Styles */
  .print-only { display: none !important; }
  @media print {
    .no-print { display: none !important; }
    .print-only { display: block !important; }
  }
</style>
@endpush
