@extends('admin.layouts.app')

@section('title', 'Buat Pesanan')

@php $activeMenu = 'order' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Buat Pesanan</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.order.index') }}">Pesan</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Buat Pesanan</span>
    </div>
  </div>
</div>

<form method="POST" action="{{ route('admin.order.store') }}" id="orderForm">
  @csrf

  {{-- Detail Pesanan --}}
  <div class="card mb-3">
    <div class="card-header-flex">
      <h6>Detail Pesanan</h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <div class="input-skeleton">
            <label class="form-label-modern">Tipe Pesanan</label>
            <select name="order_type" class="form-select-modern @error('order_type') is-invalid @enderror" required>
              <option value="dine_in" {{ old('order_type') == 'dine_in' ? 'selected' : '' }}>Dine In</option>
              <option value="take_away" {{ old('order_type') == 'take_away' ? 'selected' : '' }}>Take Away</option>
              <option value="delivery" {{ old('order_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
            </select>
            @error('order_type') <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span> @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="input-skeleton">
            <label class="form-label-modern">Meja</label>
            <select name="order_table_id" class="form-select-modern">
              <option value="">— Pilih Meja —</option>
              @foreach($tables as $table)
                <option value="{{ $table->table_id }}" {{ old('order_table_id') == $table->table_id ? 'selected' : '' }}>
                  Meja {{ $table->table_number }} ({{ $table->table_capacity }} kursi)
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="input-skeleton">
            <label class="form-label-modern">Pelanggan</label>
            <select name="order_customer_id" class="form-select-modern">
              <option value="">— Pilih Pelanggan —</option>
              @foreach($customers as $customer)
                <option value="{{ $customer->customer_id }}" {{ old('order_customer_id') == $customer->customer_id ? 'selected' : '' }}>
                  {{ $customer->customer_name }} {{ $customer->customer_phone ? '('.$customer->customer_phone.')' : '' }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-12">
          <div class="input-skeleton">
            <label class="form-label-modern">Catatan Pesanan</label>
            <textarea name="order_remark" class="form-control-modern @error('order_remark') is-invalid @enderror" rows="2">{{ old('order_remark') }}</textarea>
            @error('order_remark') <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span> @enderror
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Daftar Item + Notes --}}
  <div class="card mb-3">
    <div class="card-header-flex">
      <h6>Item Pesanan</h6>
      <span class="chip-tag" id="itemCount">{{ count($cart) }} item</span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table-modern">
          <thead>
            <tr>
              <th>Produk</th>
              <th style="width:100px;">Harga</th>
              <th style="width:80px;">Diskon</th>
              <th style="width:80px;">Qty</th>
              <th style="width:150px;">Subtotal</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($cart as $index => $item)
              @php
                $price = (float) $item['price'];
                $discType = $item['discountType'] ?? '';
                $discVal = (float) ($item['discountValue'] ?? 0);
                $discAmt = $discType === 'percentage' && $discVal > 0 ? $price * $discVal / 100 : ($discType === 'nominal' && $discVal > 0 ? min($discVal, $price) : 0);
                $discAmt = min($discAmt, $price);
                $subtotal = ($price - $discAmt) * $item['qty'];
                $grandTotal += $subtotal;
              @endphp
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    @if(!empty($item['image']))
                      <img src="{{ $item['image'] }}" class="cart-item-img">
                    @else
                      <span class="cart-item-img" style="background:var(--bg-elevated-2);display:inline-flex;align-items:center;justify-content:center;"><i class="bi bi-image" style="color:var(--text-muted);"></i></span>
                    @endif
                    <div>
                      <div style="font-weight:500;">{{ $item['name'] }}</div>
                    </div>
                  </div>
                  <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item['id'] }}">
                  <input type="hidden" name="items[{{ $index }}][product_name]" value="{{ $item['name'] }}">
                  <input type="hidden" name="items[{{ $index }}][price]" value="{{ $item['price'] }}">
                  <input type="hidden" name="items[{{ $index }}][qty]" value="{{ $item['qty'] }}">
                </td>
                <td class="text-mono">Rp {{ number_format($price, 0) }}</td>
                <td>@if($discAmt > 0)<span style="color:var(--danger);font-size:0.85rem;">-Rp {{ number_format($discAmt, 0) }}</span>@else<span class="text-muted-c">-</span>@endif</td>
                <td>{{ $item['qty'] }}</td>
                <td class="text-mono">Rp {{ number_format($subtotal, 0) }}</td>
                <td>
                  <input type="text" name="items[{{ $index }}][note]" class="form-control-modern" placeholder="Catatan..." value="{{ old('items.'.$index.'.note') }}" style="min-width:120px;">
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="4" class="text-end fw-bold">Grand Total</td>
              <td class="text-mono fw-bold" style="color:var(--accent-1);font-size:1.05rem;">Rp {{ number_format($grandTotal, 0) }}</td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  {{-- Voucher --}}
  <div class="card mb-3">
    <div class="card-header-flex">
      <h6>Voucher</h6>
      <span class="text-muted-c" style="font-size:0.8rem;">Opsional</span>
    </div>
    <div class="card-body">
      <div class="row g-3 align-items-end">
        <div class="col-md-6">
          <div class="input-skeleton">
            <label class="form-label-modern">Kode Voucher</label>
            <input type="text" name="voucher_code" id="voucherCode" class="form-control-modern @error('voucher_code') is-invalid @enderror" value="{{ old('voucher_code') }}" placeholder="Masukkan kode voucher..." maxlength="50">
            @error('voucher_code') <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span> @enderror
          </div>
        </div>
        <div class="col-md-3">
          <button type="button" class="btn btn-outline-soft" id="checkVoucherBtn" style="width:100%;">
            <i class="bi bi-ticket-perforated me-1"></i>Cek Voucher
          </button>
        </div>
        <div class="col-md-3">
          <div id="voucherResult" style="display:none;">
            <div class="d-flex align-items-center gap-2 p-2" style="background:var(--bg-elevated-2);border-radius:var(--radius-sm);">
              <i class="bi bi-check-circle-fill" style="color:var(--success);"></i>
              <div>
                <div style="font-size:0.85rem;font-weight:500;" id="voucherName"></div>
                <div style="font-size:0.75rem;color:var(--text-muted);">Potongan: <span id="voucherDiscountDisplay" class="text-mono"></span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Tombol --}}
  <div class="d-flex justify-content-end gap-2">
    <a href="{{ route('admin.order.index') }}" class="btn btn-outline-soft">Kembali</a>
    <button type="button" class="btn btn-primary-grad" id="confirmBtn">
      <i class="bi bi-check-lg me-1"></i>Konfirmasi Pesanan
    </button>
  </div>
