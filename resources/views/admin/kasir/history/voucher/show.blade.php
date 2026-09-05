@extends('admin.layouts.app')

@section('title', 'Detail Riwayat Voucher')

@php $activeMenu = 'history-voucher' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Riwayat Voucher</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.history.voucher.index') }}">Riwayat Voucher</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>#{{ $history->history_id }}</span>
    </div>
  </div>
  <a href="{{ route('admin.history.voucher.index') }}" class="btn btn-outline-soft">Kembali</a>
</div>

<div class="card mb-3">
  <div class="card-header-flex">
    <h6>
      <i class="bi bi-info-circle me-2"></i>Informasi Riwayat
      @if($history->action == 'create')
        <span class="pill pill-success ms-2" style="font-size:0.65rem;">Buat</span>
      @elseif($history->action == 'update')
        <span class="pill pill-info ms-2" style="font-size:0.65rem;">Ubah</span>
      @elseif($history->action == 'delete')
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
          @if($history->action == 'create') Buat
          @elseif($history->action == 'update') Ubah
          @elseif($history->action == 'delete') Hapus
          @else {{ $history->action }}
          @endif
        </td>
      </tr>
      <tr>
        <td class="detail-label">Diubah oleh</td>
        <td class="detail-value">{{ $history->user_id ?? $history->created_by ?? '-' }}</td>
      </tr>
    </table>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header-flex">
    <h6><i class="bi bi-ticket-perforated me-2"></i>Data Voucher</h6>
  </div>
  <div class="card-body p-0">
    <table class="detail-table">
      <tr>
        <td class="detail-label">Kode</td>
        <td class="detail-value text-mono fw-semibold">{{ $history->voucher_code ?? '-' }}</td>
      </tr>
      <tr>
        <td class="detail-label">Nama</td>
        <td class="detail-value">{{ $history->voucher_name }}</td>
      </tr>
      <tr>
        <td class="detail-label">Tipe</td>
        <td class="detail-value">
          @if($history->voucher_type == 'percentage') Persen
          @elseif($history->voucher_type == 'nominal') Nominal
          @elseif($history->voucher_type == 'free_item') Free Item
          @else {{ $history->voucher_type ?? '-' }}
          @endif
        </td>
      </tr>
      <tr>
        <td class="detail-label">Nilai</td>
        <td class="detail-value text-mono">
          @if($history->voucher_type == 'percentage')
            -{{ number_format($history->voucher_value, $history->voucher_value != (int)$history->voucher_value ? 2 : 0) }}%
          @else
            {{ $history->voucher_value ? 'Rp '.number_format($history->voucher_value, 0) : '-' }}
          @endif
        </td>
      </tr>
      <tr>
        <td class="detail-label">Maks. Diskon</td>
        <td class="detail-value text-mono">{{ $history->voucher_max_discount ? 'Rp '.number_format($history->voucher_max_discount, 0) : '-' }}</td>
      </tr>
      <tr>
        <td class="detail-label">Min. Belanja</td>
        <td class="detail-value text-mono">{{ $history->voucher_min_purchase ? 'Rp '.number_format($history->voucher_min_purchase, 0) : '-' }}</td>
      </tr>
      <tr>
        <td class="detail-label">Berlaku</td>
        <td class="detail-value text-mono">
          {{ $history->voucher_start_date ? date('d/m/Y', strtotime($history->voucher_start_date)) : '-' }}
          s/d
          {{ $history->voucher_end_date ? date('d/m/Y', strtotime($history->voucher_end_date)) : '-' }}
        </td>
      </tr>
      <tr>
        <td class="detail-label">Status</td>
        <td class="detail-value">
          @if($history->voucher_status)
            <span class="pill pill-success">Aktif</span>
          @else
            <span class="pill pill-neutral">Nonaktif</span>
          @endif
        </td>
      </tr>
      <tr>
        <td class="detail-label">Deskripsi</td>
        <td class="detail-value">{{ $history->voucher_description ?? '-' }}</td>
      </tr>
    </table>
  </div>
</div>

{{-- Perbandingan Sebelum/Sesudah (kalo update) --}}
@if($history->action == 'update' && $previous)
<div class="card mb-3">
  <div class="card-header-flex">
    <h6><i class="bi bi-arrow-left-right me-2"></i>Perubahan</h6>
    <span class="chip-tag">Sebelum → Sesudah</span>
  </div>
  <div class="card-body p-0">
    <table class="detail-table">
      @php
        $fmtVal = function($h) {
          if ($h->voucher_type == 'percentage') return '-'.number_format($h->voucher_value, $h->voucher_value != (int)$h->voucher_value ? 2 : 0).'%';
          return $h->voucher_value ? 'Rp '.number_format($h->voucher_value, 0) : '-';
        };
        $fields = [
          ['label' => 'Kode', 'before' => $previous->voucher_code, 'after' => $history->voucher_code],
          ['label' => 'Nama', 'before' => $previous->voucher_name, 'after' => $history->voucher_name],
          ['label' => 'Tipe', 'before' => $previous->voucher_type, 'after' => $history->voucher_type],
          ['label' => 'Nilai', 'before' => $fmtVal($previous), 'after' => $fmtVal($history)],
          ['label' => 'Maks. Diskon', 'before' => $previous->voucher_max_discount ? 'Rp '.number_format($previous->voucher_max_discount,0) : '-', 'after' => $history->voucher_max_discount ? 'Rp '.number_format($history->voucher_max_discount,0) : '-'],
          ['label' => 'Min. Belanja', 'before' => $previous->voucher_min_purchase ? 'Rp '.number_format($previous->voucher_min_purchase,0) : '-', 'after' => $history->voucher_min_purchase ? 'Rp '.number_format($history->voucher_min_purchase,0) : '-'],
          ['label' => 'Status', 'before' => $previous->voucher_status ? 'Aktif' : 'Nonaktif', 'after' => $history->voucher_status ? 'Aktif' : 'Nonaktif'],
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
