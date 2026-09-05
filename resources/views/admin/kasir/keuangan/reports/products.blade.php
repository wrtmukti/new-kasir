@extends('admin.layouts.app')

@section('title', 'Laporan Performa Menu Terlaris (PMIX)')

@php $activeMenu = 'reports-products' @endphp

@section('content')
<!-- Page Header -->
<div class="page-header no-print">
  <div>
    <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary);">🍔 Laporan Performa Menu Terlaris (PMIX)</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.reports.dashboard') }}">Dashboard Laporan</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Performa Menu</span>
    </div>
  </div>

  <div class="d-flex flex-wrap align-items-center gap-2">
    <button onclick="window.print()" class="btn btn-outline-soft">
      <i class="bi bi-printer me-1"></i>Cetak Laporan
    </button>
    <a href="{{ route('admin.reports.products.export', ['start_date' => $startDate, 'end_date' => $endDate, 'search' => $search]) }}" class="btn btn-success">
      <i class="bi bi-file-earmark-excel me-1"></i>Export Excel / CSV
    </a>
  </div>
</div>

<!-- Title Print Header -->
<div class="print-only mb-4 text-center">
  <h2 class="fw-bold">LAPORAN PERFORMA MENU TERLARIS (PMIX)</h2>
  <p class="text-muted-c">Periode: {{ date('d/m/Y', strtotime($startDate)) }} s.d {{ date('d/m/Y', strtotime($endDate)) }} | Dicetak: {{ date('d F Y, H:i') }} WIB</p>
  <hr style="border-color: var(--border-subtle);">
</div>

<!-- Filter Bar Card -->
<div class="card mb-4 no-print">
  <div class="card-body py-3">
    <form action="{{ route('admin.reports.products') }}" method="GET" class="row g-3 align-items-center">
      <div class="col-md-4">
        <label class="form-label-modern mb-1">Rentang Tanggal:</label>
        <div class="d-flex align-items-center gap-2">
          <input type="date" name="start_date" class="form-control-modern" value="{{ $startDate }}">
          <span class="text-muted-c">s.d</span>
          <input type="date" name="end_date" class="form-control-modern" value="{{ $endDate }}">
        </div>
      </div>

      <div class="col-md-5">
        <label class="form-label-modern mb-1">Pencarian Menu:</label>
        <input type="text" name="search" class="form-control-modern" placeholder="Cari nama produk / menu..." value="{{ $search }}">
      </div>

      <div class="col-md-3 d-flex align-items-end justify-content-md-end gap-2">
        <button type="submit" class="btn btn-primary-grad px-4">
          <i class="bi bi-filter me-1"></i>Filter
        </button>
        <a href="{{ route('admin.reports.products') }}" class="btn btn-outline-soft">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- Table Card (Nexora Category-Style Table) -->
<div class="card">
  <div class="card-header-flex">
    <h6>Ranking Performa Item Menu Terjual</h6>
    <div class="d-flex align-items-center gap-2 no-print">
      <label class="form-label-modern mb-0" style="font-size:0.85rem;">Tampilkan</label>
      <form action="{{ route('admin.reports.products') }}" method="GET" id="perPageForm">
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
      <span class="chip-tag">{{ $items->total() }} item</span>
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th style="width: 100px;">RANKING</th>
            <th>NAMA MENU / PRODUK</th>
            <th class="text-center">QTY TERJUAL</th>
            <th class="text-end">TOTAL OMZET PENJUALAN</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $index => $item)
            <tr>
              <td class="fw-bold" style="color: var(--accent-1);">#{{ $items->firstItem() + $index }}</td>
              <td class="fw-semibold" style="color: var(--text-primary);">{{ $item->product_name }}</td>
              <td class="text-center">
                <span class="chip-tag" style="background: rgba(59, 130, 246, 0.18); color: #60a5fa; font-weight: 600; padding: 0.35rem 0.85rem; font-size: 0.85rem;">
                  {{ number_format($item->total_qty) }} Porsi
                </span>
              </td>
              <td class="text-end fw-bold" style="color: #34d399;">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center py-4 text-muted-c">Belum ada data item menu terjual pada rentang tanggal ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($perPageInput !== 'all')
    <div class="px-3 py-2 d-flex justify-content-between align-items-center no-print" style="border-top: 1px solid var(--border-subtle);">
      <span class="text-muted-c" style="font-size:0.85rem;">
        Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} dari {{ $items->total() }}
      </span>
      {{ $items->appends(request()->query())->links('vendor.pagination.modern') }}
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
