@extends('admin.layouts.app')

@section('title', 'Detail Transaksi')

@php $activeMenu = 'transaction' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Transaksi</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.transaction.index') }}">Transaksi</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>{{ $transaction->transaction_code ?? '#' . $transaction->transaction_id }}</span>
    </div>
  </div>
</div>

{{-- Info Transaksi --}}
<div class="card mb-3">
  <div class="card-header-flex">
    <h6>Info Transaksi</h6>
    @if($transaction->transaction_status == 'success')
      <span class="pill pill-success">Sukses</span>
    @else
      <span class="pill pill-neutral">{{ $transaction->transaction_status }}</span>
    @endif
  </div>
  <div class="card-body">
    <div class="detail-grid">
      <div class="detail-item">
        <span class="detail-label">Kode</span>
        <span class="detail-value text-mono">{{ $transaction->transaction_code ?? '-' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Tanggal</span>
        <span class="detail-value">{{ optional($transaction->created_at)->format('d M Y H:i') ?? '-' }}</span>
      </div>
      @if($transaction->transaction_remark)
      <div class="detail-item" style="grid-column:1/-1;">
        <span class="detail-label">Keterangan</span>
        <span class="detail-value">{{ $transaction->transaction_remark }}</span>
      </div>
      @endif
    </div>
  </div>
</div>

{{-- Item Transaksi --}}
<div class="card mb-3">
  <div class="card-header-flex">
    <h6>Item</h6>
    <span class="chip-tag">{{ $transaction->items->count() }} item</span>
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
          @foreach($transaction->items as $item)
            @php $grandTotal += (float) $item->subtotal; @endphp
            <tr>
              <td style="font-weight:500;">{{ $item->product_name }}</td>
              <td class="text-mono">Rp {{ number_format($item->price, 0) }}</td>
              <td>{{ $item->qty }}</td>
              <td class="text-mono">Rp {{ number_format($item->subtotal, 0) }}</td>
              <td>{{ $item->note ?? '-' }}</td>
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

<div class="d-flex justify-content-end">
  <a href="{{ route('admin.transaction.index') }}" class="btn btn-outline-soft">Kembali</a>
</div>
@endsection

@push('styles')
<style>
.detail-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:1.25rem; }
.detail-item { display:flex; flex-direction:column; gap:0.2rem; }
.detail-label { font-size:0.8rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.03em; }
.detail-value { font-size:0.95rem; color:var(--text-primary); }
</style>
@endpush
