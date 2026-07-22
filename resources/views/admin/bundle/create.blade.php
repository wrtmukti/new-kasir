@extends('admin.layouts.app')

@section('title', 'Tambah Paket')

@php $activeMenu = 'bundle' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Tambah Paket</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.bundle.index') }}">Paket</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Tambah</span>
    </div>
  </div>
</div>

{{-- Stepper --}}
<div class="steps-modern mb-4" id="bundleStepper">
  <div class="step-item active" data-step="1">
    <div class="step-number">1</div>
    <div class="step-label">Informasi Paket</div>
  </div>
  <div class="step-item" data-step="2">
    <div class="step-number">2</div>
    <div class="step-label">Pilih Produk</div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex">
    <h6 id="stepTitle"><i class="bi bi-info-circle me-2"></i>Informasi Paket</h6>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.bundle.store') }}" method="POST" class="form-submit-loading" enctype="multipart/form-data" id="bundleForm">
      @csrf

      <input type="hidden" name="product_ids" id="productIdsInput" value="{{ old('product_ids', '[]') }}">

      {{-- ============================================================ --}}
      {{-- STEP 1: INFORMASI PAKET --}}
      {{-- ============================================================ --}}
      <div class="step-panel" id="step1Panel">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label-modern">Perusahaan</label>
            <div class="input-skeleton">
              <select name="company_id" class="form-select-modern">
                <option value="">-- Pilih Perusahaan --</option>
                @foreach($companies as $c)
                  <option value="{{ $c->company_id }}" {{ old('company_id') == $c->company_id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label-modern">Kode Paket</label>
            <div class="input-skeleton">
              <input type="text" name="bundle_code" class="form-control-modern" value="{{ old('bundle_code') }}" placeholder="BND-001">
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label-modern">Status</label>
            <div class="input-skeleton">
              <select name="bundle_status" class="form-select-modern">
                <option value="1" {{ old('bundle_status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ old('bundle_status') === '0' ? 'selected' : '' }}>Nonaktif</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label-modern">Nama Paket <span class="text-danger">*</span></label>
            <div class="input-skeleton">
              <input type="text" name="bundle_name" class="form-control-modern @error('bundle_name') is-invalid @enderror" value="{{ old('bundle_name') }}" placeholder="Nama paket">
              @error('bundle_name')
                <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
              @enderror
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label-modern">Harga Paket <span class="text-danger">*</span></label>
            <div class="input-skeleton">
              <input type="number" name="bundle_price" class="form-control-modern @error('bundle_price') is-invalid @enderror" value="{{ old('bundle_price') }}" min="0" step="0.01" placeholder="0">
              @error('bundle_price')
                <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
              @enderror
            </div>
          </div>
          <div class="col-12">
            <label class="form-label-modern">Deskripsi</label>
            <div class="input-skeleton">
              <textarea name="bundle_description" class="form-control-modern" rows="3" placeholder="Deskripsi paket">{{ old('bundle_description') }}</textarea>
            </div>
          </div>

          {{-- Image --}}
          <div class="col-12">
            <label class="form-label-modern">Gambar Paket</label>
            <div class="input-skeleton">
              <div class="image-upload-wrapper">
                <div class="image-upload-preview" id="imagePreview">
                  <i class="bi bi-image" style="font-size:2.5rem; color:var(--text-muted);"></i>
                  <span class="text-muted-c" style="font-size:0.85rem;">Belum ada gambar</span>
                </div>
                <div class="image-upload-actions">
                  <label class="btn btn-outline-soft" for="bundleImageInput">
                    <i class="bi bi-upload me-1"></i>Pilih Gambar
                  </label>
                  <button type="button" class="btn btn-ghost btn-sm text-danger" id="removeImageBtn" style="display:none;">
                    <i class="bi bi-trash3 me-1"></i>Hapus
                  </button>
                  <div class="text-muted-c mt-1" style="font-size:0.75rem;">
                    Format: JPEG, PNG, WebP, SVG. Maks 2MB.
                  </div>
                </div>
                <input type="file" name="bundle_image" id="bundleImageInput" accept="image/jpeg,image/png,image/webp,image/svg+xml" style="display:none;">
              </div>
              @error('bundle_image')
                <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
              @enderror
            </div>
          </div>
        </div>
      </div>

      {{-- ============================================================ --}}
      {{-- STEP 2: PILIH PRODUK --}}
      {{-- ============================================================ --}}
      <div class="step-panel" id="step2Panel" style="display:none;">
        <div class="mb-3">
          <label class="form-label-modern">Pilih Produk <span class="text-muted-c" style="font-weight:400;font-size:0.8rem;">— klik produk untuk ditambahkan ke paket</span></label>
          <div class="input-skeleton">
            {{-- Category tabs --}}
            @if($categories->count())
            <div class="d-flex flex-wrap gap-2 mb-3" id="categoryTabs">
              <button type="button" class="pill pill-active" data-category="">Semua</button>
              @foreach($categories as $cat)
                <button type="button" class="pill pill-neutral" data-category="{{ $cat->category_id }}">{{ $cat->category_name }}</button>
              @endforeach
            </div>
            @endif

            {{-- Search --}}
            <div class="mb-3" style="position:relative;">
              <input type="text" id="productSearchInput" class="form-control-modern" placeholder="Cari produk..." style="padding-left:2.2rem;">
              <i class="bi bi-search" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:0.85rem;"></i>
            </div>

            {{-- Product tags --}}
            <div class="product-tag-container" id="productTagContainer">
              @foreach($products as $product)
                <span class="product-tag" data-product-id="{{ $product->product_id }}"
                      data-product-name="{{ $product->product_name }}"
                      data-product-code="{{ $product->product_code }}"
                      data-product-price="{{ $product->product_price }}"
                      data-category-id="{{ $product->category_id }}">
                  {{ $product->product_name }}
                  <small style="opacity:0.6;font-size:0.65rem;">Rp {{ number_format($product->product_price, 0, ',', '.') }}</small>
                  <i class="bi bi-plus-circle tag-add-icon"></i>
                </span>
              @endforeach
            </div>
            @if($products->isEmpty())
              <div class="text-center text-muted-c py-4">
                <i class="bi bi-cup-hot" style="font-size:1.5rem;display:block;margin-bottom:0.5rem;"></i>
                Belum ada produk. <a href="{{ route('admin.product.create') }}" style="color:var(--accent-1);">Tambah produk</a> dulu.
              </div>
            @endif
          </div>
        </div>

        {{-- Selected products + quantity --}}
        <div class="selected-product-summary">
          <label class="form-label-modern">Produk Terpilih <span class="selected-count" id="selectedCount">0</span></label>
          <div id="selectedProductsList">
            <span class="text-muted-c" style="font-size:0.85rem;" id="emptySelectedMsg">Belum ada produk dipilih</span>
          </div>

          {{-- Price info --}}
          <div id="priceInfo" style="display:none;" class="mt-3 pt-3" style="border-top:1px solid var(--border-subtle);">
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted-c" style="font-size:0.85rem;">Total harga normal:</span>
              <span class="text-mono fw-semibold" id="totalNormalPrice">Rp 0</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
              <span class="text-muted-c" style="font-size:0.85rem;">Harga paket:</span>
              <span class="text-mono fw-semibold text-success" id="bundlePriceDisplay">Rp 0</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1" id="hematRow">
              <span class="text-muted-c" style="font-size:0.85rem;">Hemat:</span>
              <span class="text-mono fw-semibold text-danger" id="hematDisplay">Rp 0</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Navigation --}}
      <div class="d-flex justify-content-between mt-4 pt-3" style="border-top:1px solid var(--border-subtle);">
        <div>
          <button type="button" class="btn btn-outline-soft" id="prevBtn" style="display:none;">
            <i class="bi bi-chevron-left me-1"></i>Kembali
          </button>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('admin.bundle.index') }}" class="btn btn-ghost" id="cancelBtn">Batal</a>
          <button type="button" class="btn btn-primary-grad" id="nextBtn">
            Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
          </button>
          <button type="submit" class="btn btn-success-grad btn-loading" id="submitBtn" style="display:none;">
            <i class="bi bi-check-lg me-1"></i>Simpan Paket
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('styles')
<style>
.step-panel { animation: fadeSlideIn 0.35s ease; }
@keyframes fadeSlideIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

.product-tag-container {
  display:flex; flex-wrap:wrap; gap:0.5rem;
  padding:0.75rem;
  border:1px solid var(--border-subtle);
  border-radius:var(--radius-md);
  background:var(--bg-surface);
  max-height:280px; overflow-y:auto; min-height:60px;
}
.product-tag-container::-webkit-scrollbar { width:4px; }
.product-tag-container::-webkit-scrollbar-thumb { background:var(--border-subtle); border-radius:4px; }

.product-tag {
  cursor:pointer; transition:all 0.2s ease; user-select:none;
  border-color:var(--border-subtle); background:var(--bg-elevated);
  color:var(--text-primary);
}
.product-tag:hover { border-color:var(--accent-1); background:rgba(37,99,235,0.08); transform:translateY(-1px); }
.product-tag .tag-add-icon { opacity:0.4; transition:opacity 0.2s; }
.product-tag:hover .tag-add-icon { opacity:1; color:var(--accent-1) !important; }

.product-tag.selected { background:rgba(37,99,235,0.18); color:var(--accent-1); border-color:rgba(37,99,235,0.35); box-shadow:0 2px 8px rgba(37,99,235,0.15); }
.product-tag.selected .tag-add-icon::before { content:"\F633"; }
.product-tag.selected .tag-add-icon { opacity:1; color:var(--accent-1) !important; }

.selected-product-summary {
  padding:1rem;
  border:1px solid var(--border-subtle);
  border-radius:var(--radius-md);
  background:var(--bg-surface);
}
.selected-product-item {
  display:flex; align-items:center; gap:0.75rem;
  padding:0.6rem 0;
  border-bottom:1px solid var(--border-subtle);
}
.selected-product-item:last-child { border-bottom:none; }
.selected-product-info { flex:1; min-width:0; }
.selected-product-name { font-weight:600; font-size:0.88rem; color:var(--text-primary); }
.selected-product-meta { font-size:0.75rem; color:var(--text-muted); }
.selected-product-qty { flex-shrink:0; display:flex; align-items:center; gap:0.5rem; }
.selected-product-qty .form-control-modern { width:80px; text-align:center; font-weight:600; }

.pill-active {
  display:inline-flex; align-items:center; padding:0.35rem 0.9rem;
  background:var(--accent-gradient); color:#fff; border:none;
  border-radius:var(--radius-full); font-size:0.78rem; font-weight:500; cursor:pointer;
}
.pill-neutral {
  display:inline-flex; align-items:center; padding:0.35rem 0.9rem;
  background:var(--bg-elevated); color:var(--text-secondary); border:1px solid var(--border-subtle);
  border-radius:var(--radius-full); font-size:0.78rem; font-weight:500; cursor:pointer;
}

.remove-product { cursor:pointer; opacity:0.5; transition:opacity 0.15s; }
.remove-product:hover { opacity:1; color:var(--danger); }

.btn-success-grad { background:linear-gradient(135deg,#059669,#10B981); color:#fff; border:none; }
.btn-success-grad:hover { background:linear-gradient(135deg,#047857,#059669); color:#fff; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const STEP_INFO = 1, STEP_PRODUCT = 2;
  let currentStep = 1;
  let selectedProducts = [];

  const stepItems = document.querySelectorAll('#bundleStepper .step-item');
  const stepTitle = document.getElementById('stepTitle');
  const panel1 = document.getElementById('step1Panel');
  const panel2 = document.getElementById('step2Panel');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  const submitBtn = document.getElementById('submitBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const productTags = document.querySelectorAll('.product-tag');
  const searchInput = document.getElementById('productSearchInput');
  const selectedList = document.getElementById('selectedProductsList');
  const emptyMsg = document.getElementById('emptySelectedMsg');
  const countEl = document.getElementById('selectedCount');
  const idsInput = document.getElementById('productIdsInput');
  const totalNormalEl = document.getElementById('totalNormalPrice');
  const bundlePriceDisplay = document.getElementById('bundlePriceDisplay');
  const hematDisplay = document.getElementById('hematDisplay');
  const priceInfo = document.getElementById('priceInfo');
  const bundlePriceInput = document.querySelector('input[name="bundle_price"]');

  const stepLabels = { 1: '<i class="bi bi-info-circle me-2"></i>Informasi Paket', 2: '<i class="bi bi-gift me-2"></i>Pilih Produk' };

  // Image Upload
  const fileInput = document.getElementById('bundleImageInput');
  const preview = document.getElementById('imagePreview');
  const removeBtn = document.getElementById('removeImageBtn');
  if (fileInput) {
    fileInput.addEventListener('change', function() {
      const file = this.files[0]; if (!file) return;
      const reader = new FileReader();
      reader.onload = function(e) { preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">'; preview.classList.add('has-image'); removeBtn.style.display = ''; };
      reader.readAsDataURL(file);
    });
    removeBtn.addEventListener('click', function() { fileInput.value = ''; preview.innerHTML = '<i class="bi bi-image" style="font-size:2.5rem;color:var(--text-muted);"></i><span class="text-muted-c" style="font-size:0.85rem;">Belum ada gambar</span>'; preview.classList.remove('has-image'); removeBtn.style.display = 'none'; });
  }

  // Category filter
  document.querySelectorAll('#categoryTabs .pill').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('#categoryTabs .pill').forEach(function(b) { b.className = 'pill pill-neutral'; });
      this.className = 'pill pill-active';
      filterProducts();
    });
  });

  // Search
  if (searchInput) {
    searchInput.addEventListener('input', function() { filterProducts(); });
  }

  function filterProducts() {
    const activeCat = document.querySelector('#categoryTabs .pill-active');
    const catId = activeCat ? activeCat.dataset.category : '';
    const q = (searchInput?.value || '').toLowerCase().trim();
    productTags.forEach(function(tag) {
      const name = tag.dataset.productName.toLowerCase();
      const code = tag.dataset.productCode.toLowerCase();
      const tagCat = tag.dataset.categoryId;
      const matchCat = !catId || tagCat === catId;
      const matchSearch = name.includes(q) || code.includes(q);
      tag.style.display = matchCat && matchSearch ? '' : 'none';
    });
  }

  // Product selection
  productTags.forEach(function(tag) {
    tag.addEventListener('click', function() {
      const id = this.dataset.productId;
      const name = this.dataset.productName;
      const code = this.dataset.productCode;
      const price = parseFloat(this.dataset.productPrice) || 0;
      const idx = selectedProducts.findIndex(p => p.id === id);
      if (idx === -1) {
        selectedProducts.push({ id, name, code, price, qty: 1 });
        this.classList.add('selected');
      } else {
        selectedProducts.splice(idx, 1);
        this.classList.remove('selected');
      }
      updateSelected();
    });
  });

  function updateSelected() {
    idsInput.value = JSON.stringify(selectedProducts.map(p => p.id));
    countEl.textContent = selectedProducts.length;

    if (selectedProducts.length === 0) {
      selectedList.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;" id="emptySelectedMsg">Belum ada produk dipilih</span>';
      priceInfo.style.display = 'none';
      return;
    }

    let html = '';
    let totalNormal = 0;
    selectedProducts.forEach(function(p, i) {
      const subtotal = p.price * p.qty;
      totalNormal += subtotal;
      html += '<div class="selected-product-item" data-index="' + i + '">'
            + '<div class="selected-product-info">'
            + '<div class="selected-product-name">' + p.name + '</div>'
            + '<div class="selected-product-meta">' + p.code + ' &middot; Rp ' + Number(p.price).toLocaleString('id-ID') + '</div>'
            + '</div>'
            + '<div class="selected-product-qty">'
            + '<input type="number" name="quantities[' + i + ']" class="form-control-modern" value="' + p.qty + '" min="1" step="1" style="width:70px;text-align:center;font-weight:600;">'
            + '</div>'
            + '<i class="bi bi-x-lg remove-product" data-id="' + p.id + '"></i>'
            + '</div>';
    });
    selectedList.innerHTML = html;

    // Attach qty change & remove
    selectedList.querySelectorAll('input[name^="quantities["]').forEach(function(inp) {
      inp.addEventListener('change', function() {
        const index = parseInt(this.name.match(/\d+/)[0]);
        if (selectedProducts[index]) {
          selectedProducts[index].qty = parseInt(this.value) || 1;
          updateSelected();
        }
      });
    });
    selectedList.querySelectorAll('.remove-product').forEach(function(el) {
      el.addEventListener('click', function() {
        const id = this.dataset.id;
        selectedProducts = selectedProducts.filter(p => p.id !== id);
        document.querySelectorAll('.product-tag').forEach(function(t) {
          if (t.dataset.productId === id) t.classList.remove('selected');
        });
        updateSelected();
      });
    });

    // Price info
    const bundlePrice = parseFloat(bundlePriceInput.value) || 0;
    totalNormalEl.textContent = 'Rp ' + Number(totalNormal).toLocaleString('id-ID');
    bundlePriceDisplay.textContent = 'Rp ' + Number(bundlePrice).toLocaleString('id-ID');
    const hemat = totalNormal - bundlePrice;
    hematDisplay.textContent = 'Rp ' + Math.max(0, hemat).toLocaleString('id-ID');
    priceInfo.style.display = '';
  }

  // Live bundle price update
  if (bundlePriceInput) {
    bundlePriceInput.addEventListener('input', function() {
      if (selectedProducts.length > 0) updateSelected();
    });
  }

  // Navigation
  function goToStep(step) {
    currentStep = step;
    panel1.style.display = 'none';
    panel2.style.display = 'none';
    document.getElementById('step' + step + 'Panel').style.display = '';
    stepItems.forEach(function(item) {
      const s = parseInt(item.dataset.step);
      item.classList.remove('active', 'completed');
      if (s === step) item.classList.add('active');
      else if (s < step) item.classList.add('completed');
    });
    stepTitle.innerHTML = stepLabels[step];
    prevBtn.style.display = step > 1 ? '' : 'none';
    if (step === STEP_PRODUCT) { nextBtn.style.display = 'none'; submitBtn.style.display = ''; cancelBtn.style.display = 'none'; }
    else { nextBtn.style.display = ''; submitBtn.style.display = 'none'; cancelBtn.style.display = ''; }
    document.querySelector('.card').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function validateStep(step) {
    if (step === STEP_INFO) {
      const name = document.querySelector('input[name="bundle_name"]');
      if (!name.value.trim()) { name.focus(); NexoraToast('Nama paket wajib diisi.', 'warning'); return false; }
      return true;
    }
    return true;
  }

  nextBtn.addEventListener('click', function() {
    if (!validateStep(currentStep)) return;
    if (currentStep === STEP_INFO) goToStep(STEP_PRODUCT);
  });
  prevBtn.addEventListener('click', function() {
    if (currentStep === STEP_PRODUCT) goToStep(STEP_INFO);
  });

  // Submit
  const form = document.getElementById('bundleForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      idsInput.value = JSON.stringify(selectedProducts.map(p => p.id));
      e.preventDefault();
      form.querySelectorAll('.input-skeleton').forEach(function(el) { el.classList.add('is-loading'); });
      const btn = form.querySelector('.btn-loading');
      if (btn) { btn.classList.add('is-loading'); btn.disabled = true; }
      requestAnimationFrame(function() { setTimeout(function() { form.submit(); }, 400); });
    });
  }

  // Init from old
  (function initFromOld() {
    try {
      const oldIds = JSON.parse(idsInput.value || '[]');
      if (Array.isArray(oldIds) && oldIds.length > 0) {
        productTags.forEach(function(tag) {
          if (oldIds.includes(tag.dataset.productId)) {
            const id = tag.dataset.productId, name = tag.dataset.productName, code = tag.dataset.productCode, price = parseFloat(tag.dataset.productPrice) || 0;
            if (!selectedProducts.find(p => p.id === id)) { selectedProducts.push({ id, name, code, price, qty: 1 }); tag.classList.add('selected'); }
          }
        });
        // Restore quantities
        const oldQtys = document.querySelectorAll('input[name^="quantities["]');
        oldQtys.forEach(function(inp) {
          const idx = parseInt(inp.name.match(/\d+/)[0]);
          if (selectedProducts[idx]) selectedProducts[idx].qty = parseInt(inp.value) || 1;
        });
        updateSelected();
      }
    } catch(e) {}
  })();
});
</script>
@endpush
