@extends('guest.layouts.app')

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
    <h1>{{ $company->company_name ?? 'Selamat Datang' }}</h1>
    <p>Scan & Pesan — pilih menu favoritmu</p>
  </div>
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
  @foreach($categories as $cat)
    <button class="guest-cat-btn" data-cat="{{ $cat->category_id }}">{{ $cat->category_name }}</button>
  @endforeach
</div>

{{-- Produk --}}
<div class="guest-products" id="guestProducts">
  @foreach($products as $product)
    @include('guest.partials._product_card', ['product' => $product])
  @endforeach
</div>

@if($products->isEmpty())
<div class="guest-empty">
  <i class="bi bi-cup-straw"></i>
  <p>Belum ada menu tersedia.</p>
</div>
@endif
@endsection

@section('floating')
{{-- FAB keranjang --}}
<button class="guest-cart-fab" id="guestCartFab" type="button">
  <i class="bi bi-bag"></i>
  <span class="guest-cart-count" id="guestCartCount">0</span>
</button>

{{-- Cart offcanvas --}}
<div class="offcanvas offcanvas-end guest-cart" tabindex="-1" id="guestCartPanel">
  <div class="offcanvas-header">
    <h6 class="mb-0"><i class="bi bi-bag me-2"></i>Pesanan Saya</h6>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column">
    <div id="guestCartItems" class="flex-grow-1"></div>
    <div class="guest-cart-footer">
      <div class="guest-cart-total-row">
        <span>Total</span>
        <span class="guest-cart-total" id="guestCartTotal">Rp 0</span>
      </div>
      <button type="button" class="btn btn-primary-guest w-100 btn-loading" id="guestCheckoutBtn">
        <i class="bi bi-arrow-right me-1"></i>Lanjut ke Pembayaran
      </button>
    </div>
  </div>
</div>

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
  catBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      catBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      const cat = this.dataset.cat;
      document.querySelectorAll('#guestProducts .guest-product-card').forEach(card => {
        card.style.display = (cat === 'all' || card.dataset.cat === cat) ? '' : 'none';
      });
    });
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
    const ids = Object.keys(cart);
    const countEl = document.getElementById('guestCartCount');
    const totalEl = document.getElementById('guestCartTotal');

    let total = 0, count = 0;
    if (ids.length === 0) {
      wrap.innerHTML = '<div class="guest-cart-empty"><i class="bi bi-bag"></i><p>Keranjang masih kosong.</p></div>';
    } else {
      wrap.innerHTML = '';
      ids.forEach(id => {
        const it = cart[id];
        const sub = it.final_price * it.qty;
        total += sub;
        count += it.qty;
        const row = document.createElement('div');
        row.className = 'guest-cart-item';
        row.innerHTML = `
          <img src="${it.image || 'https://via.placeholder.com/60x60?text=Menu'}" alt="">
          <div class="flex-grow-1">
            <div class="fw-semibold">${it.name}</div>
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
    }
    countEl.textContent = count;
    totalEl.textContent = fmtRp(total);
    countEl.style.display = count > 0 ? '' : 'none';
  }

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

  // ——— FAB buka cart ———
  document.getElementById('guestCartFab').addEventListener('click', () => {
    if (Object.keys(cart).length === 0) { NexoraGuestToast('Keranjang masih kosong.', 'default'); return; }
    new bootstrap.Offcanvas(document.getElementById('guestCartPanel')).show();
  });

  // ——— Checkout ———
  document.getElementById('guestCheckoutBtn').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    setTimeout(() => {
      const cartArr = Object.keys(cart).map(id => ({
        product_id: id,
        qty: cart[id].qty,
        note: cart[id].note || '',
      }));
      let total = 0;
      Object.keys(cart).forEach(id => { total += cart[id].final_price * cart[id].qty; });

      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '{{ route("guest.checkout") }}';
      const csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
      const cartData = document.createElement('input'); cartData.type = 'hidden'; cartData.name = 'cart_data'; cartData.value = JSON.stringify(cartArr);
      const totalInput = document.createElement('input'); totalInput.type = 'hidden'; totalInput.name = 'total_price'; totalInput.value = total;
      const tableInput = document.createElement('input'); tableInput.type = 'hidden'; tableInput.name = 'table_id'; tableInput.value = '{{ $table->table_id }}';
      form.appendChild(csrf); form.appendChild(cartData); form.appendChild(totalInput); form.appendChild(tableInput);
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
