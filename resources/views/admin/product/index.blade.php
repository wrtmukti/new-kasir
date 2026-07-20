@extends('admin.layouts.app')

@section('title', 'Produk')

@php $activeMenu = 'product' @endphp
@php
  use App\Models\Admin\Category;
  $categories = Category::where('delete_status', 0)->where('category_status', 1)->get();
@endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Produk</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Produk</span>
    </div>
  </div>
  <a href="{{ route('admin.product.create') }}" class="btn btn-primary-grad">
    <i class="bi bi-plus-lg me-1"></i>Tambah Produk
  </a>
</div>

<div class="card">
  <div class="card-header-flex">
    <h6>Daftar Produk</h6>
    <div class="d-flex align-items-center gap-2">
      <label class="form-label-modern mb-0" style="font-size:0.85rem;">Tampilkan</label>
      <select class="form-select-modern" id="perPage" style="width:auto;min-width:70px;">
        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
      </select>
      <span class="text-muted-c" style="font-size:0.85rem;">data</span>
      <span class="chip-tag" id="totalCount">{{ $products->total() }} item</span>
    </div>
  </div>

  {{-- Tabs Kategori + Toggle View --}}
  <div class="px-3 pt-2">
    <div class="d-flex align-items-center justify-content-between" style="flex-wrap:wrap;gap:0.5rem;">
      <div class="tabs-modern">
        <span class="tab-link active" data-tab-target="#tabAll" data-category-id="">Semua</span>
        @foreach($categories as $cat)
          <span class="tab-link" data-tab-target="#tabCat{{ $cat->category_id }}" data-category-id="{{ $cat->category_id }}">{{ $cat->category_name }}</span>
        @endforeach
      </div>
      <button class="btn btn-ghost btn-sm" id="viewToggleBtn" data-view="list" title="Tampilan" style="flex-shrink:0;">
        <i class="bi bi-grid-fill" id="viewToggleIcon"></i>
      </button>
    </div>
  </div>

  {{-- Panel Semua --}}
  <div id="tabAll" data-tab-panel>
    <div class="card-body p-0">
      {{-- Table view --}}
      <div class="table-view">
        <div class="table-responsive" id="tableContainer">
          <table class="table-modern" id="dataTable">
            <thead>
              <tr>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="tableBody">
              @include('admin.product._data', ['products' => $products])
            </tbody>
          </table>
        </div>
        <div class="px-3 py-2 d-flex justify-content-between align-items-center" id="paginationContainer">
          <span class="text-muted-c" style="font-size:0.85rem;" id="pageInfo">
            Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }}
          </span>
          {{ $products->onEachSide(1)->links('vendor.pagination.modern') }}
        </div>
      </div>
      {{-- Card view --}}
      <div class="card-view" style="display:none;">
        <div class="product-card-grid" id="tabAllCardGrid"></div>
        <div class="px-3 py-2 d-flex justify-content-between align-items-center" id="paginationContainerCard" style="display:none;"></div>
      </div>
    </div>
  </div>

  {{-- Panel per kategori --}}
  @foreach($categories as $cat)
  <div id="tabCat{{ $cat->category_id }}" data-tab-panel style="display:none;">
    <div class="card-body p-0">
      <div class="table-view">
        <div class="table-responsive">
          <table class="table-modern">
            <thead>
              <tr>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody class="tab-table-body" data-category-id="{{ $cat->category_id }}">
              <tr><td colspan="6" class="text-center text-muted-c py-4">Pilih tab untuk memuat data.</td></tr>
            </tbody>
          </table>
        </div>
        <div class="px-3 py-2 d-flex justify-content-between align-items-center tab-pagination-container">
          <span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span>
        </div>
      </div>
      <div class="card-view" style="display:none;">
        <div class="product-card-grid" id="tabCat{{ $cat->category_id }}CardGrid"></div>
        <div class="px-3 py-2 d-flex justify-content-between align-items-center tab-pagination-container-card" style="display:none;"></div>
      </div>
    </div>
  </div>
  @endforeach
