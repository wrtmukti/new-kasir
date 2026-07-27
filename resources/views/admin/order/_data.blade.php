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
  <td class="text-mono">
    @php
      $discPct = $product->product_discount_type === 'percentage' ? (float)($product->product_discount_value ?? 0) : 0;
      $discNom = $product->product_discount_type === 'nominal' ? (float)($product->product_discount_value ?? 0) : 0;
      $discAmt = $discPct > 0 ? $product->product_price * $discPct / 100 : ($discNom > 0 ? min($discNom, $product->product_price) : 0);
      $priceDisc = $product->product_price - $discAmt;
    @endphp
    @if($discAmt > 0)
      <span style="text-decoration:line-through;color:var(--text-muted);font-size:0.75rem;">Rp {{ number_format($product->product_price, 0) }}</span>
      <span style="color:var(--danger);font-weight:600;margin-left:4px;">Rp {{ number_format($priceDisc, 0) }}</span>
      <span class="pill pill-danger" style="font-size:0.6rem;padding:0.1rem 0.35rem;vertical-align:middle;margin-left:4px;">-{{ $discPct > 0 ? $discPct.'%' : 'Rp'.number_format($discAmt,0) }}</span>
    @else
      Rp {{ number_format($product->product_price, 0) }}
    @endif
  </td>
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
      data-discount-type="{{ $product->product_discount_type ?? '' }}"
      data-discount-value="{{ $product->product_discount_value ?? 0 }}"
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
