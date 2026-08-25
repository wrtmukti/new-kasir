@extends('guest.midnight_social.layouts.app')

@section('title', 'Konfirmasi Pesanan')

@section('content')
<div class="max-w-3xl mx-auto">
  
  <!-- Back Button & Header -->
  <div class="flex items-center gap-3 mb-6">
    <a href="{{ route('guest.index', $table->table_id) }}" class="p-2 text-purple-300 hover:bg-white/10 rounded-full transition-colors" title="Kembali ke Menu">
      <span class="material-symbols-outlined">arrow_back</span>
    </a>
    <div>
      <h2 class="font-headline font-black text-2xl text-slate-100">Konfirmasi Pesanan</h2>
      <p class="text-xs text-slate-400 font-bold">Meja {{ $table->table_number }} • {{ $outlet->outlet_name ?? 'Midnight Social' }}</p>
    </div>
  </div>

  @if(session('error') || $errors->any())
    <div class="mb-6 bg-red-950/80 border border-red-500/50 text-red-300 px-4 py-3.5 rounded-2xl flex items-start gap-3 shadow-xs">
      <span class="material-symbols-outlined text-red-400 flex-shrink-0 mt-0.5">error</span>
      <div class="text-sm font-headline">
        <div class="font-bold text-red-200">Gagal Mengirim Pesanan</div>
        <div class="text-xs mt-0.5 text-red-300">{{ session('error') ?? $errors->first() }}</div>
      </div>
    </div>
  @endif

  <!-- Ordered Items Bento Card -->
  <div class="bg-[#0f172a]/90 rounded-2xl p-6 border border-white/10 shadow-lg mb-6">
    <h3 class="font-headline font-extrabold text-base text-slate-100 mb-4 flex items-center gap-2 border-b border-white/10 pb-3">
      <span class="material-symbols-outlined text-purple-400 fill-icon">shopping_bag</span>
      Rincian Pesanan
    </h3>

    <div class="divide-y divide-white/10">
      <!-- Product Items -->
      @foreach($items as $item)
        @php $p = $item['product']; @endphp
        <div class="py-3.5 first:pt-0 flex items-center justify-between gap-3">
          <div class="flex items-center gap-3 flex-grow">
            @if($p->product_image)
              <img src="{{ asset('storage/' . $p->product_image) }}" alt="" class="w-14 h-14 rounded-xl object-cover border border-white/10 flex-shrink-0"/>
            @else
              <div class="w-14 h-14 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 flex-shrink-0">
                <span class="material-symbols-outlined">local_bar</span>
              </div>
            @endif
            <div>
              <h4 class="font-headline font-bold text-sm text-slate-100">{{ $p->product_name }}</h4>
              <div class="text-xs text-slate-400">
                @if($item['qty'] > 1)<span class="font-bold text-purple-400">{{ $item['qty'] }}x</span> @endif
                Rp {{ number_format($item['price'], 0, ',', '.') }}
              </div>
              @if($item['note'])
                <div class="text-xs text-slate-300 bg-slate-800/80 px-2 py-0.5 rounded mt-1 inline-block">📝 {{ $item['note'] }}</div>
              @endif
            </div>
          </div>

          <div class="text-right">
            <div class="font-headline font-black text-sm text-slate-100">
              Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
            </div>
            @if($item['discount_amount'] > 0)
              <div class="text-[11px] text-purple-400 font-bold">
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
            <div class="w-14 h-14 rounded-xl bg-purple-950/60 flex items-center justify-center text-purple-300 flex-shrink-0 border border-purple-500/30">
              <span class="material-symbols-outlined text-2xl fill-icon">nightlife</span>
            </div>
            <div>
              <div class="flex items-center gap-1.5">
                <span class="px-1.5 py-0.5 bg-purple-600 text-white text-[10px] font-extrabold rounded">PAKET</span>
                <h4 class="font-headline font-bold text-sm text-slate-100">{{ $b['bundle_name'] }}</h4>
              </div>
              <div class="text-xs text-slate-400 mt-0.5">
                @if($b['qty'] > 1)<span class="font-bold text-purple-400">{{ $b['qty'] }}x</span> @endif
                Rp {{ number_format($b['bundle_price'], 0, ',', '.') }}
              </div>
              <div class="text-[11px] text-slate-400 mt-1">
                @foreach($b['items'] as $bi)
                  <span class="inline-block bg-slate-800 px-1.5 py-0.5 rounded mr-1">
                    {{ $bi['product_name'] ?? 'Produk' }} (x{{ $bi['quantity'] }})
                  </span>
                @endforeach
              </div>
            </div>
          </div>

          <div class="text-right font-headline font-black text-sm text-slate-100">
            Rp {{ number_format($b['subtotal'], 0, ',', '.') }}
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- Voucher Code Bento Card -->
  <div class="bg-[#0f172a]/90 rounded-2xl p-6 border border-white/10 shadow-lg mb-6">
    <h3 class="font-headline font-extrabold text-base text-slate-100 mb-3 flex items-center gap-2">
      <span class="material-symbols-outlined text-purple-400">confirmation_number</span>
      Voucher Diskon
    </h3>
    
    <div class="flex gap-2">
      <input type="text" id="msVoucherInput" value="{{ old('voucher_code') }}" placeholder="Masukkan kode voucher..."
             class="flex-grow bg-[#151b2d] border border-white/10 rounded-xl px-4 py-2.5 text-sm uppercase font-headline font-bold text-slate-100 placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all"/>
      <button type="button" id="msVoucherBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl font-headline font-extrabold text-xs transition-all active:scale-95 shadow-xs">
        Gunakan
      </button>
    </div>
    <div id="msVoucherResult" class="mt-2 text-xs"></div>
  </div>

  <!-- Order Remarks Bento Card -->
  <div class="bg-[#0f172a]/90 rounded-2xl p-6 border border-white/10 shadow-lg mb-6">
    <h3 class="font-headline font-extrabold text-base text-slate-100 mb-3 flex items-center gap-2">
      <span class="material-symbols-outlined text-purple-400">edit_note</span>
      Catatan Pesanan
    </h3>
    <textarea id="msOrderRemark" rows="2" placeholder="Cth: tolong dipisahkan es, disajikan bersamaan..."
              class="w-full bg-[#151b2d] border border-white/10 rounded-xl p-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all"></textarea>
  </div>

  <!-- Price Summary Bento Card -->
  <div class="bg-[#0f172a]/90 rounded-2xl p-6 border border-white/10 shadow-lg mb-6 space-y-3">
    <div class="flex justify-between items-center text-sm font-headline">
      <span class="text-slate-400 font-medium">Subtotal Pesanan</span>
      <span class="font-bold text-slate-100">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
    </div>

    <div id="msVoucherRow" class="flex justify-between items-center text-sm font-headline hidden">
      <span class="text-purple-400 font-medium flex items-center gap-1">
        <span class="material-symbols-outlined text-[16px]">sell</span>
        Diskon Voucher
      </span>
      <span id="msVoucherAmount" class="font-bold text-purple-400">-Rp 0</span>
    </div>

    <div class="border-t border-white/10 pt-3 flex justify-between items-center text-lg font-headline">
      <span class="font-black text-slate-100">Total Bayar</span>
      <span id="msGrandTotal" class="font-black text-purple-300 text-xl">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
    </div>
  </div>

  <!-- Submit Form -->
  <form id="msSubmitForm" method="POST" action="{{ route('guest.submit') }}">
    @csrf
    <input type="hidden" name="table_id" value="{{ $table->table_id }}"/>
    <input type="hidden" name="total_price" id="msFormTotalPrice" value="{{ $grandTotal }}"/>
    <input type="hidden" name="items" id="msFormItems"/>
    <input type="hidden" name="bundles" id="msFormBundles"/>
    <input type="hidden" name="voucher_code" id="msFormVoucherCode"/>
    <input type="hidden" name="order_remark" id="msFormOrderRemark"/>

    <button type="submit" id="msSubmitBtn" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-4 rounded-xl font-headline font-black text-base flex items-center justify-center gap-2 transition-all active:scale-95 shadow-lg">
      <span class="material-symbols-outlined text-[22px]">send</span>
      <span>Kirim Pesanan Sekarang</span>
    </button>
  </form>

  @if(($paymentTiming ?? 'post_payment') === 'pre_payment')
    <div class="mt-4 p-3.5 bg-purple-950/40 border border-purple-800 rounded-xl text-purple-200 text-xs flex items-start gap-2.5 shadow-xs">
      <span class="material-symbols-outlined text-purple-400 flex-shrink-0 text-[18px]">info</span>
      <div>
        <strong class="font-bold text-white">Mode Bayar di Awal:</strong>
        <p class="mt-0.5 text-purple-300">Setelah mengirim pesanan, silakan tunjukkan nomor meja ke kasir untuk melakukan pembayaran agar pesanan segera dimasak dapur.</p>
      </div>
    </div>
  @else
    <p class="text-xs text-center text-slate-400 mt-4 flex items-center justify-center gap-1">
      <span class="material-symbols-outlined text-[16px] text-slate-500">info</span>
      Pesanan akan langsung diteruskan ke dapur cafe.
    </p>
  @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const items = @json($itemsJson);
  const bundles = @json($bundleRows ?? []);
  const initialGrandTotal = {{ $grandTotal }};

  const voucherInput = document.getElementById('msVoucherInput');
  const voucherBtn = document.getElementById('msVoucherBtn');
  const voucherResult = document.getElementById('msVoucherResult');
  const voucherRow = document.getElementById('msVoucherRow');
  const voucherAmountEl = document.getElementById('msVoucherAmount');
  const grandTotalEl = document.getElementById('msGrandTotal');

  const submitForm = document.getElementById('msSubmitForm');
  const submitBtn = document.getElementById('msSubmitBtn');

  let appliedVoucher = null;

  function updateTotals() {
    const disc = appliedVoucher ? appliedVoucher.amount : 0;
    const finalTotal = Math.max(0, initialGrandTotal - disc);

    grandTotalEl.textContent = formatRupiah(finalTotal);
    document.getElementById('msFormTotalPrice').value = finalTotal;

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
          <div class="flex items-center gap-1 text-emerald-400 font-headline font-bold bg-emerald-950/60 px-3 py-1.5 rounded-lg border border-emerald-500/40">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            Voucher ${res.voucher_name} berhasil digunakan (Potongan ${formatRupiah(res.voucher_amount)})
          </div>
        `;
        NexoraToast('Voucher berhasil digunakan!', 'success');
      } else {
        appliedVoucher = null;
        voucherResult.innerHTML = `
          <div class="flex items-center gap-1 text-red-400 font-headline font-bold bg-red-950/60 px-3 py-1.5 rounded-lg border border-red-500/40">
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

    document.getElementById('msFormItems').value = JSON.stringify(items);
    document.getElementById('msFormBundles').value = JSON.stringify(bundles.map(b => ({
      bundle_id: b.bundle_id,
      bundle_name: b.bundle_name,
      bundle_price: b.bundle_price,
      qty: b.qty
    })));

    document.getElementById('msFormVoucherCode').value = appliedVoucher ? appliedVoucher.code : '';
    document.getElementById('msFormOrderRemark').value = document.getElementById('msOrderRemark').value.trim();

    // Clear saved local cart after submit
    sessionStorage.removeItem('guest_cart_{{ $table->table_id }}');
    sessionStorage.removeItem('ms_guest_cart_{{ $table->table_id }}');
  });

  // Auto-check voucher if pre-filled from old input
  const prefilledVoucher = voucherInput.value.trim();
  if (prefilledVoucher) {
    voucherBtn.click();
  }
});
</script>
@endpush
