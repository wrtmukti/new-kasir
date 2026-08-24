@extends('guest.standard.layouts.app')

@section('title', 'Menu')

@section('content')
{{-- Alert dari session --}}
@if(session('success'))
<div class="guest-alert guest-alert-success" id="guestFlashSuccess">
  <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="guest-alert guest-alert-danger" id="guestFlashError">
  <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif

{{-- Hero --}}
<div class="guest-hero">
  <div class="guest-hero-overlay">
    <h1>{{ $outlet->outlet_name ?? 'Selamat Datang' }}</h1>
    <p>Scan & Pesan — pilih menu favoritmu</p>
  </div>
</div>

{{-- Status toko + cek status pesanan --}}
<div class="guest-store-row">
  <span class="guest-open-status"><i class="bi bi-circle-fill"></i> Buka 10:00 - 22:00 WIB</span>
  <a href="{{ route('guest.status', $table->table_id) }}" class="guest-status-link">
    Cek Status Pesanan <i class="bi bi-arrow-right"></i>
  </a>
</div>

{{-- Search --}}
<div class="guest-search-wrap sticky-guest-top">
  <div class="guest-search">
    <i class="bi bi-search"></i>
    <input type="text" id="guestSearch" placeholder="Cari menu...">
  </div>
</div>

{{-- Kategori --}}
<div class="guest-cats-wrap sticky-guest-top">
  <button class="guest-cat-btn active" data-cat="all">Semua</button>
  @if($bundles->isNotEmpty())
    <button class="guest-cat-btn" data-cat="bundle">Bundle</button>
  @endif
  @foreach($categories as $cat)
    <button class="guest-cat-btn" data-cat="{{ $cat->category_id }}">{{ $cat->category_name }}</button>
  @endforeach
</div>

{{-- Header produk + toggle view --}}
<div class="guest-products-head">
  <span class="guest-products-title">Menu</span>
  <button type="button" class="guest-view-toggle" id="guestViewToggle" title="Tampilan daftar">
    <i class="bi bi-list-ul"></i>
  </button>
</div>

{{-- Bundle --}}
<div class="guest-products" id="guestBundles" style="display:none;">
  @foreach($bundles as $bundle)
    @include('guest.standard.partials._bundle_card', ['bundle' => $bundle])
  @endforeach
</div>

{{-- Produk --}}
<div class="guest-products" id="guestProducts">
  @foreach($products as $product)
    @include('guest.standard.partials._product_card', ['product' => $product])
  @endforeach
</div>

@if($products->isEmpty() && $bundles->isEmpty())
<div class="guest-empty">
  <i class="bi bi-cup-straw"></i>
  <p>Belum ada menu tersedia.</p>
</div>
@endif
@endsection

@section('floating')
{{-- Cart bar (muncul dari bawah saat ada item) --}}
<button class="guest-cart-bar" id="guestCartBar" type="button">
  <span class="guest-cart-bar-icon"><i class="bi bi-bag"></i></span>
  <span class="guest-cart-bar-info" id="guestCartInfo">0 item • Rp 0</span>
  <span class="guest-cart-bar-cta">Cek Keranjang <i class="bi bi-chevron-up"></i></span>
</button>

{{-- Bottom sheet keranjang --}}
<div class="guest-cart-sheet" id="guestCartSheet">
  <div class="guest-cart-sheet-handle"></div>
  <div class="guest-cart-sheet-header">
    <h6 class="mb-0"><i class="bi bi-bag me-2"></i>Keranjang Saya</h6>
    <button type="button" class="guest-cart-sheet-close" id="guestCartSheetClose"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="guest-cart-sheet-body" id="guestCartItems"></div>
  <div class="guest-cart-sheet-footer">
    <div class="guest-cart-total-row">
      <span>Total</span>
      <span class="guest-cart-total" id="guestCartTotal">Rp 0</span>
    </div>
    <button type="button" class="btn btn-primary-guest w-100 btn-loading" id="guestCheckoutBtn">
      <i class="bi bi-arrow-right me-1"></i>Lanjut ke Pembayaran
    </button>
  </div>
</div>
<div class="guest-cart-sheet-backdrop" id="guestCartBackdrop"></div>

