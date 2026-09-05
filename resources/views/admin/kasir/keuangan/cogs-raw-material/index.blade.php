@extends('admin.layouts.app')

@section('title', 'Bahan Mentah & Stock Opname')

@php $activeMenu = 'cogs-raw-material' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Bahan Mentah & Stock Opname</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Keuangan</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Bahan Mentah</span>
    </div>
  </div>
  <a href="{{ route('admin.keuangan.cogs-raw-material.create') }}" class="btn btn-primary-grad">
    <i class="bi bi-plus-lg me-1"></i>Tambah Bahan Mentah
  </a>
</div>

<div class="card">
  <div class="card-header-flex">
    <h6>Daftar Bahan Mentah & Stock Opname</h6>
    <div class="d-flex align-items-center gap-2">
      <label class="form-label-modern mb-0" style="font-size:0.85rem;">Tampilkan</label>
      <select class="form-select-modern" id="perPage" style="width:auto;min-width:70px;">
        <option value="10">10</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
      <span class="text-muted-c" style="font-size:0.85rem;">data</span>
      <span class="chip-tag" id="totalCount">{{ $rawMaterials->total() }} item</span>
    </div>
  </div>

  <div class="px-3 pt-3 pb-2">
    <div class="input-group">
      <span class="input-group-text bg-transparent border-end-0 text-muted-c" style="border-color: var(--border-subtle);"><i class="bi bi-search"></i></span>
      <input type="text" id="searchInput" class="form-control-modern border-start-0 ps-0" placeholder="Cari nama bahan mentah, kode, atau unit...">
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive" id="tableContainer">
      <div id="tableBody">
        @include('admin.kasir.keuangan.cogs-raw-material._data')
      </div>
    </div>
  </div>
</div>

<!-- Modal Stock Opname -->
<div class="modal fade" id="opnameModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); color: var(--text-primary);">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-bold" style="color: var(--text-primary);"><i class="bi bi-clipboard-check me-2 text-warning"></i>Form Stock Opname / Penyesuaian Stok</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="opnameForm">
        @csrf
        <input type="hidden" id="opnameId">
        <div class="modal-body pt-3">
          <div class="mb-3">
            <label class="form-label-modern">Nama Bahan Mentah</label>
            <input type="text" id="opnameName" class="form-control-modern" style="background: var(--bg-elevated); border-color: var(--border-subtle); color: var(--text-primary);" readonly>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label-modern">Stok Sistem Saat Ini</label>
              <div class="input-group">
                <input type="text" id="opnameCurrentAmount" class="form-control-modern" style="background: var(--bg-elevated); border-color: var(--border-subtle); color: var(--text-primary);" readonly>
                <span class="input-group-text bg-transparent opnameUnitLabel" style="border-color: var(--border-subtle); color: var(--text-muted); font-size:0.8rem;">-</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="physical_amount" class="form-label-modern">Hasil Stok Fisik (Hitung Opname) <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" step="0.0001" name="physical_amount" id="physical_amount" class="form-control-modern" placeholder="0" required>
                <span class="input-group-text bg-transparent opnameUnitLabel" style="border-color: var(--border-subtle); color: var(--text-muted); font-size:0.8rem;">-</span>
              </div>
            </div>
          </div>
          <div class="mb-2">
            <label for="opnameReason" class="form-label-modern">Alasan Penyesuaian Opname <span class="text-danger">*</span></label>
            <select name="reason" id="opnameReason" class="form-select-modern" required>
              <option value="Selisih Opname Fisik Rutin">Selisih Opname Fisik Rutin</option>
              <option value="Bahan Rusak / Hilang">Bahan Rusak / Hilang</option>
              <option value="Koreksi Lupa Catat Transaksi">Koreksi Lupa Catat Transaksi</option>
              <option value="Penyesuaian Stok Awal">Penyesuaian Stok Awal</option>
            </select>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-soft px-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-grad px-4 btn-loading" id="btnSubmitOpname">Simpan Hasil Opname</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content text-center p-3" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); color: var(--text-primary);">
      <div class="mb-3 text-danger"><i class="bi bi-exclamation-circle" style="font-size:3rem;"></i></div>
      <h6 class="fw-bold mb-2" style="color: var(--text-primary);">Konfirmasi Hapus</h6>
      <p class="text-muted-c style-sub text-wrap mb-3">Apakah Anda yakin ingin menghapus bahan mentah ini?</p>
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

    fetch(`{{ route('admin.keuangan.cogs-raw-material.data') }}?page=${page}&per_page=${perPage}&search=${encodeURIComponent(search)}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
      tableBodyEl.innerHTML = data.html;
      if (totalCountEl) totalCountEl.textContent = (data.total || 0) + ' item';
      attachHandlers();
    })
    .catch(() => {
      if (typeof NexoraToast !== 'undefined') {
        NexoraToast('Gagal memuat data.', 'danger');
      }
    });
  }

  function attachHandlers() {
    // Pagination clicks
    document.querySelectorAll('.pagination-modern a').forEach(function(link) {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const page = this.getAttribute('href').split('page=')[1];
        loadData(page);
      });
    });

    // Opname triggers
    document.querySelectorAll('.btn-opname').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('opnameId').value = this.dataset.id;
        document.getElementById('opnameName').value = this.dataset.name;
        document.getElementById('opnameCurrentAmount').value = this.dataset.amount;
        document.getElementById('physical_amount').value = this.dataset.amount;
        document.querySelectorAll('.opnameUnitLabel').forEach(el => el.textContent = this.dataset.unit);

        const modalEl = document.getElementById('opnameModal');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
      });
    });

    // Delete triggers
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

  // Submit Opname
  const opnameForm = document.getElementById('opnameForm');
  if (opnameForm) {
    opnameForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const id = document.getElementById('opnameId').value;
      const btn = document.getElementById('btnSubmitOpname');
      if (btn) btn.classList.add('disabled');

      const formData = new FormData(this);

      fetch(`{{ url('admin/keuangan/cogs-raw-material') }}/${id}/opname`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        const modalEl = document.getElementById('opnameModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        if (btn) btn.classList.remove('disabled');

        if (typeof NexoraToast !== 'undefined') {
          NexoraToast(res.message, 'success');
        } else {
          alert(res.message);
        }
        loadData(1);
      })
      .catch(err => {
        if (btn) btn.classList.remove('disabled');
        alert('Gagal menyimpan hasil opname.');
      });
    });
  }

  // Submit Delete
  const btnConfirmDelete = document.getElementById('btnConfirmDelete');
  if (btnConfirmDelete) {
    btnConfirmDelete.addEventListener('click', function() {
      if (!deleteId) return;
      const btn = this;
      btn.classList.add('disabled');

      fetch(`{{ url('admin/keuangan/cogs-raw-material') }}/${deleteId}`, {
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
        alert('Gagal menghapus bahan mentah.');
      });
    });
  }

  attachHandlers();
});
</script>
@endpush
