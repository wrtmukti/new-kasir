@extends('admin.layouts.app')

@section('title', 'Paket Bundle')

@php $activeMenu = 'bundle' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Paket Bundle</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Paket</span>
    </div>
  </div>
  <a href="{{ route('admin.bundle.create') }}" class="btn btn-primary-grad">
    <i class="bi bi-plus-lg me-1"></i>Tambah Paket
  </a>
</div>

<div class="card">
  <div class="card-header-flex">
    <h6><i class="bi bi-gift me-2"></i>Daftar Paket</h6>
    <div class="d-flex align-items-center gap-2">
      <label class="form-label-modern mb-0" style="font-size:0.85rem;">Tampilkan</label>
      <select class="form-select-modern" id="perPage" style="width:auto;min-width:70px;">
        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
      </select>
      <span class="text-muted-c" style="font-size:0.85rem;">data</span>
      <span class="chip-tag" id="totalCount">{{ $bundles->total() }} item</span>
      <button class="btn btn-ghost btn-sm" id="viewToggleBtn" data-view="list" title="Tampilan" style="flex-shrink:0;">
        <i class="bi bi-grid-fill" id="viewToggleIcon"></i>
      </button>
    </div>
  </div>

  {{-- List view --}}
  <div id="listView" class="table-view">
    <div class="table-responsive" id="tableContainer">
      <table class="table-modern" id="dataTable">
        <thead>
          <tr>
            <th>Paket</th>
            <th>Harga</th>
            <th>Item</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          @include('admin.bundle._data', ['bundles' => $bundles])
        </tbody>
      </table>
    </div>
    <div class="px-3 py-2 d-flex justify-content-between align-items-center" id="paginationContainer">
      <span class="text-muted-c" style="font-size:0.85rem;" id="pageInfo">
        Menampilkan {{ $bundles->firstItem() ?? 0 }} - {{ $bundles->lastItem() ?? 0 }} dari {{ $bundles->total() }}
      </span>
      {{ $bundles->onEachSide(1)->links('vendor.pagination.modern') }}
    </div>
  </div>

  {{-- Card view --}}
  <div id="cardView" class="card-view" style="display:none;">
    <div class="bundle-card-grid" id="cardGrid">
      @include('admin.bundle._card', ['bundles' => $bundles])
    </div>
    <div class="px-3 py-2 d-flex justify-content-between align-items-center" id="paginationContainerCard" style="display:none;">
      <span class="text-muted-c" style="font-size:0.85rem;">
        Menampilkan {{ $bundles->firstItem() ?? 0 }} - {{ $bundles->lastItem() ?? 0 }} dari {{ $bundles->total() }}
      </span>
      {{ $bundles->onEachSide(1)->links('vendor.pagination.modern') }}
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.bundle-card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 1rem;
  padding: 1.25rem;
}
.bundle-card {
  background: var(--bg-elevated);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  overflow: hidden;
  cursor: pointer;
  transition: border-color 0.2s, transform 0.15s;
  position: relative;
}
.bundle-card:hover {
  border-color: var(--accent-1);
  transform: translateY(-2px);
}
.bundle-card-img {
  width: 100%;
  height: 130px;
  overflow: hidden;
  background: var(--bg-elevated-2);
  display: flex;
  align-items: center;
  justify-content: center;
}
.bundle-card-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.bundle-card-img-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  font-size: 2rem;
  color: var(--text-muted);
}
.bundle-card-body {
  padding: 0.75rem;
}
.bundle-card-name {
  font-weight: 600;
  font-size: 0.9rem;
  line-height: 1.3;
  margin-bottom: 0.15rem;
  color: var(--text-primary);
}
.bundle-card-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.15rem;
}
.bundle-card-code {
  font-size: 0.75rem;
  color: var(--text-muted);
}
.bundle-card-items {
  font-size: 0.75rem;
  color: var(--text-secondary);
  margin-bottom: 0.35rem;
}
.bundle-card-price {
  font-weight: 700;
  font-size: 1rem;
  color: var(--accent-1);
}
.bundle-card-actions {
  position: absolute;
  top: 0.4rem;
  right: 0.4rem;
  display: flex;
  gap: 0.2rem;
  opacity: 0;
  transition: opacity 0.2s;
}
.bundle-card:hover .bundle-card-actions {
  opacity: 1;
}
</style>
@endpush

