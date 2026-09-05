@extends('admin.layouts.app')

@section('title', 'Detail Riwayat Diskon')

@php $activeMenu = 'history-discount' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Riwayat Diskon</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.history.discount.index') }}">Riwayat Diskon</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>#{{ $history->discount_history_id }}</span>
    </div>
  </div>
  <a href="{{ route('admin.history.discount.index') }}" class="btn btn-outline-soft">Kembali</a>
</div>

<div class="card mb-3">
  <div class="card-header-flex">
    <h6>
      <i class="bi bi-info-circle me-2"></i>Informasi Riwayat
      @if($history->reason == 'create')
        <span class="pill pill-success ms-2" style="font-size:0.65rem;">Buat</span>
      @elseif($history->reason == 'update')
        <span class="pill pill-info ms-2" style="font-size:0.65rem;">Ubah</span>
      @elseif($history->reason == 'delete')
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
          @if($history->reason == 'create') Buat
          @elseif($history->reason == 'update') Ubah
          @elseif($history->reason == 'delete') Hapus
          @else {{ $history->reason }}
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
    <h6><i class="bi bi-percent me-2"></i>Data Diskon</h6>
  </div>
  <div class="card-body p-0">
    <table class="detail-table">
      <tr>
        <td class="detail-label">Nama Diskon</td>
        <td class="detail-value fw-semibold">{{ $history->discount_name }}</td>
      </tr>
      <tr>
        <td class="detail-label">Tipe</td>
        <td class="detail-value">
          @if($history->discount_type == 'percentage')
            <span class="pill pill-info" style="font-size:0.65rem;">Persen</span>
          @else
            <span class="pill pill-warning" style="font-size:0.65rem;">Nominal</span>
          @endif
        </td>
      </tr>
      <tr>
        <td class="detail-label">Nilai</td>
        <td class="detail-value text-mono">
          @if($history->discount_type == 'percentage')
            {{ number_format($history->discount_value, 0) }}%
          @else
            Rp{{ number_format($history->discount_value, 0) }}
          @endif
        </td>
      </tr>
      <tr>
        <td class="detail-label">Maksimal Potongan</td>
        <td class="detail-value text-mono">
          @if($history->discount_max_amount)
            Rp{{ number_format($history->discount_max_amount, 0) }}
          @else
            <span class="text-muted-c">Tanpa batas</span>
          @endif
        </td>
      </tr>
      <tr>
        <td class="detail-label">Periode</td>
        <td class="detail-value">
          @if($history->start_date)
            {{ date('d/m/Y', strtotime($history->start_date)) }}
            @if($history->end_date)
              — {{ date('d/m/Y', strtotime($history->end_date)) }}
            @else
              <span class="text-muted-c">(tanpa batas)</span>
            @endif
          @else
            <span class="text-muted-c">-</span>
          @endif
        </td>
      </tr>
    </table>
  </div>
</div>

{{-- Perbandingan Sebelum/Sesudah (kalo update) --}}
@if($history->reason == 'update' && $previous)
<div class="card mb-3">
  <div class="card-header-flex">
    <h6><i class="bi bi-arrow-left-right me-2"></i>Perubahan</h6>
    <span class="chip-tag">Sebelum → Sesudah</span>
  </div>
  <div class="card-body p-0">
    <table class="detail-table">
      @php
        $fields = [
          ['label' => 'Nama', 'before' => $previous->discount_name, 'after' => $history->discount_name],
          ['label' => 'Tipe', 'before' => $previous->discount_type == 'percentage' ? 'Persen' : 'Nominal', 'after' => $history->discount_type == 'percentage' ? 'Persen' : 'Nominal'],
          ['label' => 'Nilai', 'before' => $previous->discount_type == 'percentage' ? $previous->discount_value.'%' : 'Rp'.number_format($previous->discount_value,0), 'after' => $history->discount_type == 'percentage' ? $history->discount_value.'%' : 'Rp'.number_format($history->discount_value,0)],
          ['label' => 'Maks Potongan', 'before' => $previous->discount_max_amount ? 'Rp'.number_format($previous->discount_max_amount,0) : '-', 'after' => $history->discount_max_amount ? 'Rp'.number_format($history->discount_max_amount,0) : '-'],
        ];
      @endphp
      @foreach($fields as $f)
      <tr class="{{ $f['before'] != $f['after'] ? 'table-row-changed' : '' }}">
        <td class="detail-label">{{ $f['label'] }}</td>
        <td class="detail-value">
          @if($f['before'] != $f['after'])
            <span style="color:var(--danger);text-decoration:line-through;margin-right:8px;">{{ $f['before'] }}</span>
            <i class="bi bi-arrow-right" style="color:var(--text-muted);margin-right:8px;"></i>
            <span style="color:var(--success);font-weight:600;">{{ $f['after'] }}</span>
          @else
            <span style="color:var(--text-muted);">{{ $f['after'] }}</span>
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
.table-row-changed td { background: rgba(37,99,235,0.03); }
.table-row-changed .detail-label { font-weight: 600; }
</style>
@endpush
