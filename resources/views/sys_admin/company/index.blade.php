@extends('admin.layouts.app')

@section('title', 'Perusahaan')

@php $activeMenu = 'company' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Perusahaan</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Perusahaan</span>
    </div>
  </div>
  <a href="{{ route('admin.company.create') }}" class="btn btn-primary-grad">
    <i class="bi bi-plus-lg me-1"></i>Tambah Perusahaan
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
    <h6>Daftar Perusahaan</h6>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Cabang</th>
            <th>Email</th>
            <th>Telepon</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($companies as $company)
          <tr>
            <td class="text-mono">{{ $company->company_code ?? '-' }}</td>
            <td class="cell-primary">
              <a href="{{ route('admin.company.show', $company) }}" class="text-decoration-none">{{ $company->company_name }}</a>
            </td>
            <td>{{ $company->company_branch ?? '-' }}</td>
            <td>{{ $company->company_email ?? '-' }}</td>
            <td class="text-mono">{{ $company->company_phone ?? '-' }}</td>
            <td>
              @if($company->company_status)
                <span class="pill pill-success">Aktif</span>
              @else
                <span class="pill pill-neutral">Nonaktif</span>
              @endif
            </td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('admin.company.edit', $company) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('admin.company.destroy', $company) }}" method="POST" onsubmit="return confirm('Hapus perusahaan ini?')">
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
            <td colspan="7" class="text-center text-muted-c py-4">Belum ada data perusahaan.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