{{-- Toast flash session --}}
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
        <p class="mt-2 mb-0">Yakin ingin menghapus paket ini?</p>
      </div>
      <div class="modal-footer justify-content-center border-0 pt-0">
        <button type="button" class="btn btn-outline-soft" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Ya, Hapus</button>
      </div>
    </div>
  </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  let currentPage = 1;
  let activeView = 'list';
  const perPageEl = document.getElementById('perPage');
  const totalCount = document.getElementById('totalCount');

  // Toggle view
  const toggleBtn = document.getElementById('viewToggleBtn');
  const toggleIcon = document.getElementById('viewToggleIcon');
  toggleBtn.addEventListener('click', function() {
    activeView = activeView === 'list' ? 'card' : 'list';
    toggleIcon.className = activeView === 'card' ? 'bi bi-list-ul' : 'bi bi-grid-fill';
    this.dataset.view = activeView;
    document.getElementById('listView').style.display = activeView === 'list' ? '' : 'none';
    document.getElementById('cardView').style.display = activeView === 'card' ? '' : 'none';
    loadData(currentPage, perPageEl.value);
  });

  // Skeleton
  function showSkeleton(count) {
    if (activeView === 'card') {
      let cards = '';
      for (let i = 0; i < count; i++) {
        cards += '<div class="bundle-card" style="pointer-events:none;">';
        cards += '<div class="bundle-card-img"><div class="skeleton" style="width:100%;height:100%;"></div></div>';
        cards += '<div class="bundle-card-body">';
        cards += '<div class="skeleton skeleton-text mb-2"></div>';
        cards += '<div class="skeleton skeleton-text" style="width:60%;"></div>';
        cards += '</div></div>';
      }
      return cards;
    }
    let rows = '';
    for (let i = 0; i < count; i++) {
      rows += '<tr>';
      for (let j = 0; j < 5; j++) {
        rows += '<td><div class="skeleton skeleton-text"></div></td>';
      }
      rows += '</tr>';
    }
    return rows;
  }

  const tableBody = document.getElementById('tableBody');
  const paginationContainer = document.getElementById('paginationContainer');
  const cardGrid = document.getElementById('cardGrid');
  const paginationContainerCard = document.getElementById('paginationContainerCard');

  function loadData(page, perPageVal) {
    currentPage = page;

    if (activeView === 'list') {
      tableBody.innerHTML = showSkeleton(parseInt(perPageVal) || 10);
      paginationContainer.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span><ul class="pagination-modern"><li class="disabled"><span>&laquo;</span></li><li class="active"><span>...</span></li><li class="disabled"><span>&raquo;</span></li></ul>';
    } else {
      cardGrid.innerHTML = showSkeleton(parseInt(perPageVal) || 10);
      paginationContainerCard.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span><ul class="pagination-modern"><li class="disabled"><span>&laquo;</span></li><li class="active"><span>...</span></li><li class="disabled"><span>&raquo;</span></li></ul>';
      paginationContainerCard.style.display = '';
    }

    const startTime = Date.now();
    fetch('{{ route("admin.bundle.data") }}?page=' + page + '&per_page=' + perPageVal + '&view=' + activeView, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      const delay = Math.max(400 - (Date.now() - startTime), 0);
      setTimeout(function() {
        if (activeView === 'list') {
          tableBody.innerHTML = data.html;
          paginationContainer.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Menampilkan ' + (data.from ?? 0) + ' - ' + (data.to ?? 0) + ' dari ' + data.total + '</span>' + data.pagination;
        } else {
          cardGrid.innerHTML = data.html;
          paginationContainerCard.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Menampilkan ' + (data.from ?? 0) + ' - ' + (data.to ?? 0) + ' dari ' + data.total + '</span>' + data.pagination;
        }
        totalCount.textContent = data.total + ' item';
        attachHandlers();
      }, delay);
    })
    .catch(function() { NexoraToast('Gagal memuat data.', 'danger'); });
  }

  function attachHandlers() {
    document.querySelectorAll('#paginationContainer [data-page], #paginationContainerCard [data-page]').forEach(function(link) {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        loadData(parseInt(this.dataset.page), perPageEl.value);
      });
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

    document.querySelectorAll('.bundle-card').forEach(function(card) {
      card.addEventListener('click', function(e) {
        if (e.target.closest('a') || e.target.closest('button') || e.target.closest('.btn')) return;
        window.location.href = this.dataset.url;
      });
    });
  }

  perPageEl.addEventListener('change', function() {
    loadData(1, this.value);
  });

  document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    const url = this.dataset.url;
    if (!url) return;
    var modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
    if (modal) modal.hide();
    fetch(url, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
      body: '_token={{ csrf_token() }}&_method=DELETE'
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      NexoraToast(data.success || 'Berhasil dihapus.', 'success');
      loadData(1, perPageEl.value);
    })
    .catch(function() { NexoraToast('Gagal menghapus data.', 'danger'); });
  });

  attachHandlers();
});
</script>
@endpush
