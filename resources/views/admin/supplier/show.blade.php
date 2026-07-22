@extends('admin.layouts.app')

@section('title', 'Detail Supplier')

@php $activeMenu = 'supplier' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Supplier</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.supplier.index') }}">Supplier</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>{{ $supplier->supplier_name }}</span>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.supplier.edit', $supplier) }}" class="btn btn-primary-grad">
      <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('admin.supplier.index') }}" class="btn btn-outline-soft">Kembali</a>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-info-circle me-2"></i>Informasi Supplier</h6></div>
      <div class="card-body p-0">
        <table class="detail-table">
          <tr>
            <td class="detail-label">Perusahaan</td>
            <td class="detail-value">{{ $supplier->company?->company_name ?? '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Kode Supplier</td>
            <td class="detail-value text-mono">{{ $supplier->supplier_code ?? '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Nama</td>
            <td class="detail-value fw-semibold">{{ $supplier->supplier_name }}</td>
          </tr>
          <tr>
            <td class="detail-label">Kontak Person</td>
            <td class="detail-value">{{ $supplier->supplier_contact ?? '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Telepon</td>
            <td class="detail-value text-mono">{{ $supplier->supplier_phone ?? '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Alamat</td>
            <td class="detail-value">{{ $supplier->supplier_address ?? '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Status</td>
            <td class="detail-value">
              @if($supplier->supplier_status)
                <span class="pill pill-success">Aktif</span>
              @else
                <span class="pill pill-neutral">Nonaktif</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Dibuat</td>
            <td class="detail-value">{{ $supplier->created_at ? $supplier->created_at->format('d M Y H:i') : '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Diupdate</td>
            <td class="detail-value">{{ $supplier->updated_at ? $supplier->updated_at->format('d M Y H:i') : '-' }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
