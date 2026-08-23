@extends('guest.standard.layouts.app')

@section('title', 'Konfirmasi Pesanan')

@section('content')
{{-- Alert dari session --}}
@if(session('error'))
<div class="guest-alert guest-alert-danger">
  <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif

<div class="guest-narrow">
<div class="guest-page-head">
  <div>
    <h1 class="guest-page-title">Konfirmasi Pesanan</h1>
    <p class="guest-page-sub">Meja {{ $table->table_number }}</p>
  </div>
</div>

{{-- Daftar item --}}
<div class="guest-card">
  @foreach($items as $item)
    @php $p = $item['product']; @endphp
    <div class="guest-review-item">
      <div class="d-flex align-items-center gap-2 flex-grow-1">
        @if($p->product_image)
          <img src="{{ asset('storage/' . $p->product_image) }}" alt="" class="guest-review-img">
        @else
          <div class="guest-review-img guest-product-img-placeholder"><i class="bi bi-image"></i></div>
        @endif
        <div>
          <div class="fw-semibold">{{ $p->product_name }}</div>
          <small class="text-muted-guest">@if($item['qty'] > 1){{ $item['qty'] }} x @endif{{ 'Rp ' . number_format($item['price'], 0) }}</small>
          @if($item['note'])
            <div class="guest-cart-note">📝 {{ $item['note'] }}</div>
          @endif
        </div>
      </div>
      <div class="text-end">
        <div class="fw-semibold">{{ 'Rp ' . number_format($item['subtotal'], 0) }}</div>
        @if($item['discount_amount'] > 0)
          <small class="guest-disc-text">-{{ 'Rp ' . number_format($item['discount_amount'], 0) }}</small>
        @endif
      </div>
    </div>
  @endforeach

  {{-- Bundle --}}
  @foreach($bundleRows ?? [] as $b)
    <div class="guest-review-item">
      <div class="d-flex align-items-center gap-2 flex-grow-1">
        <div class="guest-review-img guest-product-img-placeholder"><i class="bi bi-gift"></i></div>
        <div>
          <div class="fw-semibold"><span class="guest-bundle-tag">Paket</span>{{ $b['bundle_name'] }}</div>
          <small class="text-muted-guest">@if($b['qty'] > 1){{ $b['qty'] }} x @endif{{ 'Rp ' . number_format($b['bundle_price'], 0) }}</small>
          <div class="guest-cart-bundle-chips">
            @foreach($b['items'] as $bi)
              <span class="guest-cart-note">{{ $bi['product_name'] ?? 'Produk' }} x{{ $bi['quantity'] }}</span>
            @endforeach
          </div>
        </div>
      </div>
      <div class="text-end">
        <div class="fw-semibold">{{ 'Rp ' . number_format($b['subtotal'], 0) }}</div>
      </div>
    </div>
  @endforeach
</div>

{{-- Voucher --}}
<div class="guest-card">
  <label class="form-label-modern-guest">Kode Voucher</label>
  <div class="d-flex gap-2">
    <input type="text" id="guestVoucherInput" class="form-control-guest" placeholder="cth: BARU10" value="{{ old('voucher_code') }}">
    <button type="button" class="btn btn-outline-guest text-nowrap" id="guestVoucherBtn">Pakai</button>
  </div>
  <div id="guestVoucherResult" class="mt-2"></div>
</div>

{{-- Ringkasan --}}
<div class="guest-card">
  <div class="guest-sum-row">
    <span>Subtotal</span>
    <span id="guestSubtotal">{{ 'Rp ' . number_format($grandTotal, 0) }}</span>
  </div>
  <div class="guest-sum-row" id="guestVoucherRow" style="display:none;">
    <span>Diskon Voucher</span>
    <span class="guest-disc-text" id="guestVoucherAmount">-Rp 0</span>
  </div>
  <div class="guest-sum-row guest-sum-total">
    <span>Total Bayar</span>
    <span id="guestGrandTotal">{{ 'Rp ' . number_format($grandTotal, 0) }}</span>
  </div>
</div>

{{-- Catatan order --}}
<div class="guest-card">
  <label class="form-label-modern-guest">Catatan Pesanan</label>
  <textarea id="guestOrderRemark" class="form-control-guest" rows="2" placeholder="cth: pesan untuk dibungkus"></textarea>
</div>

