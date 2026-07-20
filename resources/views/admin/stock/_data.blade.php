@forelse($stocks as $stock)
<tr class="row-clickable" data-url="{{ route('admin.stock.show', $stock) }}">
  <td class="text-mono">{{ $stock->stock_code ?? '-' }}</td>
  <td class="cell-primary">{{ $stock->stock_name }}</td>
  <td>{{ $stock->stock_type ?? '-' }}</td>
  <td class="text-mono">{{ $stock->stock_unit ?? '-' }}</td>
  <td class="text-mono fw-semibold {{ $stock->stock_amount <= 0 ? 'text-danger' : '' }}">{{ number_format($stock->stock_amount) }}</td>
  <td class="text-mono">{{ $stock->stock_price ? 'Rp ' . number_format($stock->stock_price, 0) : '-' }}</td>
  <td>
    @if($stock->stock_status)
      <span class="pill pill-success">Aktif</span>
    @else
      <span class="pill pill-neutral">Nonaktif</span>
    @endif
  </td>
  <td>
    <div class="d-flex gap-1">
      <a href="{{ route('admin.stock.edit', $stock) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
        <i class="bi bi-pencil"></i>
      </a>
      <button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger btn-delete" data-url="{{ route('admin.stock.destroy', $stock) }}" title="Hapus">
        <i class="bi bi-trash"></i>
      </button>
    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="8" class="text-center text-muted-c py-4">Belum ada data stok.</td>
</tr>
@endforelse
