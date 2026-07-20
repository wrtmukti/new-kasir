@extends('admin.layouts.app')

@section('title', 'Purchase Order')

@php $activeMenu = 'purchase-order' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Purchase Order</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Purchase Order</span>
    </div>
  </div>
  <a href="{{ route('admin.purchase-order.create') }}" class="btn btn-primary-grad">
    <i class="bi bi-plus-lg me-1"></i>Buat PO
  </a>
</div>

<div class="card">
  <div class="card-header-flex">
    <h6>Daftar Purchase Order</h6>
    <div class="d-flex align-items-center gap-2">
      <label class="form-label-modern mb-0" style="font-size:0.85rem;">Tampilkan</label>
      <select class="form-select-modern" id="perPage" style="width:auto;min-width:70px;">
        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
      </select>
      <span class="text-muted-c" style="font-size:0.85rem;">data</span>
      <span class="chip-tag" id="totalCount">{{ $orders->total() }} item</span>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive" id="tableContainer">
      <table class="table-modern" id="dataTable">
        <thead>
          <tr>
            <th>Kode PO</th>
            <th>Supplier</th>
            <th>Tanggal</th>
            <th>Total</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          @include('admin.purchase-order._data', ['orders' => $orders])
        </tbody>
      </table>
    </div>
    <div class="px-3 py-2 d-flex justify-content-between align-items-center" id="paginationContainer">
      <span class="text-muted-c" style="font-size:0.85rem;" id="pageInfo">
        Menampilkan {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }}
      </span>
      {{ $orders->onEachSide(1)->links('vendor.pagination.modern') }}
    </div>
  </div>
</div>

@if(session('success'))
  <script>document.addEventListener('DOMContentLoaded', function() { NexoraToast('{{ session('success') }}', 'success'); });</script>
@endif
@if(session('error'))
  <script>document.addEventListener('DOMContentLoaded', function() { NexoraToast('{{ session('error') }}', 'danger'); });</script>
@endif
{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="mb-0">Konfirmasi Hapus</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-4">
        <i class="bi bi-exclamation-triangle-fill" style="font-size:2rem;color:var(--danger);"></i>
        <p class="mt-2 mb-0">Yakin ingin menghapus PO ini?</p>
      </div>
      <div class="modal-footer justify-content-center border-0 pt-0">
        <button type="button" class="btn btn-outline-soft" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Ya, Hapus</button>
      </div>
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

  function showSkeleton(count) {
    let rows = '';
    for (let i = 0; i < count; i++) {
      rows += '<tr>';
      for (let j = 0; j < 6; j++) rows += '<td><div class="skeleton skeleton-text"></div></td>';
      rows += '</tr>';
    }
    tableBody.innerHTML = rows;
  }

  function loadData(page, perPageVal) {
    currentPage = page;
    showSkeleton(parseInt(perPageVal) || 10);
    paginationContainer.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span><ul class="pagination-modern"><li class="disabled"><span>&laquo;</span></li><li class="active"><span>...</span></li><li class="disabled"><span>&raquo;</span></li></ul>';
    const startTime = Date.now();
    fetch(`{{ route('admin.purchase-order.data') }}?page=${page}&per_page=${perPageVal}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
      const delay = Math.max(400 - (Date.now() - startTime), 0);
      setTimeout(() => {
        tableBody.innerHTML = data.html;
        paginationContainer.innerHTML = `<span class="text-muted-c" style="font-size:0.85rem;">Menampilkan ${data.from ?? 0} - ${data.to ?? 0} dari ${data.total}</span>${data.pagination}`;
        totalCount.textContent = data.total + ' item';
        attachHandlers();
      }, delay);
    })
    .catch(() => NexoraToast('Gagal memuat data.', 'danger'));
  }

  function attachHandlers() {
    paginationContainer.querySelectorAll('[data-page]').forEach(function(link) {
      link.addEventListener('click', function(e) { e.preventDefault(); loadData(parseInt(this.dataset.page), perPage.value); });
    });
    document.querySelectorAll('.btn-delete').forEach(function(btn) {
      btn.addEventListener('click', function() {
        document.getElementById('confirmDeleteBtn').dataset.url = this.dataset.url;
        var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
      });
    });

    document.querySelectorAll('.row-clickable').forEach(function(row) {
      row.addEventListener('click', function(e) {
        if (e.target.closest('a') || e.target.closest('button') || e.target.closest('.btn')) return;
        window.location.href = this.dataset.url;
      });
    });
  }

  // Confirm delete — bind sekali
  document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    const url = this.dataset.url;
    if (!url) return;
    var modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
    if (modal) modal.hide();
    fetch(url, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' }, body: '_token={{ csrf_token() }}&_method=DELETE' })
    .then(res => res.json()).then(data => { NexoraToast(data.success || 'Berhasil dihapus.', 'success'); loadData(currentPage, perPage.value); })
    .catch(() => NexoraToast('Gagal menghapus data.', 'danger'));
  });

  perPage.addEventListener('change', function() { loadData(1, this.value); });
  attachHandlers();
});
</script>
@endpush
