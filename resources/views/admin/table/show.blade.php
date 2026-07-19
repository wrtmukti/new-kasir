@extends('admin.layouts.app')

@section('title', 'Detail Meja')

@php $activeMenu = 'table' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Meja</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.table.index') }}">Meja</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Meja {{ $table->table_number }}</span>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.table.edit', $table) }}" class="btn btn-primary-grad">
      <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('admin.table.index') }}" class="btn btn-outline-soft">Kembali</a>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-info-circle me-2"></i>Informasi Meja</h6></div>
      <div class="card-body">
        <table class="table table-borderless mb-0" style="font-size:0.9rem;">
          <tr><td style="width:180px; color:var(--text-muted);">Perusahaan</td><td>{{ $table->company?->company_name ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Nomor Meja</td><td class="fw-semibold">{{ $table->table_number }}</td></tr>
          <tr><td style="color:var(--text-muted);">Kapasitas</td><td class="text-mono">{{ $table->table_capacity ? $table->table_capacity . ' orang' : '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Deskripsi</td><td>{{ $table->table_description ?? '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Status</td>
            <td>
              @switch($table->table_status ?? 'inactive')
                @case('active')
                  <span class="pill pill-success">Kosong</span>
                  @break
                @case('reserved')
                  <span class="pill pill-warning">Dipesan</span>
                  @break
                @case('occupied')
                  <span class="pill pill-danger">Terisi</span>
                  @break
                @default
                  <span class="pill pill-neutral">Nonaktif</span>
              @endswitch
            </td>
          </tr>
          <tr><td style="color:var(--text-muted);">Dibuat</td><td>{{ $table->created_at ? $table->created_at->format('d M Y H:i') : '-' }}</td></tr>
          <tr><td style="color:var(--text-muted);">Diupdate</td><td>{{ $table->updated_at ? $table->updated_at->format('d M Y H:i') : '-' }}</td></tr>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
