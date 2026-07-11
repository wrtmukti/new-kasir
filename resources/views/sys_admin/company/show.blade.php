@extends('admin.layouts.app')

@section('title', 'Detail Perusahaan')

@php $activeMenu = 'company' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Perusahaan</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.company.index') }}">Perusahaan</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>{{ $company->company_name }}</span>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.company.edit', $company) }}" class="btn btn-primary-grad">
      <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('admin.company.index') }}" class="btn btn-outline-soft">Kembali</a>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-info-circle me-2"></i>Informasi Perusahaan</h6></div>
      <div class="card-body">
        <table class="table table-borderless mb-0" style="font-size:0.9rem;">
          <tr><td style="width:180px; color:var(--text-muted);">Nama</td><td class="fw-semibold">{{ $company->company_name }}</td></tr>
          <tr><td style="color:var(--text-muted);">Kode</td><td class="text-mono">{{ $company->company_code ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Cabang</td><td>{{ $company->company_branch ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Slug</td><td class="text-mono">{{ $company->company_slug ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Email</td><td>{{ $company->company_email ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Telepon</td><td class="text-mono">{{ $company->company_phone ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Alamat</td><td>{{ $company->company_address ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Status</td>
            <td>
              @if($company->company_status)
                <span class="pill pill-success">Aktif</span>
              @else
                <span class="pill pill-neutral">Nonaktif</span>
              @endif
            </td>
          </tr>
          <tr><td style="color:var(--text-muted);">Dibuat</td><td>{{ $company->created_at ? $company->created_at->format('d M Y H:i') : '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Diupdate</td><td>{{ $company->updated_at ? $company->updated_at->format('d M Y H:i') : '-' }}</td></tr>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
