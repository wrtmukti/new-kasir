@extends('admin.layouts.app')

@section('title', 'Paket Bundle')

@php $activeMenu = 'bundle' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Paket Bundle</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.bundle.index') }}">Paket</a>
    </div>
  </div>
  <div>
    <a href="{{ route('admin.bundle.create') }}" class="btn btn-primary-grad">
      <i class="bi bi-plus-lg me-1"></i>Tambah Paket
    </a>
  </div>
</div>

{{-- Filter Per Page --}}
<div class="d-flex align-items-center gap-2 mb-3">
  <label class="form-label-modern mb-0">Tampilkan</label>
  <select class="form-select-modern" id="perPage" style="width:auto;">
    <option value="10">10</option>
    <option value="50">50</option>
    <option value="100">100</option>
  </select>
  <span class="text-muted-c" style="font-size:0.85rem;">data</span>
</div>

<div class="card">
  <div class="card-header-flex">
    <h6><i class="bi bi-gift me-2"></i>Daftar Paket</h6>
    <span class="selected-count" id="totalCount">{{ $bundles->total() }}</span>
  </div>
  <div id="table-body">
    @include('admin.bundle._data', ['bundles' => $bundles])
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const perPage = document.getElementById('perPage');
  const tableBody = document.getElementById('table-body');

  function loadData(page, perPageVal) {
    const url = '{{ route('admin.bundle.data') }}' + '?page=' + page + '&per_page=' + perPageVal;

    // Skeleton
    const rows = Array.from({length: parseInt(perPageVal) || 3}, () => '<tr>' + Array.from({length: 5}, () => '<td><div class="skeleton skeleton-text"></div></td>').join('') + '</tr>');
    tableBody.innerHTML = '<div class="table-responsive"><table class="table-modern"><thead><tr><th>Kode</th><th>Nama</th><th>Harga</th><th>Item</th><th>Aksi</th></tr></thead><tbody>' + rows.join('') + '</tbody></table></div>';

    setTimeout(function() {
      fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(d => {
          tableBody.innerHTML = d.html;
          document.querySelector('.pagination-modern')?.parentElement?.replaceWith(d.pagination);
          document.getElementById('totalCount').textContent = d.total;
          bindDelete();
        })
        .catch(() => { location.reload(); });
    }, 400);
  }

  perPage.addEventListener('change', function() { loadData(1, this.value); });

  document.addEventListener('click', function(e) {
    const pageLink = e.target.closest('.pagination-modern a');
    if (pageLink) {
      e.preventDefault();
      const url = new URL(pageLink.href);
      loadData(url.searchParams.get('page') || 1, perPage.value);
    }
  });

  function bindDelete() {
    document.querySelectorAll('.btn-delete').forEach(function(btn) {
      btn.addEventListener('click', function() {
        const url = this.dataset.url;
        const row = this.closest('tr');
        // Bootstrap modal konfirmasi
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = '<div class="modal-dialog modal-sm modal-dialog-centered"><div class="modal-content" style="background:var(--bg-surface);border:1px solid var(--border-subtle);"><div class="modal-body text-center py-4"><i class="bi bi-exclamation-triangle text-danger" style="font-size:2rem;display:block;margin-bottom:0.75rem;"></i><p class="fw-semibold mb-0" style="color:var(--text-primary);">Hapus paket ini?</p><small class="text-muted-c">Data tidak bisa dikembalikan.</small></div><div class="modal-footer border-0 pt-0 justify-content-center"><button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-danger-grad btn-confirm-delete">Hapus</button></div></div></div>';
        document.body.appendChild(modal);
        const m = new bootstrap.Modal(modal);
        m.show();
        modal.querySelector('.btn-confirm-delete').addEventListener('click', function() {
          fetch(url, { method: 'DELETE', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(r => r.json())
            .then(d => {
              m.hide();
              modal.remove();
              loadData(1, perPage.value);
              NexoraToast(d.success || 'Paket berhasil dihapus.', 'success');
            });
        });
        modal.addEventListener('hidden.bs.modal', function() { modal.remove(); });
      });
    });
  }

  bindDelete();

  // Row click → show
  document.querySelectorAll('.row-clickable').forEach(function(row) {
    row.addEventListener('click', function(e) {
      if (e.target.closest('a') || e.target.closest('button') || e.target.closest('.btn')) return;
      window.location.href = this.dataset.url;
    });
  });
});
</script>
@endpush
