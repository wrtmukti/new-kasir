@extends('sys_admin.layouts.app')

@section('title', 'Platform Audit Logs Trail')

@section('content')
<div class="container-fluid p-0">

  {{-- Page Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <h4 class="fw-bold mb-1" style="font-size:1.45rem; color:var(--text-primary);">
        <i class="bi bi-shield-check-fill me-2 text-warning"></i>Platform Audit Logs Trail
      </h4>
      <p class="text-muted-c mb-0" style="font-size:0.85rem;">
        Catatan jejak audit permanen seluruh aktivitas administratif, lifecycle client, sesi impersonation, dan operasi sistem.
      </p>
    </div>
  </div>

  {{-- Main Audit Logs Table Card --}}
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
          <select class="form-select form-select-sm form-select-modern" id="filterAction" style="width:160px;">
            <option value="">Semua Aksi</option>
            @foreach($availableActions as $act)
              <option value="{{ $act }}">{{ $act }}</option>
            @endforeach
          </select>

          <select class="form-select form-select-sm form-select-modern" id="filterClient" style="width:160px;">
            <option value="">Semua Client</option>
            @foreach($clients as $cl)
              <option value="{{ $cl->client_id }}">{{ $cl->client_name }}</option>
            @endforeach
          </select>

          <select class="form-select form-select-sm form-select-modern" id="filterResult" style="width:120px;">
            <option value="">Semua Hasil</option>
            <option value="success">Success</option>
            <option value="failure">Failed</option>
          </select>

          <div class="input-group input-group-sm" style="width: 220px;">
            <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" id="inputSearch" class="form-control form-control-modern" placeholder="Cari actor/IP/target...">
          </div>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0" id="tableAuditLogs" style="font-size:0.85rem;">
          <thead style="background: var(--bg-elevated-2); color: var(--text-secondary);">
            <tr>
              <th class="ps-4">Timestamp</th>
              <th>Actor & Role</th>
              <th>Aksi Administratif</th>
              <th>Client / Client</th>
              <th>Target & Metadata</th>
              <th>Alamat IP</th>
              <th class="pe-4 text-end">Hasil</th>
            </tr>
          </thead>
          <tbody id="auditTableBody">
            @include('sys_admin.audit_logs.partials._table_rows', ['auditLogs' => $auditLogs])
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer bg-transparent border-0 px-4 py-3 d-flex flex-wrap align-items-center justify-content-between gap-2" style="border-top: 1px solid var(--border-subtle) !important;">
      <div class="text-muted-c" id="tablePaginationInfo" style="font-size:0.82rem;">
        Menampilkan {{ $auditLogs->firstItem() ?? 0 }} sampai {{ $auditLogs->lastItem() ?? 0 }} dari {{ $auditLogs->total() }} log audit
      </div>
      <div id="tablePaginationLinks">
        {{ $auditLogs->links('vendor.pagination.modern') }}
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('inputSearch');
  const actionFilter = document.getElementById('filterAction');
  const clientFilter = document.getElementById('filterClient');
  const resultFilter = document.getElementById('filterResult');
  const perPageFilter = document.getElementById('filterPerPage');
  const tableBody = document.getElementById('auditTableBody');
  const paginationLinks = document.getElementById('tablePaginationLinks');
  const paginationInfo = document.getElementById('tablePaginationInfo');

  let debounceTimer;

  function loadData(url = null) {
    const fetchUrl = url || "{{ route('sys_admin.audit-logs.index') }}";
    const params = new URLSearchParams({
      search: searchInput.value,
      action: actionFilter.value,
      client_id: clientFilter.value,
      result: resultFilter.value,
      per_page: perPageFilter.value
    });

    tableBody.innerHTML = `
      <tr>
        <td colspan="7" class="py-4 text-center">
          <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
          <span class="text-muted-c" style="font-size:0.85rem;">Memuat data audit logs...</span>
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
          paginationInfo.textContent = `Total ${data.total} log audit tercatat`;
        }
      })
      .catch(err => {
        tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Gagal memuat data.</td></tr>`;
      });
    }, 400);
  }

  searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => loadData(), 300);
  });

  actionFilter.addEventListener('change', () => loadData());
  clientFilter.addEventListener('change', () => loadData());
  resultFilter.addEventListener('change', () => loadData());
  perPageFilter.addEventListener('change', () => loadData());

  document.addEventListener('click', function(e) {
    const pageLink = e.target.closest('#tablePaginationLinks a');
    if (pageLink) {
      e.preventDefault();
      loadData(pageLink.getAttribute('href'));
    }
  });
});
</script>
@endpush
