@extends('admin.layouts.app')

@section('title', 'Detail Riwayat Stok')

@php $activeMenu = 'history-stock' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Riwayat Stok</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.history.stock.index') }}">Riwayat Stok</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>#{{ $history->stock_history_id }}</span>
    </div>
  </div>
  <a href="{{ route('admin.history.stock.index') }}" class="btn btn-outline-soft">Kembali</a>
</div>

<div class="card mb-3">
  <div class="card-header-flex">
    <h6>
      <i class="bi bi-info-circle me-2"></i>Informasi Riwayat
      @if($history->action_type == 'create')
        <span class="pill pill-success ms-2" style="font-size:0.65rem;">Buat</span>
      @elseif($history->action_type == 'update')
        <span class="pill pill-info ms-2" style="font-size:0.65rem;">Ubah</span>
      @elseif($history->action_type == 'delete')
        <span class="pill pill-danger ms-2" style="font-size:0.65rem;">Hapus</span>
      @endif
    </h6>
  </div>
  <div class="card-body p-0">
    <table class="detail-table">
      <tr>
        <td class="detail-label">Tanggal</td>
        <td class="detail-value">{{ $history->created_at ? date('d/m/Y H:i:s', strtotime($history->created_at)) : '-' }}</td>
      </tr>
      <tr>
        <td class="detail-label">Aksi</td>
        <td class="detail-value">
          @if($history->action_type == 'create') Buat
          @elseif($history->action_type == 'update') Ubah
          @elseif($history->action_type == 'delete') Hapus
          @else {{ $history->action_type }}
          @endif
        </td>
      </tr>
      <tr>
        <td class="detail-label">Diubah oleh</td>
        <td class="detail-value">{{ $history->changed_by ?? '-' }}</td>
      </tr>
    </table>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header-flex">
    <h6><i class="bi bi-box-seam me-2"></i>Data Stok</h6>
  </div>
  <div class="card-body p-0">
    <table class="detail-table">
      <tr>
        <td class="detail-label">Kode</td>
        <td class="detail-value text-mono">{{ $history->stock_code ?? '-' }}</td>
      </tr>
      <tr>
        <td class="detail-label">Nama</td>
        <td class="detail-value fw-semibold">{{ $history->stock_name }}</td>
      </tr>
      <tr>
        <td class="detail-label">Tipe</td>
        <td class="detail-value">{{ $history->stock_type ?? '-' }}</td>
      </tr>
      <tr>
        <td class="detail-label">Unit</td>
        <td class="detail-value">{{ $history->stock_unit ?? '-' }}</td>
      </tr>
      <tr>
        <td class="detail-label">Jumlah</td>
        <td class="detail-value text-mono {{ $history->stock_amount <= 0 ? 'text-danger' : '' }}">{{ number_format($history->stock_amount) }}</td>
      </tr>
      <tr>
        <td class="detail-label">Harga</td>
        <td class="detail-value text-mono">{{ $history->stock_price ? 'Rp '.number_format($history->stock_price, 0) : '-' }}</td>
      </tr>
    </table>
  </div>
</div>

{{-- Perbandingan Sebelum/Sesudah (kalo update) --}}
@if($history->action_type == 'update' && $previous)
<div class="card mb-3">
  <div class="card-header-flex">
    <h6><i class="bi bi-arrow-left-right me-2"></i>Perubahan</h6>
    <span class="chip-tag">Sebelum → Sesudah</span>
  </div>
  <div class="card-body p-0">
    <table class="detail-table">
      @php
        $fields = [
          ['label' => 'Kode', 'before' => $previous->stock_code, 'after' => $history->stock_code],
          ['label' => 'Nama', 'before' => $previous->stock_name, 'after' => $history->stock_name],
          ['label' => 'Tipe', 'before' => $previous->stock_type, 'after' => $history->stock_type],
          ['label' => 'Unit', 'before' => $previous->stock_unit, 'after' => $history->stock_unit],
          ['label' => 'Jumlah', 'before' => (string)$previous->stock_amount, 'after' => (string)$history->stock_amount],
          ['label' => 'Harga', 'before' => $previous->stock_price ? 'Rp '.number_format($previous->stock_price,0) : '-', 'after' => $history->stock_price ? 'Rp '.number_format($history->stock_price,0) : '-'],
        ];
      @endphp
      @foreach($fields as $f)
      <tr class="{{ $f['before'] != $f['after'] ? 'table-row-changed' : '' }}">
        <td class="detail-label">{{ $f['label'] }}</td>
        <td class="detail-value">
          @if($f['before'] != $f['after'])
            <span style="color:var(--danger);text-decoration:line-through;margin-right:8px;">{{ $f['before'] ?: '-' }}</span>
            <i class="bi bi-arrow-right" style="color:var(--text-muted);margin-right:8px;"></i>
            <span style="color:var(--success);font-weight:600;">{{ $f['after'] ?: '-' }}</span>
          @else
            <span style="color:var(--text-muted);">{{ $f['after'] ?: '-' }}</span>
          @endif
        </td>
      </tr>
      @endforeach
    </table>
  </div>
</div>
@endif

@if(session('error'))
  <script>document.addEventListener('DOMContentLoaded', function() { NexoraToast('{{ session('error') }}', 'danger'); });</script>
@endif
@endsection

@push('styles')
<style>
.table-row-changed td {
  background: rgba(37,99,235,0.03);
}
.table-row-changed .detail-label {
  font-weight: 600;
}
</style>
@endpush