</div>

@push('styles')
<style>
.product-card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 1rem;
  padding: 1.25rem;
}
.product-card {
  background: var(--bg-elevated);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  overflow: hidden;
  cursor: pointer;
  transition: border-color 0.2s, transform 0.15s;
  position: relative;
}
.product-card:hover {
  border-color: var(--accent-1);
  transform: translateY(-2px);
}
.product-card-img {
  width: 100%;
  height: 130px;
  overflow: hidden;
  background: var(--bg-elevated-2);
  display: flex;
  align-items: center;
  justify-content: center;
}
.product-card-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.product-card-img-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  font-size: 2rem;
  color: var(--text-muted);
}
.product-card-body {
  padding: 0.75rem;
}
.product-card-name {
  font-weight: 600;
  font-size: 0.9rem;
  line-height: 1.3;
  margin-bottom: 0.15rem;
  color: var(--text-primary);
}
.product-card-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.35rem;
}
.product-card-code {
  font-size: 0.75rem;
  color: var(--text-muted);
}
.product-card-category {
  font-size: 0.75rem;
  color: var(--text-secondary);
  margin-bottom: 0.35rem;
}
.product-card-price {
  font-weight: 600;
  font-size: 0.95rem;
  color: var(--accent-1);
  margin-bottom: 0.35rem;
}
.product-card-stock {
  margin-top: 0.25rem;
}
.product-card-actions {
  position: absolute;
  top: 0.4rem;
  right: 0.4rem;
  display: flex;
  gap: 0.2rem;
  opacity: 0;
  transition: opacity 0.2s;
}
.product-card:hover .product-card-actions {
  opacity: 1;
}

.stock-pill {
  display: inline-flex;
  align-items: center;
  padding: 0.2rem 0.55rem;
  background: rgba(37,99,235,0.12);
  color: var(--accent-1);
  border: 1px solid rgba(37,99,235,0.2);
  border-radius: var(--radius-full);
  font-size: 0.75rem;
  font-weight: 600;
}
</style>
@endpush

