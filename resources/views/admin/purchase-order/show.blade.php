@extends('admin.layouts.app')

@section('title', 'Detail PO')

@php $activeMenu = 'purchase-order' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail PO: {{ $order->po_code }}</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.purchase-order.index') }}">Purchase Order</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>{{ $order->po_code }}</span>
    </div>
  </div>
  <div class="d-flex gap-2">
    @if($order->po_status === 'draft')
      <a href="{{ route('admin.purchase-order.edit', $order) }}" class="btn btn-primary-grad">
        <i class="bi bi-pencil me-1"></i>Edit
      </a>
    @endif
    @if(in_array($order->po_status, ['ordered', 'partial']))
      <a href="{{ route('admin.purchase-order.receiving.create', $order) }}" class="btn btn-success-grad">
        <i class="bi bi-box-arrow-in-down me-1"></i>Terima Barang
      </a>
    @endif
    <a href="{{ route('admin.purchase-order.index') }}" class="btn btn-outline-soft">Kembali</a>
  </div>
</div>

<div class="row g-3">
  {{-- Info PO --}}
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-info-circle me-2"></i>Informasi PO</h6></div>
      <div class="card-body">
        <table class="table table-borderless mb-0" style="font-size:0.9rem;">
          @php
            $sClass = match($order->po_status) { 'draft'=>'pill-neutral', 'ordered'=>'pill-warning', 'partial'=>'pill-info', 'completed'=>'pill-success', default=>'pill-danger' };
            $sLabel = match($order->po_status) { 'draft'=>'Draft', 'ordered'=>'Dipesan', 'partial'=>'Sebagian', 'completed'=>'Selesai', default=>'Batal' };
          @endphp
          <tr><td style="width:140px;color:var(--text-muted);white-space:nowrap;">Kode PO</td><td class="text-mono fw-semibold">{{ $order->po_code }}</td></tr>
          <tr><td style="color:var(--text-muted);white-space:nowrap;">Supplier</td><td>{{ $order->supplier?->supplier_name ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);white-space:nowrap;">Tanggal</td><td>{{ $order->po_date ? $order->po_date->format('d M Y H:i') : '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);white-space:nowrap;">Status</td><td><span class="pill {{ $sClass }}">{{ $sLabel }}</span></td></tr>
          <tr><td style="color:var(--text-muted);white-space:nowrap;">Total</td><td class="text-mono fw-semibold">Rp {{ number_format($order->po_total_amount, 0, ',', '.') }}</td></tr>
          <tr><td style="color:var(--text-muted);white-space:nowrap;">Catatan</td><td style="white-space:pre-wrap;">{{ $order->po_notes ?? '-' }}</td></tr>
        </table>
      </div>
    </div>
  </div>

  {{-- Items PO --}}
  <div class="col-12">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-box-seam me-2"></i>Item Pesanan</h6></div>
      <div class="card-body p-0">
        <table class="table-modern" id="itemsTable">
          <thead>
            <tr>
              <th>Bahan</th>
              <th>Qty Pesan</th>
              <th>Harga</th>
              <th>Subtotal</th>
              <th>Qty Diterima</th>
              <th>Sisa</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $item)
            <tr>
              <td class="fw-semibold">{{ $item->stock?->stock_name ?? '-' }}</td>
              <td class="text-mono">{{ $item->qty }} {{ $item->stock?->stock_unit ?? '' }}</td>
              <td class="text-mono">Rp {{ number_format($item->price, 0) }}</td>
              <td class="text-mono">Rp {{ number_format($item->subtotal, 0) }}</td>
              <td class="text-mono fw-semibold">{{ $item->received_qty }}</td>
              <td class="text-mono fw-semibold {{ ($item->qty - $item->received_qty) > 0 ? 'text-warning' : 'text-success' }}">
                {{ max(0, $item->qty - $item->received_qty) }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Receiving History --}}
  @if($order->receivings->count())
  <div class="col-12">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-box-arrow-in-down me-2"></i>Riwayat Penerimaan</h6></div>
      <div class="card-body p-0">
        <table class="table-modern">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Tanggal</th>
              <th>Status</th>
              <th>Catatan</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->receivings as $rcv)
            <tr>
              <td class="text-mono fw-semibold">{{ $rcv->receiving_code }}</td>
              <td>{{ $rcv->receiving_date ? $rcv->receiving_date->format('d M Y H:i') : '-' }}</td>
              <td><span class="pill pill-success">Selesai</span></td>
              <td>{{ $rcv->receiving_notes ?? '-' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection
