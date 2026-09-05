@extends('admin.layouts.app')

@section('title', 'Laporan Penjualan & Payment Breakdown')

@php $activeMenu = 'reports-sales' @endphp

@section('content')
<!-- Page Header -->
<div class="page-header no-print">
  <div>
    <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary);">💳 Laporan Penjualan & Payment Breakdown</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.reports.dashboard') }}">Dashboard Laporan</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Laporan Penjualan</span>
    </div>
  </div>

  <div class="d-flex flex-wrap align-items-center gap-2">
    <button onclick="window.print()" class="btn btn-outline-soft">
      <i class="bi bi-printer me-1"></i>Cetak Laporan
    </button>
    <a href="{{ route('admin.reports.sales.export', ['start_date' => $startDate, 'end_date' => $endDate, 'search' => $search]) }}" class="btn btn-success">
      <i class="bi bi-file-earmark-excel me-1"></i>Export Excel / CSV
    </a>
  </div>
</div>

<!-- Title Print Header -->
<div class="print-only mb-4 text-center">
  <h2 class="fw-bold">LAPORAN PENJUALAN & PAYMENT BREAKDOWN</h2>
  <p class="text-muted-c">Periode: {{ date('d/m/Y', strtotime($startDate)) }} s.d {{ date('d/m/Y', strtotime($endDate)) }} | Dicetak: {{ date('d F Y, H:i') }} WIB</p>
  <hr style="border-color: var(--border-subtle);">
</div>

<!-- Filter Bar Card -->
<div class="card mb-4 no-print">
  <div class="card-body py-3">
    <form action="{{ route('admin.reports.sales') }}" method="GET" class="row g-3 align-items-center">
      <div class="col-md-4">
        <label class="form-label-modern mb-1">Rentang Tanggal:</label>
        <div class="d-flex align-items-center gap-2">
          <input type="date" name="start_date" class="form-control-modern" value="{{ $startDate }}">
          <span class="text-muted-c">s.d</span>
          <input type="date" name="end_date" class="form-control-modern" value="{{ $endDate }}">
        </div>
      </div>

      <div class="col-md-5">
        <label class="form-label-modern mb-1">Pencarian:</label>
        <input type="text" name="search" class="form-control-modern" placeholder="Cari kode transaksi atau catatan..." value="{{ $search }}">
      </div>

      <div class="col-md-3 d-flex align-items-end justify-content-md-end gap-2">
        <button type="submit" class="btn btn-primary-grad px-4">
          <i class="bi bi-filter me-1"></i>Filter
        </button>
        <a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-soft">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card p-3 h-100" style="background: var(--bg-surface); border: 1.5px solid #3b82f6; border-radius: 14px;">
      <div class="text-muted-c text-uppercase fw-semibold mb-1" style="font-size: 0.72rem;">TOTAL SUBTOTAL</div>
      <h3 class="fw-bold mb-0" style="color: #3b82f6;">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 h-100" style="background: var(--bg-surface); border: 1.5px solid #10b981; border-radius: 14px;">
      <div class="text-muted-c text-uppercase fw-semibold mb-1" style="font-size: 0.72rem;">SERVICE CHARGE (5%)</div>
      <h3 class="fw-bold mb-0" style="color: #10b981;">Rp {{ number_format($totalService, 0, ',', '.') }}</h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 h-100" style="background: var(--bg-surface); border: 1.5px solid #8b5cf6; border-radius: 14px;">
      <div class="text-muted-c text-uppercase fw-semibold mb-1" style="font-size: 0.72rem;">PAJAK PB1 (10%)</div>
      <h3 class="fw-bold mb-0" style="color: #8b5cf6;">Rp {{ number_format($totalTax, 0, ',', '.') }}</h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 h-100" style="background: var(--bg-surface); border: 1.5px solid #06b6d4; border-radius: 14px;">
      <div class="text-muted-c text-uppercase fw-semibold mb-1" style="font-size: 0.72rem;">GRAND TOTAL SALES</div>
      <h3 class="fw-bold mb-0" style="color: #06b6d4;">Rp {{ number_format($totalGrand, 0, ',', '.') }}</h3>
    </div>
  </div>
