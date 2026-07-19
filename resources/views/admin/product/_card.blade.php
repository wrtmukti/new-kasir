@forelse($products as $product)
<div class="product-card" data-url="{{ route('admin.product.edit', $product) }}">
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
  </div>
  <div class="product-card-actions">
    <a href="{{ route('admin.product.edit', $product) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
      <i class="bi bi-pencil"></i>
    </a>
    <button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger btn-delete" data-url="{{ route('admin.product.destroy', $product) }}" title="Hapus">
      <i class="bi bi-trash"></i>
    </button>
  </div>
</div>
@empty
<div class="text-center text-muted-c py-5" style="grid-column:1/-1;">Belum ada data produk.</div>
@endforelse
