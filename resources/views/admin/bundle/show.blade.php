@extends('admin.layouts.app')

@section('title', 'Detail Paket')

@php $activeMenu = 'bundle' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Paket</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.bundle.index') }}">Paket</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>{{ $bundle->bundle_name }}</span>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.bundle.edit', $bundle) }}" class="btn btn-primary-grad">
      <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('admin.bundle.index') }}" class="btn btn-outline-soft">Kembali</a>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-info-circle me-2"></i>Informasi Paket</h6></div>
      <div class="card-body p-0">
        <table class="detail-table">
          <tr>
            <td class="detail-label">Perusahaan</td>
            <td class="detail-value">{{ $bundle->company?->company_name ?? '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Kode Paket</td>
            <td class="detail-value text-mono">{{ $bundle->bundle_code ?? '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Nama</td>
            <td class="detail-value fw-semibold">{{ $bundle->bundle_name }}</td>
          </tr>
          <tr>
            <td class="detail-label">Gambar</td>
            <td class="detail-value">
              @if($bundle->bundle_image)
                <img src="{{ asset('storage/' . $bundle->bundle_image) }}" alt="{{ $bundle->bundle_name }}"
                     style="width:80px;height:80px;object-fit:cover;border-radius:var(--radius-md);border:1px solid var(--border-subtle);">
              @else
                <span class="text-muted-c">-</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Deskripsi</td>
            <td class="detail-value">{{ $bundle->bundle_description ?? '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Harga Paket</td>
            <td class="detail-value text-mono fw-semibold">Rp {{ number_format($bundle->bundle_price, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="detail-label">Status</td>
            <td class="detail-value">
              @if($bundle->bundle_status)
                <span class="pill pill-success">Aktif</span>
              @else
                <span class="pill pill-neutral">Nonaktif</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Dibuat</td>
            <td class="detail-value">{{ $bundle->created_at ? $bundle->created_at->format('d M Y H:i') : '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Diupdate</td>
            <td class="detail-value">{{ $bundle->updated_at ? $bundle->updated_at->format('d M Y H:i') : '-' }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  {{-- Products in bundle --}}
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header-flex">
        <h6><i class="bi bi-gift me-2"></i>Produk dalam Paket</h6>
        <span class="selected-count" style="display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:22px;padding:0 6px;background:var(--accent-gradient);color:#fff;border-radius:var(--radius-full);font-size:0.72rem;font-weight:600;">
          {{ $bundle->items->count() }}
        </span>
      </div>
      <div class="card-body p-0">
        @if($bundle->items->isNotEmpty())
          @php
            $totalNormal = $bundle->items->sum(fn($i) => ($i->product->product_price ?? 0) * $i->quantity);
            $hemat = $totalNormal - $bundle->bundle_price;
          @endphp
          <div class="bundle-product-list">
            @foreach($bundle->items as $item)
              <div class="bundle-product-item">
                <div class="bundle-product-icon"><i class="bi bi-dot"></i></div>
                <div class="bundle-product-info">
                  <div class="bundle-product-name">{{ $item->product->product_name ?? '-' }}</div>
                  <div class="bundle-product-meta">
                    <span class="stock-badge-sm">@ Rp {{ number_format($item->price_snapshot ?? 0, 0, ',', '.') }}</span>
                  </div>
                </div>
                <div class="bundle-product-qty">
                  <span class="qty-value">{{ $item->quantity }}x</span>
                  <span class="qty-subtotal text-muted-c" style="font-size:0.7rem;">Rp {{ number_format(($item->price_snapshot ?? 0) * $item->quantity, 0, ',', '.') }}</span>
                </div>
              </div>
            @endforeach
          </div>

          {{-- Price summary --}}
          <div class="bundle-price-summary">
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted-c" style="font-size:0.85rem;">Total normal:</span>
              <span class="text-mono">Rp {{ number_format($totalNormal, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
              <span class="text-muted-c" style="font-size:0.85rem;">Harga paket:</span>
              <span class="text-mono fw-semibold text-success">Rp {{ number_format($bundle->bundle_price, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
              <span class="text-muted-c" style="font-size:0.85rem;">Hemat:</span>
              <span class="text-mono fw-semibold text-danger">Rp {{ number_format(max(0, $hemat), 0, ',', '.') }}</span>
            </div>
          </div>
        @else
          <div class="text-center text-muted-c py-4">
            <i class="bi bi-gift" style="font-size:1.5rem;display:block;margin-bottom:0.5rem;"></i>
            <span style="font-size:0.85rem;">Tidak ada produk dalam paket</span>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.bundle-product-list { display:flex; flex-direction:column; }
.bundle-product-item { display:flex; align-items:center; gap:0.75rem; padding:0.65rem 1.35rem; border-bottom:1px solid var(--border-subtle); }
.bundle-product-item:last-child { border-bottom:none; }
.bundle-product-icon { color:var(--accent-1); font-size:1.1rem; opacity:0.6; flex-shrink:0; }
.bundle-product-info { flex:1; min-width:0; }
.bundle-product-name { font-size:0.88rem; font-weight:600; color:var(--text-primary); }
.bundle-product-meta { display:flex; gap:0.5rem; margin-top:0.1rem; }
.stock-badge-sm { display:inline-flex; align-items:center; padding:0.1rem 0.45rem; background:var(--bg-elevated); border-radius:var(--radius-full); font-size:0.65rem; color:var(--text-muted); border:1px solid var(--border-subtle); }
.bundle-product-qty { text-align:right; flex-shrink:0; }
.qty-value { font-size:1rem; font-weight:700; color:var(--accent-1); display:block; }
.bundle-price-summary { padding:0.85rem 1.35rem; border-top:1px solid var(--border-subtle); background:var(--bg-elevated); border-radius:0 0 var(--radius-md) var(--radius-md); }
</style>
@endpush
