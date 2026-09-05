@forelse($histories as $h)
<tr class="row-clickable" data-url="{{ route('admin.history.bundle.show', $h->bundle_history_id) }}">
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
  <td class="text-mono">{{ $h->bundle_code ?? '-' }}</td>
  <td class="cell-primary">{{ $h->bundle_name }}</td>
  <td class="text-mono">{{ $h->bundle_price ? 'Rp '.number_format($h->bundle_price, 0) : '-' }}</td>
  <td>
    @if($h->bundle_status)
      <span class="pill pill-success">Aktif</span>
    @else
      <span class="pill pill-neutral">Nonaktif</span>
    @endif
  </td>
  <td style="font-size:0.8rem;color:var(--text-muted);">{{ $h->changed_by ?? '-' }}</td>
</tr>
@empty
<tr>
  <td colspan="7" class="text-center text-muted-c py-4">Belum ada riwayat bundle.</td>
</tr>
@endforelse
