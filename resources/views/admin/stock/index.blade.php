@extends('admin.layouts.app')

@section('title', 'Stok Bahan Baku')

@php $activeMenu = 'stock' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Stok Bahan Baku</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Stok</span>
    </div>
  </div>
  <a href="{{ route('admin.stock.create') }}" class="btn btn-primary-grad">
    <i class="bi bi-plus-lg me-1"></i>Tambah Stok
  </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
  <div class="card-header-flex">
    <h6>Daftar Stok Bahan Baku</h6>
    <span class="chip-tag">{{ $stocks->count() }} item</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Tipe</th>
            <th>Unit</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($stocks as $stock)
          <tr>
            <td class="text-mono">{{ $stock->stock_code ?? '-' }}</td>
            <td class="cell-primary">
              <a href="{{ route('admin.stock.show', $stock) }}" class="text-decoration-none">{{ $stock->stock_name }}</a>
            </td>
            <td>{{ $stock->stock_type ?? '-' }}</td>
            <td class="text-mono">{{ $stock->stock_unit ?? '-' }}</td>
            <td class="text-mono fw-semibold {{ $stock->stock_amount <= 0 ? 'text-danger' : '' }}">{{ number_format($stock->stock_amount) }}</td>
            <td class="text-mono">{{ $stock->stock_price ? 'Rp ' . number_format($stock->stock_price, 0) : '-' }}</td>
            <td>
              @if($stock->stock_status)
                <span class="pill pill-success">Aktif</span>
              @else
                <span class="pill pill-neutral">Nonaktif</span>
              @endif
            </td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('admin.stock.edit', $stock) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('admin.stock.destroy', $stock) }}" method="POST" onsubmit="return confirm('Hapus stok ini?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-ghost btn-icon-sq btn-sm text-danger" title="Hapus">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center text-muted-c py-4">Belum ada data stok.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
