@extends('admin.layouts.app')

@section('title', 'Log Aktivitas Perubahan Data')

@php $activeMenu = 'history' @endphp

@section('content')
<!-- Header Page -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
  <div>
    <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary);">
      <i class="bi bi-clock-history me-2 text-primary"></i>Log Aktivitas Perubahan Data
    </h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}" class="text-muted-c text-decoration-none">Beranda</a>
      <i class="bi bi-chevron-right text-muted-c" style="font-size:0.6rem;"></i>
      <span style="color: var(--text-primary); font-weight: 600;">Log Aktivitas Perubahan Data</span>
    </div>
  </div>

  <div class="d-flex align-items-center gap-2">
    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fs-6">
      <i class="bi bi-activity me-1"></i>Total {{ number_format($totalAll, 0, ',', '.') }} Aktivitas Tercatat
    </span>
  </div>
</div>

<div class="row g-3 mb-4">
  <!-- Card 1: Stok Bahan -->
  <div class="col-md-4">
    <a href="{{ route('admin.history.stock.index') }}" class="text-decoration-none">
      <div class="card h-100 p-4 rounded-4 border-0 shadow-sm card-report-hover" 
           style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3 flex-shrink-0" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
            <i class="bi bi-archive fs-3"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <h6 class="fw-bold mb-0" style="color: var(--text-primary);">Stok Bahan</h6>
              <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">{{ $totalStock }} Log</span>
            </div>
            <small class="text-muted-c">Audit log mutasi masuk/keluar, belanja supplier & stock opname bahan mentah.</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 2: Produk & Menu -->
  <div class="col-md-4">
    <a href="{{ route('admin.history.product.index') }}" class="text-decoration-none">
      <div class="card h-100 p-4 rounded-4 border-0 shadow-sm card-report-hover" 
           style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3 flex-shrink-0" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
            <i class="bi bi-cup-hot fs-3"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <h6 class="fw-bold mb-0" style="color: var(--text-primary);">Produk & Menu</h6>
              <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">{{ $totalProduct }} Log</span>
            </div>
            <small class="text-muted-c">Histori penambahan menu, perubahan harga jual, dan status produk aktif.</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 3: Paket Bundle -->
  <div class="col-md-4">
    <a href="{{ route('admin.history.bundle.index') }}" class="text-decoration-none">
      <div class="card h-100 p-4 rounded-4 border-0 shadow-sm card-report-hover" 
           style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3 flex-shrink-0" style="background: rgba(6, 182, 212, 0.12); color: #06b6d4;">
            <i class="bi bi-gift fs-3"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <h6 class="fw-bold mb-0" style="color: var(--text-primary);">Paket Bundle</h6>
              <span class="badge bg-info-subtle text-info rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">{{ $totalBundle }} Log</span>
            </div>
            <small class="text-muted-c">Catatan pembuatan paket bundle combo, variasi item, dan kuota paket hemat.</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 4: Diskon -->
  <div class="col-md-4">
    <a href="{{ route('admin.history.discount.index') }}" class="text-decoration-none">
      <div class="card h-100 p-4 rounded-4 border-0 shadow-sm card-report-hover" 
           style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3 flex-shrink-0" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
            <i class="bi bi-percent fs-3"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <h6 class="fw-bold mb-0" style="color: var(--text-primary);">Diskon</h6>
              <span class="badge bg-purple-subtle text-purple rounded-pill px-2 py-0.5" style="font-size: 0.68rem; background: rgba(139,92,246,0.12); color:#8b5cf6;">{{ $totalDiscount }} Log</span>
            </div>
            <small class="text-muted-c">Log pembuatan diskon persentase/potongan harga dan periode berlaku.</small>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 5: Voucher -->
  <div class="col-md-4">
    <a href="{{ route('admin.history.voucher.index') }}" class="text-decoration-none">
      <div class="card h-100 p-4 rounded-4 border-0 shadow-sm card-report-hover" 
           style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3 flex-shrink-0" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
            <i class="bi bi-ticket-perforated fs-3"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <h6 class="fw-bold mb-0" style="color: var(--text-primary);">Voucher</h6>
              <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">{{ $totalVoucher }} Log</span>
            </div>
            <small class="text-muted-c">Audit penerbitan kupon, limit klaim kasir, dan validasi minimum transaksi.</small>
          </div>
        </div>
      </div>
    </a>
  </div>
</div>
@endsection

@push('styles')
<style>
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