</div>

<!-- Table Card (Nexora Category-Style Table) -->
<div class="card">
  <div class="card-header-flex">
    <h6>Daftar Transaksi Penjualan</h6>
    <div class="d-flex align-items-center gap-2 no-print">
      <label class="form-label-modern mb-0" style="font-size:0.85rem;">Tampilkan</label>
      <form action="{{ route('admin.reports.sales') }}" method="GET" id="perPageForm">
        <input type="hidden" name="start_date" value="{{ $startDate }}">
        <input type="hidden" name="end_date" value="{{ $endDate }}">
        <input type="hidden" name="search" value="{{ $search }}">
        <select name="per_page" class="form-select-modern" onchange="this.form.submit()" style="width:auto;min-width:70px;">
          <option value="10" {{ $perPageInput == '10' ? 'selected' : '' }}>10</option>
          <option value="20" {{ $perPageInput == '20' ? 'selected' : '' }}>20</option>
          <option value="50" {{ $perPageInput == '50' ? 'selected' : '' }}>50</option>
          <option value="100" {{ $perPageInput == '100' ? 'selected' : '' }}>100</option>
          <option value="all" {{ $perPageInput == 'all' ? 'selected' : '' }}>All</option>
        </select>
      </form>
      <span class="text-muted-c" style="font-size:0.85rem;">data</span>
      <span class="chip-tag">{{ $transactions->total() }} item</span>
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>KODE TRANSAKSI</th>
            <th>TANGGAL & WAKTU</th>
            <th>SHIFT AUDIT</th>
            <th class="text-end">SUBTOTAL</th>
            <th class="text-end">SERVICE (5%)</th>
            <th class="text-end">PAJAK PB1 (10%)</th>
            <th class="text-end">GRAND TOTAL</th>
            <th class="text-center">STATUS</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transactions as $trx)
            <tr>
              <td class="fw-bold" style="color: var(--accent-1);">{{ $trx->transaction_code }}</td>
              <td style="color: var(--text-secondary);">{{ $trx->transaction_date }}</td>
              <td>
                <span class="chip-tag" style="background: rgba(34, 211, 238, 0.15); color: #22d3ee; font-weight: 600;">
                  {{ optional($trx->dailyClosing)->shift_name ?? 'Shift Default' }}
                </span>
              </td>
              <td class="text-end" style="color: var(--text-secondary);">Rp {{ number_format($trx->transaction_subtotal, 0, ',', '.') }}</td>
              <td class="text-end" style="color: #34d399;">+ Rp {{ number_format($trx->transaction_service_charge, 0, ',', '.') }}</td>
              <td class="text-end" style="color: #60a5fa;">+ Rp {{ number_format($trx->transaction_tax, 0, ',', '.') }}</td>
              <td class="text-end fw-bold" style="color: #34d399;">Rp {{ number_format($trx->transaction_grand_total, 0, ',', '.') }}</td>
              <td class="text-center">
                <span class="chip-tag" style="background: var(--success-bg); color: var(--success); font-weight:600;">Success</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-4 text-muted-c">Belum ada transaksi penjualan pada rentang tanggal ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($perPageInput !== 'all')
    <div class="px-3 py-2 d-flex justify-content-between align-items-center no-print" style="border-top: 1px solid var(--border-subtle);">
      <span class="text-muted-c" style="font-size:0.85rem;">
        Menampilkan {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }}
      </span>
      {{ $transactions->appends(request()->query())->links('vendor.pagination.modern') }}
    </div>
    @endif
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
    table { width: 100% !important; border-collapse: collapse !important; }
    th, td { border: 1px solid #ddd !important; padding: 8px !important; color: #000000 !important; }
  }
</style>
@endpush
