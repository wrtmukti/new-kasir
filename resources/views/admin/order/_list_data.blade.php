@forelse($orders as $order)
<tr class="row-clickable" data-url="{{ route('admin.order.show', $order) }}">
  <td class="text-mono" style="font-weight:600; color:var(--text-primary, #f8fafc);">#{{ $order->order_id }}</td>
  <td>
    @if($order->order_type == 'dine_in')
      <span class="chip-tag" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;"><i class="bi bi-grid-3x3-gap-fill me-1"></i>Dine In</span>
    @elseif($order->order_type == 'take_away')
      <span class="chip-tag" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;"><i class="bi bi-bag-check-fill me-1"></i>Take Away</span>
    @else
      <span class="chip-tag" style="background: rgba(168, 85, 247, 0.15); color: #c084fc;"><i class="bi bi-truck me-1"></i>Delivery</span>
    @endif
  </td>
  <td>
    @php
      $status = $order->order_status;
      $isPaid = $order->isPaid();
      $badgeStyle = match($status) {
        'in_progress' => 'background: rgba(59, 130, 246, 0.15); color: #60a5fa;',
        'completed' => 'background: rgba(34, 197, 94, 0.15); color: #4ade80;',
        'cancelled' => 'background: rgba(239, 68, 68, 0.15); color: #f87171;',
        default => 'background: rgba(148, 163, 184, 0.15); color: #cbd5e1;'
      };
    @endphp
    <div class="d-flex align-items-center gap-1.5 flex-wrap">
      {{-- Payment Status Badge --}}
      @if($isPaid)
        <span class="chip-tag" style="background: rgba(34, 197, 94, 0.15); color: #22c55e; font-weight:700; font-size:0.75rem;">
          <i class="bi bi-check2 me-0.5"></i>Lunas
        </span>
      @else
        <span class="chip-tag" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; font-weight:700; font-size:0.75rem;">
          <i class="bi bi-clock me-0.5"></i>Belum Bayar
        </span>
      @endif

      {{-- Operational Status Badge --}}
      <span class="chip-tag" style="{{ $badgeStyle }} font-weight:600; font-size:0.75rem;">
        {{ str_replace('_', ' ', ucfirst($status)) }}
      </span>
    </div>
  </td>
  <td class="text-mono fw-bold text-success">
    @php
      $total = $order->order_status === 'completed' && $order->transaction
        ? (float) $order->transaction->items->sum('subtotal') + (float) $order->bundles->sum('subtotal')
        : (float) ($order->order_grand_total ?? 0);
    @endphp
    Rp {{ number_format($total, 0, ',', '.') }}
  </td>
  <td class="text-muted-c" style="font-size:0.85rem;">{{ optional($order->created_at)->format('d/m/Y H:i') ?? '-' }} WIB</td>
  <td class="text-end" onclick="event.stopPropagation();">
    <a href="{{ route('admin.order.show', $order) }}" class="btn btn-primary-grad btn-sm py-1 px-2 text-nowrap" style="font-size:0.78rem;">
      <i class="bi bi-eye me-1"></i> Detail / Aksi
    </a>
  </td>
</tr>
@empty
<tr>
  <td colspan="6" class="text-center text-muted-c py-4">
    <i class="bi bi-inbox d-block fs-3 mb-2 text-secondary"></i>
    Belum ada data pesanan.
  </td>
</tr>
@endforelse
