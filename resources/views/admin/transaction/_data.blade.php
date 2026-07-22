@forelse($transactions as $trx)
<tr class="row-clickable" data-url="{{ route('admin.transaction.show', $trx) }}">
  <td class="text-mono" style="font-weight:500;">{{ $trx->transaction_code ?? '#' . $trx->transaction_id }}</td>
  <td>{{ optional($trx->created_at)->format('d M Y H:i') ?? '-' }}</td>
  <td class="text-mono">Rp {{ number_format($trx->transaction_subtotal ?? 0, 0) }}</td>
  <td class="text-mono">Rp {{ number_format($trx->transaction_grand_total ?? 0, 0) }}</td>
  <td>
    @if($trx->transaction_status == 'success')
      <span class="pill pill-success">Sukses</span>
    @elseif($trx->transaction_status == 'pending')
      <span class="pill pill-neutral">Pending</span>
    @elseif($trx->transaction_status == 'failed')
      <span class="pill pill-danger">Gagal</span>
    @else
      <span class="pill pill-neutral">{{ $trx->transaction_status }}</span>
    @endif
  </td>
</tr>
@empty
<tr>
  <td colspan="5" class="text-center text-muted-c py-4">Belum ada transaksi.</td>
</tr>
@endforelse
