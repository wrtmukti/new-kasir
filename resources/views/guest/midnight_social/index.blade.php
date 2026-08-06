@extends('guest.midnight_social.layouts.app')

@section('title', 'Menu Cafe')

@section('content')

<!-- Hero Section -->
<section class="mb-8">
  <span class="text-xs font-headline font-extrabold uppercase tracking-widest text-purple-300 bg-purple-950/60 px-3 py-1 rounded-full border border-purple-500/30">
    Social Spot & Nightlife Menu
  </span>
  <h2 class="font-headline font-black text-3xl md:text-5xl text-slate-100 leading-tight mt-2">
    Vibe Up Your <br/><span class="text-purple-400">Midnight.</span>
  </h2>
  <p class="text-slate-400 text-sm md:text-base max-w-md mt-2">
    Sajian hangout malam pilihan, minuman kreasi, & kudapan lezat. Pilihlah menu di meja {{ $table->table_number }}.
  </p>
</section>

<!-- Search Bar Section -->
<section class="mb-6">
  <div class="relative w-full max-w-md">
    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">search</span>
    <input type="text" id="guestSearch" placeholder="Cari sajian malam, minuman, makanan..." 
           class="w-full bg-[#0f172a]/80 border border-white/10 rounded-full pl-11 pr-4 py-2.5 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all"/>
  </div>
</section>

<!-- Categories Horizontal Slider -->
<section class="mb-8 -mx-4 px-4 overflow-hidden">
  <div class="flex gap-2.5 overflow-x-auto hide-scrollbar pb-2" id="guestCategoryContainer">
    <button class="ms-cat-pill flex-shrink-0 px-5 py-2.5 rounded-full bg-purple-600 text-white font-headline font-bold text-xs transition-all active:scale-95 active" data-cat="all">
      Semua Menu
    </button>
    @if($bundles->isNotEmpty())
      <button class="ms-cat-pill flex-shrink-0 px-5 py-2.5 rounded-full border border-white/10 text-slate-300 font-headline font-bold text-xs hover:bg-white/10 transition-all active:scale-95" data-cat="bundle">
        🍹 Paket Social
      </button>
    @endif
    @foreach($categories as $cat)
      <button class="ms-cat-pill flex-shrink-0 px-5 py-2.5 rounded-full border border-white/10 text-slate-300 font-headline font-bold text-xs hover:bg-white/10 transition-all active:scale-95" data-cat="{{ $cat->category_id }}">
        {{ $cat->category_name }}
      </button>
    @endforeach
  </div>
</section>

<!-- Bundle Container (Displayed when all or bundle tab active) -->
@if($bundles->isNotEmpty())
  <div class="mb-8" id="guestBundlesSection">
    <h3 class="font-headline font-extrabold text-lg text-slate-100 mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-purple-400 fill-icon">nightlife</span>
      Paket Special Social
    </h3>
    <div class="bento-grid" id="guestBundles">
      @foreach($bundles as $bundle)
        @include('guest.midnight_social.partials._bundle_card', ['bundle' => $bundle])
      @endforeach
    </div>
  </div>
@endif

<!-- Products Section -->
<section>
  <div class="flex items-center justify-between mb-4">
    <h3 class="font-headline font-extrabold text-lg text-slate-100 flex items-center gap-2">
      <span class="material-symbols-outlined text-purple-400">local_bar</span>
      Daftar Menu
    </h3>

    <!-- View Toggle Button (Grid vs List) -->
    <button type="button" id="msViewToggle" class="p-2 rounded-full bg-[#0f172a] border border-white/10 hover:bg-white/10 text-purple-300 transition-colors flex items-center justify-center active:scale-95 shadow-xs" title="Tampilan Daftar">
      <span class="material-symbols-outlined text-[20px]" id="msViewToggleIcon">format_list_bulleted</span>
    </button>
  </div>

  <div class="bento-grid" id="guestProducts">
    @foreach($products as $product)
      @include('guest.midnight_social.partials._product_card', ['product' => $product])
    @endforeach
  </div>

  @if($products->isEmpty() && $bundles->isEmpty())
    <div class="text-center py-16 bg-[#0f172a]/60 rounded-2xl border border-dashed border-white/10 my-6">
      <span class="material-symbols-outlined text-5xl text-slate-500 mb-2">no_food</span>
      <p class="font-headline font-bold text-slate-400">Belum ada menu tersedia.</p>
    </div>
  @endif
</section>

@endsection

