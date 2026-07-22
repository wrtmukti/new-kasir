@forelse($products as $product)
<tr> {{-- no row-clickable di halaman order --}}
  <td class="cell-primary">
    <div class="d-flex align-items-center gap-2">
      @if($product->product_image)
        <img src="{{ asset('storage/' . $product->product_image) }}" alt=""
             style="width:36px;height:36px;object-fit:cover;border-radius:var(--radius-sm);flex-shrink:0;">
      @else
        <span style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-sm);background:var(--bg-elevated-2);flex-shrink:0;">
          <i class="bi bi-image" style="font-size:0.95rem;color:var(--text-muted);"></i>
        </span>
      @endif
      <div>
        <div style="font-weight:500;line-height:1.3;">{{ $product->product_name }}</div>
        <small class="text-muted-c" style="font-size:0.75rem;">{{ $product->product_code ?? '-' }}</small>
      </div>
    </div>
  </td>
  <td>{{ $product->category?->category_name ?? '-' }}</td>
  <td class="text-mono">{{ $product->product_price ? 'Rp ' . number_format($product->product_price, 0) : '-' }}</td>
  <td>
    @if($product->product_status)
      <span class="pill pill-success">Aktif</span>
    @else
      <span class="pill pill-neutral">Nonaktif</span>
    @endif
  </td>
  <td>
    <button type="button" class="btn btn-primary-grad btn-sm btn-add-cart"
      data-id="{{ $product->product_id }}"
      data-name="{{ $product->product_name }}"
      data-price="{{ $product->product_price ?? 0 }}"
      data-image="{{ $product->product_image ? asset('storage/' . $product->product_image) : '' }}">
      <i class="bi bi-cart-plus me-1"></i>Pesan
    </button>
  </td>
</tr>
@empty
<tr>
  <td colspan="5" class="text-center text-muted-c py-4">Belum ada produk.</td>
</tr>
@endforelse
