@extends('admin.layouts.app')

@section('title', 'Pesan')

@php $activeMenu = 'order' @endphp
@php
  use App\Models\Admin\Category;
  $categories = Category::where('delete_status', 0)->where('category_status', 1)->get();
@endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Pesan</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Pesan</span>
    </div>
  </div>
  <button class="btn btn-primary-grad position-relative" data-bs-toggle="modal" data-bs-target="#cartModal">
    <i class="bi bi-cart-fill me-1"></i>Keranjang
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartBadge" style="font-size:0.65rem;display:none;">0</span>
  </button>
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
      <div class="table-view">
        <div class="table-responsive" id="tableContainer">
          <table class="table-modern" id="dataTable">
            <thead>
              <tr>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="tableBody">
              @include('admin.order._data', ['products' => $products])
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
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody class="tab-table-body" data-category-id="{{ $cat->category_id }}">
              <tr><td colspan="5" class="text-center text-muted-c py-4">Pilih tab untuk memuat data.</td></tr>
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

{{-- MODAL KERANJANG --}}
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="mb-0"><i class="bi bi-cart-fill me-2"></i>Keranjang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <div class="text-center text-muted-c py-5" id="cartEmpty">
          <i class="bi bi-cart" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;opacity:0.4;"></i>
          Keranjang kosong
        </div>
        <table class="table-modern" id="cartTable" style="display:none;">
          <thead>
            <tr>
              <th>Produk</th>
              <th style="width:100px;">Harga</th>
              <th style="width:120px;">Qty</th>
              <th style="width:120px;">Subtotal</th>
              <th style="width:40px;"></th>
            </tr>
          </thead>
          <tbody id="cartTableBody"></tbody>
          <tfoot id="cartTableFoot" style="display:none;">
            <tr>
              <td colspan="3" class="text-end fw-bold">Total</td>
              <td class="text-mono fw-bold" id="cartTotalDisplay">Rp 0</td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-soft" data-bs-dismiss="modal">Lanjut</button>
        <button type="button" class="btn btn-primary-grad" id="checkoutBtn" disabled>
          <i class="bi bi-check-lg me-1"></i>Buat Pesanan
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.product-card-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:1rem; padding:1.25rem; }
.product-card { background:var(--bg-elevated); border:1px solid var(--border-subtle); border-radius:var(--radius-md); overflow:hidden; transition:border-color 0.2s,transform 0.15s; position:relative; }
.product-card:hover { border-color:var(--accent-1); transform:translateY(-2px); }
.product-card-img { width:100%; height:130px; overflow:hidden; background:var(--bg-elevated-2); display:flex; align-items:center; justify-content:center; position:relative; }
.product-card-img img { width:100%; height:100%; object-fit:cover; }
.product-card-img-placeholder { display:flex; align-items:center; justify-content:center; width:100%; height:100%; font-size:2rem; color:var(--text-muted); }
.btn-add-cart-card { position:absolute; bottom:0.5rem; right:0.5rem; width:36px; height:36px; border-radius:50%; border:none; background:var(--accent-1); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1rem; opacity:0; transition:opacity 0.2s; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.25); }
.product-card:hover .btn-add-cart-card { opacity:1; }
.product-card-body { padding:0.75rem; }
.product-card-name { font-weight:600; font-size:0.9rem; color:var(--text-primary); }
.product-card-code { font-size:0.75rem; color:var(--text-muted); }
.product-card-category { font-size:0.75rem; color:var(--text-secondary); }
.product-card-price { font-weight:600; font-size:0.95rem; color:var(--accent-1); }
.cart-qty-input { width:55px; text-align:center; border:1px solid var(--border-subtle); background:var(--bg-input); color:var(--text-primary); border-radius:var(--radius-sm); padding:0.2rem 0.3rem; }
.cart-qty-input::-webkit-inner-spin-button, .cart-qty-input::-webkit-outer-spin-button { -webkit-appearance:none; margin:0; }
.cart-qty-input[type="number"] { -moz-appearance:textfield; }
.btn-qty { width:28px; height:28px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:var(--radius-sm); border:1px solid var(--border-subtle); background:var(--bg-elevated-2); color:var(--text-primary); cursor:pointer; }
.btn-qty:hover { background:var(--bg-elevated-3); }
.cart-item-img { width:40px; height:40px; object-fit:cover; border-radius:var(--radius-sm); flex-shrink:0; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

  // ===== CART =====
  let cart = [];
  function count() { return cart.reduce((s,i) => s+i.qty, 0); }
  function total() { return cart.reduce((s,i) => s+i.price*i.qty, 0); }
  function fmt(n) { return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.'); }

  function updateCart() {
    const badge = document.getElementById('cartBadge');
    const c = count();
    if (c > 0) { badge.textContent = c; badge.style.display = ''; } else { badge.style.display = 'none'; }
  }

  function renderCart() {
    const empty = document.getElementById('cartEmpty');
    const table = document.getElementById('cartTable');
    const tbody = document.getElementById('cartTableBody');
    const foot = document.getElementById('cartTableFoot');
    const totalD = document.getElementById('cartTotalDisplay');
    const checkoutBtn = document.getElementById('checkoutBtn');

    if (!cart.length) {
      empty.style.display = ''; table.style.display = 'none'; foot.style.display = 'none'; checkoutBtn.disabled = true;
      return;
    }
    empty.style.display = 'none'; table.style.display = ''; foot.style.display = ''; checkoutBtn.disabled = false;

    let html = '';
    cart.forEach((item, idx) => {
      const img = item.image
        ? '<img src="'+item.image+'" class="cart-item-img">'
        : '<span class="cart-item-img" style="background:var(--bg-elevated-2);display:inline-flex;align-items:center;justify-content:center;"><i class="bi bi-image" style="color:var(--text-muted);"></i></span>';
      html += '<tr>';
      html += '<td><div class="d-flex align-items-center gap-2">'+img+'<div><div style="font-weight:500;">'+item.name+'</div></div></div></td>';
      html += '<td class="text-mono">Rp '+fmt(item.price)+'</td>';
      html += '<td><div class="d-flex align-items-center gap-1"><button class="btn-qty dec" data-i="'+idx+'">−</button>';
      html += '<input type="number" class="cart-qty-input qty-input" value="'+item.qty+'" min="1" data-i="'+idx+'">';
      html += '<button class="btn-qty inc" data-i="'+idx+'">+</button></div></td>';
      html += '<td class="text-mono">Rp '+fmt(item.price*item.qty)+'</td>';
      html += '<td><button class="btn btn-ghost btn-icon-sq btn-sm text-danger rm" data-i="'+idx+'"><i class="bi bi-x-lg"></i></button></td>';
      html += '</tr>';
    });
    tbody.innerHTML = html;
    totalD.textContent = 'Rp '+fmt(total());
  }

  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-add-cart, .btn-add-cart-card');
    if (!btn) return;
    const id = btn.dataset.id;
    const idx = cart.findIndex(i => i.id === id);
    if (idx > -1) cart[idx].qty++;
    else cart.push({ id, name: btn.dataset.name, price: parseFloat(btn.dataset.price)||0, image: btn.dataset.image||'', qty: 1 });
    updateCart(); renderCart();
    NexoraToast(btn.dataset.name+' ditambahkan', 'success');
  });

  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.dec'); if (!btn) return;
    const i = parseInt(btn.dataset.i);
    if (cart[i].qty > 1) cart[i].qty--; else cart.splice(i,1);
    updateCart(); renderCart();
  });
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.inc'); if (!btn) return;
    cart[parseInt(btn.dataset.i)].qty++;
    updateCart(); renderCart();
  });
  document.addEventListener('change', function(e) {
    const inp = e.target.closest('.qty-input'); if (!inp) return;
    const i = parseInt(inp.dataset.i);
    cart[i].qty = Math.max(1, parseInt(inp.value)||1);
    updateCart(); renderCart();
  });
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.rm'); if (!btn) return;
    cart.splice(parseInt(btn.dataset.i),1);
    updateCart(); renderCart();
  });

  // ===== Checkout: cart → halaman create order =====
  document.getElementById('checkoutBtn').addEventListener('click', function() {
    if (!cart.length) return;
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';
    fetch('{{ route("admin.order.store-cart") }}', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' },
      body: JSON.stringify({ cart: cart, _token: '{{ csrf_token() }}' })
    })
    .then(r => r.json())
    .then(d => {
      if (d.ok) {
        // Tutup modal
        var modal = bootstrap.Modal.getInstance(document.getElementById('cartModal'));
        if (modal) modal.hide();
        window.location.href = '{{ route("admin.order.create") }}';
      } else {
        NexoraToast('Gagal menyimpan keranjang.', 'danger');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Buat Pesanan';
      }
    })
    .catch(() => {
      NexoraToast('Gagal menyimpan keranjang.', 'danger');
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Buat Pesanan';
    });
  });

  // ===== AJAX (copy persis dari product index) =====
  let activeCategoryId = '';
  let currentPage = 1;
  let activeView = 'list';
  const perPageEl = document.getElementById('perPage');
  const totalCount = document.getElementById('totalCount');

  const toggleBtn = document.getElementById('viewToggleBtn');
  const toggleIcon = document.getElementById('viewToggleIcon');
  toggleBtn.addEventListener('click', function() {
    activeView = activeView === 'list' ? 'card' : 'list';
    toggleIcon.className = activeView === 'card' ? 'bi bi-list-ul' : 'bi bi-grid-fill';
    this.dataset.view = activeView;
    document.querySelectorAll('[data-tab-panel]').forEach(p => {
      if (p.querySelector('.table-view')) p.querySelector('.table-view').style.display = activeView === 'list' ? '' : 'none';
      if (p.querySelector('.card-view')) p.querySelector('.card-view').style.display = activeView === 'card' ? '' : 'none';
    });
    reloadActiveTab();
  });

  document.querySelectorAll('.tabs-modern .tab-link').forEach(function(link) {
    link.addEventListener('click', function() {
      activeCategoryId = this.dataset.categoryId || '';
      currentPage = 1;
      document.querySelectorAll('.tabs-modern .tab-link').forEach(l => l.classList.remove('active'));
      this.classList.add('active');
      document.querySelectorAll('[data-tab-panel]').forEach(p => p.style.display = 'none');
      const target = document.querySelector(this.dataset.tabTarget);
      if (target) { target.style.display = 'block'; target.style.animation = 'none'; setTimeout(() => target.style.animation = 'panelFadeIn 0.25s ease', 10); }
      target.querySelectorAll('.table-view').forEach(el => el.style.display = activeView === 'list' ? '' : 'none');
      target.querySelectorAll('.card-view').forEach(el => el.style.display = activeView === 'card' ? '' : 'none');
      reloadActiveTab();
    });
  });

  function reloadActiveTab() {
    if (!activeCategoryId) loadData(currentPage, perPageEl.value);
    else loadTabData(activeCategoryId, currentPage, perPageEl.value);
  }

  function showSkeleton(count) {
    if (activeView === 'card') {
      let c = '';
      for (let i = 0; i < count; i++) c += '<div class="product-card" style="pointer-events:none;"><div class="product-card-img"><div class="skeleton" style="width:100%;height:100%;"></div></div><div class="product-card-body"><div class="skeleton skeleton-text mb-2"></div><div class="skeleton skeleton-text" style="width:60%;"></div></div></div>';
      return c;
    }
    let r = '';
    for (let i = 0; i < count; i++) { r += '<tr>'; for (let j = 0; j < 5; j++) r += '<td><div class="skeleton skeleton-text"></div></td>'; r += '</tr>'; }
    return r;
  }

  const tableBody = document.getElementById('tableBody');
  const paginationContainer = document.getElementById('paginationContainer');
  const cardGridAll = document.getElementById('tabAllCardGrid');
  const paginationContainerCard = document.getElementById('paginationContainerCard');

  function loadData(page, perPageVal) {
    currentPage = page;
    if (activeView === 'list') {
      tableBody.innerHTML = showSkeleton(parseInt(perPageVal)||10);
      paginationContainer.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span><ul class="pagination-modern"><li class="disabled"><span>«</span></li><li class="active"><span>...</span></li><li class="disabled"><span>»</span></li></ul>';
    } else {
      cardGridAll.innerHTML = showSkeleton(parseInt(perPageVal)||10);
      paginationContainerCard.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span><ul class="pagination-modern"><li class="disabled"><span>«</span></li><li class="active"><span>...</span></li><li class="disabled"><span>»</span></li></ul>';
      paginationContainerCard.style.display = '';
    }
    const start = Date.now();
    fetch('{{ route("admin.order.data") }}?page=' + page + '&per_page=' + perPageVal + '&view=' + activeView, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(d => {
      setTimeout(() => {
        if (activeView === 'list') { tableBody.innerHTML = d.html; paginationContainer.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Menampilkan '+(d.from??0)+' - '+(d.to??0)+' dari '+d.total+'</span>'+d.pagination; }
        else { cardGridAll.innerHTML = d.html; paginationContainerCard.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Menampilkan '+(d.from??0)+' - '+(d.to??0)+' dari '+d.total+'</span>'+d.pagination; }
        totalCount.textContent = d.total + ' item';
        attachHandlers();
      }, Math.max(400-(Date.now()-start),0));
    })
    .catch(() => NexoraToast('Gagal memuat data.', 'danger'));
  }

  function loadTabData(catId, page, perPageVal) {
    currentPage = page;
    const panel = document.querySelector('#tabCat'+catId);
    if (!panel) return;
    const isCard = activeView === 'card';
    const tbody = panel.querySelector('.tab-table-body');
    const pd = panel.querySelector('.tab-pagination-container');
    const cg = document.getElementById('tabCat'+catId+'CardGrid');
    const pcd = panel.querySelector('.tab-pagination-container-card');
    if (isCard) { if(cg) cg.innerHTML = showSkeleton(parseInt(perPageVal)||10); if(pcd) { pcd.style.display = ''; pcd.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span><ul class="pagination-modern"><li class="disabled"><span>«</span></li><li class="active"><span>...</span></li><li class="disabled"><span>»</span></li></ul>'; } }
    else { if(tbody) tbody.innerHTML = showSkeleton(parseInt(perPageVal)||10); if(pd) pd.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Memuat...</span><ul class="pagination-modern"><li class="disabled"><span>«</span></li><li class="active"><span>...</span></li><li class="disabled"><span>»</span></li></ul>'; }
    const start = Date.now();
    fetch('{{ route("admin.order.data") }}?page='+page+'&per_page='+perPageVal+'&category_id='+catId+'&view='+activeView, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(d => {
      setTimeout(() => {
        if (isCard && cg) { cg.innerHTML = d.html; if(pcd) pcd.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Menampilkan '+(d.from??0)+' - '+(d.to??0)+' dari '+d.total+'</span>'+d.pagination; }
        else { if(tbody) tbody.innerHTML = d.html; if(pd) pd.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Menampilkan '+(d.from??0)+' - '+(d.to??0)+' dari '+d.total+'</span>'+d.pagination; }
        attachHandlers();
        attachTabHandlers(catId);
      }, Math.max(400-(Date.now()-start),0));
    })
    .catch(() => NexoraToast('Gagal memuat data.', 'danger'));
  }

  function attachHandlers() {
    document.querySelectorAll('#paginationContainer [data-page], #paginationContainerCard [data-page]').forEach(l => {
      l.addEventListener('click', function(e) { e.preventDefault(); loadData(parseInt(this.dataset.page), perPageEl.value); });
    });
  }

  function attachTabHandlers(catId) {
    const panel = document.querySelector('#tabCat'+catId);
    if (!panel) return;
    panel.querySelectorAll('.tab-pagination-container [data-page], .tab-pagination-container-card [data-page]').forEach(l => {
      l.addEventListener('click', function(e) { e.preventDefault(); loadTabData(catId, parseInt(this.dataset.page), perPageEl.value); });
    });
  }

  perPageEl.addEventListener('change', function() {
    if (!activeCategoryId) loadData(1, this.value);
    else loadTabData(activeCategoryId, 1, this.value);
  });

  attachHandlers();
});
</script>
@endpush
