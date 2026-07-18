@extends('admin.layouts.app')

@section('title', 'Terima Barang')

@php $activeMenu = 'purchase-order' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Terima Barang — {{ $order->po_code }}</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.purchase-order.index') }}">Purchase Order</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.purchase-order.show', $order) }}">{{ $order->po_code }}</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Terima</span>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header-flex"><h6><i class="bi bi-info-circle me-2"></i>Referensi PO</h6></div>
  <div class="card-body">
    <div class="row g-2" style="font-size:0.9rem;">
      <div class="col-md-3"><span class="text-muted-c">Supplier:</span> <span class="fw-semibold">{{ $order->supplier?->supplier_name ?? '-' }}</span></div>
      <div class="col-md-2"><span class="text-muted-c">Tanggal:</span> {{ $order->po_date ? $order->po_date->format('d M Y') : '-' }}</div>
      <div class="col-md-2"><span class="text-muted-c">Kode:</span> <span class="text-mono">{{ $order->po_code }}</span></div>
      <div class="col-md-2"><span class="text-muted-c">Kode Receiving:</span> <span class="text-mono">{{ $receivingCode }}</span></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex"><h6><i class="bi bi-box-arrow-in-down me-2"></i>Item Diterima</h6></div>
  <div class="card-body">
    <form action="{{ route('admin.purchase-order.receiving.store', $order) }}" method="POST">
      @csrf
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <label class="form-label-modern">Tanggal Terima <span class="text-danger">*</span></label>
          <input type="datetime-local" name="receiving_date" class="form-control-modern @error('receiving_date') is-invalid @enderror" value="{{ old('receiving_date', now()->format('Y-m-d\TH:i')) }}">
          @error('receiving_date')<div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="table-responsive">
        <table class="table-modern">
          <thead>
            <tr>
              <th>Bahan</th>
              <th>Qty PO</th>
              <th>Sudah Diterima</th>
              <th>Sisa</th>
              <th>Qty Diterima Skrg</th>
              <th>Harga</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $i => $item)
            @php $remaining = $item->qty - $item->received_qty; @endphp
            <tr>
              <td class="fw-semibold">
                {{ $item->stock?->stock_name ?? '-' }}
                <input type="hidden" name="items[{{ $i }}][po_item_id]" value="{{ $item->po_item_id }}">
                <input type="hidden" name="items[{{ $i }}][stock_id]" value="{{ $item->stock_id }}">
              </td>
              <td class="text-mono">{{ $item->qty }}</td>
              <td class="text-mono">{{ $item->received_qty }}</td>
              <td class="text-mono fw-semibold {{ $remaining > 0 ? 'text-warning' : 'text-success' }}">{{ max(0, $remaining) }}</td>
              <td style="min-width:100px;">
                @if($remaining > 0)
                  <input type="number" name="items[{{ $i }}][received_qty]" class="form-control-modern form-control-sm item-qty" value="{{ old("items.{$i}.received_qty", $remaining) }}" min="0" max="{{ $remaining }}">
                @else
                  <span class="text-muted-c">-</span>
                  <input type="hidden" name="items[{{ $i }}][received_qty]" value="0">
                @endif
              </td>
              <td style="min-width:120px;">
                <input type="number" name="items[{{ $i }}][received_price]" class="form-control-modern form-control-sm item-price" value="{{ old("items.{$i}.received_price", $item->price) }}" min="0" step="100">
              </td>
              <td><span class="item-subtotal text-mono">Rp {{ number_format($remaining * $item->price, 0) }}</span></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="row mt-3">
        <div class="col-12">
          <label class="form-label-modern">Catatan</label>
          <textarea name="receiving_notes" class="form-control-modern" rows="2" placeholder="Catatan penerimaan">{{ old('receiving_notes') }}</textarea>
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-success-grad"><i class="bi bi-check-lg me-1"></i>Konfirmasi Terima</button>
        <a href="{{ route('admin.purchase-order.show', $order) }}" class="btn btn-outline-soft">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  function calcRow(row) {
    const qty = parseInt(row.querySelector('.item-qty')?.value) || 0;
    const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
    const sub = qty * price;
    const subEl = row.querySelector('.item-subtotal');
    if (subEl) subEl.textContent = 'Rp ' + sub.toLocaleString('id-ID');
  }
  document.querySelectorAll('.item-qty, .item-price').forEach(el => {
    el.addEventListener('input', function() { calcRow(this.closest('tr')); });
  });
});
</script>
@endpush
