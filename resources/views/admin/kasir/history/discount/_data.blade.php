@forelse($histories as $h)
<tr class="row-clickable" data-url="{{ route('admin.history.discount.show', $h->discount_history_id) }}">
  <td class="text-mono" style="font-size:0.8rem;">{{ $h->created_at ? date('d/m/Y H:i', strtotime($h->created_at)) : '-' }}</td>
  <td>
    @if($h->reason == 'create')
      <span class="pill pill-success" style="font-size:0.65rem;">Buat</span>
    @elseif($h->reason == 'update')
      <span class="pill pill-info" style="font-size:0.65rem;">Ubah</span>
    @elseif($h->reason == 'delete')
      <span class="pill pill-danger" style="font-size:0.65rem;">Hapus</span>
    @else
      <span class="pill pill-neutral" style="font-size:0.65rem;">{{ $h->reason }}</span>
    @endif
  </td>
  <td class="cell-primary">{{ $h->discount_name }}</td>
  <td>
    @if($h->discount_type == 'percentage')
      <span class="pill pill-info" style="font-size:0.65rem;">Persen</span>
    @else
      <span class="pill pill-warning" style="font-size:0.65rem;">Nominal</span>
    @endif
  </td>
  <td class="text-mono">
    @if($h->discount_type == 'percentage')
      {{ number_format($h->discount_value, 0) }}%
    @else
      Rp{{ number_format($h->discount_value, 0) }}
    @endif
  </td>
  <td class="text-mono" style="font-size:0.8rem;">
    @if($h->start_date)
      {{ date('d/m/Y', strtotime($h->start_date)) }}
      @if($h->end_date)
        — {{ date('d/m/Y', strtotime($h->end_date)) }}
      @endif
    @else
      -
    @endif
  </td>
  <td style="font-size:0.8rem;color:var(--text-muted);">{{ $h->changed_by ?? '-' }}</td>
</tr>
@empty
<tr>
  <td colspan="7" class="text-center text-muted-c py-4">Belum ada riwayat diskon.</td>
</tr>
@endforelse
