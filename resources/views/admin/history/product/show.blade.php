@extends('admin.layouts.app')

@section('title', 'Detail Riwayat Produk')

@php $activeMenu = 'history-product' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Riwayat Produk</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.history.product.index') }}">Riwayat Produk</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>#{{ $history->product_history_id }}</span>
    </div>
  </div>
  <a href="{{ route('admin.history.product.index') }}" class="btn btn-outline-soft">Kembali</a>
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
      <tr>
        <td class="detail-label">Tanggal Efektif</td>
        <td class="detail-value">{{ $history->effective_date ? date('d/m/Y', strtotime($history->effective_date)) : '-' }}</td>
      </tr>
    </table>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header-flex">
    <h6><i class="bi bi-cup-hot me-2"></i>Data Produk</h6>
  </div>
  <div class="card-body p-0">
    <table class="detail-table">
      <tr>
        <td class="detail-label">Kode</td>
        <td class="detail-value text-mono">{{ $history->history_code ?? '-' }}</td>
      </tr>
      <tr>
        <td class="detail-label">Nama</td>
        <td class="detail-value fw-semibold">{{ $history->history_name }}</td>
      </tr>
      <tr>
        <td class="detail-label">Slug</td>
        <td class="detail-value text-mono">{{ $history->history_slug ?? '-' }}</td>
      </tr>
      <tr>
        <td class="detail-label">Harga</td>
        <td class="detail-value text-mono">{{ $history->history_price ? 'Rp '.number_format($history->history_price, 0) : '-' }}</td>
      </tr>
      <tr>
        <td class="detail-label">Diskon</td>
        <td class="detail-value text-mono">
          @if($history->history_discount)
            <span class="text-success">-{{ number_format($history->history_discount, $history->history_discount != (int)$history->history_discount ? 2 : 0) }}%</span>
          @else
            -
          @endif
        </td>
      </tr>
      <tr>
        <td class="detail-label">Status</td>
        <td class="detail-value">
          @if($history->history_status)
            <span class="pill pill-success">Aktif</span>
          @else
            <span class="pill pill-neutral">Nonaktif</span>
          @endif
        </td>
      </tr>
      <tr>
        <td class="detail-label">Keterangan Kategori</td>
        <td class="detail-value">{{ $history->category_remark ?? '-' }}</td>
      </tr>
      <tr>
        <td class="detail-label">Deskripsi</td>
        <td class="detail-value">{{ $history->history_description ?? '-' }}</td>
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
        $fmtHarga = function($v) { return $v ? 'Rp '.number_format($v, 0) : '-'; };
        $fmtDisk = function($v) { return $v ? '-'.number_format($v, $v != (int)$v ? 2 : 0).'%' : '-'; };
        $fields = [
          ['label' => 'Kode', 'before' => $previous->history_code, 'after' => $history->history_code],
          ['label' => 'Nama', 'before' => $previous->history_name, 'after' => $history->history_name],
          ['label' => 'Harga', 'before' => $fmtHarga($previous->history_price), 'after' => $fmtHarga($history->history_price)],
          ['label' => 'Diskon', 'before' => $fmtDisk($previous->history_discount), 'after' => $fmtDisk($history->history_discount)],
          ['label' => 'Status', 'before' => $previous->history_status ? 'Aktif' : 'Nonaktif', 'after' => $history->history_status ? 'Aktif' : 'Nonaktif'],
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
