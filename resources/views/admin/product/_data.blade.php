@forelse($products as $product)
<tr class="row-clickable" data-url="{{ route('admin.product.edit', $product) }}">
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
    @if($product->relationLoaded('stocks') && $product->stocks->isNotEmpty())
      <span class="stock-pill" title="Jumlah bahan baku">
        <i class="bi bi-box-seam me-1" style="font-size:0.65rem;"></i>{{ $product->stocks->count() }}
      </span>
    @else
      <span class="text-muted-c" style="font-size:0.75rem;">-</span>
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
    <div class="d-flex gap-1">
      <a href="{{ route('admin.product.edit', $product) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
        <i class="bi bi-pencil"></i>
      </a>
      <button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger btn-delete" data-url="{{ route('admin.product.destroy', $product) }}" title="Hapus">
        <i class="bi bi-trash"></i>
      </button>
    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="6" class="text-center text-muted-c py-4">Belum ada data produk.</td>
</tr>
@endforelse
