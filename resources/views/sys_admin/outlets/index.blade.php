@extends('sys_admin.layouts.app')

@section('title', 'Outlets Cabang Platform Overview')

@section('content')
<div class="container-fluid p-0">

  {{-- Page Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <h4 class="fw-bold mb-1" style="font-size:1.45rem; color:var(--text-primary);">
        <i class="bi bi-shop me-2 text-success"></i>Outlets (Cabang) Overview
      </h4>
      <p class="text-muted-c mb-0" style="font-size:0.85rem;">
        Monitoring seluruh cabang gerai outlet aktif di semua client (Data terisolasi via <code>outlet_id</code>).
      </p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill d-none d-sm-inline-block">
        Total {{ $totalOutlets }} Cabang dari {{ $totalClients }} Klien
      </span>
      <a href="{{ route('sys_admin.outlets.create') }}" class="btn btn-primary rounded-3 px-3 py-2 fw-semibold" style="font-size:0.85rem;">
        <i class="bi bi-plus-lg me-1"></i>Tambah Outlet Baru
      </a>
    </div>
  </div>

  {{-- Table Card --}}
  <div class="card rounded-4 border-0 shadow-sm" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
    <div class="card-header bg-transparent border-0 p-4 pb-2 d-flex align-items-center justify-content-between">
      <h6 class="fw-bold mb-0" style="color:var(--text-primary);">Daftar Outlet Cabang Terdaftar</h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0" style="font-size:0.85rem;">
          <thead style="background: var(--bg-elevated-2); color: var(--text-secondary);">
            <tr>
              <th class="ps-4">Klien / Client</th>
              <th>Nama Cabang & Outlet</th>
              <th>Company ID (ULID)</th>
              <th>Kontak & Email</th>
              <th>Alamat</th>
              <th class="pe-4">Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($outlets as $outlet)
              <tr style="border-bottom: 1px solid var(--border-subtle);">
                <td class="ps-4">
                  <div class="fw-bold text-primary">{{ $outlet->client_name }}</div>
                  <small class="text-muted-c">{{ $outlet->client_id }}</small>
                </td>
                <td>
                  <div class="fw-semibold" style="color:var(--text-primary);">{{ $outlet->outlet_name }}</div>
                  <small class="text-muted-c">Cabang: {{ $outlet->outlet_branch }}</small>
                </td>
                <td><code style="font-size:0.75rem;">{{ $outlet->outlet_id }}</code></td>
                <td>
                  <div>{{ $outlet->outlet_phone ?? '-' }}</div>
                  <small class="text-muted-c">{{ $outlet->outlet_email ?? '-' }}</small>
                </td>
                <td>{{ $outlet->outlet_address ?? '-' }}</td>
                <td class="pe-4">
                  <span class="badge {{ $outlet->outlet_status ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-2 py-0.5">
                    {{ $outlet->outlet_status ? 'Aktif' : 'Non-Aktif' }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-5 text-muted-c">
                  <i class="bi bi-shop-window fs-2 d-block mb-2 text-secondary"></i>
                  Belum ada outlet cabang terdaftar di database client.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection
