@forelse($histories as $h)
<tr class="row-clickable" data-url="{{ route('admin.history.product.show', $h->product_history_id) }}">
  <td class="text-mono" style="font-size:0.8rem;">{{ $h->created_at ? date('d/m/Y H:i', strtotime($h->created_at)) : '-' }}</td>
  <td>
    @if($h->action_type == 'create')
      <span class="pill pill-success" style="font-size:0.65rem;">Buat</span>
    @elseif($h->action_type == 'update')
      <span class="pill pill-info" style="font-size:0.65rem;">Ubah</span>
    @elseif($h->action_type == 'delete')
      <span class="pill pill-danger" style="font-size:0.65rem;">Hapus</span>
    @else
      <span class="pill pill-neutral" style="font-size:0.65rem;">{{ $h->action_type }}</span>
    @endif
  </td>
  <td class="text-mono">{{ $h->history_code ?? '-' }}</td>
  <td class="cell-primary">{{ $h->history_name }}</td>
  <td class="text-mono">{{ $h->history_price ? 'Rp '.number_format($h->history_price, 0) : '-' }}</td>
  <td class="text-mono">
    @if($h->history_discount)
      <span class="text-success">-{{ number_format($h->history_discount, $h->history_discount != (int)$h->history_discount ? 2 : 0) }}%</span>
    @else
      <span class="text-muted-c">-</span>
    @endif
  </td>
  <td>
    @if($h->history_status)
      <span class="pill pill-success">Aktif</span>
    @else
      <span class="pill pill-neutral">Nonaktif</span>
    @endif
  </td>
  <td style="font-size:0.8rem;color:var(--text-muted);">{{ $h->changed_by ?? '-' }}</td>
</tr>
@empty
<tr>
  <td colspan="8" class="text-center text-muted-c py-4">Belum ada riwayat produk.</td>
</tr>
@endforelse