{{-- Modal tambah item --}}
<div class="modal fade" id="guestItemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="mb-0" id="guestItemModalName"></h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <span class="guest-price-disc" id="guestItemModalPrice"></span>
          <div class="guest-qty-stepper">
            <button type="button" class="guest-qty-btn" id="guestQtyMinus"><i class="bi bi-dash"></i></button>
            <input type="number" id="guestQtyInput" value="1" min="1" readonly>
            <button type="button" class="guest-qty-btn" id="guestQtyPlus"><i class="bi bi-plus"></i></button>
          </div>
        </div>
        <label class="form-label-modern-guest">Catatan</label>
        <input type="text" id="guestItemNote" class="form-control-guest" placeholder="cth: tidak pedas, ekstra saus">
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-primary-guest w-100 btn-loading" id="guestAddToCartBtn">
          <i class="bi bi-bag-plus me-1"></i>Tambahkan
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // ——— State ———
  let cart = {}; // { product_id: {name, price, image, qty, note, discount_amount, final_price} }

  const productsData = {
    @foreach($products as $p)
    "{{ $p->product_id }}": {
      name: @json($p->product_name),
      price: {{ (float) $p->product_price }},
      image: @json($p->product_image ? asset('storage/' . $p->product_image) : ''),
      discount_type: @json($p->activeDiscount->first()?->discount_type ?? ''),
      discount_value: {{ (float) ($p->activeDiscount->first()?->discount_value ?? 0) }},
    },
    @endforeach
  };

  // ——— Kategori filter ———
  const catBtns = document.querySelectorAll('.guest-cat-btn');
  const bundlesWrap = document.getElementById('guestBundles');
  catBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      catBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      const cat = this.dataset.cat;
      // Tab bundle: tampil/hilang #guestBundles, sembunyikan produk
      if (bundlesWrap) {
        bundlesWrap.style.display = (cat === 'all' || cat === 'bundle') ? '' : 'none';
      }
      document.querySelectorAll('#guestProducts .guest-product-card').forEach(card => {
        const show = (cat === 'all' || card.dataset.cat === cat);
        // di tab bundle, sembunyikan semua produk (kecuali 'all')
        card.style.display = (cat === 'bundle') ? 'none' : (show ? '' : 'none');
      });
    });
  });

  // ——— Toggle view list/card ———
  const productsWrap = document.getElementById('guestProducts');
  const viewToggle = document.getElementById('guestViewToggle');
  function setView(list) {
    const wrap = productsWrap;
    const btn = viewToggle;
    if (list) {
      wrap.classList.add('guest-list-view');
      btn.classList.add('active');
      btn.title = 'Tampilan kartu';
      btn.innerHTML = '<i class="bi bi-grid-3x3-gap"></i>';
    } else {
      wrap.classList.remove('guest-list-view');
      btn.classList.remove('active');
      btn.title = 'Tampilan daftar';
      btn.innerHTML = '<i class="bi bi-list-ul"></i>';
    }
  }
  const savedView = sessionStorage.getItem('guest_view') === 'list';
  setView(savedView);
  viewToggle.addEventListener('click', function() {
    const list = !productsWrap.classList.contains('guest-list-view');
    setView(list);
    sessionStorage.setItem('guest_view', list ? 'list' : 'card');
  });

  // ——— Search ———
  document.getElementById('guestSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#guestProducts .guest-product-card').forEach(card => {
      const name = (card.dataset.name || '').toLowerCase();
      card.style.display = name.includes(q) ? '' : 'none';
    });
  });

  // ——— Hitung diskon produk ———
  function calcDiscount(price, type, value) {
    if (type === 'percentage' && value > 0) return Math.min(Math.round(price * value / 100), price);
    if (type === 'nominal' && value > 0) return Math.min(value, price);
    return 0;
  }

  // ——— Render cart ———
  function fmtRp(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); }

  function renderCart() {
    const wrap = document.getElementById('guestCartItems');
    const bar = document.getElementById('guestCartBar');
    const barInfo = document.getElementById('guestCartInfo');
    const totalEl = document.getElementById('guestCartTotal');
    const ids = Object.keys(cart);

    let total = 0, count = 0;
    if (ids.length === 0) {
      wrap.innerHTML = '<div class="guest-cart-empty"><i class="bi bi-bag"></i><p>Keranjang masih kosong.</p></div>';
      bar.classList.remove('show');
      hideSheet();
    } else {
      wrap.innerHTML = '';
      ids.forEach(id => {
        const it = cart[id];
        const sub = it.final_price * it.qty;
        total += sub;
        count += it.qty;
        const row = document.createElement('div');
        row.className = 'guest-cart-item';
        const isBundle = it.type === 'bundle';
        const bundleChips = isBundle && it.items && it.items.length
          ? '<div class="guest-cart-bundle-chips">' + it.items.map(i =>
              '<span class="guest-cart-note">' + (i.product_name || 'Produk') + ' x' + i.quantity + '</span>'
            ).join('') + '</div>'
          : '';
        row.innerHTML = `
          <img src="${it.image || (isBundle ? '' : 'https://via.placeholder.com/60x60?text=Menu')}" alt="">
          <div class="flex-grow-1">
            <div class="fw-semibold">${isBundle ? '<span class="guest-bundle-tag">Paket</span> ' : ''}${it.name}</div>
            ${bundleChips}
            ${it.discount_amount > 0 ? '<div class="guest-cart-disc">diskon -' + fmtRp(it.discount_amount) + '</div>' : ''}
            <div class="guest-cart-subtotal">${fmtRp(sub)}</div>
            ${it.note ? '<div class="guest-cart-note">' + it.note + '</div>' : ''}
            <div class="guest-cart-stepper">
              <button class="guest-qty-btn" data-id="${id}" data-act="minus"><i class="bi bi-dash"></i></button>
              <span>${it.qty}</span>
              <button class="guest-qty-btn" data-id="${id}" data-act="plus"><i class="bi bi-plus"></i></button>
            </div>
          </div>
          <button class="guest-cart-remove" data-id="${id}"><i class="bi bi-x-lg"></i></button>
        `;
        wrap.appendChild(row);
      });
      barInfo.textContent = count + ' item • ' + fmtRp(total);
      bar.classList.add('show');
    }
    totalEl.textContent = fmtRp(total);
  }

  // ——— Bottom sheet buka/tutup ———
  const sheetEl = document.getElementById('guestCartSheet');
  const backdropEl = document.getElementById('guestCartBackdrop');
  function showSheet() { sheetEl.classList.add('show'); backdropEl.classList.add('show'); }
  function hideSheet() { sheetEl.classList.remove('show'); backdropEl.classList.remove('show'); }

  function saveCart() {
    sessionStorage.setItem('guest_cart', JSON.stringify(cart));
    renderCart();
  }

  // ——— Tambah dari modal ———
  let currentProductId = null;

  document.getElementById('guestQtyPlus').addEventListener('click', () => {
    document.getElementById('guestQtyInput').value = +document.getElementById('guestQtyInput').value + 1;
  });
  document.getElementById('guestQtyMinus').addEventListener('click', () => {
    const el = document.getElementById('guestQtyInput');
    if (+el.value > 1) el.value = +el.value - 1;
  });

  document.getElementById('guestAddToCartBtn').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    setTimeout(() => {
      const id = currentProductId;
      const p = productsData[id];
      const qty = +document.getElementById('guestQtyInput').value;
      const note = document.getElementById('guestItemNote').value.trim();
      const disc = calcDiscount(p.price, p.discount_type, p.discount_value);
      cart[id] = {
        name: p.name, price: p.price, image: p.image,
        qty: (cart[id] ? cart[id].qty : 0) + qty,
        note, discount_amount: disc, final_price: p.price - disc,
      };
      saveCart();
      btn.disabled = false;
      bootstrap.Modal.getInstance(document.getElementById('guestItemModal')).hide();
      NexoraGuestToast('Menu ditambahkan.', 'success');
    }, 400);
  });

  // ——— Tambah bundle ke cart ———
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.guest-bundle-add');
    if (!btn) return;
    const key = 'bundle:' + btn.dataset.bundleId;
    const items = JSON.parse(btn.dataset.bundleItems || '[]');
    const bundle = {
      type: 'bundle',
      bundle_id: btn.dataset.bundleId,
      name: btn.dataset.bundleName,
      price: Number(btn.dataset.bundlePrice || 0),
      image: '',
      qty: (cart[key] ? cart[key].qty : 0) + 1,
      discount_amount: 0,
      final_price: Number(btn.dataset.bundlePrice || 0),
      items: items, // isi bundle utk render & submit
    };
    cart[key] = bundle;
    saveCart();
    NexoraGuestToast('Paket ditambahkan.', 'success');
  });

  // ——— Event delegation cart ———
  document.getElementById('guestCartItems').addEventListener('click', function(e) {
    const plus = e.target.closest('[data-act="plus"]');
    const minus = e.target.closest('[data-act="minus"]');
    const remove = e.target.closest('.guest-cart-remove');
    if (plus) { cart[plus.dataset.id].qty++; saveCart(); }
    else if (minus) {
      if (cart[minus.dataset.id].qty > 1) cart[minus.dataset.id].qty--;
      else delete cart[minus.dataset.id];
      saveCart();
    }
    else if (remove) { delete cart[remove.dataset.id]; saveCart(); }
  });

  // ——— Cart bar klik → buka bottom sheet ———
  document.getElementById('guestCartBar').addEventListener('click', () => {
    if (Object.keys(cart).length === 0) { NexoraGuestToast('Keranjang masih kosong.', 'default'); return; }
    showSheet();
  });
  document.getElementById('guestCartSheetClose').addEventListener('click', hideSheet);
  backdropEl.addEventListener('click', hideSheet);

  // ——— Checkout ———
  document.getElementById('guestCheckoutBtn').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    setTimeout(() => {
      // Produk: item non-bundle
      const cartArr = Object.keys(cart)
        .filter(id => cart[id].type !== 'bundle')
        .map(id => ({
          product_id: id,
          qty: cart[id].qty,
          note: cart[id].note || '',
        }));
      // Bundle: item type bundle
      const bundleArr = Object.keys(cart)
        .filter(id => cart[id].type === 'bundle')
        .map(id => ({
          bundle_id: cart[id].bundle_id,
          bundle_name: cart[id].name,
          bundle_price: cart[id].price,
          qty: cart[id].qty,
          items: cart[id].items.map(i => ({
            product_id: i.product_id,
            quantity: i.quantity,
          })),
        }));
      let total = 0;
      Object.keys(cart).forEach(id => { total += cart[id].final_price * cart[id].qty; });

      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '{{ route("guest.checkout") }}';
      const csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
      const cartData = document.createElement('input'); cartData.type = 'hidden'; cartData.name = 'cart_data'; cartData.value = JSON.stringify(cartArr);
      const bundleData = document.createElement('input'); bundleData.type = 'hidden'; bundleData.name = 'bundle_data'; bundleData.value = JSON.stringify(bundleArr);
      const totalInput = document.createElement('input'); totalInput.type = 'hidden'; totalInput.name = 'total_price'; totalInput.value = total;
      const tableInput = document.createElement('input'); tableInput.type = 'hidden'; tableInput.name = 'table_id'; tableInput.value = '{{ $table->table_id }}';
      form.appendChild(csrf); form.appendChild(cartData); form.appendChild(bundleData); form.appendChild(totalInput); form.appendChild(tableInput);
      document.body.appendChild(form); form.submit();
    }, 400);
  });

  // ——— Open item modal from product card ———
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.guest-product-add');
    if (!btn) return;
    currentProductId = btn.dataset.id;
    const p = productsData[currentProductId];
    if (!p) return;
    document.getElementById('guestItemModalName').textContent = p.name;
    const disc = calcDiscount(p.price, p.discount_type, p.discount_value);
    document.getElementById('guestItemModalPrice').innerHTML = disc > 0
      ? '<span class="guest-price-strike">' + fmtRp(p.price) + '</span> ' + fmtRp(p.price - disc)
      : fmtRp(p.price);
    document.getElementById('guestQtyInput').value = 1;
    document.getElementById('guestItemNote').value = '';
    new bootstrap.Modal(document.getElementById('guestItemModal')).show();
  });

  // ——— Restore cart dari sessionStorage ———
  const stored = sessionStorage.getItem('guest_cart');
  if (stored) { try { cart = JSON.parse(stored); } catch(e) {} }
  renderCart();

  // ——— Auto dismiss flash alert ———
  setTimeout(() => {
    document.querySelectorAll('.guest-alert').forEach(a => a.remove());
  }, 4000);
});
</script>
@endpush
