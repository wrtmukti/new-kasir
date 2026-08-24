@extends('sys_admin.layouts.app')

@section('title', 'Detail Client — ' . $client->client_name)

@section('content')
<div class="container-fluid p-0">

  {{-- Top Header & Breadcrumbs --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <div class="d-flex align-items-center gap-2 mb-1">
        <a href="{{ route('sys_admin.clients.index') }}" class="text-secondary-c text-decoration-none" style="font-size:0.85rem;">
          <i class="bi bi-arrow-left me-1"></i>Daftar Client
        </a>
        <span class="text-muted-c">&bull;</span>
        <span class="badge bg-secondary-subtle text-secondary" style="font-size:0.75rem;">{{ $client->client_id }}</span>
      </div>
      <h4 class="fw-bold mb-0" style="font-size:1.45rem; color:var(--text-primary);">
        {{ $client->client_name }}
        @if($client->status === 'active')
          <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill ms-2" style="font-size:0.72rem; vertical-align:middle;">
            <i class="bi bi-circle-fill me-1" style="font-size:0.45rem;"></i>Active
          </span>
        @elseif($client->status === 'provisioning')
          <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill ms-2" style="font-size:0.72rem; vertical-align:middle;">
            <i class="bi bi-arrow-repeat me-1 spin"></i>Provisioning
          </span>
        @elseif($client->status === 'suspended')
          <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill ms-2" style="font-size:0.72rem; vertical-align:middle;">
            <i class="bi bi-pause-circle-fill me-1"></i>Suspended
          </span>
        @else
          <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-2" style="font-size:0.72rem; vertical-align:middle;">
            {{ ucfirst($client->status) }}
          </span>
        @endif
      </h4>
    </div>

    {{-- Quick Action Buttons --}}
    <div class="d-flex align-items-center gap-2">
      @if($client->status === 'active')
        <a href="{{ route('sys_admin.impersonate.start', $client->client_id) }}" class="btn btn-warning rounded-3 px-3 py-1.5 fw-semibold" onclick="return confirm('Mulai sesi Impersonation masuk ke sistem POS klien {{ $client->client_name }}?')">
          <i class="bi bi-box-arrow-in-right me-1"></i>Login as Client (Impersonate)
        </a>
      @endif

      @if($client->status === 'suspended')
        <form action="{{ route('sys_admin.clients.reactivate', $client->client_id) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-success rounded-3 px-3 py-1.5 fw-semibold" onclick="return confirm('Aktifkan kembali akses client ini?')">
            <i class="bi bi-play-circle-fill me-1"></i>Aktifkan Kembali
          </button>
        </form>
      @elseif($client->status === 'active')
        <button type="button" class="btn btn-outline-danger rounded-3 px-3 py-1.5 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalSuspendClient">
          <i class="bi bi-pause-circle me-1"></i>Suspend Client
        </button>
      @endif
    </div>
  </div>

  {{-- 8 Tab Navigation --}}
  <div class="card rounded-4 border-0 shadow-sm mb-4" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
    <div class="card-header bg-transparent border-0 p-2">
      <ul class="nav nav-pills flex-nowrap overflow-auto scroll-thin" id="clientTabs" role="tablist">
        <li class="nav-item">
          <button class="nav-link active rounded-3 py-2 px-3 fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-overview">
            <i class="bi bi-info-circle me-1.5"></i>1. Overview
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link rounded-3 py-2 px-3 fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-outlets">
            <i class="bi bi-shop me-1.5"></i>2. Outlets ({{ $clientOutlets->count() }})
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link rounded-3 py-2 px-3 fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-users">
            <i class="bi bi-people me-1.5"></i>3. Users ({{ $clientUsers->count() }})
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link rounded-3 py-2 px-3 fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-subscription">
            <i class="bi bi-credit-card-2-front me-1.5"></i>4. Subscription
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link rounded-3 py-2 px-3 fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-database">
            <i class="bi bi-database me-1.5"></i>5. Database
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link rounded-3 py-2 px-3 fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-backup">
            <i class="bi bi-cloud-arrow-down me-1.5"></i>6. Backup
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link rounded-3 py-2 px-3 fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-audit">
            <i class="bi bi-shield-check me-1.5"></i>7. Activity / Audit
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link rounded-3 py-2 px-3 fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-settings">
            <i class="bi bi-gear me-1.5"></i>8. Settings
          </button>
        </li>
      </ul>
    </div>
  </div>

  {{-- Tab Content Panes --}}
  <div class="tab-content" id="clientTabContent">

    {{-- TAB 1: OVERVIEW --}}
    <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
      <div class="row g-4">
        <div class="col-lg-7">
          <div class="card rounded-4 border-0 shadow-sm p-4 h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-building me-2"></i>Informasi Profil Client</h6>
            <table class="table table-borderless align-middle mb-0" style="font-size:0.88rem;">
              <tr>
                <td class="text-muted-c ps-0" style="width:160px;">Client ID</td>
                <td class="fw-bold"><span class="badge bg-secondary-subtle text-secondary">{{ $client->client_id }}</span></td>
              </tr>
              <tr>
                <td class="text-muted-c ps-0">Nama Perusahaan</td>
                <td class="fw-semibold" style="color:var(--text-primary);">{{ $client->client_name }}</td>
              </tr>
              <tr>
                <td class="text-muted-c ps-0">Kode Klien (Inisial)</td>
                <td class="fw-bold"><span class="badge bg-primary-subtle text-primary font-monospace">{{ $client->client_code ?? '-' }}</span></td>
              </tr>
              <tr>
                <td class="text-muted-c ps-0">Brand / Usaha</td>
                <td>{{ $client->business_name ?? '-' }}</td>
              </tr>
              <tr>
                <td class="text-muted-c ps-0">Nama Owner (PIC)</td>
                <td class="fw-semibold">{{ $client->owner_name }}</td>
              </tr>
              <tr>
                <td class="text-muted-c ps-0">Email PIC</td>
                <td><a href="mailto:{{ $client->owner_email }}" class="text-primary">{{ $client->owner_email }}</a></td>
              </tr>
              <tr>
                <td class="text-muted-c ps-0">Telepon / WhatsApp</td>
                <td>{{ $client->owner_phone ?? '-' }}</td>
              </tr>
              <tr>
                <td class="text-muted-c ps-0">Alamat Usaha</td>
                <td>{{ $client->address ?? '-' }}</td>
              </tr>
              <tr>
                <td class="text-muted-c ps-0">Waktu Provisioning</td>
                <td>{{ $client->provisioned_at ? $client->provisioned_at->format('d M Y, H:i') : '-' }}</td>
              </tr>
            </table>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card rounded-4 border-0 shadow-sm p-4 h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-hdd-network me-2"></i>Spesifikasi Lingkungan Client</h6>
            <div class="p-3 rounded-3 mb-3" style="background: var(--bg-elevated-2); border: 1px solid var(--border-subtle);">
              <div class="text-muted-c" style="font-size:0.75rem;">DATABASE FISIK MYSQL</div>
              <div class="d-flex align-items-center gap-2 mt-1">
                <i class="bi bi-database text-info"></i>
                <code class="fw-bold" style="font-size:0.88rem; color:var(--text-primary);">{{ $client->database_name }}</code>
              </div>
            </div>

            <div class="p-3 rounded-3 mb-3" style="background: var(--bg-elevated-2); border: 1px solid var(--border-subtle);">
              <div class="text-muted-c" style="font-size:0.75rem;">PAKET SAAS AKTIF</div>
              @php
                $activeSub = $client->activeSubscription;
                $plan = $activeSub?->plan;
              @endphp
              <div class="d-flex align-items-center justify-content-between mt-1">
                <span class="fw-bold text-success">{{ $plan->plan_name ?? 'Free Trial' }}</span>
                <span class="badge bg-primary-subtle text-primary">{{ $activeSub?->status ?? 'active' }}</span>
              </div>
              <small class="text-muted-c d-block mt-1" style="font-size:0.75rem;">
                Masa Aktif s/d: <strong>{{ $activeSub?->expired_date ? $activeSub->expired_date->format('d F Y') : '-' }}</strong>
              </small>
            </div>

            @if($client->status === 'suspended')
              <div class="alert alert-danger py-2.5 px-3 rounded-3 mb-0" style="font-size:0.82rem;">
                <strong><i class="bi bi-exclamation-octagon-fill me-1"></i>Alasan Penangguhan:</strong>
                <div>{{ $client->suspension_reason ?? 'Ditangguhkan oleh Administrator.' }}</div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- TAB 2: OUTLETS --}}
    <div class="tab-pane fade" id="tab-outlets" role="tabpanel">
      <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
          <div>
            <h6 class="fw-bold mb-0" style="color:var(--text-primary);"><i class="bi bi-shop me-2 text-success"></i>Daftar Cabang Outlet Client</h6>
            <small class="text-muted-c" style="font-size:0.78rem;">Data dimuat langsung dari tabel `outlets` database klien.</small>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Total {{ $clientOutlets->count() }} Outlet</span>
            <a href="{{ route('sys_admin.outlets.create', ['client_id' => $client->client_id, 'from' => 'client']) }}" class="btn btn-sm btn-primary rounded-3 px-3 py-1.5 fw-semibold" style="font-size:0.82rem;">
              <i class="bi bi-plus-lg me-1"></i>Tambah Cabang Outlet
            </a>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table align-middle mb-0" style="font-size:0.85rem;">
            <thead style="background: var(--bg-elevated-2); color: var(--text-secondary);">
              <tr>
                <th class="ps-3">Company ID (ULID)</th>
                <th>Nama Outlet / Cabang</th>
                <th>Kode</th>
                <th>Kontak & Email</th>
                <th>Alamat</th>
                <th class="pe-3">Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($clientOutlets as $outlet)
                <tr style="border-bottom: 1px solid var(--border-subtle);">
                  <td class="ps-3">
                    <code style="font-size:0.75rem;">{{ $outlet->outlet_id }}</code>
                  </td>
                  <td class="fw-semibold text-primary">
                    {{ $outlet->outlet_name }}
                    <small class="text-muted-c d-block">Cabang: {{ $outlet->outlet_branch ?? 'Pusat' }}</small>
                  </td>
                  <td><span class="badge bg-secondary-subtle text-secondary">{{ $outlet->outlet_code }}</span></td>
                  <td>
                    <div>{{ $outlet->outlet_phone ?? '-' }}</div>
                    <small class="text-muted-c">{{ $outlet->outlet_email ?? '-' }}</small>
                  </td>
                  <td>{{ $outlet->outlet_address ?? '-' }}</td>
                  <td class="pe-3">
                    <span class="badge {{ $outlet->outlet_status ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-2 py-0.5">
                      {{ $outlet->outlet_status ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center py-4 text-muted-c">Belum ada outlet terdaftar di database client.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- TAB 3: USERS --}}
    <div class="tab-pane fade" id="tab-users" role="tabpanel">
      <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <h6 class="fw-bold mb-0" style="color:var(--text-primary);"><i class="bi bi-people me-2 text-warning"></i>Daftar Pengguna & Kasir Client</h6>
            <small class="text-muted-c" style="font-size:0.78rem;">Data dimuat langsung dari tabel `users` database klien.</small>
          </div>
          <span class="badge bg-warning-subtle text-warning rounded-pill px-2.5 py-1">Total {{ $clientUsers->count() }} Pengguna</span>
        </div>

        <div class="table-responsive">
          <table class="table align-middle mb-0" style="font-size:0.85rem;">
            <thead style="background: var(--bg-elevated-2); color: var(--text-secondary);">
              <tr>
                <th class="ps-3">Nama Pengguna</th>
                <th>Email</th>
                <th>Role</th>
                <th>Outlet / Penugasan</th>
                <th class="pe-3">Terdaftar Sejak</th>
              </tr>
            </thead>
            <tbody>
              @forelse($clientUsers as $user)
                @php
                  $userOutlet = $clientOutlets->firstWhere('outlet_id', $user->outlet_id ?? '');
                @endphp
                <tr style="border-bottom: 1px solid var(--border-subtle);">
                  <td class="ps-3 fw-semibold text-primary">{{ $user->name }}</td>
                  <td>{{ $user->email }}</td>
                  <td><span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5 text-uppercase">{{ $user->role ?? 'Kasir' }}</span></td>
                  <td>
                    @if($userOutlet)
                      <span class="badge bg-success-subtle text-success fw-semibold" style="font-size:0.75rem;">
                        <i class="bi bi-shop me-1"></i>{{ $userOutlet->outlet_name }}
                      </span>
                    @elseif($user->role === 'admin' || $user->role === 'owner')
                      <span class="badge bg-secondary-subtle text-secondary fw-semibold" style="font-size:0.75rem;">
                        <i class="bi bi-globe me-1"></i>Semua Cabang
                      </span>
                    @else
                      <span class="text-muted-c" style="font-size:0.75rem;">-</span>
                    @endif
                  </td>
                  <td class="pe-3 text-muted-c">{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center py-4 text-muted-c">Belum ada data user di database client.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- TAB 4: SUBSCRIPTION --}}
    <div class="tab-pane fade" id="tab-subscription" role="tabpanel">
      <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="fw-bold mb-0" style="color:var(--text-primary);"><i class="bi bi-credit-card-2-front me-2 text-primary"></i>Riwayat Langganan SaaS</h6>
        </div>

        <div class="table-responsive">
          <table class="table align-middle mb-0" style="font-size:0.85rem;">
            <thead style="background: var(--bg-elevated-2); color: var(--text-secondary);">
              <tr>
                <th class="ps-3">ID Subscription</th>
                <th>Paket</th>
                <th>Periode Mulai</th>
                <th>Expired Date</th>
                <th>Status</th>
                <th>Ref Billing</th>
              </tr>
            </thead>
            <tbody>
              @forelse($subscriptions as $sub)
                <tr style="border-bottom: 1px solid var(--border-subtle);">
                  <td class="ps-3"><code style="font-size:0.78rem;">{{ $sub->subscription_id }}</code></td>
                  <td class="fw-semibold text-primary">{{ $sub->plan?->plan_name ?? 'Plan' }}</td>
                  <td>{{ $sub->start_date ? $sub->start_date->format('d M Y') : '-' }}</td>
                  <td>{{ $sub->expired_date ? $sub->expired_date->format('d M Y') : '-' }}</td>
                  <td>
                    <span class="badge {{ $sub->status === 'active' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill px-2 py-0.5">
                      {{ ucfirst($sub->status) }}
                    </span>
                  </td>
                  <td><span class="badge bg-secondary-subtle text-secondary">{{ $sub->billing_reference ?? '-' }}</span></td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center py-4 text-muted-c">Belum ada riwayat langganan.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- TAB 5: DATABASE --}}
    <div class="tab-pane fade" id="tab-database" role="tabpanel">
      <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <h6 class="fw-bold mb-3" style="color:var(--text-primary);"><i class="bi bi-database me-2 text-info"></i>Koneksi & Statistik Database Fisik</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <table class="table table-borderless mb-0" style="font-size:0.85rem;">
              <tr><td class="text-muted-c ps-0">Database Name</td><td><code class="fw-bold">{{ $client->database_name }}</code></td></tr>
              <tr><td class="text-muted-c ps-0">Server Host</td><td>{{ $client->db_host }}:{{ $client->db_port }}</td></tr>
              <tr><td class="text-muted-c ps-0">Status Koneksi</td><td><span class="badge bg-success-subtle text-success">Connected</span></td></tr>
              <tr><td class="text-muted-c ps-0">Latency Query</td><td><span class="text-success fw-bold">{{ $databaseConnection?->latency_ms ?? 0 }} ms</span></td></tr>
              <tr><td class="text-muted-c ps-0">Total Tabel</td><td>{{ $databaseConnection?->tables_count ?? 0 }} tabel</td></tr>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- TAB 6: BACKUP --}}
    <div class="tab-pane fade" id="tab-backup" role="tabpanel">
      <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="fw-bold mb-0" style="color:var(--text-primary);"><i class="bi bi-cloud-arrow-down me-2 text-success"></i>Snapshot Database Backup</h6>
          <button type="button" class="btn btn-outline-primary btn-sm rounded-3" onclick="alert('Snapshot backup dimulai...')">
            <i class="bi bi-cloud-upload me-1"></i>Trigger Backup Sekarang
          </button>
        </div>
        <p class="text-muted-c mb-0" style="font-size:0.85rem;">Otomatisasi backup snapshot harian berjalan setiap pukul 02:00 WIB.</p>
      </div>
    </div>

    {{-- TAB 7: AUDIT LOGS --}}
    <div class="tab-pane fade" id="tab-audit" role="tabpanel">
      <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <h6 class="fw-bold mb-3" style="color:var(--text-primary);"><i class="bi bi-shield-check me-2 text-warning"></i>Riwayat Audit Log Client Ini</h6>
        <div class="table-responsive">
          <table class="table align-middle mb-0" style="font-size:0.82rem;">
            <thead style="background: var(--bg-elevated-2); color: var(--text-secondary);">
              <tr>
                <th class="ps-3">Waktu</th>
                <th>Actor</th>
                <th>Aksi</th>
                <th>Target</th>
                <th class="pe-3">Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($auditLogs as $log)
                <tr style="border-bottom: 1px solid var(--border-subtle);">
                  <td class="ps-3 text-muted-c">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                  <td class="fw-semibold">{{ $log->actor_name ?? 'System' }}</td>
                  <td><span class="badge bg-secondary-subtle text-secondary">{{ $log->action }}</span></td>
                  <td>{{ $log->target_type }} #{{ $log->target_id }}</td>
                  <td class="pe-3"><span class="badge bg-success-subtle text-success">{{ $log->result }}</span></td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center py-4 text-muted-c">Belum ada audit log untuk client ini.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- TAB 8: SETTINGS & ACTIONS --}}
    <div class="tab-pane fade" id="tab-settings" role="tabpanel">
      <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <h6 class="fw-bold mb-3" style="color:var(--text-primary);"><i class="bi bi-gear me-2 text-primary"></i>Pengaturan & Pembaruan Profil</h6>
        <form action="{{ route('sys_admin.clients.update', $client->client_id) }}" method="POST">
          @csrf
          <div class="row g-3 mb-3">
            <div class="col-md-4 input-skeleton">
              <label class="form-label-modern fw-semibold">Nama Perusahaan</label>
              <input type="text" name="client_name" class="form-control form-control-modern" value="{{ $client->client_name }}" required>
            </div>
            <div class="col-md-4 input-skeleton">
              <label class="form-label-modern fw-semibold">Kode Klien (Inisial DB)</label>
              <input type="text" name="client_code" class="form-control form-control-modern text-uppercase font-monospace" value="{{ $client->client_code }}">
            </div>
            <div class="col-md-4 input-skeleton">
              <label class="form-label-modern fw-semibold">Nama Brand</label>
              <input type="text" name="business_name" class="form-control form-control-modern" value="{{ $client->business_name }}">
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6 input-skeleton">
              <label class="form-label-modern fw-semibold">Nama Owner</label>
              <input type="text" name="owner_name" class="form-control form-control-modern" value="{{ $client->owner_name }}" required>
            </div>
            <div class="col-md-6 input-skeleton">
              <label class="form-label-modern fw-semibold">Email Owner</label>
              <input type="email" name="owner_email" class="form-control form-control-modern" value="{{ $client->owner_email }}" required>
            </div>
          </div>
          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary-grad px-4 btn-loading">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>

  </div>

</div>

{{-- MODAL SUSPEND CLIENT --}}
<div class="modal fade" id="modalSuspendClient" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important; color:var(--text-primary);">
      <div class="modal-header px-4 py-3 border-0" style="border-bottom: 1px solid var(--border-subtle) !important;">
        <h5 class="modal-title fw-bold text-danger"><i class="bi bi-pause-circle-fill me-2"></i>Konfirmasi Suspend Client</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('sys_admin.clients.suspend', $client->client_id) }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <p style="font-size:0.88rem;">Menangguhkan client akan mengunci seluruh akses login ke modul POS kasir dan self-ordering meja untuk client <strong>{{ $client->client_name }}</strong>.</p>
          <div class="mb-3">
            <label class="form-label-modern fw-semibold">Alasan Penangguhan <span class="text-danger">*</span></label>
            <textarea name="reason" rows="2" class="form-control form-control-modern" placeholder="Contoh: Masa berlangganan kadaluarsa / kendala pembayaran billing" required></textarea>
          </div>
        </div>
        <div class="modal-footer px-4 py-3 border-0" style="border-top: 1px solid var(--border-subtle) !important;">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger px-4">Suspend Sekarang</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
