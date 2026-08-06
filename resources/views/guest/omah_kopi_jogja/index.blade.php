@extends('guest.omah_kopi_jogja.layouts.app')

@section('title', 'Menu Cafe')

@section('content')

<!-- Hero Section -->
<section class="mb-8">
  <span class="text-xs font-headline font-extrabold uppercase tracking-widest text-primary bg-primary/10 px-3 py-1 rounded-full border border-primary/20">
    Artisan Coffee & Traditional Cuisine
  </span>
  <h2 class="font-headline font-black text-3xl md:text-5xl text-on-background leading-tight mt-2">
    Cita Rasa Khas <br/><span class="text-primary">Omah Jogja.</span>
  </h2>
  <p class="text-on-surface-variant text-sm md:text-base max-w-md mt-2">
    Seduhan kopi tradisional, camilan warisan, & suasana hangat khas Yogya. Pilihlah menu di meja {{ $table->table_number }}.
  </p>
</section>

<!-- Search Bar Section -->
<section class="mb-6">
  <div class="relative w-full max-w-md">
    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
    <input type="text" id="guestSearch" placeholder="Cari kopi tubruk, jadah tempe, wedang..." 
           class="w-full bg-surface-container-low border border-surface-variant rounded-full pl-11 pr-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all"/>
  </div>
</section>

<!-- Categories Horizontal Slider -->
<section class="mb-8 -mx-4 px-4 overflow-hidden">
  <div class="flex gap-2.5 overflow-x-auto hide-scrollbar pb-2" id="guestCategoryContainer">
    <button class="ok-cat-pill flex-shrink-0 px-5 py-2.5 rounded-full bg-primary text-white font-headline font-bold text-xs transition-all active:scale-95 active shadow-xs" data-cat="all">
      Semua Menu
    </button>
    @if($bundles->isNotEmpty())
      <button class="ok-cat-pill flex-shrink-0 px-5 py-2.5 rounded-full border border-outline-variant text-on-surface-variant font-headline font-bold text-xs hover:bg-surface-container transition-all active:scale-95" data-cat="bundle">
        ☕ Paket Warisan
      </button>
    @endif
    @foreach($categories as $cat)
      <button class="ok-cat-pill flex-shrink-0 px-5 py-2.5 rounded-full border border-outline-variant text-on-surface-variant font-headline font-bold text-xs hover:bg-surface-container transition-all active:scale-95" data-cat="{{ $cat->category_id }}">
        {{ $cat->category_name }}
      </button>
    @endforeach
  </div>
</section>

<!-- Bundle Container (Displayed when all or bundle tab active) -->
@if($bundles->isNotEmpty())
  <div class="mb-8" id="guestBundlesSection">
    <h3 class="font-headline font-extrabold text-lg text-on-background mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary fill-icon">coffee</span>
      Paket Hemat Warisan
    </h3>
    <div class="bento-grid" id="guestBundles">
      @foreach($bundles as $bundle)
        @include('guest.omah_kopi_jogja.partials._bundle_card', ['bundle' => $bundle])
      @endforeach
    </div>
  </div>
@endif

<!-- Products Section -->
<section>
  <div class="flex items-center justify-between mb-4">
    <h3 class="font-headline font-extrabold text-lg text-on-background flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">flatware</span>
      Daftar Menu
    </h3>

    <!-- View Toggle Button (Grid vs List) -->
    <button type="button" id="okViewToggle" class="p-2 rounded-full bg-surface-container-low border border-surface-variant hover:bg-surface-variant text-primary transition-colors flex items-center justify-center active:scale-95 shadow-xs" title="Tampilan Daftar">
      <span class="material-symbols-outlined text-[20px]" id="okViewToggleIcon">format_list_bulleted</span>
    </button>
  </div>

  <div class="bento-grid" id="guestProducts">
    @foreach($products as $product)
      @include('guest.omah_kopi_jogja.partials._product_card', ['product' => $product])
    @endforeach
  </div>

  @if($products->isEmpty() && $bundles->isEmpty())
    <div class="text-center py-16 bg-surface-container-low rounded-2xl border border-dashed border-surface-variant my-6">
      <span class="material-symbols-outlined text-5xl text-outline mb-2">no_food</span>
      <p class="font-headline font-bold text-on-surface-variant">Belum ada menu tersedia.</p>
    </div>
  @endif
