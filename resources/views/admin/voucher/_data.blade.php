@forelse($vouchers as $voucher)
<tr class="row-clickable" data-url="{{ route('admin.voucher.show', $voucher) }}">
  <td class="cell-primary">
    <span class="text-mono fw-semibold">{{ $voucher->voucher_code }}</span>
  </td>
  <td>
    <span>{{ $voucher->voucher_name }}</span>
  </td>
  <td>
    @if($voucher->voucher_type == 'percentage')
      <span class="pill pill-info">Persen</span>
    @elseif($voucher->voucher_type == 'nominal')
      <span class="pill pill-warning">Nominal</span>
    @else
      <span class="pill pill-neutral">Free Item</span>
    @endif
  </td>
  <td>
    @if($voucher->voucher_type == 'percentage')
      {{ number_format($voucher->voucher_value, 0) }}%
    @elseif($voucher->voucher_type == 'nominal')
      Rp{{ number_format($voucher->voucher_value, 0) }}
    @else
      <span class="text-muted-c">-</span>
    @endif
  </td>
  <td>
    @if($voucher->voucher_min_purchase)
      Rp{{ number_format($voucher->voucher_min_purchase, 0) }}
    @else
      <span class="text-muted-c">-</span>
    @endif
  </td>
  <td>
    @if($voucher->voucher_status && (!$voucher->voucher_end_date || $voucher->voucher_end_date >= now()))
      <span class="pill pill-success">Aktif</span>
    @else
      <span class="pill pill-neutral">Nonaktif</span>
    @endif
  </td>
  <td>
    <div class="d-flex gap-1">
      <a href="{{ route('admin.voucher.edit', $voucher) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
        <i class="bi bi-pencil"></i>
      </a>
      <button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger btn-delete" data-url="{{ route('admin.voucher.destroy', $voucher) }}" title="Hapus">
        <i class="bi bi-trash"></i>
      </button>
    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="7" class="text-center text-muted-c py-4">Belum ada data voucher.</td>
</tr>
@endforelse
