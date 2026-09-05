<table class="table-modern" id="dataTable">
  <thead>
    <tr>
      <th class="ps-3">Kode / Nama Bahan</th>
      <th>Satuan</th>
      <th class="text-end">Stok Fisik</th>
      <th class="text-end">Harga Beli</th>
      <th class="text-center">Susut (%)</th>
      <th class="text-end">Harga Efektif</th>
      <th class="text-end pe-3">Aksi</th>
    </tr>
  </thead>
  <tbody>
    @forelse($rawMaterials as $item)
    <tr class="clickable-row" onclick="window.location='{{ route('admin.keuangan.cogs-raw-material.show', $item) }}'" style="cursor:pointer;">
      <td class="ps-3">
        <div class="fw-bold" style="color: var(--text-primary);">{{ $item->name }}</div>
        <small class="text-muted-c">{{ $item->raw_material_code }}</small>
      </td>
      <td><span class="chip-tag">{{ $item->unit }}</span></td>
      <td class="text-end fw-semibold" style="color: var(--text-primary);">
        {{ number_format($item->amount, 2, ',', '.') }}
        @if($item->min_amount > 0 && $item->amount <= $item->min_amount)
          <i class="bi bi-exclamation-triangle-fill text-warning ms-1" title="Stok berada di bawah minimal ({{ number_format($item->min_amount) }})"></i>
        @endif
      </td>
      <td class="text-end" style="color: var(--text-secondary);">Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}</td>
      <td class="text-center"><span class="badge" style="background: rgba(248, 113, 113, 0.15); color: var(--danger);">{{ number_format($item->loss_percent, 1) }}%</span></td>
      <td class="text-end fw-bold text-success">Rp {{ number_format($item->effective_price, 2, ',', '.') }}</td>
      <td class="text-end pe-3" onclick="event.stopPropagation();">
        <div class="d-flex gap-1 justify-content-end">
          <button type="button" class="btn btn-ghost btn-sm text-warning btn-opname" data-id="{{ $item->cogs_raw_material_id }}" data-name="{{ $item->name }}" data-amount="{{ $item->amount }}" data-unit="{{ $item->unit }}" title="Stock Opname"><i class="bi bi-clipboard-check"></i> Opname</button>
          <a href="{{ route('admin.keuangan.cogs-raw-material.show', $item) }}" class="btn btn-ghost btn-sm" title="Detail & History"><i class="bi bi-eye"></i></a>
          <a href="{{ route('admin.keuangan.cogs-raw-material.edit', $item) }}" class="btn btn-ghost btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
          <button type="button" class="btn btn-ghost btn-sm text-danger btn-delete" data-id="{{ $item->cogs_raw_material_id }}" title="Hapus"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="7" class="text-center py-4 text-muted-c">
        <i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada data bahan mentah.
      </td>
    </tr>
    @endforelse
  </tbody>
</table>

<div class="px-3 py-2 d-flex justify-content-between align-items-center border-top" style="border-color: var(--border-subtle) !important;">
  <span class="text-muted-c" style="font-size:0.85rem;">
    Menampilkan {{ $rawMaterials->firstItem() ?? 0 }} - {{ $rawMaterials->lastItem() ?? 0 }} dari {{ $rawMaterials->total() }} data
  </span>
  {{ $rawMaterials->onEachSide(1)->links('vendor.pagination.modern') }}
</div>