</section>

@endsection

@section('floating')

<!-- Sticky Floating Cart Bar (Appears when items are in cart) -->
<div id="okCartBar" class="ok-cart-bar hidden cursor-pointer">
  <div class="flex items-center gap-3">
    <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
      <span class="material-symbols-outlined text-[20px] fill-icon">shopping_bag</span>
    </div>
    <div>
      <div id="okCartCount" class="font-headline font-bold text-xs uppercase tracking-wide opacity-90">0 Item</div>
      <div id="okCartTotal" class="font-headline font-black text-sm">Rp 0</div>
    </div>
  </div>
  <button type="button" class="flex items-center gap-1 font-headline font-bold text-xs bg-white text-primary px-4 py-2 rounded-full shadow-sm hover:scale-105 transition-transform">
    Cek Keranjang <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
  </button>
</div>

<!-- Cart Sheet Backdrop & Bottom Sheet Drawer -->
<div id="okCartBackdrop" class="ok-cart-backdrop"></div>
<div id="okCartSheet" class="ok-cart-sheet">
  <div class="w-12 h-1.5 bg-surface-variant rounded-full mx-auto my-3"></div>
  <div class="px-6 py-3 border-b border-surface-variant flex items-center justify-between">
    <h4 class="font-headline font-black text-lg text-on-background flex items-center gap-2">
      <span class="material-symbols-outlined text-primary fill-icon">shopping_cart</span>
      Keranjang Saya
    </h4>
    <button type="button" id="okCartClose" class="text-on-surface-variant hover:text-primary p-1 rounded-full">
      <span class="material-symbols-outlined">close</span>
    </button>
  </div>

  <div id="okCartBody" class="p-6 overflow-y-auto max-h-[50vh] space-y-4 divide-y divide-surface-variant">
    <!-- Rendered via JS -->
  </div>

  <div class="p-6 border-t border-surface-variant bg-surface-container-low rounded-b-1.5rem">
    <div class="flex items-center justify-between mb-4">
      <span class="font-headline font-bold text-sm text-on-surface-variant">Total Pembayaran</span>
      <span id="okSheetTotal" class="font-headline font-black text-xl text-primary">Rp 0</span>
    </div>
    
    <form id="okCheckoutForm" method="POST" action="{{ route('guest.checkout') }}">
      @csrf
      <input type="hidden" name="table_id" value="{{ $table->table_id }}"/>
      <input type="hidden" name="total_price" id="okFormTotalPrice" value="0"/>
      <input type="hidden" name="cart_data" id="okFormCartData" value="[]"/>
      <input type="hidden" name="bundle_data" id="okFormBundleData" value="[]"/>
      
      <button type="submit" id="okCheckoutBtn" class="w-full bg-primary hover:bg-primary-hover text-white py-3.5 rounded-xl font-headline font-extrabold text-sm flex items-center justify-center gap-2 transition-all active:scale-95 shadow-md">
        <span>Lanjut ke Pembayaran</span>
        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
      </button>
    </form>
  </div>
</div>

