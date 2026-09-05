@extends('admin.layouts.app')

@section('title', 'Daftar Pesanan')

@php $activeMenu = 'order-list' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Daftar Pesanan</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Daftar Pesanan</span>
    </div>
  </div>
  <a href="{{ route('admin.order.index') }}" class="btn btn-primary-grad">
    <i class="bi bi-plus-lg me-1"></i>Pesan Baru
  </a>
</div>

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px;">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-exclamation-triangle-fill fs-4"></i>
      <div>
        <strong class="d-block">Gagal Memproses Pesanan!</strong>
        {{ session('error') }}
      </div>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>

  <!-- MODAL ERROR NOTICE -->
  <div class="modal fade" id="listErrorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="background: var(--bg-elevated, #1e293b); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 16px; color: #f8fafc;">
        <div class="modal-header border-secondary border-opacity-25 px-4 py-3">
          <h5 class="modal-title fw-bold text-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Pemberitahuan Sistem
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center p-4">
          <div class="mb-3 text-danger fs-1">
            <i class="bi bi-x-circle-fill"></i>
          </div>
          <h6 class="fw-bold mb-2">Pesanan Tidak Dapat Diproses</h6>
          <p class="text-muted-c mb-0" style="font-size:0.9rem;">
            {{ session('error') }}
          </p>
        </div>
        <div class="modal-footer border-secondary border-opacity-25 px-4 py-3">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
@endif

<div class="card">

  <div class="card-header-flex">
    <h6>Pesanan</h6>
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
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tipe</th>
            <th>Status</th>
            <th>Total</th>
            <th>Tanggal</th>
            <th style="width:130px;" class="text-end">Aksi</th>
          </tr>

        </thead>
        <tbody id="tableBody">
          @include('admin.kasir.order._list_data', ['orders' => $orders])
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
        <p class="mt-2 mb-0">Yakin ingin menghapus pesanan ini?</p>
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
  let currentPage = 1;
  const perPageEl = document.getElementById('perPage');
  const tableBody = document.getElementById('tableBody');
  const paginationContainer = document.getElementById('paginationContainer');

  function skeleton(count) {
    let r = '';
    for (let i = 0; i < count; i++) { r += '<tr>'; for (let j = 0; j < 5; j++) r += '<td><div class="skeleton skeleton-text"></div></td>'; r += '</tr>'; }
    return r;
  }

  function loadData(page, perPageVal) {
    currentPage = page;
    tableBody.innerHTML = skeleton(parseInt(perPageVal)||10);
    paginationContainer.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span><ul class="pagination-modern"><li class="disabled"><span>«</span></li><li class="active"><span>...</span></li><li class="disabled"><span>»</span></li></ul>';
    const t = Date.now();
    fetch('{{ route("admin.order.list-data") }}?page='+page+'&per_page='+perPageVal, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(d => {
      setTimeout(() => {
        tableBody.innerHTML = d.html;
        paginationContainer.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Menampilkan '+(d.from??0)+' - '+(d.to??0)+' dari '+d.total+'</span>'+d.pagination;
        document.getElementById('totalCount').textContent = d.total + ' item';
        attachHandlers();
      }, Math.max(400-(Date.now()-t),0));
    })
    .catch(() => NexoraToast('Gagal memuat data.', 'danger'));
  }

  function attachHandlers() {
    document.querySelectorAll('#paginationContainer [data-page]').forEach(l => {
      l.addEventListener('click', function(e) { e.preventDefault(); loadData(parseInt(this.dataset.page), perPageEl.value); });
    });
    // row click
    document.querySelectorAll('.row-clickable').forEach(function(row) {
      row.addEventListener('click', function(e) {
        if (e.target.closest('a') || e.target.closest('button') || e.target.closest('.btn')) return;
        window.location.href = this.dataset.url;
      });
    });
  }

  perPageEl.addEventListener('change', function() { loadData(1, this.value); });
  attachHandlers();

  @if(session('error'))
    const listErrModalEl = document.getElementById('listErrorModal');
    if (listErrModalEl) {
      const listErrModal = new bootstrap.Modal(listErrModalEl);
      listErrModal.show();
    }
  @endif
});
</script>
@endpush

