@forelse($bundles as $bundle)
<tr class="row-clickable" data-url="{{ route('admin.bundle.show', $bundle) }}">
  <td class="cell-primary">
    <div class="d-flex align-items-center gap-2">
      @if($bundle->bundle_image)
        <img src="{{ asset('storage/' . $bundle->bundle_image) }}" alt=""
             style="width:36px;height:36px;object-fit:cover;border-radius:var(--radius-sm);flex-shrink:0;">
      @else
        <span style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-sm);background:var(--bg-elevated-2);flex-shrink:0;">
          <i class="bi bi-gift" style="font-size:0.95rem;color:var(--text-muted);"></i>
        </span>
      @endif
      <div>
        <div style="font-weight:500;line-height:1.3;">{{ $bundle->bundle_name }}</div>
        <small class="text-muted-c" style="font-size:0.75rem;">{{ $bundle->bundle_code ?? '-' }}</small>
      </div>
    </div>
  </td>
  <td class="text-mono fw-semibold">Rp {{ number_format($bundle->bundle_price, 0, ',', '.') }}</td>
  <td>
    <span class="pill pill-info">{{ $bundle->items->count() }} produk</span>
  </td>
  <td>
    @if($bundle->bundle_status)
      <span class="pill pill-success">Aktif</span>
    @else
      <span class="pill pill-neutral">Nonaktif</span>
    @endif
  </td>
  <td>
    <div class="d-flex gap-1">
      <a href="{{ route('admin.bundle.edit', $bundle) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
        <i class="bi bi-pencil"></i>
      </a>
      <button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger btn-delete" data-url="{{ route('admin.bundle.destroy', $bundle) }}" title="Hapus">
        <i class="bi bi-trash"></i>
      </button>
    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="5" class="text-center text-muted-c py-4">Belum ada data paket bundle.</td>
</tr>
@endforelse
