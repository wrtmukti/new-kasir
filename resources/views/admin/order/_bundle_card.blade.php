@forelse($bundles as $bundle)
<div class="bundle-card" style="background:var(--bg-elevated);border:1px solid var(--border-subtle);border-radius:var(--radius-md);overflow:hidden;transition:border-color 0.2s,transform 0.15s;">
  <div style="display:flex;align-items:stretch;gap:0;">
    {{-- Image --}}
    <div style="width:120px;min-height:120px;flex-shrink:0;overflow:hidden;background:var(--bg-elevated-2);display:flex;align-items:center;justify-content:center;">
      @if($bundle->bundle_image)
        <img src="{{ asset('storage/' . $bundle->bundle_image) }}" alt="{{ $bundle->bundle_name }}" style="width:100%;height:100%;object-fit:cover;">
      @else
        <i class="bi bi-gift" style="font-size:2rem;color:var(--text-muted);"></i>
      @endif
    </div>
    {{-- Body --}}
    <div style="flex:1;padding:0.85rem 1rem;">
      <div style="font-weight:600;font-size:0.95rem;color:var(--text-primary);margin-bottom:0.25rem;">{{ $bundle->bundle_name }}</div>
      @if($bundle->bundle_description)
        <div style="font-size:0.78rem;color:var(--text-muted);margin-bottom:0.5rem;">{{ $bundle->bundle_description }}</div>
      @endif
      {{-- Daftar produk di dalam bundle --}}
      <div style="display:flex;flex-wrap:wrap;gap:0.3rem;margin-bottom:0.5rem;">
        @foreach($bundle->items as $item)
          <span class="chip-tag" style="font-size:0.68rem;background:var(--bg-elevated-2);border:1px solid var(--border-subtle);color:var(--text-secondary);">
            {{ $item->product?->product_name ?? 'Produk' }} <strong>x{{ $item->quantity }}</strong>
          </span>
        @endforeach
      </div>
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;">
        <span style="font-weight:700;font-size:1.1rem;color:var(--accent-1);">Rp {{ number_format($bundle->bundle_price, 0) }}</span>
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
        <button type="button" class="btn btn-primary-grad btn-sm btn-add-bundle"
          data-bundle-id="{{ $bundle->bundle_id }}"
          data-bundle-name="{{ $bundle->bundle_name }}"
          data-bundle-price="{{ $bundle->bundle_price }}"
          data-bundle-items='{{ json_encode($bundleItems) }}'>
          <i class="bi bi-cart-plus me-1"></i>Pesan
        </button>
      </div>
    </div>
  </div>
</div>
@empty
<div class="text-center text-muted-c py-5">
  <i class="bi bi-gift" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.4;"></i>
  Belum ada paket bundle aktif.
</div>
@endforelse
