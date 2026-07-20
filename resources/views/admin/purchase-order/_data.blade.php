@forelse($orders as $order)
<tr class="row-clickable" data-url="{{ route('admin.purchase-order.show', $order) }}">
  <td class="text-mono fw-semibold">{{ $order->po_code }}</td>
  <td>{{ $order->supplier?->supplier_name ?? '-' }}</td>
  <td style="font-size:0.85rem;">{{ $order->po_date ? $order->po_date->format('d M Y') : '-' }}</td>
  <td class="text-mono">Rp {{ number_format($order->po_total_amount, 0) }}</td>
  <td>
    @php
      $statusClass = match($order->po_status) {
        'draft' => 'pill-neutral',
        'ordered' => 'pill-warning',
        'partial' => 'pill-info',
        'completed' => 'pill-success',
        'cancelled' => 'pill-danger',
        default => 'pill-neutral'
      };
      $statusLabel = match($order->po_status) {
        'draft' => 'Draft', 'ordered' => 'Dipesan', 'partial' => 'Sebagian',
        'completed' => 'Selesai', 'cancelled' => 'Batal', default => $order->po_status
      };
    @endphp
    <span class="pill {{ $statusClass }}">{{ $statusLabel }}</span>
  </td>
  <td>
    <div class="d-flex gap-1">
      <a href="{{ route('admin.purchase-order.show', $order) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Detail">
        <i class="bi bi-eye"></i>
      </a>
      @if($order->po_status === 'draft')
        <a href="{{ route('admin.purchase-order.edit', $order) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
          <i class="bi bi-pencil"></i>
        </a>
        <button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger btn-delete" data-url="{{ route('admin.purchase-order.destroy', $order) }}" title="Hapus">
          <i class="bi bi-trash"></i>
        </button>
      @endif
    </div>
  </td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted-c py-4">Belum ada Purchase Order.</td></tr>
@endforelse
