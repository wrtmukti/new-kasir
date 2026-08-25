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

{{-- Info Pesanan Card --}}
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
      $isPaid = $order->isPaid();
      $isPrePayment = ($paymentTiming ?? 'post_payment') === 'pre_payment';
    @endphp
    <div class="d-flex align-items-center gap-2">
      @if($isPaid)
        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size:0.8rem;">
          <i class="bi bi-check2-circle me-1"></i>Lunas
        </span>
      @else
        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size:0.8rem;">
          <i class="bi bi-clock-history me-1"></i>Belum Bayar
        </span>
      @endif

      <span class="pill {{ $badge }}" style="font-size:0.85rem;">
        @if($status === 'pending' && $isPrePayment && !$isPaid)
          Menunggu Pembayaran Kasir
        @elseif($status === 'in_progress' && $isPaid)
          Sedang Dimasak (Lunas)
        @elseif($status === 'in_progress')
          Sedang Dimasak
        @else
          {{ str_replace('_', ' ', ucfirst($status)) }}
        @endif
      </span>
    </div>
  </div>
  <div class="card-body">
    <div class="detail-grid">
      <div class="detail-item">
        <span class="detail-label">ID Pesanan</span>
        <span class="detail-value text-mono fw-bold">#{{ $order->order_id }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Tipe Pesanan</span>
        <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</span>
      </div>
      @if($table)
      <div class="detail-item">
        <span class="detail-label">Meja</span>
        <span class="detail-value fw-bold">Meja {{ $table->table_number }} ({{ $table->table_capacity }} kursi)</span>
      </div>
      @endif
      @if($customer)
      <div class="detail-item">
        <span class="detail-label">Pelanggan</span>
        <span class="detail-value">{{ $customer->customer_name }} {{ $customer->customer_phone ? '('.$customer->customer_phone.')' : '' }}</span>
      </div>
      @endif
      <div class="detail-item">
        <span class="detail-label">Tanggal Transaksi</span>
        <span class="detail-value">{{ optional($order->created_at)->format('d M Y H:i') ?? '-' }} WIB</span>
      </div>
      @if($order->order_remark)
      <div class="detail-item" style="grid-column:1/-1;">
        <span class="detail-label">Catatan Pesanan</span>
        <span class="detail-value text-muted-c">{{ $order->order_remark }}</span>
      </div>
      @endif
    </div>
  </div>
</div>

