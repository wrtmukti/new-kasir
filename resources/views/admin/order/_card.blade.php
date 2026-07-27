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
    <div class="product-card-price">
      @php
        $discPct = $product->product_discount_type === 'percentage' ? (float)($product->product_discount_value ?? 0) : 0;
        $discNom = $product->product_discount_type === 'nominal' ? (float)($product->product_discount_value ?? 0) : 0;
        $discAmt = $discPct > 0 ? $product->product_price * $discPct / 100 : ($discNom > 0 ? min($discNom, $product->product_price) : 0);
        $priceDisc = $product->product_price - $discAmt;
      @endphp
      @if($discAmt > 0)
        <span style="text-decoration:line-through;color:var(--text-muted);font-size:0.75rem;">Rp {{ number_format($product->product_price, 0) }}</span>
        <span style="color:var(--danger);font-weight:700;display:block;font-size:1.1rem;">Rp {{ number_format($priceDisc, 0) }}</span>
        <span class="pill pill-danger" style="font-size:0.6rem;padding:0.1rem 0.35rem;">-{{ $discPct > 0 ? $discPct.'%' : 'Rp'.number_format($discAmt,0) }}</span>
      @else
        Rp {{ number_format($product->product_price, 0) }}
      @endif
    </div>
    <button type="button" class="btn btn-primary-grad btn-sm btn-add-cart mt-2 w-100"
      data-id="{{ $product->product_id }}"
      data-name="{{ $product->product_name }}"
      data-price="{{ $product->product_price ?? 0 }}"
      data-discount-type="{{ $product->product_discount_type ?? '' }}"
      data-discount-value="{{ $product->product_discount_value ?? 0 }}"
      data-image="{{ $product->product_image ? asset('storage/' . $product->product_image) : '' }}">
      <i class="bi bi-cart-plus me-1"></i>Pesan
    </button>
  </div>
</div>
@empty
<div class="text-center text-muted-c py-5" style="grid-column:1/-1;">Belum ada produk.</div>
@endforelse
