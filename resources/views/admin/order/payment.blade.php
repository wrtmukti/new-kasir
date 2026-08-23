@extends('admin.layouts.app')

@section('title', 'Pembayaran Pesanan #' . $order->order_id)

@php $activeMenu = 'order-list' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Pembayaran Pesanan #{{ $order->order_id }}</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.order.list') }}">Daftar Pesanan</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.order.show', $order) }}">#{{ $order->order_id }}</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Pembayaran</span>
    </div>
  </div>
</div>

<div class="row g-4">
  {{-- KOLOM KIRI: RINGKASAN PESANAN --}}
  <div class="col-lg-5">
    <div class="card mb-3">
      <div class="card-header-flex">
        <h6><i class="bi bi-receipt-cutoff me-2"></i>Ringkasan Tagihan</h6>
        <span class="chip-tag" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; font-weight:600;">
          {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
        </span>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary-subtle">
          <div>
            <div class="fw-bold fs-6">Pesanan #{{ $order->order_id }}</div>
            <div class="text-muted-c" style="font-size:0.85rem;">
              {{ optional($order->created_at)->format('d M Y H:i') }} WIB
            </div>
          </div>
          <div class="text-end">
            @if($table)
              <span class="chip-tag" style="background:var(--bg-elevated-2);border:1px solid var(--border-subtle);font-weight:600;">
                <i class="bi bi-grid-3x3-gap-fill text-primary me-1"></i>Meja {{ $table->table_number }}
              </span>
            @endif
            @if($customer)
              <div class="text-muted-c mt-1" style="font-size:0.8rem;">
                <i class="bi bi-person me-1"></i>{{ $customer->customer_name }}
              </div>
            @endif
          </div>
        </div>

        {{-- Daftar Item Singkat --}}
        <div class="table-responsive mb-3" style="max-height: 220px; overflow-y: auto;">
          <table class="table table-sm table-borderless align-middle mb-0" style="font-size:0.88rem;">
            <tbody>
              @foreach($items as $item)
                <tr>
                  <td>
                    <div class="fw-semibold text-truncate" style="max-width: 180px;">{{ $item['product']->product_name }}</div>
                    @if($item['discount_amount'] > 0)
                      <small class="text-success d-block" style="font-size:0.75rem;">Hemat -Rp {{ number_format($item['discount_amount'], 0, ',', '.') }}</small>
                    @endif
                  </td>
                  <td class="text-center text-muted-c">x{{ $item['qty'] }}</td>
                  <td class="text-end text-mono fw-medium">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                </tr>
              @endforeach

              @foreach($order->bundles as $ob)
                <tr>
                  <td>
                    <div class="fw-semibold text-truncate" style="max-width: 180px;">
                      {{ $ob->bundle_name }} <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size:0.65rem;">Paket</span>
                    </div>
                  </td>
                  <td class="text-center text-muted-c">x{{ $ob->quantity }}</td>
                  <td class="text-end text-mono fw-medium">Rp {{ number_format($ob->subtotal, 0, ',', '.') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <hr class="border-secondary-subtle my-2">

        {{-- Kalkulasi Rincian --}}
        <div class="d-flex justify-content-between mb-1" style="font-size:0.9rem;">
          <span class="text-muted-c">Subtotal Menu</span>
          <span class="text-mono">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</span>
        </div>

        @if($order->vouchers->isNotEmpty())
          @foreach($order->vouchers as $v)
            <div class="d-flex justify-content-between mb-1 text-danger" style="font-size:0.9rem;">
              <span><i class="bi bi-ticket-perforated me-1"></i>Voucher ({{ $v->voucher_code }})</span>
              <span class="text-mono">-Rp {{ number_format($v->voucher_amount, 0, ',', '.') }}</span>
            </div>
          @endforeach
        @endif

        @if($order->service_charge_amount > 0)
          <div class="d-flex justify-content-between mb-1" style="font-size:0.9rem;">
            <span class="text-muted-c">Service Charge ({{ number_format($order->service_charge_percent, 0) }}%)</span>
            <span class="text-mono">Rp {{ number_format($order->service_charge_amount, 0, ',', '.') }}</span>
          </div>
        @endif

        @if($order->tax_amount > 0)
          <div class="d-flex justify-content-between mb-1" style="font-size:0.9rem;">
            <span class="text-muted-c">Pajak Restoran PB1 ({{ number_format($order->tax_percent, 0) }}%)</span>
            <span class="text-mono">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
          </div>
        @endif

        <div class="p-3 mt-3 rounded-3" style="background: var(--bg-elevated-2, rgba(255,255,255,0.05)); border: 1px solid var(--border-subtle, rgba(255,255,255,0.1));">
          <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold text-uppercase" style="letter-spacing:0.05em; font-size:0.9rem;">Total Tagihan</span>
            <span class="text-mono fw-bold fs-4" style="color: var(--accent-1, #60a5fa);" id="displayGrandTotal" data-value="{{ (float) $order->order_grand_total }}">
              Rp {{ number_format((float) $order->order_grand_total, 0, ',', '.') }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- KOLOM KANAN: FORM PEMBAYARAN KASIR --}}
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header-flex">
        <h6><i class="bi bi-credit-card-2-front me-2"></i>Pilih Metode Pembayaran</h6>
        <span class="chip-tag" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; font-weight:600;">
          <i class="bi bi-shield-lock-fill me-1"></i>Secure POS
        </span>
      </div>
      <div class="card-body">
        <form id="paymentForm" action="{{ route('admin.order.processPayment', $order) }}" method="POST">
          @csrf

          {{-- Hidden input nilai metode pembayaran aktual --}}
          <input type="hidden" name="payment_metode" id="paymentMetodeInput" value="cash">

          {{-- Pilihan Card Metode Pembayaran --}}
          <div class="row g-3 mb-4">
            <div class="col-6">
              <div class="payment-method-card active w-100 text-center p-3 rounded-3 cursor-pointer" id="methodCashCard" onclick="selectPaymentMethod('cash')">
                <i class="bi bi-cash-stack d-block fs-2 mb-1 text-success"></i>
                <div class="fw-bold fs-6">Tunai (Cash)</div>
                <small class="text-muted-c" style="font-size:0.75rem;">Pembayaran Uang Fisik</small>
              </div>
            </div>
            <div class="col-6">
              <div class="payment-method-card w-100 text-center p-3 rounded-3 cursor-pointer" id="methodDebitCard" onclick="selectPaymentMethod('debit')">
                <i class="bi bi-credit-card-2-front d-block fs-2 mb-1 text-primary"></i>
                <div class="fw-bold fs-6">Debit Card</div>
                <small class="text-muted-c" style="font-size:0.75rem;">Mesin EDC / Gesek Kartu</small>
              </div>
            </div>
          </div>

          {{-- SECTION CASH --}}
          <div id="sectionCash" class="payment-section">
            <div class="mb-3 input-skeleton">
              <label for="cashAmountInput" class="form-label-modern fw-semibold">Uang Diterima (Rp)</label>
              <div class="input-group input-group-lg">
                <span class="input-group-text bg-elevated text-muted-c border-secondary-subtle">Rp</span>
                <input type="text" id="cashAmountInput" class="form-control form-control-modern text-mono fs-4 fw-bold"
                       placeholder="0" autofocus>
              </div>
              <small class="text-muted-c mt-1 d-block">Masukkan jumlah uang tunai yang diserahkan oleh pelanggan.</small>
            </div>

            {{-- Quick Cash Preset Buttons --}}
            <div class="mb-4">
              <label class="form-label-modern text-muted-c mb-2" style="font-size:0.8rem;">Pilihan Cepat Uang Pas & Pecahan:</label>
              <div class="d-flex flex-wrap gap-2" id="quickCashButtons">
                <button type="button" class="btn btn-outline-soft btn-sm quick-cash-btn" data-amount="{{ (float) $order->order_grand_total }}">
                  <i class="bi bi-check2-all me-1 text-success"></i>Uang Pas
                </button>
                <button type="button" class="btn btn-outline-soft btn-sm quick-cash-btn" data-amount="20000">Rp 20.000</button>
                <button type="button" class="btn btn-outline-soft btn-sm quick-cash-btn" data-amount="50000">Rp 50.000</button>
                <button type="button" class="btn btn-outline-soft btn-sm quick-cash-btn" data-amount="100000">Rp 100.000</button>
                <button type="button" class="btn btn-outline-soft btn-sm quick-cash-btn" data-amount="200000">Rp 200.000</button>
              </div>
            </div>

            {{-- Box Kembalian (Live Calculate) --}}
            <div class="card p-3 mb-4 rounded-3 border-0" id="changeBox" style="background: rgba(34, 197, 94, 0.1); border-left: 4px solid #22c55e !important;">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-uppercase fw-semibold" style="font-size:0.8rem; color: #22c55e;">Uang Kembalian</div>
                  <div class="text-mono fs-4 fw-bold text-success" id="displayChange">Rp 0</div>
                </div>
                <div id="changeStatusBadge">
                  <span class="chip-tag" style="background: rgba(34, 197, 94, 0.2); color: #22c55e; font-weight:600;">
                    <i class="bi bi-check-circle-fill me-1"></i>Uang Pas
                  </span>
                </div>
              </div>
            </div>
          </div>

          {{-- SECTION DEBIT --}}
          <div id="sectionDebit" class="payment-section d-none">
            <div class="alert alert-info border-0 rounded-3 mb-3 d-flex align-items-center gap-2" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
              <i class="bi bi-info-circle-fill fs-5"></i>
              <div style="font-size:0.85rem;">
                Nominal pembayaran debit otomatis disesuaikan tepat sebesar total tagihan: <strong>Rp {{ number_format((float) $order->order_grand_total, 0, ',', '.') }}</strong>
              </div>
            </div>

            <div class="mb-3 input-skeleton">
              <label for="paymentReference" class="form-label-modern fw-semibold">No. Referensi / Trace / Approval EDC <span class="text-danger">*</span></label>
              <input type="text" name="payment_reference" id="paymentReference"
                     class="form-control form-control-modern"
                     placeholder="Contoh: EDC-BCA-981240 / No. Kartu">
              <small class="text-muted-c mt-1 d-block">Masukkan nomor trace / approval code dari mesin EDC kartu debit.</small>
            </div>
          </div>

          {{-- Input Actual Payment Amount (Dikirim ke server) --}}
          <input type="hidden" name="payment_amount" id="actualPaymentAmount" value="{{ (float) $order->order_grand_total }}">

          {{-- Catatan Tambahan (Remark) --}}
          <div class="mb-4 input-skeleton">
            <label for="paymentRemark" class="form-label-modern">Catatan Pembayaran (Opsional)</label>
            <input type="text" name="payment_remark" id="paymentRemark"
                   class="form-control form-control-modern"
                   placeholder="Contoh: Dibayar oleh pelanggan A / EDC BCA Kasir 1">
          </div>

          {{-- Action Buttons --}}
          <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary-subtle">
            <a href="{{ route('admin.order.show', $order) }}" class="btn btn-outline-soft">
              <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
            <button type="submit" class="btn btn-success-grad btn-lg px-4 btn-loading" id="submitPaymentBtn">
              <i class="bi bi-check2-circle me-1"></i>Proses & Selesaikan Pembayaran
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.payment-method-card {
  border: 2px solid var(--border-subtle, rgba(255, 255, 255, 0.1));
  background: var(--bg-elevated, rgba(255, 255, 255, 0.03));
  transition: all 0.2s ease-in-out;
  user-select: none;
}
.payment-method-card:hover {
  border-color: var(--accent-1, #3b82f6);
  background: var(--bg-elevated-2, rgba(255, 255, 255, 0.06));
}
.payment-method-card.active {
  border-color: var(--accent-1, #3b82f6) !important;
  background: rgba(59, 130, 246, 0.12) !important;
  box-shadow: 0 0 0 1px var(--accent-1, #3b82f6);
}
.cursor-pointer {
  cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
// Global function untuk switch metode pembayaran
function selectPaymentMethod(method) {
  const grandTotal = parseFloat(document.getElementById('displayGrandTotal').dataset.value) || 0;
  const paymentMetodeInput = document.getElementById('paymentMetodeInput');
  const methodCashCard = document.getElementById('methodCashCard');
  const methodDebitCard = document.getElementById('methodDebitCard');
  const sectionCash = document.getElementById('sectionCash');
  const sectionDebit = document.getElementById('sectionDebit');
  const cashAmountInput = document.getElementById('cashAmountInput');
  const actualPaymentAmount = document.getElementById('actualPaymentAmount');
  const paymentReference = document.getElementById('paymentReference');
  const submitPaymentBtn = document.getElementById('submitPaymentBtn');

  paymentMetodeInput.value = method;

  if (method === 'debit') {
    methodDebitCard.classList.add('active');
    methodCashCard.classList.remove('active');
    sectionDebit.classList.remove('d-none');
    sectionCash.classList.add('d-none');
    actualPaymentAmount.value = grandTotal;
    submitPaymentBtn.disabled = false;
    setTimeout(() => paymentReference.focus(), 100);
  } else {
    methodCashCard.classList.add('active');
    methodDebitCard.classList.remove('active');
    sectionCash.classList.remove('d-none');
    sectionDebit.classList.add('d-none');
    const rawVal = parseFloat(cashAmountInput.value.replace(/[^0-9]/g, '')) || grandTotal;
    actualPaymentAmount.value = rawVal;
    if (typeof window.updateChangeDisplay === 'function') {
      window.updateChangeDisplay(rawVal);
    }
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const grandTotal = parseFloat(document.getElementById('displayGrandTotal').dataset.value) || 0;
  const cashAmountInput = document.getElementById('cashAmountInput');
  const actualPaymentAmount = document.getElementById('actualPaymentAmount');
  const displayChange = document.getElementById('displayChange');
  const changeBox = document.getElementById('changeBox');
  const changeStatusBadge = document.getElementById('changeStatusBadge');
  const paymentReference = document.getElementById('paymentReference');
  const quickCashButtons = document.querySelectorAll('.quick-cash-btn');
  const paymentForm = document.getElementById('paymentForm');
  const submitPaymentBtn = document.getElementById('submitPaymentBtn');

  // Format angka ke format rupiah ribuan
  function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID').format(Math.round(num));
  }

  // Update tampilan kembalian
  window.updateChangeDisplay = function(enteredAmount) {
    const change = enteredAmount - grandTotal;
    if (change >= 0) {
      displayChange.textContent = 'Rp ' + formatRupiah(change);
      displayChange.className = 'text-mono fs-4 fw-bold text-success';
      changeBox.style.background = 'rgba(34, 197, 94, 0.1)';
      changeBox.style.borderLeftColor = '#22c55e';
      if (change === 0) {
        changeStatusBadge.innerHTML = '<span class="chip-tag" style="background: rgba(34, 197, 94, 0.2); color: #22c55e; font-weight:600;"><i class="bi bi-check-circle-fill me-1"></i>Uang Pas</span>';
      } else {
        changeStatusBadge.innerHTML = '<span class="chip-tag" style="background: rgba(34, 197, 94, 0.2); color: #22c55e; font-weight:600;"><i class="bi bi-cash me-1"></i>Kembalian Ada</span>';
      }
      submitPaymentBtn.disabled = false;
    } else {
      const deficiency = Math.abs(change);
      displayChange.textContent = '-Rp ' + formatRupiah(deficiency) + ' (Kurang)';
      displayChange.className = 'text-mono fs-4 fw-bold text-danger';
      changeBox.style.background = 'rgba(239, 68, 68, 0.1)';
      changeBox.style.borderLeftColor = '#ef4444';
      changeStatusBadge.innerHTML = '<span class="chip-tag" style="background: rgba(239, 68, 68, 0.2); color: #f87171; font-weight:600;"><i class="bi bi-exclamation-circle-fill me-1"></i>Kurang</span>';
    }
  };

  // Set nilai default cash awal = Grand Total (Uang Pas)
  cashAmountInput.value = formatRupiah(grandTotal);
  actualPaymentAmount.value = grandTotal;
  window.updateChangeDisplay(grandTotal);

  // Input Uang Tunai Realtime
  cashAmountInput.addEventListener('input', function() {
    const rawVal = this.value.replace(/[^0-9]/g, '');
    const numVal = parseFloat(rawVal) || 0;
    actualPaymentAmount.value = numVal;
    if (rawVal) {
      this.value = formatRupiah(numVal);
    } else {
      this.value = '';
    }
    window.updateChangeDisplay(numVal);
  });

  // Quick Cash Buttons
  quickCashButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      const amount = parseFloat(this.dataset.amount) || 0;
      cashAmountInput.value = formatRupiah(amount);
      actualPaymentAmount.value = amount;
      window.updateChangeDisplay(amount);
    });
  });

  // Handle Form Submit via AJAX
  paymentForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const selectedMethod = document.getElementById('paymentMetodeInput').value;
    const amountVal = parseFloat(actualPaymentAmount.value) || 0;

    if (selectedMethod === 'cash' && amountVal < grandTotal) {
      NexoraToast('Nominal uang tunai kurang dari total tagihan!', 'danger');
      cashAmountInput.focus();
      return;
    }

    if (selectedMethod === 'debit') {
      actualPaymentAmount.value = grandTotal;
      if (!paymentReference.value.trim()) {
        NexoraToast('Nomor referensi / trace EDC wajib diisi untuk pembayaran debit!', 'danger');
        paymentReference.focus();
        return;
      }
    }

    // Button loading
    submitPaymentBtn.disabled = true;
    submitPaymentBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses Pembayaran...';

    const formData = new FormData(paymentForm);

    setTimeout(() => {
      fetch(paymentForm.action, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: formData
      })
      .then(res => res.json().then(data => ({ status: res.status, body: data })))
      .then(result => {
        if (result.status === 200 && result.body.success) {
          NexoraToast(result.body.message || 'Pembayaran berhasil!', 'success');
          setTimeout(() => {
            if (result.body.redirect_url) {
              window.open(result.body.redirect_url, '_blank');
            }
            window.location.href = result.body.show_url || '{{ route("admin.order.show", $order) }}';
          }, 800);
        } else {
          submitPaymentBtn.disabled = false;
          submitPaymentBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Proses & Selesaikan Pembayaran';
          NexoraToast(result.body.message || 'Terjadi kesalahan saat memproses pembayaran.', 'danger');
        }
      })
      .catch(err => {
        submitPaymentBtn.disabled = false;
        submitPaymentBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Proses & Selesaikan Pembayaran';
        NexoraToast('Gagal menghubungi server. Silakan coba lagi.', 'danger');
      });
    }, 400);
  });
});
</script>
@endpush
