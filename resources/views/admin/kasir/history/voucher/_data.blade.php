@forelse($histories as $h)
<tr class="row-clickable" data-url="{{ route('admin.history.voucher.show', $h->history_id) }}">
  <td class="text-mono" style="font-size:0.8rem;">{{ $h->created_at ? date('d/m/Y H:i', strtotime($h->created_at)) : '-' }}</td>
  <td>
    @if($h->action == 'create')
      <span class="pill pill-success" style="font-size:0.65rem;">Buat</span>
    @elseif($h->action == 'update')
      <span class="pill pill-info" style="font-size:0.65rem;">Ubah</span>
    @elseif($h->action == 'delete')
      <span class="pill pill-danger" style="font-size:0.65rem;">Hapus</span>
    @else
      <span class="pill pill-neutral" style="font-size:0.65rem;">{{ $h->action }}</span>
    @endif
  </td>
  <td class="text-mono">{{ $h->voucher_code ?? '-' }}</td>
  <td class="cell-primary">{{ $h->voucher_name }}</td>
  <td>
    @if($h->voucher_type == 'percentage')
      <span class="pill pill-info" style="font-size:0.65rem;">Persen</span>
    @elseif($h->voucher_type == 'nominal')
      <span class="pill pill-warning" style="font-size:0.65rem;">Nominal</span>
    @elseif($h->voucher_type == 'free_item')
      <span class="pill pill-neutral" style="font-size:0.65rem;">Free Item</span>
    @else
      <span class="pill pill-neutral" style="font-size:0.65rem;">{{ $h->voucher_type ?? '-' }}</span>
    @endif
  </td>
  <td class="text-mono">
    @if($h->voucher_type == 'percentage')
      -{{ number_format($h->voucher_value, $h->voucher_value != (int)$h->voucher_value ? 2 : 0) }}%
    @else
      {{ $h->voucher_value ? 'Rp '.number_format($h->voucher_value, 0) : '-' }}
    @endif
  </td>
  <td>
    @if($h->voucher_status)
      <span class="pill pill-success">Aktif</span>
    @else
      <span class="pill pill-neutral">Nonaktif</span>
    @endif
  </td>
  <td style="font-size:0.8rem;color:var(--text-muted);">{{ $h->user_id ?? $h->created_by ?? '-' }}</td>
</tr>
@empty
<tr>
  <td colspan="8" class="text-center text-muted-c py-4">Belum ada riwayat voucher.</td>
</tr>
@endforelse
