@extends('guest.ignite_spice.layouts.app')

@section('title', 'Status Pesanan')

@section('content')
<div class="max-w-4xl mx-auto">

  <!-- Headline Section -->
  <div class="flex items-center justify-between mb-8">
    <div class="flex items-center gap-4">
      <div class="heat-gradient p-3.5 rounded-2xl text-white pulse-heat shadow-md">
        <span class="material-symbols-outlined text-3xl fill-icon">local_fire_department</span>
      </div>
      <div>
        <h2 class="font-headline font-black text-2xl md:text-3xl text-primary leading-tight">Ignite Tracker</h2>
        <p class="text-xs md:text-sm text-on-surface-variant font-bold">Status Pesanan Meja {{ $table->table_number }}</p>
      </div>
    </div>

    <button type="button" id="isRefreshBtn" class="bg-surface-container-high hover:bg-surface-variant text-on-surface p-2.5 rounded-full flex items-center justify-center transition-all active:scale-95 shadow-xs" title="Refresh Status">
      <span class="material-symbols-outlined text-[20px]">refresh</span>
    </button>
  </div>

  @if($orders->isEmpty())
    <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-surface-variant shadow-xs my-6">
      <span class="material-symbols-outlined text-5xl text-outline mb-2">receipt_long</span>
      <h3 class="font-headline font-extrabold text-base text-on-background">Belum ada pesanan aktif</h3>
      <p class="text-xs text-on-surface-variant mt-1 mb-6">Silakan pilih menu favoritmu terlebih dahulu.</p>
      <a href="{{ route('guest.index', $table->table_id) }}" class="inline-flex items-center gap-2 heat-gradient hover:opacity-90 text-white px-6 py-3 rounded-xl font-headline font-bold text-xs shadow-md transition-all active:scale-95">
        <span class="material-symbols-outlined text-[18px]">restaurant_menu</span>
        <span>Lihat Menu</span>
      </a>
    </div>
  @else
    <div class="space-y-6">
      @foreach($orders as $order)
        @php
          $status = $order->order_status;
          $isPaid = $order->isPaid();
          $isPre = ($paymentTiming ?? 'post_payment') === 'pre_payment';
          
          if ($isPre) {
            $statusLabel = match($status) {
              'pending' => 'Menunggu Pembayaran Kasir',
              'in_progress' => ($isPaid ? 'Lunas & Sedang Dimasak' : 'Sedang Dimasak'),
              'completed' => 'Selesai Disajikan',
              'cancelled' => 'Dibatalkan',
              default => ucfirst($status),
            };
          } else {
            $statusLabel = match($status) {
              'pending' => 'Menunggu Konfirmasi',
              'in_progress' => 'Sedang Dimasak',
              'completed' => 'Selesai & Lunas',
              'cancelled' => 'Dibatalkan',
              default => ucfirst($status),
            };
          }
          $stepActive = $status === 'in_progress' || $status === 'completed';
          $stepDone = $status === 'completed';
        @endphp

        <div class="bg-white rounded-2xl p-6 border border-surface-variant shadow-xs bento-card">
          <!-- Order Card Header -->
          <div class="flex flex-wrap items-center justify-between gap-2 mb-4 border-b border-surface-variant pb-3">
            <div>
              <span class="font-headline font-black text-base text-on-background">Pesanan #{{ $order->order_id }}</span>
              <div class="text-xs text-on-surface-variant font-medium mt-0.5">
                {{ $order->created_at->format('d M Y, H:i') }} WIB
              </div>
            </div>
            
            <div class="flex items-center gap-1.5 flex-wrap">
              @if($isPaid)
                <span class="px-2.5 py-0.5 rounded-full font-headline font-bold text-[11px] bg-green-100 text-green-800 flex items-center gap-1">
                  <span class="material-symbols-outlined text-[14px]">check</span> Lunas
                </span>
              @else
                <span class="px-2.5 py-0.5 rounded-full font-headline font-bold text-[11px] bg-amber-100 text-amber-800 flex items-center gap-1">
                  <span class="material-symbols-outlined text-[14px]">schedule</span> Belum Bayar
                </span>
              @endif

              <span class="px-3.5 py-1 rounded-full font-headline font-extrabold text-xs flex items-center gap-1.5 shadow-xs
              @if($status === 'pending') bg-yellow-100 text-yellow-800
              @elseif($status === 'in_progress') bg-orange-100 text-orange-800
              @elseif($status === 'completed') bg-green-100 text-green-800
              @else bg-red-100 text-red-800 @endif">
              <span class="w-2 h-2 rounded-full @if($status === 'pending') bg-yellow-500 animate-ping @elseif($status === 'in_progress') bg-orange-500 animate-pulse @elseif($status === 'completed') bg-green-500 @else bg-red-500 @endif"></span>
              {{ $statusLabel }}
            </span>
            </div>
          </div>

          @if($isPre && !$isPaid && $status === 'pending')
            <div class="mb-4 p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs flex items-start gap-2.5 shadow-xs">
              <span class="material-symbols-outlined text-amber-600 flex-shrink-0 text-[20px]">payments</span>
              <div>
                <strong class="font-bold">Silakan Lakukan Pembayaran di Kasir:</strong>
                <p class="mt-0.5 text-amber-800">Tunjukkan nomor pesanan <strong>#{{ $order->order_id }}</strong> atau Meja {{ $table->table_number }} ke kasir dengan total <strong>Rp {{ number_format($order->order_grand_total, 0, ',', '.') }}</strong> agar pesanan segera dimasak.</p>
              </div>
            </div>
          @endif

          <!-- Tracking Stepper Bar -->
          @if($status !== 'cancelled')
            <div class="my-6 px-2">
              <div class="grid grid-cols-3 gap-2 relative text-center">
                <!-- Progress Line Behind -->
                <div class="absolute top-5 left-1/6 right-1/6 h-1 bg-surface-container-high -z-0">
                  <div class="h-full heat-gradient transition-all duration-500" style="width: {{ $stepDone ? '100%' : ($stepActive ? '50%' : '0%') }}"></div>
                </div>

                <!-- Step 1: Diterima -->
                <div class="flex flex-col items-center gap-1.5 z-10">
                  <div class="w-10 h-10 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center font-bold shadow-xs border-2 border-white">
                    <span class="material-symbols-outlined text-[20px] fill-icon">check_circle</span>
                  </div>
                  <span class="font-headline font-bold text-xs text-on-background">{{ $isPre ? ($isPaid ? 'Sudah Bayar' : 'Bayar Kasir') : 'Diterima' }}</span>
                </div>

                <!-- Step 2: Dimasak -->
                <div class="flex flex-col items-center gap-1.5 z-10">
                  <div class="w-10 h-10 rounded-full {{ $stepActive ? 'heat-gradient text-white pulse-heat' : 'bg-surface-container-high text-on-surface-variant' }} flex items-center justify-center font-bold shadow-xs border-2 border-white">
                    <span class="material-symbols-outlined text-[20px] {{ $stepActive ? 'fill-icon' : '' }}">skillet</span>
                  </div>
                  <span class="font-headline font-bold text-xs {{ $stepActive ? 'text-primary' : 'text-on-surface-variant' }}">Dimasak</span>
                </div>

                <!-- Step 3: Selesai -->
                <div class="flex flex-col items-center gap-1.5 z-10">
                  <div class="w-10 h-10 rounded-full {{ $stepDone ? 'bg-green-600 text-white' : 'bg-surface-container-high text-on-surface-variant' }} flex items-center justify-center font-bold shadow-xs border-2 border-white">
                    <span class="material-symbols-outlined text-[20px]">task_alt</span>
                  </div>
                  <span class="font-headline font-bold text-xs {{ $stepDone ? 'text-green-700' : 'text-on-surface-variant' }}">Selesai</span>
                </div>
              </div>
            </div>
          @endif

          <!-- Products & Voucher Details -->
          <div class="bg-surface-container-low rounded-xl p-4 space-y-2 mb-4">
            @foreach($order->products as $p)
              <div class="flex items-center justify-between text-sm">
                <div>
                  <span class="font-headline font-bold text-primary">{{ $p->pivot->quantity }}x</span>
                  <span class="font-medium text-on-background ml-1">{{ $p->product_name }}</span>
                  @if($p->pivot->note)
                    <div class="text-xs text-on-surface-variant bg-white px-2 py-0.5 rounded border border-surface-variant mt-0.5">📝 {{ $p->pivot->note }}</div>
                  @endif
                </div>
              </div>
            @endforeach

            @if($order->vouchers->isNotEmpty())
              <div class="border-t border-surface-variant pt-2 mt-2">
                @foreach($order->vouchers as $v)
                  <div class="text-xs text-primary font-bold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">confirmation_number</span>
                    Voucher: {{ $v->voucher_code }} (-Rp {{ number_format($v->voucher_amount, 0, ',', '.') }})
                  </div>
                @endforeach
              </div>
            @endif
          </div>

          <!-- Order Footer Total -->
          <div class="flex items-center justify-between">
            <span class="font-headline font-bold text-xs text-on-surface-variant uppercase tracking-wider">Total Pesanan</span>
            <span class="font-headline font-black text-lg text-primary">Rp {{ number_format($order->order_grand_total, 0, ',', '.') }}</span>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <!-- Bottom Add Order Link -->
    <div class="text-center mt-8">
      <a href="{{ route('guest.index', $table->table_id) }}" class="inline-flex items-center gap-2 heat-gradient hover:opacity-90 text-white px-6 py-3 rounded-full font-headline font-bold text-xs shadow-md transition-all active:scale-95">
        <span class="material-symbols-outlined text-[18px]">add</span>
        <span>Tambah Pesanan Lain</span>
      </a>
    </div>
  @endif

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const refreshBtn = document.getElementById('isRefreshBtn');
  if (refreshBtn) {
    refreshBtn.addEventListener('click', function() {
      refreshBtn.disabled = true;
      refreshBtn.querySelector('.material-symbols-outlined').classList.add('animate-spin');
      setTimeout(() => location.reload(), 400);
    });
  }
});
</script>
@endpush
