@extends('guest.omah_kopi_jogja.layouts.app')

@section('title', 'Konfirmasi Pesanan')

@section('content')
<div class="max-w-3xl mx-auto">
  
  <!-- Back Button & Header -->
  <div class="flex items-center gap-3 mb-6">
    <a href="{{ route('guest.index', $table->table_id) }}" class="p-2 text-primary hover:bg-surface-container-high rounded-full transition-colors" title="Kembali ke Menu">
      <span class="material-symbols-outlined">arrow_back</span>
    </a>
    <div>
      <h2 class="font-headline font-black text-2xl text-on-background">Konfirmasi Pesanan</h2>
      <p class="text-xs text-on-surface-variant font-bold">Meja {{ $table->table_number }} • {{ $outlet->outlet_name ?? 'Omah Kopi Jogja' }}</p>
    </div>
  </div>

  @if(session('error') || $errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3.5 rounded-2xl flex items-start gap-3 shadow-xs">
      <span class="material-symbols-outlined text-red-600 flex-shrink-0 mt-0.5">error</span>
      <div class="text-sm font-headline">
        <div class="font-bold">Gagal Mengirim Pesanan</div>
        <div class="text-xs mt-0.5 text-red-600">{{ session('error') ?? $errors->first() }}</div>
      </div>
    </div>
  @endif

  <!-- Ordered Items Bento Card -->
  <div class="bg-white rounded-2xl p-6 border border-surface-variant shadow-xs mb-6">
    <h3 class="font-headline font-extrabold text-base text-on-background mb-4 flex items-center gap-2 border-b border-surface-variant pb-3">
      <span class="material-symbols-outlined text-primary fill-icon">shopping_bag</span>
      Rincian Pesanan
    </h3>

    <div class="divide-y divide-surface-variant">
      <!-- Product Items -->
      @foreach($items as $item)
        @php $p = $item['product']; @endphp
        <div class="py-3.5 first:pt-0 flex items-center justify-between gap-3">
          <div class="flex items-center gap-3 flex-grow">
            @if($p->product_image)
              <img src="{{ asset('storage/' . $p->product_image) }}" alt="" class="w-14 h-14 rounded-xl object-cover border border-surface-variant flex-shrink-0"/>
            @else
              <div class="w-14 h-14 rounded-xl bg-surface-container-high flex items-center justify-center text-on-surface-variant flex-shrink-0">
                <span class="material-symbols-outlined">coffee</span>
              </div>
            @endif
            <div>
              <h4 class="font-headline font-bold text-sm text-on-background">{{ $p->product_name }}</h4>
              <div class="text-xs text-on-surface-variant">
                @if($item['qty'] > 1)<span class="font-bold text-primary">{{ $item['qty'] }}x</span> @endif
                Rp {{ number_format($item['price'], 0, ',', '.') }}
              </div>
              @if($item['note'])
                <div class="text-xs text-on-surface-variant bg-surface-container-low px-2 py-0.5 rounded mt-1 inline-block">📝 {{ $item['note'] }}</div>
              @endif
            </div>
          </div>

          <div class="text-right">
            <div class="font-headline font-black text-sm text-on-background">
              Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
            </div>
            @if($item['discount_amount'] > 0)
              <div class="text-[11px] text-primary font-bold">
                -Rp {{ number_format($item['discount_amount'] * $item['qty'], 0, ',', '.') }}
              </div>
            @endif
          </div>
        </div>
      @endforeach

      <!-- Bundle Items -->
      @foreach($bundleRows ?? [] as $b)
        <div class="py-3.5 flex items-center justify-between gap-3">
          <div class="flex items-center gap-3 flex-grow">
            <div class="w-14 h-14 rounded-xl bg-primary text-white flex items-center justify-center font-bold flex-shrink-0">
              <span class="material-symbols-outlined text-2xl fill-icon">coffee</span>
            </div>
            <div>
              <div class="flex items-center gap-1.5">
                <span class="px-1.5 py-0.5 bg-primary text-white text-[10px] font-extrabold rounded">PAKET</span>
                <h4 class="font-headline font-bold text-sm text-on-background">{{ $b['bundle_name'] }}</h4>
              </div>
              <div class="text-xs text-on-surface-variant mt-0.5">
                @if($b['qty'] > 1)<span class="font-bold text-primary">{{ $b['qty'] }}x</span> @endif
                Rp {{ number_format($b['bundle_price'], 0, ',', '.') }}
              </div>
              <div class="text-[11px] text-on-surface-variant mt-1">
                @foreach($b['items'] as $bi)
                  <span class="inline-block bg-surface-container-low px-1.5 py-0.5 rounded mr-1">
                    {{ $bi['product_name'] ?? 'Produk' }} (x{{ $bi['quantity'] }})
                  </span>
                @endforeach
              </div>
            </div>
          </div>

          <div class="text-right font-headline font-black text-sm text-on-background">
            Rp {{ number_format($b['subtotal'], 0, ',', '.') }}
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- Voucher Code Bento Card -->
  <div class="bg-white rounded-2xl p-6 border border-surface-variant shadow-xs mb-6">
    <h3 class="font-headline font-extrabold text-base text-on-background mb-3 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">confirmation_number</span>
      Voucher Diskon
    </h3>
    
    <div class="flex gap-2">
      <input type="text" id="okVoucherInput" value="{{ old('voucher_code') }}" placeholder="Masukkan kode voucher..."
             class="flex-grow bg-surface-container-low border border-surface-variant rounded-xl px-4 py-2.5 text-sm uppercase font-headline font-bold focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all"/>
      <button type="button" id="okVoucherBtn" class="bg-secondary-container hover:bg-secondary-fixed text-on-secondary-container px-5 py-2.5 rounded-xl font-headline font-extrabold text-xs transition-all active:scale-95 shadow-xs">
        Gunakan
      </button>
    </div>
    <div id="okVoucherResult" class="mt-2 text-xs"></div>
  </div>

  <!-- Order Remarks Bento Card -->
  <div class="bg-white rounded-2xl p-6 border border-surface-variant shadow-xs mb-6">
    <h3 class="font-headline font-extrabold text-base text-on-background mb-3 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">edit_note</span>
      Catatan Pesanan
    </h3>
    <textarea id="okOrderRemark" rows="2" placeholder="Cth: kopi manis sedang, disajikan bersamaan..."
              class="w-full bg-surface-container-low border border-surface-variant rounded-xl p-3 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all"></textarea>
  </div>

  <!-- Price Summary Bento Card -->
  <div class="bg-white rounded-2xl p-6 border border-surface-variant shadow-xs mb-6 space-y-3">
    <div class="flex justify-between items-center text-sm font-headline">
      <span class="text-on-surface-variant font-medium">Subtotal Pesanan</span>
      <span class="font-bold text-on-background">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
    </div>

    <div id="okVoucherRow" class="flex justify-between items-center text-sm font-headline hidden">
      <span class="text-primary font-medium flex items-center gap-1">
        <span class="material-symbols-outlined text-[16px]">sell</span>
        Diskon Voucher
      </span>
      <span id="okVoucherAmount" class="font-bold text-primary">-Rp 0</span>
    </div>

    <div class="border-t border-surface-variant pt-3 flex justify-between items-center text-lg font-headline">
      <span class="font-black text-on-background">Total Bayar</span>
      <span id="okGrandTotal" class="font-black text-primary text-xl">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
    </div>
  </div>

  <!-- Submit Form -->
  <form id="okSubmitForm" method="POST" action="{{ route('guest.submit') }}">
    @csrf
    <input type="hidden" name="table_id" value="{{ $table->table_id }}"/>
    <input type="hidden" name="total_price" id="okFormTotalPrice" value="{{ $grandTotal }}"/>
    <input type="hidden" name="items" id="okFormItems"/>
    <input type="hidden" name="bundles" id="okFormBundles"/>
    <input type="hidden" name="voucher_code" id="okFormVoucherCode"/>
    <input type="hidden" name="order_remark" id="okFormOrderRemark"/>

    <button type="submit" id="okSubmitBtn" class="w-full bg-primary hover:bg-primary-hover text-white py-4 rounded-xl font-headline font-black text-base flex items-center justify-center gap-2 transition-all active:scale-95 shadow-md">
      <span class="material-symbols-outlined text-[22px]">send</span>
      <span>Kirim Pesanan Sekarang</span>
    </button>
  </form>

  <p class="text-xs text-center text-on-surface-variant mt-4 flex items-center justify-center gap-1">
    <span class="material-symbols-outlined text-[16px] text-outline">info</span>
    Pesanan akan langsung diteruskan ke dapur omah kopi.
  </p>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const items = @json($itemsJson);
  const bundles = @json($bundleRows ?? []);
  const initialGrandTotal = {{ $grandTotal }};

  const voucherInput = document.getElementById('okVoucherInput');
  const voucherBtn = document.getElementById('okVoucherBtn');
  const voucherResult = document.getElementById('okVoucherResult');
  const voucherRow = document.getElementById('okVoucherRow');
  const voucherAmountEl = document.getElementById('okVoucherAmount');
  const grandTotalEl = document.getElementById('okGrandTotal');

  const submitForm = document.getElementById('okSubmitForm');
  const submitBtn = document.getElementById('okSubmitBtn');

  let appliedVoucher = null;

  function updateTotals() {
    const disc = appliedVoucher ? appliedVoucher.amount : 0;
    const finalTotal = Math.max(0, initialGrandTotal - disc);

    grandTotalEl.textContent = formatRupiah(finalTotal);
    document.getElementById('okFormTotalPrice').value = finalTotal;

    if (appliedVoucher) {
      voucherRow.classList.remove('hidden');
      voucherAmountEl.textContent = '-' + formatRupiah(disc);
    } else {
      voucherRow.classList.add('hidden');
    }
  }

  // Voucher validation via AJAX
  voucherBtn.addEventListener('click', function() {
    const code = voucherInput.value.trim();
    if (!code) {
      NexoraToast('Masukkan kode voucher terlebih dahulu.', 'danger');
      return;
    }

    voucherBtn.disabled = true;
    voucherBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span>';

    fetch('{{ route("guest.check-voucher") }}', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: `voucher_code=${encodeURIComponent(code)}&grand_total=${initialGrandTotal}`
    })
    .then(r => r.json())
    .then(res => {
      voucherBtn.disabled = false;
      voucherBtn.textContent = 'Gunakan';

      if (res.ok) {
        appliedVoucher = {
          code: res.voucher_code,
          amount: res.voucher_amount
        };
        voucherResult.innerHTML = `
          <div class="flex items-center gap-1 text-green-700 font-headline font-bold bg-green-50 px-3 py-1.5 rounded-lg border border-green-200">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            Voucher ${res.voucher_name} berhasil digunakan (Potongan ${formatRupiah(res.voucher_amount)})
          </div>
        `;
        NexoraToast('Voucher berhasil digunakan!', 'success');
      } else {
        appliedVoucher = null;
        voucherResult.innerHTML = `
          <div class="flex items-center gap-1 text-primary font-headline font-bold bg-red-50 px-3 py-1.5 rounded-lg border border-red-200">
            <span class="material-symbols-outlined text-[18px]">error</span>
            ${res.message || 'Voucher tidak valid.'}
          </div>
        `;
        NexoraToast(res.message || 'Voucher tidak valid.', 'danger');
      }

      updateTotals();
    })
    .catch(() => {
      voucherBtn.disabled = false;
      voucherBtn.textContent = 'Gunakan';
      NexoraToast('Gagal memvalidasi voucher.', 'danger');
    });
  });

  // Submit Order Handler
  submitForm.addEventListener('submit', function(e) {
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
      <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
      <span>Mengirim Pesanan...</span>
    `;

    document.getElementById('okFormItems').value = JSON.stringify(items);
    document.getElementById('okFormBundles').value = JSON.stringify(bundles.map(b => ({
      bundle_id: b.bundle_id,
      bundle_name: b.bundle_name,
      bundle_price: b.bundle_price,
      qty: b.qty
    })));

    document.getElementById('okFormVoucherCode').value = appliedVoucher ? appliedVoucher.code : '';
    document.getElementById('okFormOrderRemark').value = document.getElementById('okOrderRemark').value.trim();

    // Clear saved local cart after submit
    sessionStorage.removeItem('ok_guest_cart_{{ $table->table_id }}');
    sessionStorage.removeItem('guest_cart_{{ $table->table_id }}');
  });

  // Auto-check voucher if pre-filled from old input
  const prefilledVoucher = voucherInput.value.trim();
  if (prefilledVoucher) {
    voucherBtn.click();
  }
});
</script>
@endpush