{{-- Toast untuk flash session --}}
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
        <p class="mt-2 mb-0">Yakin ingin menghapus produk ini?</p>
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
  let activeCategoryId = '';
  let currentPage = 1;
  let activeView = 'list';
  const perPageEl = document.getElementById('perPage');
  if (!perPageEl) { console.error('#perPage not found'); }

  const totalCount = document.getElementById('totalCount');

  // --- Toggle view ---
  const toggleBtn = document.getElementById('viewToggleBtn');
  const toggleIcon = document.getElementById('viewToggleIcon');
  toggleBtn.addEventListener('click', function() {
    activeView = activeView === 'list' ? 'card' : 'list';
    toggleIcon.className = activeView === 'card' ? 'bi bi-list-ul' : 'bi bi-grid-fill';
    this.dataset.view = activeView;
    // toggle semua panel
    document.querySelectorAll('[data-tab-panel]').forEach(function(panel) {
      const tableView = panel.querySelector('.table-view');
      const cardView = panel.querySelector('.card-view');
      if (tableView) tableView.style.display = activeView === 'list' ? '' : 'none';
      if (cardView) cardView.style.display = activeView === 'card' ? '' : 'none';
    });
    // reload active tab with current view
    reloadActiveTab();
  });

  // --- Tabs ---
  document.querySelectorAll('.tabs-modern .tab-link').forEach(function(link) {
    link.addEventListener('click', function() {
      const catId = this.dataset.categoryId || '';
      activeCategoryId = catId;
      currentPage = 1;

      document.querySelectorAll('.tabs-modern .tab-link').forEach(function(l) { l.classList.remove('active'); });
      this.classList.add('active');

      document.querySelectorAll('[data-tab-panel]').forEach(function(p) { p.style.display = 'none'; });
      const target = document.querySelector(this.dataset.tabTarget);
      if (target) {
        target.style.display = 'block';
        target.style.animation = 'none';
        setTimeout(function() { target.style.animation = 'panelFadeIn 0.25s ease'; }, 10);
      }

      // sync view visibility
      target.querySelectorAll('.table-view').forEach(function(el) { el.style.display = activeView === 'list' ? '' : 'none'; });
      target.querySelectorAll('.card-view').forEach(function(el) { el.style.display = activeView === 'card' ? '' : 'none'; });

      reloadActiveTab();
    });
  });

  function reloadActiveTab() {
    if (!activeCategoryId) {
      loadData(currentPage, perPageEl.value);
    } else {
      loadTabData(activeCategoryId, currentPage, perPageEl.value);
    }
  }

  function getViewParam() { return '&view=' + activeView; }

  // --- Skeleton ---
  function showSkeleton(count) {
    if (activeView === 'card') {
      let cards = '';
      for (let i = 0; i < count; i++) {
        cards += '<div class="product-card" style="pointer-events:none;">';
        cards += '<div class="product-card-img"><div class="skeleton" style="width:100%;height:100%;"></div></div>';
        cards += '<div class="product-card-body">';
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

  // --- Load "Semua" tab ---
  const tableBody = document.getElementById('tableBody');
  const paginationContainer = document.getElementById('paginationContainer');
  const cardGridAll = document.getElementById('tabAllCardGrid');
  const paginationContainerCard = document.getElementById('paginationContainerCard');

  function loadData(page, perPageVal) {
    currentPage = page;

    if (activeView === 'list') {
      tableBody.innerHTML = showSkeleton(parseInt(perPageVal) || 10);
      paginationContainer.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span><ul class="pagination-modern"><li class="disabled"><span>&laquo;</span></li><li class="active"><span>...</span></li><li class="disabled"><span>&raquo;</span></li></ul>';
    } else {
      cardGridAll.innerHTML = showSkeleton(parseInt(perPageVal) || 10);
      paginationContainerCard.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span><ul class="pagination-modern"><li class="disabled"><span>&laquo;</span></li><li class="active"><span>...</span></li><li class="disabled"><span>&raquo;</span></li></ul>';
      paginationContainerCard.style.display = '';
    }

    const startTime = Date.now();
    fetch('{{ route("admin.product.data") }}?page=' + page + '&per_page=' + perPageVal + getViewParam(), {
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
          cardGridAll.innerHTML = data.html;
          paginationContainerCard.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Menampilkan ' + (data.from ?? 0) + ' - ' + (data.to ?? 0) + ' dari ' + data.total + '</span>' + data.pagination;
        }
        totalCount.textContent = data.total + ' item';
        attachHandlers();
      }, delay);
    })
    .catch(function() { NexoraToast('Gagal memuat data.', 'danger'); });
  }

  // --- Load per-kategori tab ---
  function loadTabData(catId, page, perPageVal) {
    currentPage = page;
    const panel = document.querySelector('#tabCat' + catId);
    if (!panel) return;

    const isCard = activeView === 'card';
    const tbody = panel.querySelector('.tab-table-body');
    const paginationDiv = panel.querySelector('.tab-pagination-container');
    const cardGrid = document.getElementById('tabCat' + catId + 'CardGrid');
    const paginationCardDiv = panel.querySelector('.tab-pagination-container-card');

    if (isCard) {
      if (cardGrid) { cardGrid.innerHTML = showSkeleton(parseInt(perPageVal) || 10); }
      if (paginationCardDiv) { paginationCardDiv.style.display = ''; paginationCardDiv.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span><ul class="pagination-modern"><li class="disabled"><span>&laquo;</span></li><li class="active"><span>...</span></li><li class="disabled"><span>&raquo;</span></li></ul>'; }
    } else {
      if (tbody) { tbody.innerHTML = showSkeleton(parseInt(perPageVal) || 10); }
      if (paginationDiv) { paginationDiv.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span><ul class="pagination-modern"><li class="disabled"><span>&laquo;</span></li><li class="active"><span>...</span></li><li class="disabled"><span>&raquo;</span></li></ul>'; }
    }

    const startTime = Date.now();
    fetch('{{ route("admin.product.data") }}?page=' + page + '&per_page=' + perPageVal + '&category_id=' + catId + getViewParam(), {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      const delay = Math.max(400 - (Date.now() - startTime), 0);
      setTimeout(function() {
        if (isCard) {
          if (cardGrid) cardGrid.innerHTML = data.html;
          if (paginationCardDiv) paginationCardDiv.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Menampilkan ' + (data.from ?? 0) + ' - ' + (data.to ?? 0) + ' dari ' + data.total + '</span>' + data.pagination;
        } else {
          if (tbody) tbody.innerHTML = data.html;
          if (paginationDiv) paginationDiv.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Menampilkan ' + (data.from ?? 0) + ' - ' + (data.to ?? 0) + ' dari ' + data.total + '</span>' + data.pagination;
        }
        attachHandlers();
        attachTabHandlers(catId);
      }, delay);
    })
    .catch(function() { NexoraToast('Gagal memuat data.', 'danger'); });
  }

  // --- Handlers ---
  function attachHandlers() {
    // pagination Semua
    document.querySelectorAll('#paginationContainer [data-page], #paginationContainerCard [data-page]').forEach(function(link) {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        loadData(parseInt(this.dataset.page), perPageEl.value);
      });
    });

    // delete — set data-url doang, confirm handler udah di bind sekali di luar
    document.querySelectorAll('.btn-delete').forEach(function(btn) {
      btn.addEventListener('click', function() {
        document.getElementById('confirmDeleteBtn').dataset.url = this.dataset.url;
        var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
      });
    });

    // row click
    document.querySelectorAll('.row-clickable').forEach(function(row) {
      row.addEventListener('click', function(e) {
        if (e.target.closest('a') || e.target.closest('button') || e.target.closest('.btn')) return;
        window.location.href = this.dataset.url;
      });
    });

    // card click
    document.querySelectorAll('.product-card').forEach(function(card) {
      card.addEventListener('click', function(e) {
        if (e.target.closest('a') || e.target.closest('button') || e.target.closest('.btn')) return;
        window.location.href = this.dataset.url;
      });
    });
  }

  function attachTabHandlers(catId) {
    const panel = document.querySelector('#tabCat' + catId);
    if (!panel) return;

    // pagination
    panel.querySelectorAll('.tab-pagination-container [data-page], .tab-pagination-container-card [data-page]').forEach(function(link) {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        loadTabData(catId, parseInt(this.dataset.page), perPageEl.value);
      });
    });

    // delete
    panel.querySelectorAll('.btn-delete').forEach(function(btn) {
      btn.addEventListener('click', function() {
        document.getElementById('confirmDeleteBtn').dataset.url = this.dataset.url;
        var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
      });
    });

    // row click
    panel.querySelectorAll('.row-clickable').forEach(function(row) {
      row.addEventListener('click', function(e) {
        if (e.target.closest('a') || e.target.closest('button') || e.target.closest('.btn')) return;
        window.location.href = this.dataset.url;
      });
    });

    // card click
    panel.querySelectorAll('.product-card').forEach(function(card) {
      card.addEventListener('click', function(e) {
        if (e.target.closest('a') || e.target.closest('button') || e.target.closest('.btn')) return;
        window.location.href = this.dataset.url;
      });
    });
  }

  // per-page change
  perPageEl.addEventListener('change', function() {
    if (!activeCategoryId) {
      loadData(1, this.value);
    } else {
      loadTabData(activeCategoryId, 1, this.value);
    }
  });

  // confirm delete — bind sekali doang, gak di attachHandlers biar gak duplikat
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
      reloadActiveTab();
    })
    .catch(function() { NexoraToast('Gagal menghapus data.', 'danger'); });
  });

  attachHandlers();
});
</script>
@endpush
