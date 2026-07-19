@extends('admin.layouts.app')

@section('title', 'Detail Kategori')

@php $activeMenu = 'category' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Kategori</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.category.index') }}">Kategori</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>{{ $category->category_name }}</span>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.category.edit', $category) }}" class="btn btn-primary-grad">
      <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('admin.category.index') }}" class="btn btn-outline-soft">Kembali</a>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-info-circle me-2"></i>Informasi Kategori</h6></div>
      <div class="card-body">
        <table class="table table-borderless mb-0" style="font-size:0.9rem;">
          <tr><td style="width:180px; color:var(--text-muted);">Perusahaan</td><td>{{ $category->company?->company_name ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Nama</td><td class="fw-semibold">{{ $category->category_name }}</td></tr>
          <tr><td style="color:var(--text-muted);">Gambar</td>
            <td>
              @if($category->category_image)
                <img src="{{ asset('storage/' . $category->category_image) }}" alt="{{ $category->category_name }}"
                     style="width:80px;height:80px;object-fit:cover;border-radius:var(--radius-md);border:1px solid var(--border-subtle);">
              @else
                <span class="text-muted-c">-</span>
              @endif
            </td>
          </tr>
          <tr><td style="color:var(--text-muted);">Tipe</td><td>{{ $category->category_type ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Deskripsi</td><td>{{ $category->category_description ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Status</td>
            <td>
              @if($category->category_status)
                <span class="pill pill-success">Aktif</span>
              @else
                <span class="pill pill-neutral">Nonaktif</span>
              @endif
            </td>
          </tr>
          <tr><td style="color:var(--text-muted);">Remark</td><td>{{ $category->category_remark ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Dibuat</td><td>{{ $category->created_at ? $category->created_at->format('d M Y H:i') : '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Diupdate</td><td>{{ $category->updated_at ? $category->updated_at->format('d M Y H:i') : '-' }}</td></tr>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