</form>

{{-- MODAL KONFIRMASI --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="mb-0"><i class="bi bi-check2-circle me-2"></i>Konfirmasi Pesanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-3">
          <i class="bi bi-receipt" style="font-size:2.5rem;color:var(--accent-1);display:block;margin-bottom:0.5rem;"></i>
          <p class="mb-1" style="font-weight:500;">Yakin ingin membuat pesanan ini?</p>
          <p class="text-muted-c" style="font-size:0.85rem;">Pastikan semua item dan keterangan sudah benar.</p>
        </div>
        <div class="d-flex justify-content-between px-2 py-2" style="background:var(--bg-elevated-2);border-radius:var(--radius-sm);">
          <span>Total Item</span>
          <span class="fw-bold" id="modalItemCount">{{ count($cart) }}</span>
        </div>
        <div class="d-flex justify-content-between px-2 py-2 mt-1" style="background:var(--bg-elevated-2);border-radius:var(--radius-sm);">
          <span>Grand Total</span>
          <span class="fw-bold text-mono" id="modalGrandTotal" style="color:var(--accent-1);">
            Rp {{ number_format($grandTotal, 0) }}
          </span>
        </div>
        <div id="modalVoucherInfo" style="display:none;" class="d-flex justify-content-between px-2 py-2 mt-1" style="background:var(--bg-elevated-2);border-radius:var(--radius-sm);">
          <span>Potongan Voucher</span>
          <span class="fw-bold text-mono" id="modalVoucherAmount" style="color:var(--danger);">-Rp 0</span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-soft" data-bs-dismiss="modal">Cek Lagi</button>
        <button type="button" class="btn btn-primary-grad" id="submitBtn">
          <i class="bi bi-check-lg me-1"></i>Ya, Buat Pesanan
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.cart-item-img { width:40px; height:40px; object-fit:cover; border-radius:var(--radius-sm); flex-shrink:0; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('orderForm');
  const confirmBtn = document.getElementById('confirmBtn');
  const submitBtn = document.getElementById('submitBtn');
  const voucherCode = document.getElementById('voucherCode');
  const checkVoucherBtn = document.getElementById('checkVoucherBtn');
  const voucherResult = document.getElementById('voucherResult');
  const voucherName = document.getElementById('voucherName');
  const voucherDiscountDisplay = document.getElementById('voucherDiscountDisplay');
  const modalVoucherInfo = document.getElementById('modalVoucherInfo');
  const modalVoucherAmount = document.getElementById('modalVoucherAmount');
  const modalGrandTotal = document.getElementById('modalGrandTotal');

  let grandTotal = {{ $grandTotal }};
  let voucherAmount = 0;

  confirmBtn.addEventListener('click', function() {
    var modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
  });

  submitBtn.addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';
    form.submit();
  });

  // Cek voucher via AJAX
  checkVoucherBtn.addEventListener('click', function() {
    const code = voucherCode.value.trim();
    if (!code) {
      NexoraToast('Masukkan kode voucher terlebih dahulu.', 'danger');
      return;
    }

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengecek...';

    fetch('{{ route("admin.order.check-voucher") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ voucher_code: code, grand_total: grandTotal })
    })
    .then(res => res.json())
    .then(data => {
      if (data.ok) {
        voucherAmount = data.voucher_amount;
        voucherResult.style.display = 'block';
        voucherName.textContent = data.voucher_name;
        voucherDiscountDisplay.textContent = '-Rp ' + new Intl.NumberFormat('id-ID').format(voucherAmount);

        // Update modal
        const afterVoucher = grandTotal - voucherAmount;
        modalVoucherInfo.style.display = 'flex';
        modalVoucherAmount.textContent = '-Rp ' + new Intl.NumberFormat('id-ID').format(voucherAmount);
        modalGrandTotal.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(afterVoucher);

        NexoraToast('Voucher berlaku! Potongan Rp ' + new Intl.NumberFormat('id-ID').format(voucherAmount), 'success');
      } else {
        voucherResult.style.display = 'none';
        voucherAmount = 0;
        modalVoucherInfo.style.display = 'none';
        modalGrandTotal.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);
        NexoraToast(data.message || 'Voucher tidak valid.', 'danger');
      }
    })
    .catch(() => {
      NexoraToast('Gagal mengecek voucher.', 'danger');
    })
    .finally(() => {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-ticket-perforated me-1"></i>Cek Voucher';
    });
  });

  // Reset voucher state kalo kode diubah
  voucherCode.addEventListener('input', function() {
    voucherResult.style.display = 'none';
    voucherAmount = 0;
    modalVoucherInfo.style.display = 'none';
    modalGrandTotal.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);
  });
});
</script>
@endpush
