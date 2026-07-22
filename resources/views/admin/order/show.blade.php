@extends('admin.layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_id)

@php $activeMenu = 'order-list' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Pesanan #{{ $order->order_id }}</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.order.list') }}">Daftar Pesanan</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>#{{ $order->order_id }}</span>
    </div>
  </div>
</div>

{{-- Info Pesanan --}}
<div class="card mb-3">
  <div class="card-header-flex">
    <h6>Info Pesanan</h6>
    @php
      $status = $order->order_status;
      $badge = match($status) {
        'in_progress' => 'pill-info',
        'completed' => 'pill-success',
        'cancelled' => 'pill-danger',
        default => 'pill-neutral'
      };
    @endphp
    <span class="pill {{ $badge }}" style="font-size:0.85rem;">{{ str_replace('_', ' ', ucfirst($status)) }}</span>
  </div>
  <div class="card-body">
    <div class="detail-grid">
      <div class="detail-item">
        <span class="detail-label">ID Pesanan</span>
        <span class="detail-value text-mono">#{{ $order->order_id }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Tipe</span>
        <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</span>
      </div>
      @if($table)
      <div class="detail-item">
        <span class="detail-label">Meja</span>
        <span class="detail-value">Meja {{ $table->table_number }} ({{ $table->table_capacity }} kursi)</span>
      </div>
      @endif
      @if($customer)
      <div class="detail-item">
        <span class="detail-label">Pelanggan</span>
        <span class="detail-value">{{ $customer->customer_name }} {{ $customer->customer_phone ? '('.$customer->customer_phone.')' : '' }}</span>
      </div>
      @endif
      <div class="detail-item">
        <span class="detail-label">Tanggal</span>
        <span class="detail-value">{{ optional($order->created_at)->format('d M Y H:i') ?? '-' }}</span>
      </div>
      @if($order->order_remark)
      <div class="detail-item" style="grid-column:1/-1;">
        <span class="detail-label">Catatan</span>
        <span class="detail-value">{{ $order->order_remark }}</span>
      </div>
      @endif
    </div>
  </div>
</div>

{{-- Item Pesanan --}}
<div class="card mb-3">
  <div class="card-header-flex">
    <h6>Item Pesanan</h6>
    <span class="chip-tag">{{ $order->products->count() }} item</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>Produk</th>
            <th style="width:100px;">Harga</th>
            <th style="width:80px;">Qty</th>
            <th style="width:150px;">Subtotal</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @php $grandTotal = 0; @endphp
          @foreach($order->products as $product)
            @php
              $qty = (int) $product->pivot->quantity;
              $price = (float) $product->product_price;
              $subtotal = $price * $qty;
              $grandTotal += $subtotal;
            @endphp
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  @if($product->product_image)
                    <img src="{{ asset('storage/' . $product->product_image) }}" style="width:36px;height:36px;object-fit:cover;border-radius:var(--radius-sm);">
                  @else
                    <span style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-sm);background:var(--bg-elevated-2);"><i class="bi bi-image" style="color:var(--text-muted);"></i></span>
                  @endif
                  <span style="font-weight:500;">{{ $product->product_name }}</span>
                </div>
              </td>
              <td class="text-mono">Rp {{ number_format($price, 0) }}</td>
              <td>{{ $qty }}</td>
              <td class="text-mono">Rp {{ number_format($subtotal, 0) }}</td>
              <td>{{ $product->pivot->note ?? '-' }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" class="text-end fw-bold">Grand Total</td>
            <td class="text-mono fw-bold" style="color:var(--accent-1);font-size:1.05rem;">Rp {{ number_format($grandTotal, 0) }}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

{{-- Tombol --}}
<div class="d-flex justify-content-end gap-2">
  <a href="{{ route('admin.order.list') }}" class="btn btn-outline-soft">Kembali</a>

  @if($order->order_status === 'in_progress')
    <button type="button" class="btn btn-success-grad" id="completeBtn">
      <i class="bi bi-check2-circle me-1"></i>Selesaikan Pesanan
    </button>
  @endif

  @if($order->order_status === 'completed')
    <a href="{{ route('admin.order.receipt', $order) }}" class="btn btn-primary-grad" target="_blank">
      <i class="bi bi-printer me-1"></i>Cetak Struk
    </a>
  @endif
</div>
@endsection

{{-- MODAL KONFIRMASI SELESAI --}}
<div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="mb-0"><i class="bi bi-check2-circle me-2"></i>Selesaikan Pesanan</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-3">
        <i class="bi bi-check-circle" style="font-size:2.5rem;color:var(--success);display:block;margin-bottom:0.5rem;"></i>
        <p class="mb-0">Yakin ingin menyelesaikan pesanan <strong>#{{ $order->order_id }}</strong>?</p>
        <small class="text-muted-c">Pesanan akan selesai dan transaksi akan tercatat.</small>
      </div>
      <div class="modal-footer justify-content-center border-0 pt-0">
        <button type="button" class="btn btn-outline-soft" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-success-grad" id="confirmCompleteBtn">
          <i class="bi bi-check-lg me-1"></i>Ya, Selesaikan
        </button>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
.detail-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1.25rem;
}
.detail-item {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}
.detail-label {
  font-size: 0.8rem;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.03em;
}
.detail-value {
  font-size: 0.95rem;
  color: var(--text-primary);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const completeBtn = document.getElementById('completeBtn');
  const confirmBtn = document.getElementById('confirmCompleteBtn');

  if (completeBtn) {
    completeBtn.addEventListener('click', function() {
      var modal = new bootstrap.Modal(document.getElementById('completeModal'));
      modal.show();
    });
  }

  if (confirmBtn) {
    confirmBtn.addEventListener('click', function() {
      const btn = this;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';
      // Tutup modal
      var modal = bootstrap.Modal.getInstance(document.getElementById('completeModal'));
      if (modal) modal.hide();
      // Kirim request
      fetch('{{ route("admin.order.complete", $order) }}', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '_token={{ csrf_token() }}'
      })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          NexoraToast(d.message || 'Pesanan selesai.', 'success');
          setTimeout(() => location.reload(), 800);
        } else {
          NexoraToast(d.message || 'Gagal.', 'danger');
        }
      })
      .catch(() => {
        NexoraToast('Terjadi kesalahan.', 'danger');
      });
    });
  }
});
</script>
@endpush
