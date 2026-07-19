@extends('admin.layouts.app')

@section('title', 'Detail Produk')

@php $activeMenu = 'product' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Produk</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.product.index') }}">Produk</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>{{ $product->product_name }}</span>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.product.edit', $product) }}" class="btn btn-primary-grad">
      <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('admin.product.index') }}" class="btn btn-outline-soft">Kembali</a>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-info-circle me-2"></i>Informasi Produk</h6></div>
      <div class="card-body">
        <table class="table table-borderless mb-0" style="font-size:0.9rem;">
          <tr><td style="width:180px; color:var(--text-muted);">Perusahaan</td><td>{{ $product->company?->company_name ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Kode Produk</td><td class="text-mono">{{ $product->product_code ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Nama</td><td class="fw-semibold">{{ $product->product_name }}</td></tr>
          <tr><td style="color:var(--text-muted);">Gambar</td>
            <td>
              @if($product->product_image)
                <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->product_name }}"
                     style="width:80px;height:80px;object-fit:cover;border-radius:var(--radius-md);border:1px solid var(--border-subtle);">
              @else
                <span class="text-muted-c">-</span>
              @endif
            </td>
          </tr>
          <tr><td style="color:var(--text-muted);">Kategori</td><td>{{ $product->category?->category_name ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Harga</td><td class="text-mono">{{ $product->product_price ? 'Rp ' . number_format($product->product_price, 2) : '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Deskripsi</td><td>{{ $product->product_description ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Status</td>
            <td>
              @if($product->product_status)
                <span class="pill pill-success">Aktif</span>
              @else
                <span class="pill pill-neutral">Nonaktif</span>
              @endif
            </td>
          </tr>
          <tr><td style="color:var(--text-muted);">Dibuat</td><td>{{ $product->created_at ? $product->created_at->format('d M Y H:i') : '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Diupdate</td><td>{{ $product->updated_at ? $product->updated_at->format('d M Y H:i') : '-' }}</td></tr>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
