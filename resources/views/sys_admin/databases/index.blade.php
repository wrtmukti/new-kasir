@extends('sys_admin.layouts.app')

@section('title', 'Client Database Management')

@section('content')
<div class="container-fluid p-0">

  {{-- Page Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <h4 class="fw-bold mb-1" style="font-size:1.45rem; color:var(--text-primary);">
        <i class="bi bi-database-fill-gear me-2 text-info"></i>Client Database Management
      </h4>
      <p class="text-muted-c mb-0" style="font-size:0.85rem;">
        Monitoring dan pemeliharaan arsitektur isolasi Database-per-Client untuk seluruh client.
      </p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-primary-grad rounded-3 px-3 py-2 fw-semibold" id="btnMigrateAll">
        <i class="bi bi-arrow-repeat me-1.5"></i>Migrasi Massal Client
      </button>
    </div>
  </div>

  {{-- 3 Top KPI Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card p-3 rounded-4 border-0 shadow-sm" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.8rem;">TOTAL CLIENT DATABASES</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
            <i class="bi bi-database fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0" style="font-size:1.75rem; color:var(--text-primary);">{{ $stats['total'] }}</h3>
        <small class="text-muted-c">1 DB Fisik MySQL per Klien</small>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card p-3 rounded-4 border-0 shadow-sm" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.8rem;">CONNECTED & HEALTHY</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(16, 185, 129, 0.15); color: #10b981;">
            <i class="bi bi-shield-check fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-success" style="font-size:1.75rem;">{{ $stats['connected'] }}</h3>
        <small class="text-muted-c">Status koneksi aktif & responsive</small>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card p-3 rounded-4 border-0 shadow-sm" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.8rem;">RATA-RATA LATENCY</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
            <i class="bi bi-speedometer fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-primary" style="font-size:1.75rem;">{{ $stats['avg_latency'] }} ms</h3>
        <small class="text-muted-c">Response time query database</small>
      </div>
    </div>
  </div>

  {{-- Main Database Table Card --}}
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

        <div class="d-flex align-items-center gap-2">
          <select class="form-select form-select-sm form-select-modern" id="filterStatus" style="width:140px;">
            <option value="">Semua Status</option>
            <option value="connected">Connected</option>
            <option value="disconnected">Disconnected</option>
            <option value="warning">Warning</option>
          </select>

          <div class="input-group input-group-sm" style="width: 240px;">
            <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" id="inputSearch" class="form-control form-control-modern" placeholder="Cari client/DB...">
          </div>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0" id="tableDatabases" style="font-size:0.85rem;">
          <thead style="background: var(--bg-elevated-2); color: var(--text-secondary);">
            <tr>
              <th class="ps-4">Klien / Client</th>
              <th>Nama Database Fisik</th>
              <th>Status Koneksi</th>
              <th>Latency Ping</th>
              <th>Jumlah Tabel</th>
              <th>Health Check Terakhir</th>
              <th class="pe-4 text-end">Aksi Pemeliharaan</th>
            </tr>
          </thead>
          <tbody id="databasesTableBody">
            @include('sys_admin.databases.partials._table_rows', ['databases' => $databases])
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer bg-transparent border-0 px-4 py-3 d-flex flex-wrap align-items-center justify-content-between gap-2" style="border-top: 1px solid var(--border-subtle) !important;">
      <div class="text-muted-c" id="tablePaginationInfo" style="font-size:0.82rem;">
        Menampilkan {{ $databases->firstItem() ?? 0 }} sampai {{ $databases->lastItem() ?? 0 }} dari {{ $databases->total() }} database
      </div>
      <div id="tablePaginationLinks">
        {{ $databases->links('vendor.pagination.modern') }}
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('inputSearch');
  const statusFilter = document.getElementById('filterStatus');
  const perPageFilter = document.getElementById('filterPerPage');
  const tableBody = document.getElementById('databasesTableBody');
  const paginationLinks = document.getElementById('tablePaginationLinks');
  const paginationInfo = document.getElementById('tablePaginationInfo');

  let debounceTimer;

  function loadData(url = null) {
    const fetchUrl = url || "{{ route('sys_admin.databases.index') }}";
    const params = new URLSearchParams({
      search: searchInput.value,
      status: statusFilter.value,
      per_page: perPageFilter.value
    });

    // Skeleton shimmer min 400ms
    tableBody.innerHTML = `
      <tr>
        <td colspan="7" class="py-4 text-center">
          <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
          <span class="text-muted-c" style="font-size:0.85rem;">Memuat data database...</span>
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
          paginationInfo.textContent = `Total ${data.total} database terdaftar`;
          bindActionButtons();
        }
      })
      .catch(err => {
        tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Gagal memuat data.</td></tr>`;
      });
    }, 400); // 400ms loading feedback
  }

  // Filter Event Listeners
  searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => loadData(), 300);
  });

  statusFilter.addEventListener('change', () => loadData());
  perPageFilter.addEventListener('change', () => loadData());

  // Pagination click handler
  document.addEventListener('click', function(e) {
    const pageLink = e.target.closest('#tablePaginationLinks a');
    if (pageLink) {
      e.preventDefault();
      loadData(pageLink.getAttribute('href'));
    }
  });

  // Action Buttons Binding (Test Ping & Run Migration)
  function bindActionButtons() {
    // Test Ping Button
    document.querySelectorAll('.btn-test-conn').forEach(btn => {
      btn.onclick = function() {
        const clientId = this.getAttribute('data-client-id');
        const origHtml = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Ping...';

        fetch(`/sys_admin/databases/${clientId}/test-connection`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          }
        })
        .then(res => res.json())
        .then(data => {
          this.disabled = false;
          this.innerHTML = origHtml;

          if (data.success) {
            const badge = document.getElementById(`badge-status-${clientId}`);
            const latency = document.getElementById(`latency-${clientId}`);
            const tables = document.getElementById(`tables-${clientId}`);
            const time = document.getElementById(`health-time-${clientId}`);

            if (badge) {
              badge.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1';
              badge.innerHTML = '<i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i>Connected';
            }
            if (latency) latency.textContent = `${data.latency_ms} ms`;
            if (tables) tables.textContent = `${data.tables_count} tabel`;
            if (time) time.textContent = 'Baru saja';

            if (typeof NexoraToast === 'function') {
              NexoraToast(data.message, 'success');
            } else {
              alert(data.message);
            }
          } else {
            if (typeof NexoraToast === 'function') {
              NexoraToast(data.message, 'error');
            } else {
              alert(data.message);
            }
          }
        })
        .catch(err => {
          this.disabled = false;
          this.innerHTML = origHtml;
          alert('Koneksi gagal.');
        });
      };
    });

    // Run Migration Button
    document.querySelectorAll('.btn-run-migrate').forEach(btn => {
      btn.onclick = function() {
        const clientId = this.getAttribute('data-client-id');
        if (!confirm(`Jalankan migrasi skema database untuk ${clientId}?`)) return;

        const origHtml = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(`/sys_admin/databases/${clientId}/migrate`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          }
        })
        .then(res => res.json())
        .then(data => {
          this.disabled = false;
          this.innerHTML = origHtml;
          if (typeof NexoraToast === 'function') {
            NexoraToast(data.message, data.success ? 'success' : 'error');
          } else {
            alert(data.message);
          }
          loadData();
        })
        .catch(err => {
          this.disabled = false;
          this.innerHTML = origHtml;
          alert('Gagal menjalankan migrasi.');
        });
      };
    });
  }

  bindActionButtons();
});
</script>
@endpush
