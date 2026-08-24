@extends('sys_admin.layouts.app')

@section('title', 'Detail Perusahaan')

@php $activeMenu = 'outlet' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Perusahaan</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('sys_admin.company.index') }}">Perusahaan</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>{{ $outlet->outlet_name }}</span>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('sys_admin.company.edit', $outlet) }}" class="btn btn-primary-grad">
      <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('sys_admin.company.index') }}" class="btn btn-outline-soft">Kembali</a>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-info-circle me-2"></i>Informasi Perusahaan</h6></div>
      <div class="card-body">
        <table class="table table-borderless mb-0" style="font-size:0.9rem;">
          <tr><td style="width:180px; color:var(--text-muted);">Nama</td><td class="fw-semibold">{{ $outlet->outlet_name }}</td></tr>
          <tr><td style="color:var(--text-muted);">Kode</td><td class="text-mono">{{ $outlet->outlet_code ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Cabang</td><td>{{ $outlet->outlet_branch ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Slug</td><td class="text-mono">{{ $outlet->outlet_slug ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Email</td><td>{{ $outlet->outlet_email ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Telepon</td><td class="text-mono">{{ $outlet->outlet_phone ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Alamat</td><td>{{ $outlet->outlet_address ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Status</td>
            <td>
              @if($outlet->outlet_status)
                <span class="pill pill-success">Aktif</span>
              @else
                <span class="pill pill-neutral">Nonaktif</span>
              @endif
            </td>
          </tr>
          <tr><td style="color:var(--text-muted);">Dibuat</td><td>{{ $outlet->created_at ? $outlet->created_at->format('d M Y H:i') : '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Diupdate</td><td>{{ $outlet->updated_at ? $outlet->updated_at->format('d M Y H:i') : '-' }}</td></tr>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