@section('floating')

<!-- Sticky Floating Cart Bar (Appears when items are in cart) -->
<div id="msCartBar" class="ms-cart-bar hidden cursor-pointer">
  <div class="flex items-center gap-3">
    <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
      <span class="material-symbols-outlined text-[20px] fill-icon">shopping_bag</span>
    </div>
    <div>
      <div id="msCartCount" class="font-headline font-bold text-xs uppercase tracking-wide opacity-90">0 Item</div>
      <div id="msCartTotal" class="font-headline font-black text-sm">Rp 0</div>
    </div>
  </div>
  <button type="button" class="flex items-center gap-1 font-headline font-bold text-xs bg-white text-purple-950 px-4 py-2 rounded-full shadow-sm hover:scale-105 transition-transform">
    Cek Keranjang <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
  </button>
</div>

<!-- Cart Sheet Backdrop & Bottom Sheet Drawer -->
<div id="msCartBackdrop" class="ms-cart-backdrop"></div>
<div id="msCartSheet" class="ms-cart-sheet">
  <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto my-3"></div>
  <div class="px-6 py-3 border-b border-white/10 flex items-center justify-between">
    <h4 class="font-headline font-black text-lg text-slate-100 flex items-center gap-2">
      <span class="material-symbols-outlined text-purple-400 fill-icon">shopping_cart</span>
      Keranjang Saya
    </h4>
    <button type="button" id="msCartClose" class="text-slate-400 hover:text-purple-300 p-1 rounded-full">
      <span class="material-symbols-outlined">close</span>
    </button>
  </div>

  <div id="msCartBody" class="p-6 overflow-y-auto max-h-[50vh] space-y-4 divide-y divide-white/10">
    <!-- Rendered via JS -->
  </div>

  <div class="p-6 border-t border-white/10 bg-[#0a0f1d] rounded-b-1.5rem">
    <div class="flex items-center justify-between mb-4">
      <span class="font-headline font-bold text-sm text-slate-400">Total Pembayaran</span>
      <span id="msSheetTotal" class="font-headline font-black text-xl text-purple-300">Rp 0</span>
    </div>
    
    <form id="msCheckoutForm" method="POST" action="{{ route('guest.checkout') }}">
      @csrf
      <input type="hidden" name="table_id" value="{{ $table->table_id }}"/>
      <input type="hidden" name="total_price" id="msFormTotalPrice" value="0"/>
      <input type="hidden" name="cart_data" id="msFormCartData" value="[]"/>
      <input type="hidden" name="bundle_data" id="msFormBundleData" value="[]"/>
      
      <button type="submit" id="msCheckoutBtn" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3.5 rounded-xl font-headline font-extrabold text-sm flex items-center justify-center gap-2 transition-all active:scale-95 shadow-md">
        <span>Lanjut ke Pembayaran</span>
        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
      </button>
    </form>
  </div>
</div>