{{-- Submit --}}
<form id="guestSubmitForm" method="POST" action="{{ route('guest.submit') }}">
  @csrf
  <input type="hidden" name="table_id" value="{{ $table->table_id }}">
  <input type="hidden" name="total_price" value="{{ $grandTotal }}">
  <input type="hidden" name="items" id="guestSubmitItems">
  <input type="hidden" name="bundles" id="guestSubmitBundles">
  <input type="hidden" name="voucher_code" id="guestSubmitVoucher">
  <input type="hidden" name="order_remark" id="guestSubmitRemark">

  <button type="submit" class="btn btn-primary-guest w-100 btn-loading guest-submit-btn">
    <i class="bi bi-check2-circle me-1"></i>Kirim Pesanan
  </button>
</form>

<p class="guest-hint text-center mt-3">
  <i class="bi bi-info-circle me-1"></i>Pesanan akan diterima oleh kasir terlebih dahulu sebelum dimasak.
</p>
</div>{{-- /.guest-narrow --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const items = @json($itemsJson);
  const bundles = @json($bundleRows ?? []);

  const grandTotal = {{ $grandTotal }};
  const voucherCodeEl = document.getElementById('guestVoucherInput');
  const voucherResultEl = document.getElementById('guestVoucherResult');
  const voucherRow = document.getElementById('guestVoucherRow');
  const voucherAmountEl = document.getElementById('guestVoucherAmount');
  const grandTotalEl = document.getElementById('guestGrandTotal');

  let appliedVoucher = null;

  function fmtRp(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); }

  function renderTotal() {
    const disc = appliedVoucher ? appliedVoucher.amount : 0;
    const final = Math.max(0, grandTotal - disc);
    grandTotalEl.textContent = fmtRp(final);
    if (appliedVoucher) {
      voucherRow.style.display = 'flex';
      voucherAmountEl.textContent = '-' + fmtRp(disc);
    } else {
      voucherRow.style.display = 'none';
    }
  }

  // ——— Cek voucher ———
  document.getElementById('guestVoucherBtn').addEventListener('click', function() {
    const code = voucherCodeEl.value.trim();
    if (!code) { NexoraGuestToast('Masukkan kode voucher.', 'default'); return; }
    const btn = this;
    btn.disabled = true;
    fetch('{{ route("guest.check-voucher") }}', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
      body: '_token={{ csrf_token() }}&voucher_code=' + encodeURIComponent(code) + '&grand_total=' + grandTotal
    })
    .then(r => r.json())
    .then(d => {
      btn.disabled = false;
      if (d.ok) {
        appliedVoucher = { code: d.voucher_code, amount: d.voucher_amount };
        voucherResultEl.innerHTML = '<div class="guest-voucher-ok"><i class="bi bi-check-circle-fill me-1"></i>' + d.voucher_name + ' · potongan ' + fmtRp(d.voucher_amount) + '</div>';
        renderTotal();
      } else {
        appliedVoucher = null;
        voucherResultEl.innerHTML = '<div class="guest-voucher-err"><i class="bi bi-x-circle-fill me-1"></i>' + (d.message || 'Voucher tidak valid.') + '</div>';
        renderTotal();
      }
    })
    .catch(() => { btn.disabled = false; NexoraGuestToast('Terjadi kesalahan.', 'danger'); });
  });

  // ——— Submit ———
  document.getElementById('guestSubmitForm').addEventListener('submit', function(e) {
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengirim...';
    document.getElementById('guestSubmitItems').value = JSON.stringify(items);
    document.getElementById('guestSubmitBundles').value = JSON.stringify(bundles.map(b => ({
      bundle_id: b.bundle_id,
      bundle_name: b.bundle_name,
      bundle_price: b.bundle_price,
      qty: b.qty
    })));
    document.getElementById('guestSubmitVoucher').value = appliedVoucher ? appliedVoucher.code : '';
    document.getElementById('guestSubmitRemark').value = document.getElementById('guestOrderRemark').value.trim();
    // Total akhir (setelah voucher) — biar submit dapet nilai yang bener
    document.querySelector('input[name="total_price"]').value = Math.max(0, grandTotal - (appliedVoucher ? appliedVoucher.amount : 0));
    // Biarkan form submit normal (POST), redirect ke status page
  });

  // Auto check voucher if prefilled
  if (voucherCodeEl.value.trim()) {
    document.getElementById('guestVoucherBtn').click();
  }
});
</script>
@endpush