{{-- Item Pesanan Card --}}
<div class="card mb-3">
  <div class="card-header-flex">
    <h6>Item Pesanan</h6>
    <span class="chip-tag">{{ ($transactionItems ? $transactionItems->count() : $order->products->count()) + $order->bundles->count() }} item</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>Produk / Paket</th>
            <th style="width:120px;" class="text-end">Harga</th>
            <th style="width:100px;" class="text-center">Diskon</th>
            <th style="width:80px;" class="text-center">Qty</th>
            <th style="width:150px;" class="text-end">Subtotal</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @php $grandTotal = 0; @endphp
          @if(($order->order_status === 'completed' || $isPaid) && $transactionItems && $transactionItems->isNotEmpty())
            {{-- Snapshot Transaction Items --}}
            @foreach($transactionItems as $item)
              @php $grandTotal += (float) $item->subtotal; @endphp
              <tr>
                <td>
                  <span style="font-weight:600;">{{ $item->product_name }}</span>
                </td>
                <td class="text-end text-mono">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-center">
                  @if($item->discount_amount > 0)
                    <span style="color:var(--danger);font-size:0.85rem;font-weight:600;">-Rp {{ number_format($item->discount_amount, 0, ',', '.') }}</span>
                  @else
                    <span class="text-muted-c">-</span>
                  @endif
                </td>
                <td class="text-center"><span class="chip-tag">{{ $item->qty }}</span></td>
                <td class="text-end text-mono fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                <td class="text-muted-c">{{ $item->note ?? '-' }}</td>
              </tr>
            @endforeach
            @foreach($order->bundles as $ob)
              @php $grandTotal += (float) $ob->subtotal; @endphp
              <tr style="background: rgba(var(--accent-1-rgb, 99, 102, 241), 0.04);">
                <td>
                  <div class="d-flex align-items-center gap-1.5">
                    <span class="chip-tag" style="background: rgba(99, 102, 241, 0.15); color: var(--accent-1); font-weight: 700; font-size: 0.7rem;">PAKET</span>
                    <strong style="font-weight:600;">{{ $ob->bundle_name }}</strong>
                  </div>
                  @if($ob->bundle && $ob->bundle->items)
                    <small class="text-muted-c d-block mt-0.5" style="font-size:0.75rem;">
                      Isi: {{ $ob->bundle->items->map(fn($i) => optional($i->product)->product_name . ' (' . $i->quantity . 'x)')->filter()->implode(', ') }}
                    </small>
                  @endif
                </td>
                <td class="text-end text-mono">Rp {{ number_format($ob->bundle_price, 0, ',', '.') }}</td>
                <td class="text-center"><span class="text-muted-c">-</span></td>
                <td class="text-center"><span class="chip-tag">{{ $ob->quantity }}</span></td>
                <td class="text-end text-mono fw-bold">Rp {{ number_format($ob->subtotal, 0, ',', '.') }}</td>
                <td class="text-muted-c">-</td>
              </tr>
            @endforeach
          @else
            {{-- Live Order Products & Bundles --}}
            @foreach($order->products as $product)
              @php
                $qty = (int) $product->pivot->quantity;
                $price = (float) $product->product_price;
                $activeDisc = $product->activeDiscount()->first();
                $discType = $activeDisc?->discount_type;
                $discVal = $activeDisc ? (float)($activeDisc->discount_value ?? 0) : 0;
                $dPct = $discType === 'percentage' ? $discVal : 0;
                $dNom = $discType === 'nominal' ? $discVal : 0;
                $dAmt = $dPct > 0 ? $price * $dPct / 100 : ($dNom > 0 ? min($dNom, $price) : 0);
                $dAmt = min($dAmt, $price);
                $subtotal = ($price - $dAmt) * $qty;
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
                    <span style="font-weight:600;">{{ $product->product_name }}</span>
                  </div>
                </td>
                <td class="text-end text-mono">Rp {{ number_format($price, 0, ',', '.') }}</td>
                <td class="text-center">
                  @if($dAmt > 0)
                    <span style="color:var(--danger);font-size:0.85rem;font-weight:600;">-Rp {{ number_format($dAmt, 0, ',', '.') }}</span>
                  @else
                    <span class="text-muted-c">-</span>
                  @endif
                </td>
                <td class="text-center"><span class="chip-tag">{{ $qty }}</span></td>
                <td class="text-end text-mono fw-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                <td class="text-muted-c">{{ $product->pivot->note ?? '-' }}</td>
              </tr>
            @endforeach
            @foreach($order->bundles as $ob)
              @php
                $bSubtotal = (float) $ob->bundle_price * (int) $ob->quantity;
                $grandTotal += $bSubtotal;
              @endphp
              <tr style="background: rgba(var(--accent-1-rgb, 99, 102, 241), 0.04);">
                <td>
                  <div class="d-flex align-items-center gap-1.5">
                    <span class="chip-tag" style="background: rgba(99, 102, 241, 0.15); color: var(--accent-1); font-weight: 700; font-size: 0.7rem;">PAKET</span>
                    <strong style="font-weight:600;">{{ $ob->bundle_name }}</strong>
                  </div>
                  @if($ob->bundle && $ob->bundle->items)
                    <small class="text-muted-c d-block mt-0.5" style="font-size:0.75rem;">
                      Isi: {{ $ob->bundle->items->map(fn($i) => optional($i->product)->product_name . ' (' . $i->quantity . 'x)')->filter()->implode(', ') }}
                    </small>
                  @endif
                </td>
                <td class="text-end text-mono">Rp {{ number_format($ob->bundle_price, 0, ',', '.') }}</td>
                <td class="text-center"><span class="text-muted-c">-</span></td>
                <td class="text-center"><span class="chip-tag">{{ $ob->quantity }}</span></td>
                <td class="text-end text-mono fw-bold">Rp {{ number_format($bSubtotal, 0, ',', '.') }}</td>
                <td class="text-muted-c">-</td>
              </tr>
            @endforeach
          @endif
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4" class="text-end fw-bold">Grand Total Tagihan</td>
            <td class="text-end text-mono fw-bold" style="color:var(--accent-1);font-size:1.05rem;">
              Rp {{ number_format($order->order_grand_total ?? $grandTotal, 0, ',', '.') }}
            </td>
            <td></td>
          </tr>
          @if($order->vouchers && $order->vouchers->isNotEmpty())
            @foreach($order->vouchers as $v)
            <tr>
              <td colspan="4" class="text-end" style="font-size:0.85rem;color:var(--text-muted);">
                <i class="bi bi-ticket-perforated me-1"></i>Voucher Diskon ({{ $v->voucher_code }})
              </td>
              <td class="text-end text-mono" style="color:var(--danger);font-size:0.9rem;">
                -Rp {{ number_format($v->voucher_amount, 0, ',', '.') }}
              </td>
              <td></td>
            </tr>
            @endforeach
          @endif
        </tfoot>
      </table>
    </div>
  </div>
