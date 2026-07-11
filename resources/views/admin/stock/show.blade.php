@extends('admin.layouts.app')

@section('title', 'Detail Stok')

@php $activeMenu = 'stock' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Stok</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.stock.index') }}">Stok</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>{{ $stock->stock_name }}</span>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.stock.edit', $stock) }}" class="btn btn-primary-grad">
      <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-soft">Kembali</a>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-info-circle me-2"></i>Informasi Stok</h6></div>
      <div class="card-body">
        <table class="table table-borderless mb-0" style="font-size:0.9rem;">
          <tr><td style="width:180px; color:var(--text-muted);">Perusahaan</td><td>{{ $stock->company?->company_name ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Kode Stok</td><td class="text-mono">{{ $stock->stock_code ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Nama</td><td class="fw-semibold">{{ $stock->stock_name }}</td></tr>
          <tr><td style="color:var(--text-muted);">Tipe</td><td>{{ $stock->stock_type ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Deskripsi</td><td>{{ $stock->stock_description ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Unit</td><td class="text-mono">{{ $stock->stock_unit ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Jumlah Stok</td>
            <td class="text-mono fw-semibold {{ $stock->stock_amount <= 0 ? 'text-danger' : '' }}">
              {{ number_format($stock->stock_amount) }}
              @if($stock->stock_unit) {{ $stock->stock_unit }} @endif
            </td>
          </tr>
          <tr><td style="color:var(--text-muted);">Harga Satuan</td><td class="text-mono">{{ $stock->stock_price ? 'Rp ' . number_format($stock->stock_price, 2) : '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Status</td>
            <td>
              @if($stock->stock_status)
                <span class="pill pill-success">Aktif</span>
              @else
                <span class="pill pill-neutral">Nonaktif</span>
              @endif
            </td>
          </tr>
          <tr><td style="color:var(--text-muted);">Dibuat</td><td>{{ $stock->created_at ? $stock->created_at->format('d M Y H:i') : '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Diupdate</td><td>{{ $stock->updated_at ? $stock->updated_at->format('d M Y H:i') : '-' }}</td></tr>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
