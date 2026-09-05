@extends('admin.layouts.app')

@section('title', 'Riwayat Bundle')

@php $activeMenu = 'history-bundle' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Riwayat Bundle</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Riwayat Bundle</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex">
    <h6>Riwayat Paket Bundle</h6>
    <div class="d-flex align-items-center gap-2">
      <label class="form-label-modern mb-0" style="font-size:0.85rem;">Tampilkan</label>
      <select class="form-select-modern" id="perPage" style="width:auto;min-width:70px;">
        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
      </select>
      <span class="text-muted-c" style="font-size:0.85rem;">data</span>
      <span class="chip-tag" id="totalCount">{{ $histories->total() }} riwayat</span>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive" id="tableContainer">
      <table class="table-modern" id="dataTable">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Aksi</th>
            <th>Kode</th>
            <th>Nama Bundle</th>
            <th>Harga</th>
            <th>Status</th>
            <th>Diubah</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          @include('admin.kasir.history.bundle._data', ['histories' => $histories])
        </tbody>
      </table>
    </div>
    <div class="px-3 py-2 d-flex justify-content-between align-items-center" id="paginationContainer">
      <span class="text-muted-c" style="font-size:0.85rem;" id="pageInfo">
        Menampilkan {{ $histories->firstItem() ?? 0 }} - {{ $histories->lastItem() ?? 0 }} dari {{ $histories->total() }}
      </span>
      {{ $histories->onEachSide(1)->links('vendor.pagination.modern') }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const tableBody = document.getElementById('tableBody');
  const paginationContainer = document.getElementById('paginationContainer');
  const totalCount = document.getElementById('totalCount');
  const perPage = document.getElementById('perPage');
  let currentPage = 1;

  function loadData(page, perPageVal) {
    const url = '{{ route("admin.history.bundle.data") }}?page=' + page + '&per_page=' + perPageVal;

    tableBody.innerHTML = '';
    for (let i = 0; i < Math.min(perPageVal, 5); i++) {
      const tr = document.createElement('tr');
      tr.innerHTML = '<td colspan="7"><div class="skeleton skeleton-text" style="height:20px;margin:4px 0;"></div></td>';
      tableBody.appendChild(tr);
    }

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(d => {
        setTimeout(() => {
          tableBody.innerHTML = d.html;
          paginationContainer.innerHTML = d.pagination
            ? '<div class="d-flex justify-content-between align-items-center w-100">'
            + '<span class="text-muted-c" style="font-size:0.85rem;">Menampilkan ' + d.from + ' - ' + d.to + ' dari ' + d.total + '</span>'
            + d.pagination + '</div>'
            : '<span class="text-muted-c">Tidak ada data</span>';
          totalCount.textContent = d.total + ' riwayat';
          bindRowClickable();
        }, 400);
      });
  }

  perPage.addEventListener('change', function() {
    currentPage = 1;
    loadData(currentPage, this.value);
  });

  document.addEventListener('click', function(e) {
    const pageLink = e.target.closest('.pagination a');
    if (pageLink) {
      e.preventDefault();
      const url = new URL(pageLink.href);
      currentPage = url.searchParams.get('page') || 1;
      loadData(currentPage, perPage.value);
    }
  });

  // Row click → detail
  function bindRowClickable() {
    document.querySelectorAll('.row-clickable').forEach(function(row) {
      row.addEventListener('click', function(e) {
        if (e.target.closest('a') || e.target.closest('button') || e.target.closest('.btn')) return;
        window.location.href = this.dataset.url;
      });
    });
  }
  bindRowClickable();
});
</script>
@endpush
