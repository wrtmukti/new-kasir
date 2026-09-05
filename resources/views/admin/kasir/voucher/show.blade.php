@extends('admin.layouts.app')

@section('title', 'Detail Voucher')

@php $activeMenu = 'voucher' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>{{ $voucher->voucher_name }}</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.voucher.index') }}">Voucher</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>{{ $voucher->voucher_name }}</span>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.voucher.edit', $voucher) }}" class="btn btn-primary-grad">
      <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('admin.voucher.index') }}" class="btn btn-outline-soft">Kembali</a>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-info-circle me-2"></i>Informasi Voucher</h6></div>
      <div class="card-body p-0">
        <table class="detail-table">
          <tr>
            <td class="detail-label">Perusahaan</td>
            <td class="detail-value">{{ $voucher->outlet?->outlet_name ?? '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Kode</td>
            <td class="detail-value"><span class="text-mono fw-semibold">{{ $voucher->voucher_code }}</span></td>
          </tr>
          <tr>
            <td class="detail-label">Nama</td>
            <td class="detail-value fw-semibold">{{ $voucher->voucher_name }}</td>
          </tr>
          <tr>
            <td class="detail-label">Tipe</td>
            <td class="detail-value">
              @if($voucher->voucher_type == 'percentage')
                <span class="pill pill-info">Persen</span>
              @elseif($voucher->voucher_type == 'nominal')
                <span class="pill pill-warning">Nominal</span>
              @else
                <span class="pill pill-neutral">Free Item</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Nilai</td>
            <td class="detail-value">
              @if($voucher->voucher_type == 'percentage')
                <strong>{{ number_format($voucher->voucher_value, 0) }}%</strong>
              @elseif($voucher->voucher_type == 'nominal')
                <strong>Rp{{ number_format($voucher->voucher_value, 0) }}</strong>
              @else
                <strong>{{ $voucher->voucher_value }}</strong>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Maksimal Potongan</td>
            <td class="detail-value">
              @if($voucher->voucher_max_discount)
                Rp{{ number_format($voucher->voucher_max_discount, 0) }}
              @else
                <span class="text-muted-c">Tanpa batas</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Minimal Pembelian</td>
            <td class="detail-value">
              @if($voucher->voucher_min_purchase)
                Rp{{ number_format($voucher->voucher_min_purchase, 0) }}
              @else
                <span class="text-muted-c">Tanpa minimal</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Penerapan</td>
            <td class="detail-value">
              @if($voucher->voucher_applicable_to == 'all')
                Semua Produk
              @elseif($voucher->voucher_applicable_to == 'specific_products')
                Produk Tertentu
              @elseif($voucher->voucher_applicable_to == 'specific_categories')
                Kategori Tertentu
              @else
                <span class="text-muted-c">-</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Batas Penggunaan</td>
            <td class="detail-value">{{ $voucher->voucher_usage_limit ?? 'Tidak terbatas' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Batas Per Pelanggan</td>
            <td class="detail-value">{{ $voucher->voucher_usage_per_customer ?? 'Tidak terbatas' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Deskripsi</td>
            <td class="detail-value">{{ $voucher->voucher_description ?? '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Status</td>
            <td class="detail-value">
              @if($voucher->voucher_status && (!$voucher->voucher_end_date || $voucher->voucher_end_date >= now()))
                <span class="pill pill-success">Aktif</span>
              @else
                <span class="pill pill-neutral">Nonaktif</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Periode Berlaku</td>
            <td class="detail-value">
              @if($voucher->voucher_start_date)
                {{ $voucher->voucher_start_date->format('d M Y H:i') }}
                @if($voucher->voucher_end_date)
                  — {{ $voucher->voucher_end_date->format('d M Y H:i') }}
                @endif
              @else
                <span class="text-muted-c">-</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Dibuat</td>
            <td class="detail-value">{{ $voucher->created_at ? $voucher->created_at->format('d M Y H:i') : '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Diupdate</td>
            <td class="detail-value">{{ $voucher->updated_at ? $voucher->updated_at->format('d M Y H:i') : '-' }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header-flex">
        <h6><i class="bi bi-credit-card me-2"></i>Ringkasan</h6>
      </div>
      <div class="card-body">
        <div class="d-flex flex-column gap-3">
          <div class="d-flex justify-content-between align-items-center p-3" style="background:var(--bg-elevated-2);border-radius:var(--radius-sm);">
            <span class="text-muted-c" style="font-size:0.8rem;">Kode Voucher</span>
            <span class="text-mono fw-bold" style="font-size:1.1rem;">{{ $voucher->voucher_code }}</span>
          </div>
          <div class="d-flex justify-content-between align-items-center p-3" style="background:var(--bg-elevated-2);border-radius:var(--radius-sm);">
            <span class="text-muted-c" style="font-size:0.8rem;">Potongan</span>
            <span class="fw-bold" style="font-size:1.1rem;color:var(--accent-1);">
              @if($voucher->voucher_type == 'percentage')
                {{ number_format($voucher->voucher_value, 0) }}%
              @elseif($voucher->voucher_type == 'nominal')
                Rp{{ number_format($voucher->voucher_value, 0) }}
              @else
                Free Item
              @endif
            </span>
          </div>
          <div class="d-flex justify-content-between align-items-center p-3" style="background:var(--bg-elevated-2);border-radius:var(--radius-sm);">
            <span class="text-muted-c" style="font-size:0.8rem;">Min. Belanja</span>
            <span class="fw-bold">
              @if($voucher->voucher_min_purchase)
                Rp{{ number_format($voucher->voucher_min_purchase, 0) }}
              @else
                <span class="text-muted-c">-</span>
              @endif
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Toast --}}
@if(session('success'))
  <script>document.addEventListener('DOMContentLoaded', function() { NexoraToast('{{ session('success') }}', 'success'); });</script>
@endif
@if(session('error'))
  <script>document.addEventListener('DOMContentLoaded', function() { NexoraToast('{{ session('error') }}', 'danger'); });</script>
@endif

@endsection
