@forelse($orders as $order)
<tr class="row-clickable" data-url="{{ route('admin.order.show', $order) }}">
  <td class="text-mono" style="font-weight:500;">#{{ $order->order_id }}</td>
  <td>
    @if($order->order_type == 'dine_in')
      <span class="pill pill-info">Dine In</span>
    @elseif($order->order_type == 'take_away')
      <span class="pill pill-warning">Take Away</span>
    @else
      <span class="pill pill-neutral">Delivery</span>
    @endif
  </td>
  <td>
    @php
      $status = $order->order_status;
      $badge = match($status) {
        'in_progress' => 'pill-info',
        'completed' => 'pill-success',
        'cancelled' => 'pill-danger',
        default => 'pill-neutral'
      };
    @endphp
    <span class="pill {{ $badge }}">{{ str_replace('_', ' ', ucfirst($status)) }}</span>
  </td>
  <td class="text-mono">Rp {{ number_format($order->order_grand_total ?? 0, 0) }}</td>
  <td>{{ optional($order->created_at)->format('d M Y H:i') ?? '-' }}</td>
</tr>
@empty
<tr>
  <td colspan="5" class="text-center text-muted-c py-4">Belum ada pesanan.</td>
</tr>
@endforelse
