@extends('admin.layouts.app')

@section('title', 'Laporan Pajak (PB1) & Service Charge')

@php $activeMenu = 'reports-tax-service' @endphp

@section('content')
<!-- Page Header -->
<div class="page-header no-print">
  <div>
    <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary);">🏛️ Laporan Pajak (PB1) & Service Charge</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.reports.dashboard') }}">Dashboard Laporan</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Pajak & Service</span>
    </div>
  </div>

  <div class="d-flex flex-wrap align-items-center gap-2">
    <button onclick="window.print()" class="btn btn-outline-soft">
      <i class="bi bi-printer me-1"></i>Cetak Laporan
    </button>
    <a href="{{ route('admin.reports.tax-service.export', ['start_date' => $startDate, 'end_date' => $endDate, 'search' => $search]) }}" class="btn btn-success">
      <i class="bi bi-file-earmark-excel me-1"></i>Export Excel / CSV
    </a>
  </div>
</div>

<!-- Title Print Header -->
<div class="print-only mb-4 text-center">
  <h2 class="fw-bold">LAPORAN PAJAK PB1 & SERVICE CHARGE</h2>
  <p class="text-muted-c">Periode: {{ date('d/m/Y', strtotime($startDate)) }} s.d {{ date('d/m/Y', strtotime($endDate)) }} | Dicetak: {{ date('d F Y, H:i') }} WIB</p>
  <hr style="border-color: var(--border-subtle);">
</div>

<!-- Filter Bar Card -->
<div class="card mb-4 no-print">
  <div class="card-body py-3">
    <form action="{{ route('admin.reports.tax-service') }}" method="GET" class="row g-3 align-items-center">
      <div class="col-md-4">
        <label class="form-label-modern mb-1">Rentang Tanggal:</label>
        <div class="d-flex align-items-center gap-2">
          <input type="date" name="start_date" class="form-control-modern" value="{{ $startDate }}">
          <span class="text-muted-c">s.d</span>
          <input type="date" name="end_date" class="form-control-modern" value="{{ $endDate }}">
        </div>
      </div>

      <div class="col-md-5">
        <label class="form-label-modern mb-1">Pencarian Order:</label>
        <input type="text" name="search" class="form-control-modern" placeholder="Cari ID Order atau Tipe Pajak..." value="{{ $search }}">
      </div>

      <div class="col-md-3 d-flex align-items-end justify-content-md-end gap-2">
        <button type="submit" class="btn btn-primary-grad px-4">
          <i class="bi bi-filter me-1"></i>Filter
        </button>
        <a href="{{ route('admin.reports.tax-service') }}" class="btn btn-outline-soft">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card p-3 h-100" style="background: var(--bg-surface); border: 1.5px solid #8b5cf6; border-radius: 14px;">
      <div class="text-muted-c text-uppercase fw-semibold mb-1" style="font-size: 0.72rem;">TOTAL SETORAN PAJAK PB1 (10%)</div>
      <h3 class="fw-bold mb-0" style="color: #8b5cf6;">Rp {{ number_format($totalTax, 0, ',', '.') }}</h3>
      <small class="text-muted-c">Wajib disetorkan ke Bapenda / Kas Daerah</small>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card p-3 h-100" style="background: var(--bg-surface); border: 1.5px solid #10b981; border-radius: 14px;">
      <div class="text-muted-c text-uppercase fw-semibold mb-1" style="font-size: 0.72rem;">TOTAL POOL SERVICE CHARGE (5%)</div>
      <h3 class="fw-bold mb-0" style="color: #10b981;">Rp {{ number_format($totalService, 0, ',', '.') }}</h3>
      <small class="text-muted-c">Pool tip / insentif layanan karyawan</small>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card p-3 h-100" style="background: var(--bg-surface); border: 1.5px solid #3b82f6; border-radius: 14px;">
      <div class="text-muted-c text-uppercase fw-semibold mb-1" style="font-size: 0.72rem;">DASAR PENGENAAN PAJAK (DPP)</div>
      <h3 class="fw-bold mb-0" style="color: #3b82f6;">Rp {{ number_format($totalTaxableBase, 0, ',', '.') }}</h3>
      <small class="text-muted-c">Net sales + taxable service charge</small>
    </div>
  </div>
</div>

<!-- Table Card (Nexora Category-Style Table) -->
<div class="card">
  <div class="card-header-flex">
    <h6>Daftar Rincian Pajak & Service Order</h6>
    <div class="d-flex align-items-center gap-2 no-print">
      <label class="form-label-modern mb-0" style="font-size:0.85rem;">Tampilkan</label>
      <form action="{{ route('admin.reports.tax-service') }}" method="GET" id="perPageForm">
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
      <span class="chip-tag">{{ $orders->total() }} item</span>
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>NO ORDER</th>
            <th>TANGGAL & WAKTU</th>
            <th>TIPE PAJAK</th>
            <th class="text-center">TARIF PAJAK</th>
            <th class="text-end">SERVICE CHARGE (5%)</th>
            <th class="text-end">PAJAK PB1 (10%)</th>
            <th class="text-end">GRAND TOTAL STRUK</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $ord)
            <tr>
              <td class="fw-bold" style="color: var(--accent-1);">#{{ $ord->order_id }}</td>
              <td style="color: var(--text-secondary);">{{ $ord->created_at }}</td>
              <td>
                <span class="chip-tag" style="background: rgba(34, 211, 238, 0.15); color: #22d3ee; font-weight: 600;">
                  {{ strtoupper($ord->tax_type ?? 'exclusive') }}
                </span>
              </td>
              <td class="text-center" style="color: var(--text-primary);">{{ $ord->tax_percent ?? 10 }}%</td>
              <td class="text-end" style="color: #34d399;">+ Rp {{ number_format($ord->service_charge_amount ?? 0, 0, ',', '.') }}</td>
              <td class="text-end" style="color: #60a5fa;">+ Rp {{ number_format($ord->tax_amount ?? 0, 0, ',', '.') }}</td>
              <td class="text-end fw-bold" style="color: #34d399;">Rp {{ number_format($ord->order_grand_total, 0, ',', '.') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted-c">Belum ada transaksi dengan rincian pajak pada periode ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($perPageInput !== 'all')
    <div class="px-3 py-2 d-flex justify-content-between align-items-center no-print" style="border-top: 1px solid var(--border-subtle);">
      <span class="text-muted-c" style="font-size:0.85rem;">
        Menampilkan {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }}
      </span>
      {{ $orders->appends(request()->query())->links('vendor.pagination.modern') }}
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
