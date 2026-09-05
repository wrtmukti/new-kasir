@forelse($discounts as $discount)
<tr class="row-clickable" data-url="{{ route('admin.discount.show', $discount) }}">
  <td class="cell-primary">
    <span>{{ $discount->discount_name }}</span>
  </td>
  <td>
    @if($discount->discount_type == 'percentage')
      <span class="pill pill-info">Persen</span>
    @else
      <span class="pill pill-warning">Nominal</span>
    @endif
  </td>
  <td>
    @if($discount->discount_type == 'percentage')
      {{ number_format($discount->discount_value, 0) }}%
    @else
      Rp{{ number_format($discount->discount_value, 0) }}
    @endif
  </td>
  <td>
    @if($discount->discount_max_amount)
      Rp{{ number_format($discount->discount_max_amount, 0) }}
    @else
      <span class="text-muted-c">-</span>
    @endif
  </td>
  <td>
    @if($discount->discount_status)
      <span class="pill pill-success">Aktif</span>
    @else
      <span class="pill pill-neutral">Nonaktif</span>
    @endif
  </td>
  <td style="font-size:0.8rem;">
    @if($discount->start_date)
      {{ $discount->start_date->format('d/m/Y') }}
      @if($discount->end_date)
        — {{ $discount->end_date->format('d/m/Y') }}
      @endif
    @else
      <span class="text-muted-c">-</span>
    @endif
  </td>
  <td>
    <div class="d-flex gap-1">
      <a href="{{ route('admin.discount.edit', $discount) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
        <i class="bi bi-pencil"></i>
      </a>
      <button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger btn-delete" data-url="{{ route('admin.discount.destroy', $discount) }}" title="Hapus">
        <i class="bi bi-trash"></i>
      </button>
    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="7" class="text-center text-muted-c py-4">Belum ada data diskon.</td>
</tr>
@endforelse
