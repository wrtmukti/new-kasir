@php
  $bundleItems = $bundle->items->map(function ($i) {
      return [
          'product_id' => $i->product_id,
          'product_name' => $i->product?->product_name ?? 'Produk',
          'product_price' => (float) ($i->product?->product_price ?? 0),
          'quantity' => (int) ($i->quantity ?? 1),
          'image' => $i->product?->product_image ? asset('storage/' . $i->product->product_image) : '',
      ];
  })->values()->toArray();
@endphp
<div class="guest-product-card guest-bundle-card" data-cat="bundle" data-name="{{ $bundle->bundle_name }}">
  <div class="guest-product-img-wrap">
    @if($bundle->bundle_image)
      <img src="{{ asset('storage/' . $bundle->bundle_image) }}" alt="{{ $bundle->bundle_name }}" class="guest-product-img">
    @else
      <div class="guest-product-img guest-product-img-placeholder"><i class="bi bi-gift"></i></div>
    @endif
  </div>
  <div class="guest-product-body">
    <div class="guest-product-name">{{ $bundle->bundle_name }}</div>
    @if($bundle->bundle_description)
      <div class="guest-product-desc">{{ \Illuminate\Support\Str::limit($bundle->bundle_description, 60) }}</div>
    @endif
    <div class="guest-bundle-chips">
      @foreach($bundle->items as $item)
        <span class="guest-bundle-chip">{{ $item->product?->product_name ?? 'Produk' }} x{{ $item->quantity }}</span>
      @endforeach
    </div>
    <div class="d-flex align-items-center justify-content-between mt-auto">
      <div class="guest-price-final">{{ 'Rp ' . number_format($bundle->bundle_price, 0) }}</div>
      <button type="button" class="guest-product-add btn-loading guest-bundle-add"
        data-bundle-id="{{ $bundle->bundle_id }}"
        data-bundle-name="{{ $bundle->bundle_name }}"
        data-bundle-price="{{ $bundle->bundle_price }}"
        data-bundle-items='{{ json_encode($bundleItems) }}'
        aria-label="Tambah bundle">
        <i class="bi bi-plus-lg"></i>
      </button>
    </div>
  </div>
</div>
