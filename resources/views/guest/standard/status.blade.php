@extends('guest.standard.layouts.app')

@section('title', 'Status Pesanan')

@section('content')
{{-- Alert dari session --}}
@if(session('success'))
<div class="guest-alert guest-alert-success">
  <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="guest-alert guest-alert-danger">
  <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif

<div class="guest-narrow">
<div class="guest-page-head">
  <div>
    <h1 class="guest-page-title">Status Pesanan</h1>
    <p class="guest-page-sub">Meja {{ $table->table_number }}</p>
  </div>
  <button type="button" class="btn btn-outline-guest btn-sm" id="guestRefreshStatus">
    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
  </button>
</div>

@if($orders->isEmpty())
  <div class="guest-empty">
    <i class="bi bi-receipt-cutoff"></i>
    <p>Belum ada pesanan untuk meja ini.</p>
    <a href="{{ route('guest.index', $table->table_id) }}" class="btn btn-primary-guest mt-2">Lihat Menu</a>
  </div>
@else
  @foreach($orders as $order)
    @php
      $status = $order->order_status;
      $statusLabel = match($status) {
        'pending' => 'Menunggu Konfirmasi',
        'in_progress' => 'Sedang Disiapkan',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        default => ucfirst($status),
      };
      $statusIcon = match($status) {
        'pending' => 'bi-hourglass-split',
        'in_progress' => 'bi-fire',
        'completed' => 'bi-check2-circle',
        'cancelled' => 'bi-x-circle',
        default => 'bi-clock',
      };
      $stepActive = $status === 'in_progress' || $status === 'completed';
      $stepDone = $status === 'completed';
    @endphp

    <div class="guest-card guest-order-card">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="fw-semibold">Pesanan #{{ $order->order_id }}</div>
        <span class="guest-status-pill guest-status-{{ $status }}">
          <i class="bi {{ $statusIcon }} me-1"></i>{{ $statusLabel }}
        </span>
      </div>
      <small class="text-muted-guest">{{ $order->created_at->format('d M Y H:i') }}</small>

      {{-- Item --}}
      <div class="guest-order-items mt-2">
        @foreach($order->products as $p)
          <div class="d-flex justify-content-between">
            <span><strong>{{ $p->pivot->quantity }}x</strong> {{ $p->product_name }}</span>
            @if($p->pivot->note)<span class="guest-cart-note ms-1">📝 {{ $p->pivot->note }}</span>@endif
          </div>
        @endforeach
      </div>

      {{-- Progress stepper --}}
      <div class="guest-progress mt-3">
        <div class="guest-progress-step {{ $status !== 'cancelled' ? 'active' : '' }}">
          <i class="bi bi-check-lg"></i>
          <span>Diterima</span>
        </div>
        <div class="guest-progress-line {{ $stepActive ? 'active' : '' }}"></div>
        <div class="guest-progress-step {{ $stepActive ? 'active' : '' }}">
          <i class="bi {{ $status === 'in_progress' ? 'bi-fire' : 'bi-check-lg' }}"></i>
          <span>Dimasak</span>
        </div>
        <div class="guest-progress-line {{ $stepDone ? 'active' : '' }}"></div>
        <div class="guest-progress-step {{ $stepDone ? 'active' : '' }}">
          <i class="bi bi-check-lg"></i>
          <span>Selesai</span>
        </div>
      </div>

      @if($order->vouchers->isNotEmpty())
        <div class="guest-voucher-row mt-2">
          @foreach($order->vouchers as $v)
            <span class="guest-voucher-chip"><i class="bi bi-ticket-perforated me-1"></i>{{ $v->voucher_code }} · -{{ 'Rp ' . number_format($v->voucher_amount, 0) }}</span>
          @endforeach
        </div>
      @endif

      <div class="guest-order-total mt-2">
        Total: <strong>{{ 'Rp ' . number_format($order->order_grand_total, 0) }}</strong>
      </div>
    </div>
  @endforeach

  <div class="text-center mt-3 mb-4">
    <a href="{{ route('guest.index', $table->table_id) }}" class="btn btn-outline-guest">
      <i class="bi bi-plus-lg me-1"></i>Tambah Pesanan
    </a>
  </div>
@endif
</div>{{-- /.guest-narrow --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('guestRefreshStatus').addEventListener('click', function() {
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memuat...';
    setTimeout(() => location.reload(), 400);
  });
});
</script>
@endpush
