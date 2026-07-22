@forelse($products as $product)
<div class="product-card">
  <div class="product-card-img">
    @if($product->product_image)
      <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->product_name }}">
    @else
      <span class="product-card-img-placeholder">
        <i class="bi bi-image"></i>
      </span>
    @endif
  </div>
  <div class="product-card-body">
    <div class="product-card-name">{{ $product->product_name }}</div>
    <div class="product-card-meta">
      <span class="product-card-code">{{ $product->product_code ?? '-' }}</span>
      @if($product->product_status)
        <span class="pill pill-success">Aktif</span>
      @else
        <span class="pill pill-neutral">Nonaktif</span>
      @endif
    </div>
    <div class="product-card-category">{{ $product->category?->category_name ?? '-' }}</div>
    <div class="product-card-price">{{ $product->product_price ? 'Rp ' . number_format($product->product_price, 0) : '-' }}</div>
    <button type="button" class="btn btn-primary-grad btn-sm btn-add-cart mt-2 w-100"
      data-id="{{ $product->product_id }}"
      data-name="{{ $product->product_name }}"
      data-price="{{ $product->product_price ?? 0 }}"
      data-image="{{ $product->product_image ? asset('storage/' . $product->product_image) : '' }}">
      <i class="bi bi-cart-plus me-1"></i>Pesan
    </button>
  </div>
</div>
@empty
<div class="text-center text-muted-c py-5" style="grid-column:1/-1;">Belum ada produk.</div>
@endforelse
