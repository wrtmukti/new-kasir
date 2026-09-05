<table class="table-modern" id="dataTable">
  <thead>
    <tr>
      <th class="ps-3">Nama Resep / Menu Terhubung</th>
      <th class="text-center">Jumlah Bahan</th>
      <th class="text-end">Estimasi COGS (Modal)</th>
      <th class="text-center">Target Food Cost %</th>
      <th class="text-end">Saran Harga Jual</th>
      <th class="text-end pe-3">Aksi</th>
    </tr>
  </thead>
  <tbody>
    @forelse($recipes as $item)
    <tr class="clickable-row" onclick="window.location='{{ route('admin.keuangan.cogs-recipe.show', $item) }}'" style="cursor:pointer;">
      <td class="ps-3">
        <div class="fw-bold" style="color: var(--text-primary);">{{ $item->recipe_name }}</div>
        @if($item->product)
          <small class="text-primary"><i class="bi bi-link-45deg me-1"></i>Terhubung: {{ $item->product->product_name }} (Rp {{ number_format($item->product->product_price) }})</small>
        @else
          <small class="text-muted-c">Resep Independen</small>
        @endif
      </td>
      <td class="text-center"><span class="chip-tag">{{ $item->items->count() }} Bahan</span></td>
      <td class="text-end fw-bold text-danger">Rp {{ number_format($item->estimated_cogs, 2, ',', '.') }}</td>
      <td class="text-center fw-semibold"><span class="badge" style="background: rgba(34, 211, 238, 0.15); color: var(--info);">{{ number_format($item->target_food_cost, 1) }}%</span></td>
      <td class="text-end fw-bold text-success">Rp {{ number_format($item->suggested_price, 0, ',', '.') }}</td>
      <td class="text-end pe-3" onclick="event.stopPropagation();">
        <div class="d-flex gap-1 justify-content-end">
          <a href="{{ route('admin.keuangan.cogs-recipe.show', $item) }}" class="btn btn-ghost btn-sm" title="Detail"><i class="bi bi-eye"></i></a>
          <a href="{{ route('admin.keuangan.cogs-recipe.edit', $item) }}" class="btn btn-ghost btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
          <button type="button" class="btn btn-ghost btn-sm text-danger btn-delete" data-id="{{ $item->cogs_recipe_id }}" title="Hapus"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="6" class="text-center py-4 text-muted-c">
        <i class="bi bi-book fs-2 d-block mb-2"></i>Belum ada resep standar HPP.
      </td>
    </tr>
    @endforelse
  </tbody>
</table>

<div class="px-3 py-2 d-flex justify-content-between align-items-center border-top" style="border-color: var(--border-subtle) !important;">
  <span class="text-muted-c" style="font-size:0.85rem;">
    Menampilkan {{ $recipes->firstItem() ?? 0 }} - {{ $recipes->lastItem() ?? 0 }} dari {{ $recipes->total() }} data
  </span>
  {{ $recipes->onEachSide(1)->links('vendor.pagination.modern') }}
</div>
