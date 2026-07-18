@extends('admin.layouts.app')

@section('title', 'Supplier')

@php $activeMenu = 'supplier' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Supplier</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Supplier</span>
    </div>
  </div>
  <a href="{{ route('admin.supplier.create') }}" class="btn btn-primary-grad">
    <i class="bi bi-plus-lg me-1"></i>Tambah Supplier
  </a>
</div>

<div class="card">
  <div class="card-header-flex">
    <h6>Daftar Supplier</h6>
    <div class="d-flex align-items-center gap-2">
      <label class="form-label-modern mb-0" style="font-size:0.85rem;">Tampilkan</label>
      <select class="form-select-modern" id="perPage" style="width:auto;min-width:70px;">
        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
      </select>
      <span class="text-muted-c" style="font-size:0.85rem;">data</span>
      <span class="chip-tag" id="totalCount">{{ $suppliers->total() }} item</span>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive" id="tableContainer">
      <table class="table-modern" id="dataTable">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Kontak</th>
            <th>Telepon</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          @include('admin.supplier._data', ['suppliers' => $suppliers])
        </tbody>
      </table>
    </div>
    <div class="px-3 py-2 d-flex justify-content-between align-items-center" id="paginationContainer">
      <span class="text-muted-c" style="font-size:0.85rem;" id="pageInfo">
        Menampilkan {{ $suppliers->firstItem() ?? 0 }} - {{ $suppliers->lastItem() ?? 0 }} dari {{ $suppliers->total() }}
      </span>
      {{ $suppliers->onEachSide(1)->links('vendor.pagination.modern') }}
    </div>
  </div>
</div>

@if(session('success'))
  <script>document.addEventListener('DOMContentLoaded', function() { NexoraToast('{{ session('success') }}', 'success'); });</script>
@endif
@if(session('error'))
  <script>document.addEventListener('DOMContentLoaded', function() { NexoraToast('{{ session('error') }}', 'danger'); });</script>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const tableBody = document.getElementById('tableBody');
  const paginationContainer = document.getElementById('paginationContainer');
  const totalCount = document.getElementById('totalCount');
  const perPage = document.getElementById('perPage');
  let currentPage = 1;

  function showSkeleton(count) {
    let rows = '';
    for (let i = 0; i < count; i++) {
      rows += '<tr>';
      for (let j = 0; j < 6; j++) {
        rows += '<td><div class="skeleton skeleton-text"></div></td>';
      }
      rows += '</tr>';
    }
    tableBody.innerHTML = rows;
  }

  function loadData(page, perPageVal) {
    currentPage = page;
    showSkeleton(parseInt(perPageVal) || 10);
    paginationContainer.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span><ul class="pagination-modern"><li class="disabled"><span>&laquo;</span></li><li class="active"><span>...</span></li><li class="disabled"><span>&raquo;</span></li></ul>';

    const startTime = Date.now();

    fetch(`{{ route('admin.supplier.data') }}?page=${page}&per_page=${perPageVal}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
      const elapsed = Date.now() - startTime;
      const delay = Math.max(400 - elapsed, 0);

      setTimeout(() => {
        tableBody.innerHTML = data.html;
        paginationContainer.innerHTML = `
          <span class="text-muted-c" style="font-size:0.85rem;">Menampilkan ${data.from ?? 0} - ${data.to ?? 0} dari ${data.total}</span>
          ${data.pagination}
        `;
        totalCount.textContent = data.total + ' item';
        attachHandlers();
      }, delay);
    })
    .catch(() => {
      NexoraToast('Gagal memuat data.', 'danger');
    });
  }

  function attachHandlers() {
    paginationContainer.querySelectorAll('[data-page]').forEach(function(link) {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        loadData(parseInt(this.dataset.page), perPage.value);
      });
    });

    document.querySelectorAll('.btn-delete').forEach(function(btn) {
      btn.addEventListener('click', function() {
        if (!confirm('Hapus supplier ini?')) return;

        const url = this.dataset.url;
        fetch(url, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: '_token={{ csrf_token() }}&_method=DELETE'
        })
        .then(res => res.json())
        .then(data => {
          NexoraToast(data.success || 'Berhasil dihapus.', 'success');
          loadData(currentPage, perPage.value);
        })
        .catch(() => {
          NexoraToast('Gagal menghapus data.', 'danger');
        });
      });
    });
  }

  perPage.addEventListener('change', function() {
    loadData(1, this.value);
  });

  attachHandlers();
});
</script>
@endpush
