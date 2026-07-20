@extends('admin.layouts.app')

@section('title', 'Detail Produk')

@php $activeMenu = 'product' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Produk</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.product.index') }}">Produk</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>{{ $product->product_name }}</span>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.product.edit', $product) }}" class="btn btn-primary-grad">
      <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('admin.product.index') }}" class="btn btn-outline-soft">Kembali</a>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-info-circle me-2"></i>Informasi Produk</h6></div>
      <div class="card-body">
        <table class="table table-borderless mb-0" style="font-size:0.9rem;">
          <tr><td style="width:180px; color:var(--text-muted);">Perusahaan</td><td>{{ $product->company?->company_name ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Kode Produk</td><td class="text-mono">{{ $product->product_code ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Nama</td><td class="fw-semibold">{{ $product->product_name }}</td></tr>
          <tr><td style="color:var(--text-muted);">Gambar</td>
            <td>
              @if($product->product_image)
                <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->product_name }}"
                     style="width:80px;height:80px;object-fit:cover;border-radius:var(--radius-md);border:1px solid var(--border-subtle);">
              @else
                <span class="text-muted-c">-</span>
              @endif
            </td>
          </tr>
          <tr><td style="color:var(--text-muted);">Kategori</td><td>{{ $product->category?->category_name ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Harga</td><td class="text-mono">{{ $product->product_price ? 'Rp ' . number_format($product->product_price, 2) : '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Deskripsi</td><td>{{ $product->product_description ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Status</td>
            <td>
              @if($product->product_status)
                <span class="pill pill-success">Aktif</span>
              @else
                <span class="pill pill-neutral">Nonaktif</span>
              @endif
            </td>
          </tr>
          <tr><td style="color:var(--text-muted);">Dibuat</td><td>{{ $product->created_at ? $product->created_at->format('d M Y H:i') : '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Diupdate</td><td>{{ $product->updated_at ? $product->updated_at->format('d M Y H:i') : '-' }}</td></tr>
        </table>
      </div>
    </div>
  </div>

  {{-- BOM / Ingredients Card --}}
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header-flex">
        <h6><i class="bi bi-box-seam me-2"></i>Komposisi Stok</h6>
        <span class="selected-count" style="display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:22px;padding:0 6px;background:var(--accent-gradient);color:#fff;border-radius:var(--radius-full);font-size:0.72rem;font-weight:600;">
          {{ $product->stocks->count() }}
        </span>
      </div>
      <div class="card-body">
        @if($product->stocks->isNotEmpty())
          <div class="ingredient-list">
            @foreach($product->stocks as $stock)
              <div class="ingredient-item">
                <div class="ingredient-icon">
                  <i class="bi bi-dot"></i>
                </div>
                <div class="ingredient-info">
                  <div class="ingredient-name">{{ $stock->stock_name }}</div>
                  <div class="ingredient-meta">
                    <span class="stock-badge-sm">{{ $stock->stock_unit }}</span>
                    @if($stock->stock_code)
                      <span class="text-muted-c" style="font-size:0.7rem;">{{ $stock->stock_code }}</span>
                    @endif
                  </div>
                </div>
                <div class="ingredient-qty">
                  <span class="qty-value">{{ $stock->pivot->quantity }}</span>
                  <span class="qty-unit">{{ $stock->stock_unit }}</span>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center text-muted-c py-4">
            <i class="bi bi-box-seam" style="font-size:1.5rem;display:block;margin-bottom:0.5rem;"></i>
            <span style="font-size:0.85rem;">Tidak ada komposisi stok</span>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.ingredient-list {
  display: flex;
  flex-direction: column;
  gap: 0;
}
.ingredient-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.65rem 0;
  border-bottom: 1px solid var(--border-subtle);
}
.ingredient-item:last-child {
  border-bottom: none;
}
.ingredient-icon {
  color: var(--accent-1);
  font-size: 1.1rem;
  opacity: 0.6;
  flex-shrink: 0;
}
.ingredient-info {
  flex: 1;
  min-width: 0;
}
.ingredient-name {
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--text-primary);
}
.ingredient-meta {
  display: flex;
  gap: 0.5rem;
  align-items: center;
  margin-top: 0.1rem;
}
.stock-badge-sm {
  display: inline-flex;
  align-items: center;
  padding: 0.1rem 0.45rem;
  background: var(--bg-elevated);
  border-radius: var(--radius-full);
  font-size: 0.65rem;
  color: var(--text-muted);
  border: 1px solid var(--border-subtle);
}
.ingredient-qty {
  text-align: right;
  flex-shrink: 0;
}
.qty-value {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--accent-1);
}
.qty-unit {
  display: block;
  font-size: 0.65rem;
  color: var(--text-muted);
  margin-top: -0.1rem;
}
.selected-count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 22px;
  height: 22px;
  padding: 0 6px;
  background: var(--accent-gradient);
  color: #fff;
  border-radius: var(--radius-full);
  font-size: 0.72rem;
  font-weight: 600;
}
</style>
@endpush
