@extends('sys_admin.layouts.app')

@section('title', 'Client Management')

@section('content')
<div class="container-fluid p-0">

  {{-- Page Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <h4 class="fw-bold mb-1" style="font-size:1.45rem; color:var(--text-primary);">
        <i class="bi bi-buildings-fill me-2 text-primary"></i>Client Management
      </h4>
      <p class="text-muted-c mb-0" style="font-size:0.85rem;">
        Kelola lifecycle seluruh client (penyewa platform), langganan SaaS, dan database terisolasi.
      </p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-primary-grad rounded-3 px-3.5 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCreateClient">
        <i class="bi bi-plus-circle me-1.5"></i>Tambah Client Baru
      </button>
    </div>
  </div>

  {{-- 4 KPI Bento Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card p-3 rounded-4 border-0 shadow-sm" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.8rem;">TOTAL CLIENT</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
            <i class="bi bi-buildings fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0" style="font-size:1.75rem; color:var(--text-primary);">{{ $stats['total'] }}</h3>
        <small class="text-muted-c">Terdaftar di platform</small>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card p-3 rounded-4 border-0 shadow-sm" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.8rem;">ACTIVE CLIENTS</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(16, 185, 129, 0.15); color: #10b981;">
            <i class="bi bi-check-circle-fill fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-success" style="font-size:1.75rem;">{{ $stats['active'] }}</h3>
        <small class="text-muted-c">Status operasional aktif</small>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card p-3 rounded-4 border-0 shadow-sm" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.8rem;">PROVISIONING / TRIAL</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
            <i class="bi bi-clock-history fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-warning" style="font-size:1.75rem;">{{ $stats['trial'] }}</h3>
        <small class="text-muted-c">Sedang uji coba / proses</small>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card p-3 rounded-4 border-0 shadow-sm" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.8rem;">SUSPENDED / CANCELLED</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(239, 68, 68, 0.15); color: #ef4444;">
            <i class="bi bi-pause-circle-fill fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-danger" style="font-size:1.75rem;">{{ $stats['suspended'] }}</h3>
        <small class="text-muted-c">Akses terkunci / nonaktif</small>
      </div>
    </div>
  </div>

  {{-- Main Client Table Card --}}
  <div class="card rounded-4 border-0 shadow-sm" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
    <div class="card-header bg-transparent border-0 p-4 pb-2">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
          <label class="form-label-modern mb-0" style="font-size:0.82rem; color:var(--text-secondary);">Tampilkan</label>
          <select class="form-select form-select-sm form-select-modern" id="filterPerPage" style="width:75px;">
            <option value="10" selected>10</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span class="text-muted-c" style="font-size:0.82rem;">per halaman</span>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
          <select class="form-select form-select-sm form-select-modern" id="filterStatus" style="width:140px;">
            <option value="">Semua Status</option>
            <option value="active">Active</option>
            <option value="provisioning">Provisioning</option>
            <option value="suspended">Suspended</option>
            <option value="cancelled">Cancelled</option>
          </select>

          <select class="form-select form-select-sm form-select-modern" id="filterPlan" style="width:150px;">
            <option value="">Semua Paket</option>
            @foreach($plans as $p)
              <option value="{{ $p->id }}">{{ $p->plan_name }}</option>
            @endforeach
          </select>

          <div class="input-group input-group-sm" style="width: 240px;">
            <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" id="inputSearch" class="form-control form-control-modern" placeholder="Cari nama/email/ID...">
          </div>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0" id="tableClients" style="font-size:0.85rem;">
          <thead style="background: var(--bg-elevated-2); color: var(--text-secondary);">
            <tr>
              <th class="ps-4">Klien / Perusahaan</th>
              <th>Owner & Kontak</th>
              <th>Database Isolated</th>
              <th>Paket Langganan</th>
              <th>Status</th>
              <th class="pe-4 text-end">Aksi</th>
            </tr>
          </thead>
          <tbody id="clientsTableBody">
            @include('sys_admin.clients.partials._table_rows', ['clients' => $clients])
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer bg-transparent border-0 px-4 py-3 d-flex flex-wrap align-items-center justify-content-between gap-2" style="border-top: 1px solid var(--border-subtle) !important;">
      <div class="text-muted-c" id="tablePaginationInfo" style="font-size:0.82rem;">
        Menampilkan {{ $clients->firstItem() ?? 0 }} sampai {{ $clients->lastItem() ?? 0 }} dari {{ $clients->total() }} client
      </div>
      <div id="tablePaginationLinks">
        {{ $clients->links('vendor.pagination.modern') }}
      </div>
    </div>
  </div>

</div>

{{-- MODAL: PROVISION CLIENT BARU --}}
<div class="modal fade" id="modalCreateClient" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 border-0 shadow-lg" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important; color: var(--text-primary);">
      <div class="modal-header px-4 py-3 border-0" style="border-bottom: 1px solid var(--border-subtle) !important;">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-plus-circle text-primary me-2"></i>Daftarkan & Otomatisasi Provisioning Client Baru
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="formCreateClient" action="{{ route('sys_admin.clients.store') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          
          <div class="alert alert-info py-2 px-3 rounded-3 mb-4 d-flex align-items-center gap-2" style="font-size:0.82rem;">
            <i class="bi bi-info-circle-fill fs-5"></i>
            <div>
              Sistem akan secara otomatis membuat <strong>Database Fisik MySQL Baru</strong>, menjalankan migrasi skema POS, membuat akun owner pertama, dan mengaktifkan outlet utama.
            </div>
          </div>

          {{-- Section 1: Data Perusahaan --}}
          <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-building me-1.5"></i>1. Identitas Perusahaan / Klien</h6>
          <div class="row g-3 mb-3">
            <div class="col-md-6 input-skeleton">
              <label for="client_name" class="form-label-modern fw-semibold">Nama Klien / Perusahaan <span class="text-danger">*</span></label>
              <input type="text" name="client_name" id="client_name" class="form-control form-control-modern" placeholder="Contoh: PT Bagaskara Food Group" required>
              <span class="text-danger d-block mt-1 field-error" id="error-client_name" style="font-size:0.8rem;"></span>
            </div>
            <div class="col-md-6 input-skeleton">
              <label for="client_code" class="form-label-modern fw-semibold">Kode Klien (Inisial DB) <span class="text-danger">*</span></label>
              <input type="text" name="client_code" id="client_code" class="form-control form-control-modern text-uppercase font-monospace" placeholder="Contoh: BAGASKARA" style="letter-spacing:1px;" required>
              <span class="text-danger d-block mt-1 field-error" id="error-client_code" style="font-size:0.8rem;"></span>
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6 input-skeleton">
              <label for="business_name" class="form-label-modern fw-semibold">Nama Brand / Resto <span class="text-danger">*</span></label>
              <input type="text" name="business_name" id="business_name" class="form-control form-control-modern" placeholder="Contoh: Bagaskara Cafe & Resto" required>
              <span class="text-danger d-block mt-1 field-error" id="error-business_name" style="font-size:0.8rem;"></span>
            </div>
            <div class="col-md-6 input-skeleton">
              <label for="environment" class="form-label-modern fw-semibold">Environment DB</label>
              <select name="environment" id="environment" class="form-select form-select-modern">
                <option value="dev" selected>Development / Uji Coba (_dev)</option>
                <option value="prod">Production / Klien Live (_prod)</option>
                <option value="staging">Staging (_staging)</option>
              </select>
            </div>
          </div>

          {{-- Live DB Name Preview --}}
          <div class="p-2.5 rounded-3 mb-3 d-flex align-items-center gap-2" style="background: var(--bg-elevated-2); border: 1px solid var(--border-subtle); font-size:0.82rem;">
            <i class="bi bi-database-gear text-primary fs-5"></i>
            <div>
              <span class="text-muted-c">Preview Target Database:</span>
              <code id="previewDbName" class="fw-bold text-primary ms-1">new_kasir_clientcode_dev</code>
            </div>
          </div>

          {{-- Section 2: Owner & Kontak PIC --}}
          <h6 class="fw-bold mb-3 mt-4 text-primary"><i class="bi bi-person-badge me-1.5"></i>2. Akun Owner & PIC</h6>
          <div class="row g-3 mb-3">
            <div class="col-md-4 input-skeleton">
              <label for="owner_name" class="form-label-modern fw-semibold">Nama Lengkap Owner <span class="text-danger">*</span></label>
              <input type="text" name="owner_name" id="owner_name" class="form-control form-control-modern" placeholder="Contoh: Budi Santoso" required>
              <span class="text-danger d-block mt-1 field-error" id="error-owner_name" style="font-size:0.8rem;"></span>
            </div>
            <div class="col-md-4 input-skeleton">
              <label for="owner_email" class="form-label-modern fw-semibold">Email Login Owner <span class="text-danger">*</span></label>
              <input type="email" name="owner_email" id="owner_email" class="form-control form-control-modern" placeholder="Contoh: owner@bagaskara.com" required>
              <span class="text-danger d-block mt-1 field-error" id="error-owner_email" style="font-size:0.8rem;"></span>
            </div>
            <div class="col-md-4 input-skeleton">
              <label for="owner_phone" class="form-label-modern fw-semibold">Nomor WhatsApp</label>
              <input type="text" name="owner_phone" id="owner_phone" class="form-control form-control-modern" placeholder="Contoh: 081234567890">
              <span class="text-danger d-block mt-1 field-error" id="error-owner_phone" style="font-size:0.8rem;"></span>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6 input-skeleton">
              <label for="owner_password" class="form-label-modern fw-semibold">Kata Sandi Akun Owner <span class="text-danger">*</span></label>
              <input type="password" name="owner_password" id="owner_password" class="form-control form-control-modern" value="password123" placeholder="Minimal 6 karakter" required>
              <span class="text-danger d-block mt-1 field-error" id="error-owner_password" style="font-size:0.8rem;"></span>
            </div>
            <div class="col-md-6 input-skeleton">
              <label for="plan_id" class="form-label-modern fw-semibold">Pilihan Paket Langganan <span class="text-danger">*</span></label>
              <select name="plan_id" id="plan_id" class="form-select form-select-modern" required>
                @foreach($plans as $plan)
                  <option value="{{ $plan->id }}" {{ $plan->plan_code === 'TRIAL' ? 'selected' : '' }}>
                    {{ $plan->plan_name }} ({{ $plan->badge_label }}) - Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}/bln
                  </option>
                @endforeach
              </select>
              <span class="text-danger d-block mt-1 field-error" id="error-plan_id" style="font-size:0.8rem;"></span>
            </div>
          </div>

          <div class="mb-3 input-skeleton">
            <label for="address" class="form-label-modern">Alamat Usaha</label>
            <textarea name="address" id="address" rows="2" class="form-control form-control-modern" placeholder="Alamat lengkap gerai/kantor pusat"></textarea>
          </div>

        </div>
        <div class="modal-footer px-4 py-3 border-0" style="border-top: 1px solid var(--border-subtle) !important;">
          <button type="button" class="btn btn-outline-secondary rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-grad rounded-3 px-4 fw-semibold btn-loading" id="btnSubmitProvision">
            <i class="bi bi-cpu me-1.5"></i>Eksekusi Provisioning
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('inputSearch');
  const statusFilter = document.getElementById('filterStatus');
  const planFilter = document.getElementById('filterPlan');
  const perPageFilter = document.getElementById('filterPerPage');
  const tableBody = document.getElementById('clientsTableBody');
  const paginationLinks = document.getElementById('tablePaginationLinks');
  const paginationInfo = document.getElementById('tablePaginationInfo');

  let debounceTimer;

  function loadData(url = null) {
    const fetchUrl = url || "{{ route('sys_admin.clients.index') }}";
    const params = new URLSearchParams({
      search: searchInput.value,
      status: statusFilter.value,
      plan_id: planFilter.value,
      per_page: perPageFilter.value
    });

    tableBody.innerHTML = `
      <tr>
        <td colspan="6" class="py-4 text-center">
          <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
          <span class="text-muted-c" style="font-size:0.85rem;">Memuat data clients...</span>
        </td>
      </tr>
    `;

    setTimeout(() => {
      fetch(`${fetchUrl}?${params.toString()}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          tableBody.innerHTML = data.html;
          paginationLinks.innerHTML = data.pagination;
          paginationInfo.textContent = `Total ${data.total} client terdaftar`;
        }
      })
      .catch(err => {
        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Gagal memuat data.</td></tr>`;
      });
    }, 400);
  }

  searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => loadData(), 300);
  });

  statusFilter.addEventListener('change', () => loadData());
  planFilter.addEventListener('change', () => loadData());
  perPageFilter.addEventListener('change', () => loadData());

  document.addEventListener('click', function(e) {
    const pageLink = e.target.closest('#tablePaginationLinks a');
    if (pageLink) {
      e.preventDefault();
      loadData(pageLink.getAttribute('href'));
    }
  });

  // Live Preview DB Name
  const clientNameInput = document.getElementById('client_name');
  const clientCodeInput = document.getElementById('client_code');
  const envSelect = document.getElementById('environment');
  const previewDbName = document.getElementById('previewDbName');

  function updateDbPreview() {
    let rawCode = clientCodeInput.value.trim();
    if (!rawCode && clientNameInput.value.trim()) {
      rawCode = clientNameInput.value.trim().replace(/[^a-zA-Z0-9]/g, '').substring(0, 15).toUpperCase();
    }
    const cleanCode = (rawCode || 'CLIENTCODE').toLowerCase().replace(/[^a-z0-9_]/g, '_');
    const env = envSelect.value || 'dev';
    previewDbName.textContent = `new_kasir_${cleanCode}_${env}`;
  }

  clientNameInput?.addEventListener('input', function() {
    if (!clientCodeInput.dataset.touched) {
      clientCodeInput.value = this.value.replace(/[^a-zA-Z0-9]/g, '').substring(0, 10).toUpperCase();
    }
    updateDbPreview();
  });

  clientCodeInput?.addEventListener('input', function() {
    this.dataset.touched = "true";
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
    updateDbPreview();
  });

  envSelect?.addEventListener('change', updateDbPreview);
  updateDbPreview();

  // Handle Form Submit Create Client (Provisioning)
  const formCreate = document.getElementById('formCreateClient');
  const btnSubmit = document.getElementById('btnSubmitProvision');

  formCreate.addEventListener('submit', function(e) {
    e.preventDefault();

    document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sedang Memproses Database & Migrasi...';
    formCreate.querySelectorAll('.input-skeleton').forEach(el => el.classList.add('loading-shimmer'));

    const formData = new FormData(formCreate);

    setTimeout(() => {
      fetch(formCreate.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: formData
      })
      .then(async res => {
        const data = await res.json();
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="bi bi-cpu me-1.5"></i>Eksekusi Provisioning';
        formCreate.querySelectorAll('.input-skeleton').forEach(el => el.classList.remove('loading-shimmer'));

        if (res.ok && data.success) {
          if (typeof NexoraToast === 'function') {
            NexoraToast(data.message, 'success');
          }
          const modalEl = document.getElementById('modalCreateClient');
          const modal = bootstrap.Modal.getInstance(modalEl);
          if (modal) modal.hide();

          setTimeout(() => {
            window.location.href = data.redirect_url;
          }, 400);
        } else {
          if (res.status === 422 && data.errors) {
            for (const [key, msgs] of Object.entries(data.errors)) {
              const errEl = document.getElementById(`error-${key}`);
              if (errEl) errEl.textContent = msgs[0];
            }
          } else {
            alert(data.message || 'Gagal memproses provisioning.');
          }
        }
      })
      .catch(err => {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="bi bi-cpu me-1.5"></i>Eksekusi Provisioning';
        formCreate.querySelectorAll('.input-skeleton').forEach(el => el.classList.remove('loading-shimmer'));
        alert('Koneksi ke server terputus.');
      });
    }, 400);
  });
});
</script>
@endpush
