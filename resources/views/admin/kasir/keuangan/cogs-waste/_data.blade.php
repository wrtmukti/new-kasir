<table class="table-modern" id="dataTable">
  <thead>
    <tr>
      <th class="ps-3">Tanggal Kejadian</th>
      <th>Bahan Mentah</th>
      <th class="text-center">Alasan Waste</th>
      <th class="text-end">Jumlah Terbuang</th>
      <th class="text-end">Nilai Kerugian (Rp)</th>
      <th class="text-end pe-3">Aksi</th>
    </tr>
  </thead>
  <tbody>
    @forelse($wasteLogs as $item)
    <tr>
      <td class="ps-3 fw-semibold" style="color: var(--text-primary);">{{ \Carbon\Carbon::parse($item->loss_date)->format('d/m/Y') }}</td>
      <td class="fw-bold" style="color: var(--text-primary);">{{ $item->rawMaterial->name ?? 'Bahan Terhapus' }}</td>
      <td class="text-center">
        @if($item->reason == 'Basi/Rotten' || $item->reason == 'Basi')
          <span class="badge" style="background: rgba(248, 113, 113, 0.15); color: var(--danger);">Basi / Rotten</span>
        @elseif($item->reason == 'Expired')
          <span class="badge" style="background: rgba(251, 191, 36, 0.15); color: var(--warning);">Expired</span>
        @else
          <span class="chip-tag">{{ $item->reason }}</span>
        @endif
      </td>
      <td class="text-end fw-semibold text-danger">
        -{{ number_format($item->qty_lost, 2, ',', '.') }} {{ $item->rawMaterial->unit ?? '' }}
      </td>
      <td class="text-end fw-bold text-danger">Rp {{ number_format($item->waste_cost, 2, ',', '.') }}</td>
      <td class="text-end pe-3">
        <button type="button" class="btn btn-ghost btn-sm text-danger btn-delete" data-id="{{ $item->cogs_waste_log_id }}" title="Hapus"><i class="bi bi-trash"></i></button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="6" class="text-center py-4 text-muted-c">
        <i class="bi bi-trash3 fs-2 d-block mb-2"></i>Belum ada riwayat bahan terbuang/basi.
      </td>
    </tr>
    @endforelse
  </tbody>
</table>

<div class="px-3 py-2 d-flex justify-content-between align-items-center border-top" style="border-color: var(--border-subtle) !important;">
  <span class="text-muted-c" style="font-size:0.85rem;">
    Menampilkan {{ $wasteLogs->firstItem() ?? 0 }} - {{ $wasteLogs->lastItem() ?? 0 }} dari {{ $wasteLogs->total() }} data
  </span>
  {{ $wasteLogs->onEachSide(1)->links('vendor.pagination.modern') }}
</div>
