@extends('admin.layouts.app')

@section('title', 'Laporan Stok Bahan Mentah & Inventory')

@php $activeMenu = 'reports-inventory' @endphp

@section('content')
<!-- Page Header -->
<div class="page-header no-print">
  <div>
    <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary);">📦 Laporan Stok Bahan Mentah & Inventory</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.reports.dashboard') }}">Dashboard Laporan</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Stok & Inventory</span>
    </div>
  </div>

  <div class="d-flex flex-wrap align-items-center gap-2">
    <button onclick="window.print()" class="btn btn-outline-soft">
      <i class="bi bi-printer me-1"></i>Cetak Laporan
    </button>
    <a href="{{ route('admin.reports.inventory.export', ['search' => $search]) }}" class="btn btn-success">
      <i class="bi bi-file-earmark-excel me-1"></i>Export Excel / CSV
    </a>
  </div>
</div>

<!-- Title Print Header -->
<div class="print-only mb-4 text-center">
  <h2 class="fw-bold">LAPORAN STOK BAHAN MENTAH & INVENTORY GUDANG</h2>
  <p class="text-muted-c">Tanggal Cetak: {{ date('d F Y, H:i') }} WIB</p>
  <hr style="border-color: var(--border-subtle);">
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card p-3 h-100" style="background: var(--bg-surface); border: 1.5px solid #3b82f6; border-radius: 14px;">
      <div class="text-muted-c text-uppercase fw-semibold mb-1" style="font-size: 0.72rem;">NILAI ASET BAHAN MENTAH GUDANG</div>
      <h3 class="fw-bold mb-0" style="color: #3b82f6;">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</h3>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 h-100" style="background: var(--bg-surface); border: 1.5px solid #10b981; border-radius: 14px;">
      <div class="text-muted-c text-uppercase fw-semibold mb-1" style="font-size: 0.72rem;">TOTAL PO BELANJA RECEIVING</div>
      <h3 class="fw-bold mb-0" style="color: #10b981;">Rp {{ number_format($totalPo, 0, ',', '.') }}</h3>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 h-100" style="background: var(--bg-surface); border: 1.5px solid #ef4444; border-radius: 14px;">
      <div class="text-muted-c text-uppercase fw-semibold mb-1" style="font-size: 0.72rem;">TOTAL KERUGIAN WASTE LOG (RUSAK)</div>
      <h3 class="fw-bold mb-0" style="color: #ef4444;">Rp {{ number_format($totalWaste, 0, ',', '.') }}</h3>
    </div>
  </div>
</div>

<!-- Filter Bar Card -->
<div class="card mb-4 no-print">
  <div class="card-body py-3">
    <form action="{{ route('admin.reports.inventory') }}" method="GET" class="row g-3 align-items-center">
      <div class="col-md-6">
        <div class="input-group">
          <input type="text" name="search" class="form-control-modern" placeholder="Cari kode atau nama bahan mentah..." value="{{ $search }}">
        </div>
      </div>
      <div class="col-md-6 d-flex align-items-center justify-content-md-end gap-2">
        <button type="submit" class="btn btn-primary-grad">
          <i class="bi bi-filter me-1"></i>Filter
        </button>
        <a href="{{ route('admin.reports.inventory') }}" class="btn btn-outline-soft">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- Table Card (Nexora Category-Style Table) -->
<div class="card">
  <div class="card-header-flex">
    <h6>Daftar Stok Bahan Mentah & Inventory</h6>
    <div class="d-flex align-items-center gap-2 no-print">
      <label class="form-label-modern mb-0" style="font-size:0.85rem;">Tampilkan</label>
      <form action="{{ route('admin.reports.inventory') }}" method="GET" id="perPageForm">
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
      <span class="chip-tag">{{ $materials->total() }} item</span>
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>KODE BAHAN</th>
            <th>NAMA BAHAN MENTAH</th>
            <th class="text-center">SISA STOK</th>
            <th class="text-end">HARGA EFEKTIF UNIT</th>
            <th class="text-end">NILAI ASET</th>
          </tr>
        </thead>
        <tbody>
          @forelse($materials as $m)
            @php $assetValue = (float) $m->amount * (float) $m->effective_price; @endphp
            <tr>
              <td class="fw-bold" style="color: var(--accent-1);">{{ $m->raw_material_code }}</td>
              <td class="fw-semibold" style="color: var(--text-primary);">{{ $m->name }}</td>
              <td class="text-center">
                <span class="chip-tag" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; font-weight: 600;">
                  {{ number_format($m->amount, 2) }} {{ $m->unit }}
                </span>
              </td>
              <td class="text-end" style="color: var(--text-secondary);">Rp {{ number_format($m->effective_price, 0, ',', '.') }} / {{ $m->unit }}</td>
              <td class="text-end fw-bold" style="color: #34d399;">Rp {{ number_format($assetValue, 0, ',', '.') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4 text-muted-c">Belum ada data stok bahan mentah yang sesuai.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($perPageInput !== 'all')
    <div class="px-3 py-2 d-flex justify-content-between align-items-center no-print" style="border-top: 1px solid var(--border-subtle);">
      <span class="text-muted-c" style="font-size:0.85rem;">
        Menampilkan {{ $materials->firstItem() ?? 0 }} - {{ $materials->lastItem() ?? 0 }} dari {{ $materials->total() }}
      </span>
      {{ $materials->appends(request()->query())->links('vendor.pagination.modern') }}
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
