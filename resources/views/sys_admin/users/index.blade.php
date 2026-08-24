@extends('sys_admin.layouts.app')

@section('title', 'Platform & Client Users Overview')

@section('content')
<div class="container-fluid p-0">

  {{-- Page Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <h4 class="fw-bold mb-1" style="font-size:1.45rem; color:var(--text-primary);">
        <i class="bi bi-people-fill me-2 text-warning"></i>Users Overview
      </h4>
      <p class="text-muted-c mb-0" style="font-size:0.85rem;">
        Monitoring seluruh pengguna System Admin Platform dan daftar Owner Klien Client.
      </p>
    </div>
  </div>

  <div class="row g-4">
    {{-- Section 1: System Admin Users --}}
    <div class="col-lg-6">
      <div class="card rounded-4 border-0 shadow-sm p-4 h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="fw-bold mb-0" style="color:var(--text-primary);">
            <i class="bi bi-shield-lock text-primary me-2"></i>System Administrator Accounts
          </h6>
          <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $systemUsers->count() }} Pengguna</span>
        </div>

        <div class="table-responsive">
          <table class="table align-middle mb-0" style="font-size:0.85rem;">
            <thead style="background: var(--bg-elevated-2); color: var(--text-secondary);">
              <tr>
                <th class="ps-3">Nama & Email</th>
                <th>Username</th>
                <th>Role</th>
                <th class="pe-3">Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($systemUsers as $su)
                <tr style="border-bottom: 1px solid var(--border-subtle);">
                  <td class="ps-3">
                    <div class="fw-semibold text-primary">{{ $su->name }}</div>
                    <small class="text-muted-c">{{ $su->email }}</small>
                  </td>
                  <td><code>{{ $su->username }}</code></td>
                  <td>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5 text-uppercase">
                      {{ str_replace('_', ' ', $su->role) }}
                    </span>
                  </td>
                  <td class="pe-3">
                    <span class="badge {{ $su->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-2 py-0.5">
                      {{ $su->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Section 2: Client Owners --}}
    <div class="col-lg-6">
      <div class="card rounded-4 border-0 shadow-sm p-4 h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="fw-bold mb-0" style="color:var(--text-primary);">
            <i class="bi bi-person-badge text-success me-2"></i>Client Owner PIC
          </h6>
          <span class="badge bg-success-subtle text-success rounded-pill">{{ $clients->count() }} Owner</span>
        </div>

        <div class="table-responsive">
          <table class="table align-middle mb-0" style="font-size:0.85rem;">
            <thead style="background: var(--bg-elevated-2); color: var(--text-secondary);">
              <tr>
                <th class="ps-3">Nama Owner</th>
                <th>Email PIC</th>
                <th>Klien / Perusahaan</th>
                <th class="pe-3">Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($clients as $cl)
                <tr style="border-bottom: 1px solid var(--border-subtle);">
                  <td class="ps-3 fw-semibold text-primary">{{ $cl->owner_name }}</td>
                  <td>{{ $cl->owner_email }}</td>
                  <td>
                    <div>{{ $cl->client_name }}</div>
                    <small class="text-muted-c">{{ $cl->client_id }}</small>
                  </td>
                  <td class="pe-3">
                    <span class="badge {{ $cl->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-2 py-0.5">
                      {{ ucfirst($cl->status) }}
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
