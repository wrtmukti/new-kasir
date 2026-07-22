@if($bundles->count())
<div class="table-responsive">
  <table class="table-modern">
    <thead>
      <tr>
        <th>Kode</th>
        <th>Nama Paket</th>
        <th>Harga</th>
        <th>Item</th>
        <th>Status</th>
        <th style="width:120px;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach($bundles as $bundle)
      <tr class="row-clickable" data-url="{{ route('admin.bundle.show', $bundle) }}">
        <td class="text-mono fw-semibold">{{ $bundle->bundle_code ?? '-' }}</td>
        <td class="fw-semibold">{{ $bundle->bundle_name }}</td>
        <td class="text-mono">Rp {{ number_format($bundle->bundle_price, 0, ',', '.') }}</td>
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
            <a href="{{ route('admin.bundle.edit', $bundle) }}" class="btn btn-sm btn-outline-soft" title="Edit">
              <i class="bi bi-pencil"></i>
            </a>
            <button type="button" class="btn btn-sm btn-outline-soft btn-delete text-danger" data-url="{{ route('admin.bundle.destroy', $bundle) }}" title="Hapus">
              <i class="bi bi-trash3"></i>
            </button>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="d-flex justify-content-center mt-3">
  {{ $bundles->links('vendor.pagination.modern') }}
</div>
@else
<div class="text-center py-5">
  <i class="bi bi-gift" style="font-size:2.5rem;color:var(--text-muted);display:block;margin-bottom:0.75rem;"></i>
  <span style="color:var(--text-muted);font-size:0.95rem;">Belum ada paket bundle.</span>
</div>
@endif
