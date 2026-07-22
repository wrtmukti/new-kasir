@extends('admin.layouts.app')

@section('title', 'Transaksi')

@php $activeMenu = 'transaction' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Transaksi</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Transaksi</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex">
    <h6>Riwayat Transaksi</h6>
    <div class="d-flex align-items-center gap-2">
      <label class="form-label-modern mb-0" style="font-size:0.85rem;">Tampilkan</label>
      <select class="form-select-modern" id="perPage" style="width:auto;min-width:70px;">
        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
      </select>
      <span class="text-muted-c" style="font-size:0.85rem;">data</span>
      <span class="chip-tag" id="totalCount">{{ $transactions->total() }} item</span>
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Tanggal</th>
            <th>Subtotal</th>
            <th>Total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          @include('admin.transaction._data', ['transactions' => $transactions])
        </tbody>
      </table>
    </div>
    <div class="px-3 py-2 d-flex justify-content-between align-items-center" id="paginationContainer">
      <span class="text-muted-c" style="font-size:0.85rem;" id="pageInfo">
        Menampilkan {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }}
      </span>
      {{ $transactions->onEachSide(1)->links('vendor.pagination.modern') }}
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

  function skeleton(c) { let r=''; for(let i=0;i<c;i++){r+='<tr>';for(let j=0;j<5;j++)r+='<td><div class="skeleton skeleton-text"></div></td>';r+='</tr>';} return r; }

  function loadData(page, pp) {
    currentPage = page;
    tableBody.innerHTML = skeleton(parseInt(pp)||10);
    paginationContainer.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span><ul class="pagination-modern"><li class="disabled"><span>«</span></li><li class="active"><span>...</span></li><li class="disabled"><span>»</span></li></ul>';
    const t = Date.now();
    fetch('{{ route("admin.transaction.data") }}?page='+page+'&per_page='+pp, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r=>r.json()).then(d=>{
      setTimeout(()=>{
        tableBody.innerHTML = d.html;
        paginationContainer.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Menampilkan '+(d.from??0)+' - '+(d.to??0)+' dari '+d.total+'</span>'+d.pagination;
        document.getElementById('totalCount').textContent = d.total+' item';
        document.querySelectorAll('#paginationContainer [data-page]').forEach(l=>{l.addEventListener('click',function(e){e.preventDefault();loadData(parseInt(this.dataset.page),perPageEl.value);});});
        document.querySelectorAll('.row-clickable').forEach(function(row){row.addEventListener('click',function(e){if(e.target.closest('a')||e.target.closest('button')||e.target.closest('.btn'))return;window.location.href=this.dataset.url;});});
      },Math.max(400-(Date.now()-t),0));
    }).catch(()=>NexoraToast('Gagal memuat data.','danger'));
  }

  perPageEl.addEventListener('change', function() { loadData(1, this.value); });
  document.querySelectorAll('#paginationContainer [data-page]').forEach(l=>{l.addEventListener('click',function(e){e.preventDefault();loadData(parseInt(this.dataset.page),perPageEl.value);});});
});
</script>
@endpush
