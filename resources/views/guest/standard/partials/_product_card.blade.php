@php
  $activeDisc = $product->activeDiscount->first();
  $discType = $activeDisc?->discount_type;
  $discVal = $activeDisc ? (float) ($activeDisc->discount_value ?? 0) : 0;
  $discAmt = $discType === 'percentage' && $discVal > 0
    ? min(round((float) $product->product_price * $discVal / 100), (float) $product->product_price)
    : ($discType === 'nominal' && $discVal > 0
        ? min($discVal, (float) $product->product_price)
        : 0);
  $priceFinal = (float) $product->product_price - $discAmt;
@endphp
<div class="guest-product-card" data-cat="{{ $product->category_id }}" data-name="{{ $product->product_name }}">
  <div class="guest-product-img-wrap">
    @if($product->product_image)
      <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->product_name }}" class="guest-product-img">
    @else
      <div class="guest-product-img guest-product-img-placeholder">
        <i class="bi bi-image"></i>
      </div>
    @endif
    @if($discAmt > 0)
      <span class="guest-disc-badge">-{{ $discType === 'percentage' ? round($discVal) . '%' : 'Rp' . number_format($discAmt, 0) }}</span>
    @endif
  </div>
  <div class="guest-product-body">
    <div class="guest-product-name">{{ $product->product_name }}</div>
    @if($product->product_description)
      <div class="guest-product-desc">{{ \Illuminate\Support\Str::limit($product->product_description, 60) }}</div>
    @endif
    <div class="d-flex align-items-center justify-content-between mt-auto">
      <div>
        @if($discAmt > 0)
          <div class="guest-price-strike">{{ 'Rp ' . number_format($product->product_price, 0) }}</div>
          <div class="guest-price-final">{{ 'Rp ' . number_format($priceFinal, 0) }}</div>
        @else
          <div class="guest-price-final">{{ 'Rp ' . number_format($product->product_price, 0) }}</div>
        @endif
      </div>
      <button type="button" class="guest-product-add btn-loading" data-id="{{ $product->product_id }}" aria-label="Tambah">
        <i class="bi bi-plus-lg"></i>
      </button>
    </div>
  </div>
</div>