<!-- Modal Item Add/Edit Stepper & Note -->
<div id="okItemModal" class="ok-modal-overlay">
  <div class="ok-modal-card">
    <div class="flex items-center justify-between border-b border-surface-variant pb-3 mb-4">
      <h4 id="okModalTitle" class="font-headline font-extrabold text-base text-on-background truncate">Nama Menu</h4>
      <button type="button" id="okModalClose" class="text-on-surface-variant hover:text-primary">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <div class="mb-4">
      <div class="flex items-center justify-between mb-3">
        <span id="okModalPrice" class="font-headline font-black text-lg text-primary">Rp 0</span>
        <div class="flex items-center gap-3 bg-surface-container-high rounded-full px-3 py-1">
          <button type="button" id="okQtyMinus" class="w-8 h-8 rounded-full bg-white text-on-background flex items-center justify-center font-bold shadow-xs hover:bg-surface transition-all active:scale-90">
            <span class="material-symbols-outlined text-[18px]">remove</span>
          </button>
          <span id="okQtyVal" class="font-headline font-bold text-sm min-w-[24px] text-center">1</span>
          <button type="button" id="okQtyPlus" class="w-8 h-8 rounded-full bg-white text-on-background flex items-center justify-center font-bold shadow-xs hover:bg-surface transition-all active:scale-90">
            <span class="material-symbols-outlined text-[18px]">add</span>
          </button>
        </div>
      </div>

      <label class="block text-xs font-headline font-bold text-on-surface-variant mb-1">Catatan Tambahan (opsional)</label>
      <input type="text" id="okModalNote" placeholder="Cth: tidak terlalu manis, es dipisah..."
             class="w-full bg-surface-container-low border border-surface-variant rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all"/>
    </div>

    <button type="button" id="okAddToCartBtn" class="w-full bg-primary hover:bg-primary-hover text-white py-3 rounded-xl font-headline font-bold text-sm flex items-center justify-center gap-2 transition-all active:scale-95 shadow-md">
      <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
      <span id="okAddToCartBtnText">Simpan Pesanan</span>
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
                 || sessionStorage.getItem('ok_guest_cart_{{ $table->table_id }}')
                 || sessionStorage.getItem('bb_guest_cart_{{ $table->table_id }}')
                 || sessionStorage.getItem('is_guest_cart_{{ $table->table_id }}')
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
  const cartBar = document.getElementById('okCartBar');
  const cartCountEl = document.getElementById('okCartCount');
  const cartTotalEl = document.getElementById('okCartTotal');

  const cartSheet = document.getElementById('okCartSheet');
  const cartBackdrop = document.getElementById('okCartBackdrop');
  const cartClose = document.getElementById('okCartClose');
  const cartBody = document.getElementById('okCartBody');
  const sheetTotalEl = document.getElementById('okSheetTotal');

  const modal = document.getElementById('okItemModal');
  const modalTitle = document.getElementById('okModalTitle');
  const modalPrice = document.getElementById('okModalPrice');
  const modalNote = document.getElementById('okModalNote');
  const modalClose = document.getElementById('okModalClose');
  const qtyVal = document.getElementById('okQtyVal');
  const qtyMinus = document.getElementById('okQtyMinus');
  const qtyPlus = document.getElementById('okQtyPlus');
  const addToCartBtn = document.getElementById('okAddToCartBtn');
  const addToCartBtnText = document.getElementById('okAddToCartBtnText');

  const checkoutForm = document.getElementById('okCheckoutForm');

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
  const viewToggleBtn = document.getElementById('okViewToggle');
  const viewToggleIcon = document.getElementById('okViewToggleIcon');
  const productsGrid = document.getElementById('guestProducts');

  function setProductsView(isList) {
    if (!productsGrid || !viewToggleBtn) return;
    if (isList) {
      productsGrid.classList.add('ok-list-view');
      viewToggleBtn.title = 'Tampilan Kartu';
      viewToggleIcon.textContent = 'grid_view';
    } else {
      productsGrid.classList.remove('ok-list-view');
      viewToggleBtn.title = 'Tampilan Daftar';
      viewToggleIcon.textContent = 'format_list_bulleted';
    }
  }

  const savedViewMode = sessionStorage.getItem('ok_guest_view') === 'list';
  setProductsView(savedViewMode);

  if (viewToggleBtn) {
    viewToggleBtn.addEventListener('click', function() {
      const isCurrentlyList = productsGrid.classList.contains('ok-list-view');
      const nextIsList = !isCurrentlyList;
      setProductsView(nextIsList);
      sessionStorage.setItem('ok_guest_view', nextIsList ? 'list' : 'grid');
    });
  }

  // Save cart state
  function saveCart() {
    sessionStorage.setItem('guest_cart_{{ $table->table_id }}', JSON.stringify(cart));
    sessionStorage.setItem('ok_guest_cart_{{ $table->table_id }}', JSON.stringify(cart));
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
        <div class="text-center py-8 text-on-surface-variant">
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
            <h5 class="font-headline font-extrabold text-sm text-on-background truncate">${item.name}</h5>
            <div class="text-xs text-primary font-bold mt-0.5">${formatRupiah(item.price * item.qty)} <span class="text-[11px] text-on-surface-variant font-normal">(${item.qty} x ${formatRupiah(item.price)})</span></div>
            ${item.note ? `<div class="text-xs text-on-surface-variant bg-surface-container-high px-2 py-0.5 rounded mt-1 inline-block">📝 ${item.note}</div>` : ''}
          </div>

          <div class="flex items-center gap-2 bg-surface-container-high rounded-full px-2 py-1 flex-shrink-0">
            <button type="button" class="btn-change-item-qty w-7 h-7 rounded-full bg-white text-on-background flex items-center justify-center font-bold text-xs hover:bg-surface active:scale-90 transition-all shadow-xs" data-id="${itemId}" data-delta="-1">
              <span class="material-symbols-outlined text-[16px]">remove</span>
            </button>
            <span class="font-headline font-bold text-xs min-w-[20px] text-center">${item.qty}</span>
            <button type="button" class="btn-change-item-qty w-7 h-7 rounded-full bg-white text-on-background flex items-center justify-center font-bold text-xs hover:bg-surface active:scale-90 transition-all shadow-xs" data-id="${itemId}" data-delta="1">
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
              <span class="px-1.5 py-0.5 bg-primary text-white text-[10px] font-extrabold rounded">PAKET</span>
              <h5 class="font-headline font-extrabold text-sm text-on-background truncate">${bName}</h5>
            </div>
            <div class="text-xs text-primary font-bold mt-0.5">${formatRupiah(bPrice * b.qty)} <span class="text-[11px] text-on-surface-variant font-normal">(${b.qty} x ${formatRupiah(bPrice)})</span></div>
          </div>

          <div class="flex items-center gap-2 bg-surface-container-high rounded-full px-2 py-1 flex-shrink-0">
            <button type="button" class="btn-change-bundle-qty w-7 h-7 rounded-full bg-white text-on-background flex items-center justify-center font-bold text-xs hover:bg-surface active:scale-90 transition-all shadow-xs" data-bundle-id="${bId}" data-delta="-1">
              <span class="material-symbols-outlined text-[16px]">remove</span>
            </button>
            <span class="font-headline font-bold text-xs min-w-[20px] text-center">${b.qty}</span>
            <button type="button" class="btn-change-bundle-qty w-7 h-7 rounded-full bg-white text-on-background flex items-center justify-center font-bold text-xs hover:bg-surface active:scale-90 transition-all shadow-xs" data-bundle-id="${bId}" data-delta="1">
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
  const catPills = document.querySelectorAll('.ok-cat-pill');
  const productCards = document.querySelectorAll('.guest-product-card');
  const bundleSection = document.getElementById('guestBundlesSection');

  catPills.forEach(pill => {
    pill.addEventListener('click', function() {
      catPills.forEach(p => p.classList.remove('active', 'bg-primary', 'text-white'));
      catPills.forEach(p => p.classList.add('border', 'border-outline-variant', 'text-on-surface-variant'));

      this.classList.remove('border', 'border-outline-variant', 'text-on-surface-variant');
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

    document.getElementById('okFormTotalPrice').value = grandTotal;
    document.getElementById('okFormCartData').value = JSON.stringify(itemsPayload);
    document.getElementById('okFormBundleData').value = JSON.stringify(bundlesPayload);

    // Disable button safely without breaking submit payload
    const checkoutBtn = document.getElementById('okCheckoutBtn');
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
