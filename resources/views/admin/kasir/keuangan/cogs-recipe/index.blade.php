@extends('admin.layouts.app')

@section('title', 'Resep & COGS Menu')

@php $activeMenu = 'cogs-recipe' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Resep Standar & COGS Menu</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Analitik</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Resep & COGS Menu</span>
    </div>
  </div>
  <a href="{{ route('admin.keuangan.cogs-recipe.create') }}" class="btn btn-primary-grad">
    <i class="bi bi-plus-lg me-1"></i>Buat Resep Standar
  </a>
</div>

<div class="card">
  <div class="card-header-flex">
    <h6>Daftar Resep Standar & Kalkulasi HPP Ideal</h6>
    <div class="d-flex align-items-center gap-2">
      <label class="form-label-modern mb-0" style="font-size:0.85rem;">Tampilkan</label>
      <select class="form-select-modern" id="perPage" style="width:auto;min-width:70px;">
        <option value="10">10</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
      <span class="text-muted-c" style="font-size:0.85rem;">data</span>
      <span class="chip-tag" id="totalCount">{{ $recipes->total() }} resep</span>
    </div>
  </div>

  <div class="px-3 pt-3 pb-2">
    <div class="input-group">
      <span class="input-group-text bg-transparent border-end-0 text-muted-c" style="border-color: var(--border-subtle);"><i class="bi bi-search"></i></span>
      <input type="text" id="searchInput" class="form-control-modern border-start-0 ps-0" placeholder="Cari nama resep atau menu terhubung...">
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive" id="tableContainer">
      <div id="tableBody">
        @include('admin.kasir.keuangan.cogs-recipe._data')
      </div>
    </div>
  </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content text-center p-3" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); color: var(--text-primary);">
      <div class="mb-3 text-danger"><i class="bi bi-exclamation-circle" style="font-size:3rem;"></i></div>
      <h6 class="fw-bold mb-2" style="color: var(--text-primary);">Konfirmasi Hapus</h6>
      <p class="text-muted-c style-sub text-wrap mb-3">Apakah Anda yakin ingin menghapus resep standar ini?</p>
      <div class="d-flex justify-content-center gap-2">
        <button type="button" class="btn btn-outline-soft px-3" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger px-3 btn-loading" id="btnConfirmDelete">Hapus</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  let deleteId = null;
  const perPageEl = document.getElementById('perPage');
  const searchInputEl = document.getElementById('searchInput');
  const tableBodyEl = document.getElementById('tableBody');
  const totalCountEl = document.getElementById('totalCount');

  function loadData(page = 1) {
    const perPage = perPageEl ? perPageEl.value : 10;
    const search = searchInputEl ? searchInputEl.value : '';
    
    tableBodyEl.innerHTML = `
      <div class="p-4 text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `;

    fetch(`{{ route('admin.keuangan.cogs-recipe.data') }}?page=${page}&per_page=${perPage}&search=${encodeURIComponent(search)}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
      tableBodyEl.innerHTML = data.html;
      if (totalCountEl) totalCountEl.textContent = (data.total || 0) + ' resep';
      attachHandlers();
    })
    .catch(() => {
      if (typeof NexoraToast !== 'undefined') {
        NexoraToast('Gagal memuat data.', 'danger');
      }
    });
  }

  function attachHandlers() {
    document.querySelectorAll('.pagination-modern a').forEach(function(link) {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const page = this.getAttribute('href').split('page=')[1];
        loadData(page);
      });
    });

    document.querySelectorAll('.btn-delete').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        deleteId = this.dataset.id;
        const modalEl = document.getElementById('deleteModal');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
      });
    });
  }

  if (perPageEl) {
    perPageEl.addEventListener('change', () => loadData(1));
  }

  let searchTimer;
  if (searchInputEl) {
    searchInputEl.addEventListener('keyup', function() {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(() => loadData(1), 300);
    });
  }

  const btnConfirmDelete = document.getElementById('btnConfirmDelete');
  if (btnConfirmDelete) {
    btnConfirmDelete.addEventListener('click', function() {
      if (!deleteId) return;
      const btn = this;
      btn.classList.add('disabled');

      fetch(`{{ url('admin/keuangan/cogs-recipe') }}/${deleteId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(res => {
        const modalEl = document.getElementById('deleteModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        btn.classList.remove('disabled');

        if (typeof NexoraToast !== 'undefined') {
          NexoraToast(res.message, 'success');
        } else {
          alert(res.message);
        }
        loadData(1);
      })
      .catch(err => {
        btn.classList.remove('disabled');
        alert('Gagal menghapus resep.');
      });
    });
  }

  attachHandlers();
});
</script>
@endpush