<!-- Modal Item Add/Edit Stepper & Note -->
<div id="msItemModal" class="ms-modal-overlay">
  <div class="ms-modal-card">
    <div class="flex items-center justify-between border-b border-white/10 pb-3 mb-4">
      <h4 id="msModalTitle" class="font-headline font-extrabold text-base text-slate-100 truncate">Nama Menu</h4>
      <button type="button" id="msModalClose" class="text-slate-400 hover:text-purple-300">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <div class="mb-4">
      <div class="flex items-center justify-between mb-3">
        <span id="msModalPrice" class="font-headline font-black text-lg text-purple-300">Rp 0</span>
        <div class="flex items-center gap-3 bg-slate-800 rounded-full px-3 py-1">
          <button type="button" id="msQtyMinus" class="w-8 h-8 rounded-full bg-slate-700 text-slate-100 flex items-center justify-center font-bold shadow-xs hover:bg-slate-600 transition-all active:scale-90">
            <span class="material-symbols-outlined text-[18px]">remove</span>
          </button>
          <span id="msQtyVal" class="font-headline font-bold text-sm min-w-[24px] text-center text-slate-100">1</span>
          <button type="button" id="msQtyPlus" class="w-8 h-8 rounded-full bg-slate-700 text-slate-100 flex items-center justify-center font-bold shadow-xs hover:bg-slate-600 transition-all active:scale-90">
            <span class="material-symbols-outlined text-[18px]">add</span>
          </button>
        </div>
      </div>

      <label class="block text-xs font-headline font-bold text-slate-400 mb-1">Catatan Tambahan (opsional)</label>
      <input type="text" id="msModalNote" placeholder="Cth: kurang manis, ekstra es..."
             class="w-full bg-[#151b2d] border border-white/10 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all"/>
    </div>

    <button type="button" id="msAddToCartBtn" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-xl font-headline font-bold text-sm flex items-center justify-center gap-2 transition-all active:scale-95 shadow-md">
      <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
      <span id="msAddToCartBtnText">Simpan Pesanan</span>
    </button>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Cart state with multi-key fallback
  let cart = { items: {}, bundles: {} };

  const savedCart = sessionStorage.getItem('guest_cart_{{ $table->table_id }}') 
                 || sessionStorage.getItem('ms_guest_cart_{{ $table->table_id }}')
                 || sessionStorage.getItem('mb_guest_cart_{{ $table->table_id }}')
                 || sessionStorage.getItem('sb_guest_cart_{{ $table->table_id }}');
  if (savedCart) {
    try { cart = JSON.parse(savedCart); } catch(e) {}
  }

  // Selected item modal state
  let currentProduct = null;
  let currentQty = 1;

  // DOM Elements
  const cartBar = document.getElementById('msCartBar');
  const cartCountEl = document.getElementById('msCartCount');
  const cartTotalEl = document.getElementById('msCartTotal');

  const cartSheet = document.getElementById('msCartSheet');
  const cartBackdrop = document.getElementById('msCartBackdrop');
  const cartClose = document.getElementById('msCartClose');
  const cartBody = document.getElementById('msCartBody');
  const sheetTotalEl = document.getElementById('msSheetTotal');

  const modal = document.getElementById('msItemModal');
  const modalTitle = document.getElementById('msModalTitle');
  const modalPrice = document.getElementById('msModalPrice');
  const modalNote = document.getElementById('msModalNote');
  const modalClose = document.getElementById('msModalClose');
  const qtyVal = document.getElementById('msQtyVal');
  const qtyMinus = document.getElementById('msQtyMinus');
  const qtyPlus = document.getElementById('msQtyPlus');
  const addToCartBtn = document.getElementById('msAddToCartBtn');
  const addToCartBtnText = document.getElementById('msAddToCartBtnText');

  const checkoutForm = document.getElementById('msCheckoutForm');

  // Robust Key Matching Helper Functions
  function getCartItemKey(id) {
    if (!id) return null;
    if (cart.items[id]) return id;
    const strId = String(id);
    for (let k in cart.items) {
      if (String(k) === strId || String(cart.items[k].id) === strId || String(cart.items[k].product_id) === strId) {
        return k;
      }
    }
    return null;
  }

  function getCartBundleKey(id) {
    if (!id) return null;
    if (cart.bundles[id]) return id;
    const strId = String(id);
    for (let k in cart.bundles) {
      if (String(k) === strId || String(cart.bundles[k].id) === strId || String(cart.bundles[k].bundle_id) === strId) {
        return k;
      }
    }
    return null;
  }

  // View Toggle (Grid vs List)
  const viewToggleBtn = document.getElementById('msViewToggle');
  const viewToggleIcon = document.getElementById('msViewToggleIcon');
  const productsGrid = document.getElementById('guestProducts');

  function setProductsView(isList) {
    if (!productsGrid || !viewToggleBtn) return;
    if (isList) {
      productsGrid.classList.add('ms-list-view');
      viewToggleBtn.title = 'Tampilan Kartu';
      viewToggleIcon.textContent = 'grid_view';
    } else {
      productsGrid.classList.remove('ms-list-view');
      viewToggleBtn.title = 'Tampilan Daftar';
      viewToggleIcon.textContent = 'format_list_bulleted';
    }
  }

  const savedViewMode = sessionStorage.getItem('ms_guest_view') === 'list';
  setProductsView(savedViewMode);

  if (viewToggleBtn) {
    viewToggleBtn.addEventListener('click', function() {
      const isCurrentlyList = productsGrid.classList.contains('ms-list-view');
      const nextIsList = !isCurrentlyList;
      setProductsView(nextIsList);
      sessionStorage.setItem('ms_guest_view', nextIsList ? 'list' : 'grid');
    });
  }

  // Save cart state
  function saveCart() {
    sessionStorage.setItem('guest_cart_{{ $table->table_id }}', JSON.stringify(cart));
    sessionStorage.setItem('ms_guest_cart_{{ $table->table_id }}', JSON.stringify(cart));
    renderCartSummary();
  }

  // Compute total price and item count
  function getCartTotals() {
    let itemCount = 0;
    let grandTotal = 0;

    Object.values(cart.items).forEach(item => {
      itemCount += item.qty;
      grandTotal += item.price * item.qty;
    });

    Object.values(cart.bundles).forEach(b => {
      const price = b.price !== undefined ? b.price : (b.bundle_price || 0);
      itemCount += b.qty;
      grandTotal += price * b.qty;
    });

    return { itemCount, grandTotal };
  }

  // Render bottom bar summary
  function renderCartSummary() {
    const { itemCount, grandTotal } = getCartTotals();

    if (itemCount > 0) {
      cartBar.classList.remove('hidden');
      cartCountEl.textContent = `${itemCount} Item`;
      cartTotalEl.textContent = formatRupiah(grandTotal);
      sheetTotalEl.textContent = formatRupiah(grandTotal);
    } else {
      cartBar.classList.add('hidden');
      closeCartSheet();
    }
  }

  // Render items inside cart bottom sheet
  function renderCartSheetItems() {
    const { itemCount, grandTotal } = getCartTotals();
    sheetTotalEl.textContent = formatRupiah(grandTotal);

    if (itemCount === 0) {
      cartBody.innerHTML = `
        <div class="text-center py-8 text-slate-400">
          <span class="material-symbols-outlined text-4xl mb-1">remove_shopping_cart</span>
          <p class="font-headline font-bold text-sm">Keranjang kamu masih kosong.</p>
        </div>
      `;
      return;
    }

    let html = '';

    // Render Product Items
    Object.values(cart.items).forEach(item => {
      const itemId = item.id || item.product_id;
      html += `
        <div class="pt-3 first:pt-0 flex items-center justify-between gap-3">
          <div class="flex-grow min-w-0">
            <h5 class="font-headline font-extrabold text-sm text-slate-100 truncate">${item.name}</h5>
            <div class="text-xs text-purple-300 font-bold mt-0.5">${formatRupiah(item.price * item.qty)} <span class="text-[11px] text-slate-400 font-normal">(${item.qty} x ${formatRupiah(item.price)})</span></div>
            ${item.note ? `<div class="text-xs text-slate-300 bg-slate-800 px-2 py-0.5 rounded mt-1 inline-block">📝 ${item.note}</div>` : ''}
          </div>

          <div class="flex items-center gap-2 bg-slate-800 rounded-full px-2 py-1 flex-shrink-0">
            <button type="button" class="btn-change-item-qty w-7 h-7 rounded-full bg-slate-700 text-slate-100 flex items-center justify-center font-bold text-xs hover:bg-slate-600 active:scale-90 transition-all shadow-xs" data-id="${itemId}" data-delta="-1">
              <span class="material-symbols-outlined text-[16px]">remove</span>
            </button>
            <span class="font-headline font-bold text-xs min-w-[20px] text-center text-slate-100">${item.qty}</span>
            <button type="button" class="btn-change-item-qty w-7 h-7 rounded-full bg-slate-700 text-slate-100 flex items-center justify-center font-bold text-xs hover:bg-slate-600 active:scale-90 transition-all shadow-xs" data-id="${itemId}" data-delta="1">
              <span class="material-symbols-outlined text-[16px]">add</span>
            </button>
          </div>
        </div>
      `;
    });

    // Render Bundle Items
    Object.values(cart.bundles).forEach(b => {
      const bId = b.id || b.bundle_id;
      const bName = b.name || b.bundle_name || 'Paket';
      const bPrice = b.price !== undefined ? b.price : (b.bundle_price || 0);

      html += `
        <div class="pt-3 first:pt-0 flex items-center justify-between gap-3">
          <div class="flex-grow min-w-0">
            <div class="flex items-center gap-1">
              <span class="px-1.5 py-0.5 bg-purple-600 text-white text-[10px] font-extrabold rounded">PAKET</span>
              <h5 class="font-headline font-extrabold text-sm text-slate-100 truncate">${bName}</h5>
            </div>
            <div class="text-xs text-purple-300 font-bold mt-0.5">${formatRupiah(bPrice * b.qty)} <span class="text-[11px] text-slate-400 font-normal">(${b.qty} x ${formatRupiah(bPrice)})</span></div>
          </div>

          <div class="flex items-center gap-2 bg-slate-800 rounded-full px-2 py-1 flex-shrink-0">
            <button type="button" class="btn-change-bundle-qty w-7 h-7 rounded-full bg-slate-700 text-slate-100 flex items-center justify-center font-bold text-xs hover:bg-slate-600 active:scale-90 transition-all shadow-xs" data-bundle-id="${bId}" data-delta="-1">
              <span class="material-symbols-outlined text-[16px]">remove</span>
            </button>
            <span class="font-headline font-bold text-xs min-w-[20px] text-center text-slate-100">${b.qty}</span>
            <button type="button" class="btn-change-bundle-qty w-7 h-7 rounded-full bg-slate-700 text-slate-100 flex items-center justify-center font-bold text-xs hover:bg-slate-600 active:scale-90 transition-all shadow-xs" data-bundle-id="${bId}" data-delta="1">
              <span class="material-symbols-outlined text-[16px]">add</span>
            </button>
          </div>
        </div>
      `;
    });

    cartBody.innerHTML = html;
  }

  // Event Delegation for Cart Body Quantity Buttons (+ / -)
  cartBody.addEventListener('click', function(e) {
    const itemBtn = e.target.closest('.btn-change-item-qty');
    if (itemBtn) {
      const rawId = itemBtn.dataset.id;
      const delta = parseInt(itemBtn.dataset.delta);
      const key = getCartItemKey(rawId);
      if (key && cart.items[key]) {
        cart.items[key].qty += delta;
        if (cart.items[key].qty <= 0) {
          delete cart.items[key];
        }
        saveCart();
        renderCartSheetItems();
      }
      return;
    }

    const bundleBtn = e.target.closest('.btn-change-bundle-qty');
    if (bundleBtn) {
      const rawId = bundleBtn.dataset.bundleId;
      const delta = parseInt(bundleBtn.dataset.delta);
      const key = getCartBundleKey(rawId);
      if (key && cart.bundles[key]) {
        cart.bundles[key].qty += delta;
        if (cart.bundles[key].qty <= 0) {
          delete cart.bundles[key];
        }
        saveCart();
        renderCartSheetItems();
      }
      return;
    }
  });

  // Bottom Sheet Open/Close
  function openCartSheet() {
    renderCartSheetItems();
    cartSheet.classList.add('show');
    cartBackdrop.classList.add('show');
  }
  function closeCartSheet() {
    cartSheet.classList.remove('show');
    cartBackdrop.classList.remove('show');
  }

  cartBar.addEventListener('click', openCartSheet);
  cartClose.addEventListener('click', closeCartSheet);
  cartBackdrop.addEventListener('click', closeCartSheet);

  // Update Dynamic Price and Stepper inside Modal
  function updateModalPriceAndQty() {
    if (!currentProduct) return;
    qtyVal.textContent = currentQty;
    const totalPrice = currentProduct.price * currentQty;
    modalPrice.textContent = formatRupiah(totalPrice);
    if (addToCartBtnText) {
      addToCartBtnText.textContent = `Tambahkan · ${formatRupiah(totalPrice)}`;
    }
  }

  // Open Product Modal
  function openProductModal(card) {
    const id = card.dataset.id;
    const name = card.dataset.name;
    const price = parseFloat(card.dataset.price);

    currentProduct = { id, name, price };
    const itemKey = getCartItemKey(id);
    currentQty = (itemKey && cart.items[itemKey]) ? cart.items[itemKey].qty : 1;
    
    modalTitle.textContent = name;
    modalNote.value = (itemKey && cart.items[itemKey]) ? (cart.items[itemKey].note || '') : '';

    updateModalPriceAndQty();
    modal.classList.add('show');
  }

  document.querySelectorAll('.guest-product-card').forEach(card => {
    card.style.cursor = 'pointer';
    card.addEventListener('click', function(e) {
      if (e.target.closest('.btn-add-cart')) return;
      openProductModal(this);
    });
  });

  document.querySelectorAll('.btn-add-cart').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      const card = this.closest('.guest-product-card');
      if (card) openProductModal(card);
    });
  });

  // Modal Qty Stepper Controls (+ / -)
  qtyMinus.addEventListener('click', function(e) {
    e.preventDefault();
    if (currentQty > 1) {
      currentQty--;
      updateModalPriceAndQty();
    }
  });

  qtyPlus.addEventListener('click', function(e) {
    e.preventDefault();
    currentQty++;
    updateModalPriceAndQty();
  });

  // Modal Close
  function closeModal() {
    modal.classList.remove('show');
  }
  modalClose.addEventListener('click', closeModal);
  modal.addEventListener('click', function(e) {
    if (e.target === modal) closeModal();
  });

  // Add product item to cart from modal
  addToCartBtn.addEventListener('click', function() {
    if (!currentProduct) return;
    const note = modalNote.value.trim();
    const itemKey = getCartItemKey(currentProduct.id) || currentProduct.id;

    cart.items[itemKey] = {
      id: currentProduct.id,
      product_id: currentProduct.id,
      name: currentProduct.name,
      price: currentProduct.price,
      qty: currentQty,
      note: note
    };

    saveCart();
    closeModal();
    NexoraToast(`${currentProduct.name} (x${currentQty}) diperbarui!`, 'success');
  });

  // Add Bundle directly to cart
  document.querySelectorAll('.btn-add-bundle').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      const card = this.closest('.guest-bundle-card');
      const bId = card.dataset.bundleId;
      const bName = card.dataset.bundleName;
      const bPrice = parseFloat(card.dataset.bundlePrice);
      let bItems = [];
      try { bItems = JSON.parse(card.dataset.bundleItems || '[]'); } catch(err) {}

      const bKey = getCartBundleKey(bId) || bId;

      if (cart.bundles[bKey]) {
        cart.bundles[bKey].qty += 1;
      } else {
        cart.bundles[bKey] = {
          id: bId,
          bundle_id: bId,
          name: bName,
          bundle_name: bName,
          price: bPrice,
          bundle_price: bPrice,
          qty: 1,
          items: bItems
        };
      }

      saveCart();
      NexoraToast(`${bName} ditambahkan!`, 'success');
    });
  });

  // Category Filtering
  const catPills = document.querySelectorAll('.ms-cat-pill');
  const productCards = document.querySelectorAll('.guest-product-card');
  const bundleSection = document.getElementById('guestBundlesSection');

  catPills.forEach(pill => {
    pill.addEventListener('click', function() {
      catPills.forEach(p => p.classList.remove('active', 'bg-purple-600', 'text-white'));
      catPills.forEach(p => p.classList.add('border', 'border-white/10', 'text-slate-300'));

      this.classList.remove('border', 'border-white/10', 'text-slate-300');
      this.classList.add('active');

      const selectedCat = this.dataset.cat;

      // Handle Bundle Section Visibility
      if (bundleSection) {
        if (selectedCat === 'all' || selectedCat === 'bundle') {
          bundleSection.style.display = 'block';
        } else {
          bundleSection.style.display = 'none';
        }
      }

      // Handle Products Visibility
      productCards.forEach(card => {
        const cardCat = card.dataset.cat;
        if (selectedCat === 'bundle') {
          card.style.display = 'none';
        } else if (selectedCat === 'all' || cardCat === selectedCat) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  // Live Search Filter
  const searchInput = document.getElementById('guestSearch');
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const q = this.value.toLowerCase().trim();
      productCards.forEach(card => {
        const name = card.dataset.name.toLowerCase();
        if (name.includes(q)) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });
  }

  // Submit Checkout Form safely
  checkoutForm.addEventListener('submit', function(e) {
    const { itemCount, grandTotal } = getCartTotals();
    if (itemCount === 0) {
      e.preventDefault();
      NexoraToast('Keranjang kamu masih kosong.', 'danger');
      return;
    }

    const itemsPayload = Object.values(cart.items).map(i => ({
      product_id: i.product_id || i.id,
      qty: i.qty,
      note: i.note || '',
      price: i.price
    }));

    const bundlesPayload = Object.values(cart.bundles).map(b => ({
      bundle_id: b.bundle_id || b.id,
      bundle_name: b.bundle_name || b.name,
      bundle_price: b.bundle_price !== undefined ? b.bundle_price : b.price,
      qty: b.qty,
      items: (b.items || []).map(bi => ({ product_id: bi.product_id, quantity: bi.quantity }))
    }));

    document.getElementById('msFormTotalPrice').value = grandTotal;
    document.getElementById('msFormCartData').value = JSON.stringify(itemsPayload);
    document.getElementById('msFormBundleData').value = JSON.stringify(bundlesPayload);

    // Disable button safely without breaking submit payload
    const checkoutBtn = document.getElementById('msCheckoutBtn');
    checkoutBtn.style.pointerEvents = 'none';
    checkoutBtn.innerHTML = `
      <span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
      <span>Memproses...</span>
    `;
  });

  // Initial cart sync
  renderCartSummary();
});
</script>
@endpush
