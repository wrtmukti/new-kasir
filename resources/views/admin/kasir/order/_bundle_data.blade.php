<div class="table-responsive">
  <table class="table-modern">
    <thead>
      <tr>
        <th>Paket</th>
        <th>Isi</th>
        <th style="width:140px;">Harga</th>
        <th style="width:120px;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($bundles as $bundle)
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
        <tr>
          <td class="cell-primary">
            <div class="d-flex align-items-center gap-2">
              @if($bundle->bundle_image)
                <img src="{{ asset('storage/' . $bundle->bundle_image) }}" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:var(--radius-sm);flex-shrink:0;">
              @else
                <span style="width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-sm);background:var(--bg-elevated-2);flex-shrink:0;"><i class="bi bi-gift" style="font-size:1rem;color:var(--text-muted);"></i></span>
              @endif
              <div>
                <div style="font-weight:500;line-height:1.3;">{{ $bundle->bundle_name }}</div>
                <small class="text-muted-c" style="font-size:0.75rem;">{{ $bundle->bundle_code ?? '-' }}</small>
              </div>
            </div>
          </td>
          <td>
            <div style="display:flex;flex-wrap:wrap;gap:0.3rem;">
              @foreach($bundle->items as $item)
                <span class="chip-tag" style="font-size:0.68rem;background:var(--bg-elevated-2);border:1px solid var(--border-subtle);color:var(--text-secondary);">
                  {{ $item->product?->product_name ?? 'Produk' }} <strong>x{{ $item->quantity }}</strong>
                </span>
              @endforeach
            </div>
          </td>
          <td class="text-mono" style="font-weight:600;color:var(--accent-1);">Rp {{ number_format($bundle->bundle_price, 0) }}</td>
          <td>
            <button type="button" class="btn btn-primary-grad btn-sm btn-add-bundle"
              data-bundle-id="{{ $bundle->bundle_id }}"
              data-bundle-name="{{ $bundle->bundle_name }}"
              data-bundle-price="{{ $bundle->bundle_price }}"
              data-bundle-items='{{ json_encode($bundleItems) }}'>
              <i class="bi bi-cart-plus me-1"></i>Pesan
            </button>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="text-center text-muted-c py-4">Belum ada paket bundle aktif.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