</div>

{{-- Informasi Pembayaran jika Sudah Dibayar --}}
@if(($order->order_status === 'completed' || $isPaid) && $transaction && $transaction->payment)
<div class="card mb-3 border-success-subtle">
  <div class="card-header-flex">
    <h6 class="text-success"><i class="bi bi-check-circle-fill me-2"></i>Informasi Pembayaran</h6>
    <span class="chip-tag" style="background: rgba(34, 197, 94, 0.15); color: #22c55e; font-weight:700;">
      LUNAS
    </span>
  </div>
  <div class="card-body">
    <div class="detail-grid">
      <div class="detail-item">
        <span class="detail-label">Metode Pembayaran</span>
        <span class="detail-value fw-bold text-uppercase">
          @if($transaction->payment->payment_metode === 'cash')
            <i class="bi bi-cash-stack text-success me-1"></i>Tunai (Cash)
          @else
            <i class="bi bi-credit-card-2-front text-primary me-1"></i>Debit Card
          @endif
        </span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Total Tagihan</span>
        <span class="detail-value text-mono fw-bold">Rp {{ number_format($transaction->payment->payment_grand_total, 0, ',', '.') }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Uang Diterima / Dibayar</span>
        <span class="detail-value text-mono fw-bold text-success">Rp {{ number_format($transaction->payment->payment_amount, 0, ',', '.') }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">No. Referensi</span>
        <span class="detail-value text-mono">{{ $transaction->payment->payment_reference ?? '-' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Waktu Pembayaran</span>
        <span class="detail-value">{{ $transaction->payment->payment_date ?? '-' }}</span>
      </div>
      <div class="detail-item" style="grid-column:1/-1;">
        <span class="detail-label">Catatan Pembayaran</span>
        <span class="detail-value text-muted-c">{{ $transaction->payment->payment_remark ?? '-' }}</span>
      </div>
    </div>
  </div>
</div>
@endif

{{-- Tombol Aksi Adaptif --}}
<div class="d-flex justify-content-end gap-2">
  <a href="{{ route('admin.order.list') }}" class="btn btn-outline-soft">Kembali</a>

  {{-- Kasus 1: Status Pending --}}
  @if($order->order_status === 'pending')
    @if($isPrePayment)
      {{-- Pre-Payment: Bayar di awal sebelum dapur masak --}}
      <a href="{{ route('admin.order.payment', $order) }}" class="btn btn-success-grad">
        <i class="bi bi-credit-card me-1"></i>Bayar & Proses Pesanan
      </a>
    @else
      {{-- Post-Payment: Terima pesanan langsung dimasak --}}
      <button type="button" class="btn btn-primary-grad" id="acceptBtn">
        <i class="bi bi-check-lg me-1"></i>Terima Pesanan
      </button>
    @endif
  @endif

  {{-- Kasus 2: Status In Progress --}}
  @if($order->order_status === 'in_progress')
    @if($isPaid)
      {{-- Pre-Payment yang sudah lunas: Tombol Selesai Disajikan & Cetak Struk --}}
      <a href="{{ route('admin.order.receipt', $order) }}" class="btn btn-outline-soft" target="_blank">
        <i class="bi bi-printer me-1"></i>Cetak Struk
      </a>
      <button type="button" class="btn btn-success-grad" id="completeServingBtn">
        <i class="bi bi-check-circle me-1"></i>Tandai Selesai Disajikan
      </button>
    @else
      {{-- Post-Payment yang belum lunas: Lanjut ke Pembayaran --}}
      <a href="{{ route('admin.order.payment', $order) }}" class="btn btn-success-grad">
        <i class="bi bi-credit-card me-1"></i>Lanjut ke Pembayaran
      </a>
    @endif
  @endif

  {{-- Kasus 3: Status Completed --}}
  @if($order->order_status === 'completed')
    <a href="{{ route('admin.order.receipt', $order) }}" class="btn btn-primary-grad" target="_blank">
      <i class="bi bi-printer me-1"></i>Cetak Struk
    </a>
  @endif
</div>
@endsection

{{-- MODAL KONFIRMASI TERIMA (POST-PAYMENT) --}}
<div class="modal fade" id="acceptModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="mb-0"><i class="bi bi-check-lg me-2"></i>Terima Pesanan</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-3">
        <i class="bi bi-clipboard-check" style="font-size:2.5rem;color:var(--accent-1);display:block;margin-bottom:0.5rem;"></i>
        <p class="mb-0">Terima pesanan <strong>#{{ $order->order_id }}</strong>?</p>
        <small class="text-muted-c">Stok bahan akan otomatis dikurangi dan meja ditandai terisi.</small>
      </div>
      <div class="modal-footer justify-content-center border-0 pt-0">
        <button type="button" class="btn btn-outline-soft" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary-grad" id="confirmAcceptBtn">
          <i class="bi bi-check-lg me-1"></i>Ya, Terima
        </button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL KONFIRMASI SELESAI DISAJIKAN (PRE-PAYMENT) --}}
<div class="modal fade" id="completeServingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="mb-0"><i class="bi bi-check2-circle me-2 text-success"></i>Selesaikan Pesanan</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-3">
        <i class="bi bi-patch-check" style="font-size:2.5rem;color:var(--success, #22c55e);display:block;margin-bottom:0.5rem;"></i>
        <p class="mb-0">Tandai pesanan <strong>#{{ $order->order_id }}</strong> selesai disajikan?</p>
        <small class="text-muted-c">Status pesanan akan diselesaikan dan meja akan kembali dikosongkan.</small>
      </div>
      <div class="modal-footer justify-content-center border-0 pt-0">
        <button type="button" class="btn btn-outline-soft" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-success-grad" id="confirmServingBtn">
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
  const acceptBtn = document.getElementById('acceptBtn');
  const confirmAcceptBtn = document.getElementById('confirmAcceptBtn');

  if (acceptBtn) {
    acceptBtn.addEventListener('click', function() {
      var modal = new bootstrap.Modal(document.getElementById('acceptModal'));
      modal.show();
    });
  }

  if (confirmAcceptBtn) {
    confirmAcceptBtn.addEventListener('click', function() {
      const btn = this;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';
      var modal = bootstrap.Modal.getInstance(document.getElementById('acceptModal'));
      if (modal) modal.hide();
      fetch('{{ route("admin.order.accept", $order) }}', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '_token={{ csrf_token() }}'
      })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          NexoraToast(d.message || 'Pesanan diterima.', 'success');
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

  // Complete Serving Handler (Pre-Payment)
  const completeServingBtn = document.getElementById('completeServingBtn');
  const confirmServingBtn = document.getElementById('confirmServingBtn');

  if (completeServingBtn) {
    completeServingBtn.addEventListener('click', function() {
      var modal = new bootstrap.Modal(document.getElementById('completeServingModal'));
      modal.show();
    });
  }

  if (confirmServingBtn) {
    confirmServingBtn.addEventListener('click', function() {
      const btn = this;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyelesaikan...';
      var modal = bootstrap.Modal.getInstance(document.getElementById('completeServingModal'));
      if (modal) modal.hide();
      fetch('{{ route("admin.order.completeServing", $order) }}', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '_token={{ csrf_token() }}'
      })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          NexoraToast(d.message || 'Pesanan selesai disajikan.', 'success');
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
