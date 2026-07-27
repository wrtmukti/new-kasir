@forelse($bundles as $bundle)
<div class="bundle-card" data-url="{{ route('admin.bundle.show', $bundle) }}">
  <div class="bundle-card-img">
    @if($bundle->bundle_image)
      <img src="{{ asset('storage/' . $bundle->bundle_image) }}" alt="{{ $bundle->bundle_name }}">
    @else
      <span class="bundle-card-img-placeholder">
        <i class="bi bi-gift"></i>
      </span>
    @endif
  </div>
  <div class="bundle-card-body">
    <div class="bundle-card-name">{{ $bundle->bundle_name }}</div>
    <div class="bundle-card-meta">
      <span class="bundle-card-code">{{ $bundle->bundle_code ?? '-' }}</span>
      @if($bundle->bundle_status)
        <span class="pill pill-success">Aktif</span>
      @else
        <span class="pill pill-neutral">Nonaktif</span>
      @endif
    </div>
    <div class="bundle-card-items">{{ $bundle->items->count() }} produk</div>
    <div class="bundle-card-price">Rp {{ number_format($bundle->bundle_price, 0, ',', '.') }}</div>
  </div>
  <div class="bundle-card-actions">
    <a href="{{ route('admin.bundle.edit', $bundle) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
      <i class="bi bi-pencil"></i>
    </a>
    <button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger btn-delete" data-url="{{ route('admin.bundle.destroy', $bundle) }}" title="Hapus">
      <i class="bi bi-trash"></i>
    </button>
  </div>
</div>
@empty
<div class="text-center text-muted-c py-5" style="grid-column:1/-1;">Belum ada data paket bundle.</div>
@endforelse
