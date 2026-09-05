@forelse($products as $product)
<div class="bprod-card" data-product-id="{{ $product->product_id }}"
     data-product-name="{{ $product->product_name }}"
     data-product-code="{{ $product->product_code ?? '-' }}"
     data-product-price="{{ $product->product_price }}"
     data-category-id="{{ $product->category_id }}">
  <div class="bprod-card-img">
    @if($product->product_image)
      <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->product_name }}">
    @else
      <span class="bprod-card-img-placeholder"><i class="bi bi-image"></i></span>
    @endif
    <div class="bprod-card-check"><i class="bi bi-check-lg"></i></div>
  </div>
  <div class="bprod-card-body">
    <div class="bprod-card-name">{{ $product->product_name }}</div>
    <div class="bprod-card-code">{{ $product->product_code ?? '-' }}</div>
    <div class="bprod-card-price">Rp {{ number_format($product->product_price, 0, ',', '.') }}</div>
  </div>
</div>
@empty
<div class="text-center text-muted-c py-5" style="grid-column:1/-1;">
  <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:0.5rem;"></i>
  <span>Tidak ada produk ditemukan</span>
</div>
@endforelse
