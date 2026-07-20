@extends('admin.layouts.app')

@section('title', 'Edit Produk')

@php $activeMenu = 'product' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Edit Produk</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.product.index') }}">Produk</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Edit</span>
    </div>
  </div>
</div>

{{-- Stepper --}}
<div class="steps-modern mb-4" id="productStepper">
  <div class="step-item active" data-step="1">
    <div class="step-number">1</div>
    <div class="step-label">Informasi Produk</div>
  </div>
  <div class="step-item" data-step="2">
    <div class="step-number">2</div>
    <div class="step-label">Pilih Stok</div>
  </div>
  <div class="step-item" data-step="3">
    <div class="step-number">3</div>
    <div class="step-label">Jumlah Stok</div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex">
    <h6 id="stepTitle"><i class="bi bi-info-circle me-2"></i>Informasi Produk</h6>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.product.update', $product) }}" method="POST" class="form-submit-loading" enctype="multipart/form-data" id="productForm">
      @csrf @method('PUT')

      {{-- Hidden fields for stock tracking --}}
      <input type="hidden" name="stock_ids" id="stockIdsInput" value="{{ old('stock_ids', '[]') }}">
      <input type="hidden" name="has_stocks" id="hasStocksInput" value="{{ old('has_stocks', '0') }}">

      {{-- ============================================================ --}}
      {{-- STEP 1: INFORMASI PRODUK --}}
      {{-- ============================================================ --}}
      <div class="step-panel" id="step1Panel">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label-modern">Perusahaan</label>
            <div class="input-skeleton">
              <select name="company_id" class="form-select-modern">
                <option value="">-- Pilih Perusahaan --</option>
                @foreach($companies as $c)
                  <option value="{{ $c->company_id }}" {{ old('company_id', $product->company_id) == $c->company_id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label-modern">Kode Produk</label>
            <div class="input-skeleton">
              <input type="text" name="product_code" class="form-control-modern" value="{{ old('product_code', $product->product_code) }}" placeholder="PRD-001">
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label-modern">Kategori</label>
            <div class="input-skeleton">
              <select name="category_id" class="form-select-modern">
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->category_id }}" {{ old('category_id', $product->category_id) == $cat->category_id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label-modern">Nama Produk <span class="text-danger">*</span></label>
            <div class="input-skeleton">
              <input type="text" name="product_name" class="form-control-modern @error('product_name') is-invalid @enderror" value="{{ old('product_name', $product->product_name) }}" placeholder="Nama produk">
              @error('product_name')
                <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
              @enderror
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label-modern">Harga</label>
            <div class="input-skeleton">
              <input type="number" name="product_price" class="form-control-modern" value="{{ old('product_price', $product->product_price) }}" min="0" step="0.01" placeholder="0">
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label-modern">Deskripsi</label>
            <div class="input-skeleton">
              <textarea name="product_description" class="form-control-modern" rows="3" placeholder="Deskripsi produk">{{ old('product_description', $product->product_description) }}</textarea>
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label-modern">Status</label>
            <div class="input-skeleton">
              <select name="product_status" class="form-select-modern">
                <option value="1" {{ old('product_status', $product->product_status) == '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ old('product_status', $product->product_status) === '0' ? 'selected' : '' }}>Nonaktif</option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label-modern">Remark</label>
            <div class="input-skeleton">
              <input type="text" name="category_remark" class="form-control-modern" value="{{ old('category_remark', $product->category_remark) }}" placeholder="Catatan">
            </div>
          </div>

          {{-- Image Upload --}}
          <div class="col-12">
            <label class="form-label-modern">Gambar Produk</label>
            <div class="input-skeleton">
              <div class="image-upload-wrapper">
                <div class="image-upload-preview {{ $product->product_image ? 'has-image' : '' }}" id="imagePreview">
                  @if($product->product_image)
                    <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->product_name }}">
                  @else
                    <i class="bi bi-image" style="font-size:2.5rem; color:var(--text-muted);"></i>
                    <span class="text-muted-c" style="font-size:0.85rem;">Belum ada gambar</span>
                  @endif
                </div>
                <div class="image-upload-actions">
                  <label class="btn btn-outline-soft" for="productImageInput">
                    <i class="bi bi-upload me-1"></i>Ganti Gambar
                  </label>
                  <button type="button" class="btn btn-ghost btn-sm text-danger" id="removeImageBtn" style="{{ $product->product_image ? '' : 'display:none;' }}">
                    <i class="bi bi-trash3 me-1"></i>Hapus
                  </button>
                  <div class="text-muted-c mt-1" style="font-size:0.75rem;">
                    Format: JPEG, PNG, WebP, SVG. Maks 2MB.
                  </div>
                </div>
                <input type="file" name="product_image" id="productImageInput"
                       accept="image/jpeg,image/png,image/webp,image/svg+xml"
                       style="display:none;">
              </div>
              @error('product_image')
                <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
              @enderror
            </div>
          </div>
        </div>
      </div>

      {{-- ============================================================ --}}
      {{-- STEP 2: PILIH STOK (TAGS/CHIPS) --}}
      {{-- ============================================================ --}}
      <div class="step-panel" id="step2Panel" style="display:none;">
        <div class="mb-3">
          <label class="form-label-modern">Pilih Bahan Stok <span class="text-muted-c" style="font-weight:400;font-size:0.8rem;">— klik tag untuk memilih</span></label>
          <div class="input-skeleton">
            <div class="mb-3" style="position:relative;">
              <input type="text" id="stockSearchInput" class="form-control-modern" placeholder="Cari bahan stok..." style="padding-left:2.2rem;">
              <i class="bi bi-search" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:0.85rem;"></i>
            </div>
            <div class="stock-tag-container" id="stockTagContainer">
              @php
                $selectedStockIds = $product->stocks->pluck('stock_id')->toArray();
              @endphp
              @foreach($stocks as $stock)
                <span class="stock-tag tag {{ in_array($stock->stock_id, $selectedStockIds) ? 'selected' : '' }}"
                      data-stock-id="{{ $stock->stock_id }}"
                      data-stock-name="{{ $stock->stock_name }}"
                      data-stock-code="{{ $stock->stock_code }}"
                      data-stock-unit="{{ $stock->stock_unit }}"
                      data-stock-price="{{ $stock->stock_price }}">
                  {{ $stock->stock_name }}
                  <small style="opacity:0.6;font-size:0.65rem;margin-left:2px;">({{ $stock->stock_unit }})</small>
                  <i class="bi {{ in_array($stock->stock_id, $selectedStockIds) ? 'bi-check-circle-fill' : 'bi-plus-circle' }} tag-add-icon"></i>
                </span>
              @endforeach
            </div>
          </div>
        </div>

        <div class="selected-stock-summary" id="selectedSummary">
          <label class="form-label-modern">Stok Terpilih <span class="selected-count" id="selectedCount">{{ count($selectedStockIds) }}</span></label>
          <div class="selected-tags" id="selectedTagsContainer">
            @forelse($product->stocks as $ps)
              <span class="selected-tag-item">
                <i class="bi bi-check-circle-fill" style="font-size:0.65rem;"></i>
                {{ $ps->stock_name }} <small style="opacity:0.6;">({{ $ps->stock_unit }})</small>
                <i class="bi bi-x remove-tag" data-id="{{ $ps->stock_id }}"></i>
              </span>
            @empty
              <span class="text-muted-c" style="font-size:0.85rem;" id="emptySelectedMsg">Belum ada stok dipilih</span>
            @endforelse
          </div>
        </div>
      </div>

      {{-- ============================================================ --}}
      {{-- STEP 3: INPUT JUMLAH STOK --}}
      {{-- ============================================================ --}}
      <div class="step-panel" id="step3Panel" style="display:none;">
        <div class="mb-3">
          <label class="form-label-modern">Atur Jumlah Setiap Stok</label>
          <div class="text-muted-c mb-3" style="font-size:0.85rem;">
            <i class="bi bi-info-circle me-1"></i> Tentukan berapa banyak setiap bahan yang dibutuhkan untuk 1 unit produk ini.
          </div>
          <div class="input-skeleton">
            <div id="quantityList" class="quantity-list">
              @php
                $hasStocks = $product->stocks->isNotEmpty();
              @endphp
              @if($hasStocks)
                @foreach($product->stocks as $index => $ps)
                  <div class="qty-row" data-index="{{ $index }}">
                    <div class="qty-row-info">
                      <div class="qty-row-name">{{ $ps->stock_name }}</div>
                      <div class="qty-row-meta">
                        <span><i class="bi bi-upc-scan" style="font-size:0.65rem;"></i>{{ $ps->stock_code }}</span>
                        <span class="stock-badge"><i class="bi bi-box-seam me-1" style="font-size:0.6rem;"></i>{{ $ps->stock_unit }}</span>
                        @if($ps->stock_price > 0)
                          <span><i class="bi bi-coin" style="font-size:0.65rem;"></i>Rp {{ number_format($ps->stock_price, 0, ',', '.') }}</span>
                        @endif
                      </div>
                    </div>
                    <div class="qty-row-input">
                      <input type="number" name="quantities[{{ $index }}]" class="form-control-modern" value="{{ old('quantities.' . $index, $ps->pivot->quantity) }}" min="0" step="1" placeholder="0">
                      <span class="unit-label">{{ $ps->stock_unit }}</span>
                    </div>
                  </div>
                @endforeach
              @else
                <div class="text-center text-muted-c py-4" id="emptyQtyMsg">
                  <i class="bi bi-arrow-left" style="font-size:1.2rem;display:block;margin-bottom:0.5rem;"></i>
                  Pilih stok di langkah sebelumnya
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- ============================================================ --}}
      {{-- NAVIGATION BUTTONS --}}
      {{-- ============================================================ --}}
      <div class="d-flex justify-content-between mt-4 pt-3" style="border-top:1px solid var(--border-subtle);">
        <div>
          <button type="button" class="btn btn-outline-soft" id="prevBtn" style="display:none;">
            <i class="bi bi-chevron-left me-1"></i>Kembali
          </button>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('admin.product.index') }}" class="btn btn-ghost" id="cancelBtn">Batal</a>
          <button type="button" class="btn btn-primary-grad" id="nextBtn">
            Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
          </button>
          <button type="submit" class="btn btn-success-grad btn-loading" id="submitBtn" style="display:none;">
            <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('styles')
<style>
.step-panel {
  animation: fadeSlideIn 0.35s ease;
}
@keyframes fadeSlideIn {
  from { opacity: 0; transform: translateY(10px); }
  to   { opacity: 1; transform: translateY(0); }
}

.stock-tag-container {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  padding: 0.75rem;
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  background: var(--bg-surface);
  max-height: 260px;
  overflow-y: auto;
  min-height: 60px;
}
.stock-tag-container::-webkit-scrollbar { width: 4px; }
.stock-tag-container::-webkit-scrollbar-thumb { background: var(--border-subtle); border-radius: 4px; }

.stock-tag {
  cursor: pointer;
  transition: all 0.2s ease;
  user-select: none;
  border-color: var(--border-subtle);
  background: var(--bg-elevated);
}
.stock-tag:hover {
  border-color: var(--accent-1);
  background: rgba(37,99,235,0.08);
  transform: translateY(-1px);
}
.stock-tag .tag-add-icon { opacity: 0.4; transition: opacity 0.2s; }
.stock-tag:hover .tag-add-icon { opacity: 1; color: var(--accent-1) !important; }

.stock-tag.selected {
  background: rgba(37,99,235,0.18);
  color: var(--accent-1);
  border-color: rgba(37,99,235,0.35);
  box-shadow: 0 2px 8px rgba(37,99,235,0.15);
}

.selected-stock-summary {
  padding: 1rem;
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  background: var(--bg-surface);
}
.selected-tags { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem; }
.selected-tag-item {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.35rem 0.7rem;
  background: rgba(37,99,235,0.12);
  color: var(--accent-1);
  border: 1px solid rgba(37,99,235,0.2);
  border-radius: var(--radius-full);
  font-size: 0.78rem;
  font-weight: 500;
  animation: popIn 0.2s ease;
}
.selected-tag-item .remove-tag { cursor: pointer; font-size: 0.65rem; opacity: 0.6; transition: opacity 0.15s; }
.selected-tag-item .remove-tag:hover { opacity: 1; color: var(--danger); }
@keyframes popIn {
  0%   { transform: scale(0.85); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
.selected-count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 22px; height: 22px; padding: 0 6px;
  background: var(--accent-gradient);
  color: #fff;
  border-radius: var(--radius-full);
  font-size: 0.72rem; font-weight: 600;
  margin-left: 0.4rem;
}

.quantity-list {
  display: flex; flex-direction: column; gap: 0;
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  overflow: hidden;
  background: var(--bg-surface);
}
.qty-row {
  display: flex; align-items: center; gap: 1rem;
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--border-subtle);
  transition: background 0.15s;
}
.qty-row:last-child { border-bottom: none; }
.qty-row:hover { background: rgba(37,99,235,0.03); }
.qty-row-info { flex: 1; min-width: 0; }
.qty-row-name { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
.qty-row-meta { display: flex; gap: 0.75rem; font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem; }
.qty-row-meta span { display: inline-flex; align-items: center; gap: 0.25rem; }
.qty-row-input { flex-shrink: 0; display: flex; align-items: center; gap: 0.5rem; }
.qty-row-input .form-control-modern { width: 100px; text-align: center; font-weight: 600; }
.qty-row-input .unit-label { font-size: 0.78rem; color: var(--text-muted); font-weight: 500; min-width: 30px; }
.qty-row .stock-badge {
  display: inline-flex; align-items: center;
  padding: 0.15rem 0.5rem;
  background: var(--bg-elevated);
  border-radius: var(--radius-full);
  font-size: 0.68rem; color: var(--text-muted);
  border: 1px solid var(--border-subtle);
}

.image-upload-wrapper { display: flex; align-items: center; gap: 1.25rem; padding: 0.75rem 0; }
.image-upload-preview {
  width: 100px; height: 100px;
  border-radius: var(--radius-md);
  border: 2px dashed var(--border-subtle);
  display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.25rem;
  flex-shrink: 0; overflow: hidden;
  background: var(--bg-elevated);
  transition: border-color 0.2s, background 0.2s;
}
.image-upload-preview.has-image { border-style: solid; border-color: var(--border-subtle); }
.image-upload-preview img { width: 100%; height: 100%; object-fit: cover; }
.image-upload-actions { display: flex; flex-direction: column; gap: 0.5rem; }

.btn-success-grad { background: linear-gradient(135deg, #059669, #10B981); color: #fff; border: none; }
.btn-success-grad:hover { background: linear-gradient(135deg, #047857, #059669); color: #fff; }
.btn-success-grad.is-loading { opacity: 0.7; pointer-events: none; }

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  20% { transform: translateX(-4px); }
  40% { transform: translateX(4px); }
  60% { transform: translateX(-3px); }
  80% { transform: translateX(3px); }
}
.shake { animation: shake 0.35s ease; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const STEP_PRODUCT = 1;
  const STEP_STOCKS  = 2;
  const STEP_AMOUNT  = 3;

  let currentStep = 1;

  // ─── selectedStocks from the server ───
  let selectedStocks = [
    @foreach($product->stocks as $s)
      { id: '{{ $s->stock_id }}', name: '{{ str_replace("'", "\'", $s->stock_name) }}', code: '{{ $s->stock_code }}', unit: '{{ $s->stock_unit }}', price: '{{ $s->stock_price }}' },
    @endforeach
  ];

  // ─── Pre-existing quantities keyed by stock_id ───
  const existingQtys = {
    @foreach($product->stocks as $s)
      '{{ $s->stock_id }}': {{ $s->pivot->quantity ?? 1 }},
    @endforeach
  };

  // ─── DOM refs ───
  const stepItems        = document.querySelectorAll('#productStepper .step-item');
  const stepTitle        = document.getElementById('stepTitle');
  const panel1           = document.getElementById('step1Panel');
  const panel2           = document.getElementById('step2Panel');
  const panel3           = document.getElementById('step3Panel');
  const prevBtn          = document.getElementById('prevBtn');
  const nextBtn          = document.getElementById('nextBtn');
  const submitBtn        = document.getElementById('submitBtn');
  const cancelBtn        = document.getElementById('cancelBtn');
  const stockTags        = document.querySelectorAll('.stock-tag');
  const stockSearchInput = document.getElementById('stockSearchInput');
  const selectedTagsCont = document.getElementById('selectedTagsContainer');
  const selectedCountEl  = document.getElementById('selectedCount');
  const quantityList     = document.getElementById('quantityList');
  const stockIdsInput    = document.getElementById('stockIdsInput');

  const stepLabels = {
    1: '<i class="bi bi-info-circle me-2"></i>Informasi Produk',
    2: '<i class="bi bi-box-seam me-2"></i>Pilih Bahan Stok',
    3: '<i class="bi bi-sliders me-2"></i>Atur Jumlah Stok',
  };

  // ─── Image Upload ───
  const fileInput = document.getElementById('productImageInput');
  const preview = document.getElementById('imagePreview');
  const removeBtn = document.getElementById('removeImageBtn');
  if (fileInput) {
    fileInput.addEventListener('change', function() {
      const file = this.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
        preview.classList.add('has-image');
        removeBtn.style.display = '';
      };
      reader.readAsDataURL(file);
    });
    removeBtn.addEventListener('click', function() {
      fileInput.value = '';
      @if($product->product_image)
      preview.innerHTML = '<img src="{{ asset("storage/" . $product->product_image) }}" alt="{{ $product->product_name }}">';
      preview.classList.add('has-image');
      removeBtn.style.display = '';
      @else
      preview.innerHTML = '<i class="bi bi-image" style="font-size:2.5rem; color:var(--text-muted);"></i><span class="text-muted-c" style="font-size:0.85rem;">Belum ada gambar</span>';
      preview.classList.remove('has-image');
      removeBtn.style.display = 'none';
      @endif
    });
  }

  // ─── Stock Tag Selection ───
  stockTags.forEach(function(tag) {
    tag.addEventListener('click', function() {
      const id    = this.dataset.stockId;
      const name  = this.dataset.stockName;
      const code  = this.dataset.stockCode;
      const unit  = this.dataset.stockUnit;
      const price = this.dataset.stockPrice;

      const idx = selectedStocks.findIndex(s => s.id === id);
      if (idx === -1) {
        selectedStocks.push({ id, name, code, unit, price });
        this.classList.add('selected');
        this.querySelector('.tag-add-icon').className = 'bi bi-check-circle-fill tag-add-icon';
      } else {
        selectedStocks.splice(idx, 1);
        this.classList.remove('selected');
        this.querySelector('.tag-add-icon').className = 'bi bi-plus-circle tag-add-icon';
      }
      updateSelectedUI();
    });
  });

  // ─── Stock Search ───
  if (stockSearchInput) {
    stockSearchInput.addEventListener('input', function() {
      const q = this.value.toLowerCase().trim();
      stockTags.forEach(function(tag) {
        const name = tag.dataset.stockName.toLowerCase();
        const code = tag.dataset.stockCode.toLowerCase();
        tag.style.display = (name.includes(q) || code.includes(q)) ? '' : 'none';
      });
    });
  }

  // ─── Update Selected UI ───
  function updateSelectedUI() {
    stockIdsInput.value = JSON.stringify(selectedStocks.map(s => s.id));
    selectedCountEl.textContent = selectedStocks.length;

    if (selectedStocks.length === 0) {
      selectedTagsCont.innerHTML = '<span class="text-muted-c" style="font-size:0.85rem;">Belum ada stok dipilih</span>';
    } else {
      let html = '';
      selectedStocks.forEach(function(s) {
        html += '<span class="selected-tag-item">'
              + '<i class="bi bi-check-circle-fill" style="font-size:0.65rem;"></i>'
              + s.name + ' <small style="opacity:0.6;">(' + s.unit + ')</small>'
              + '<i class="bi bi-x remove-tag" data-id="' + s.id + '"></i>'
              + '</span>';
      });
      selectedTagsCont.innerHTML = html;

      selectedTagsCont.querySelectorAll('.remove-tag').forEach(function(el) {
        el.addEventListener('click', function() {
          const id = this.dataset.id;
          selectedStocks = selectedStocks.filter(s => s.id !== id);
          document.querySelectorAll('.stock-tag').forEach(function(t) {
            if (t.dataset.stockId === id) {
              t.classList.remove('selected');
              t.querySelector('.tag-add-icon').className = 'bi bi-plus-circle tag-add-icon';
            }
          });
          updateSelectedUI();
        });
      });
    }
    renderQuantityList();
  }

  // ─── Render Quantity Inputs ───
  function renderQuantityList() {
    if (selectedStocks.length === 0) {
      quantityList.innerHTML = '<div class="text-center text-muted-c py-4"><i class="bi bi-arrow-left" style="font-size:1.2rem;display:block;margin-bottom:0.5rem;"></i>Pilih stok di langkah sebelumnya</div>';
      return;
    }

    let html = '';
    selectedStocks.forEach(function(s, index) {
      const qty = existingQtys[s.id] || 1;
      html += '<div class="qty-row" data-index="' + index + '">'
            +   '<div class="qty-row-info">'
            +     '<div class="qty-row-name">' + s.name + '</div>'
            +     '<div class="qty-row-meta">'
            +       '<span><i class="bi bi-upc-scan" style="font-size:0.65rem;"></i>' + s.code + '</span>'
            +       '<span class="stock-badge"><i class="bi bi-box-seam me-1" style="font-size:0.6rem;"></i>' + s.unit + '</span>'
            +       (parseFloat(s.price) > 0 ? '<span><i class="bi bi-coin" style="font-size:0.65rem;"></i>Rp ' + Number(s.price).toLocaleString('id-ID') + '</span>' : '')
            +     '</div>'
            +   '</div>'
            +   '<div class="qty-row-input">'
            +     '<input type="number" name="quantities[' + index + ']" class="form-control-modern" value="' + qty + '" min="0" step="1" placeholder="0">'
            +     '<span class="unit-label">' + s.unit + '</span>'
            +   '</div>'
            + '</div>';
    });
    quantityList.innerHTML = html;
  }

  // ─── Step Navigation ───
  function goToStep(step) {
    currentStep = step;
    panel1.style.display = step === 1 ? '' : 'none';
    panel2.style.display = step === 2 ? '' : 'none';
    panel3.style.display = step === 3 ? '' : 'none';

    stepItems.forEach(function(item) {
      const s = parseInt(item.dataset.step);
      item.classList.remove('active', 'completed');
      if (s === step) item.classList.add('active');
      else if (s < step) item.classList.add('completed');
    });

    stepTitle.innerHTML = stepLabels[step];
    prevBtn.style.display = step > 1 ? '' : 'none';

    if (step === STEP_AMOUNT) {
      nextBtn.style.display = 'none';
      submitBtn.style.display = '';
      cancelBtn.style.display = 'none';
      renderQuantityList();
    } else {
      nextBtn.style.display = '';
      submitBtn.style.display = 'none';
      cancelBtn.style.display = '';
    }

    document.querySelector('.card').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  // ─── Validation ───
  function validateStep(step) {
    if (step === STEP_PRODUCT) {
      const name = document.querySelector('input[name="product_name"]');
      if (!name.value.trim()) {
        name.closest('.input-skeleton').classList.add('shake');
        setTimeout(function() { name.closest('.input-skeleton').classList.remove('shake'); }, 350);
        name.focus();
        if (typeof NexoraToast !== 'undefined') {
          NexoraToast({ type: 'warning', title: 'Lengkapi Data', message: 'Nama produk wajib diisi.' });
        }
        return false;
      }
    }
    return true;
  }

  // ─── Event: Next ───
  nextBtn.addEventListener('click', function() {
    if (!validateStep(currentStep)) return;
    if (currentStep === STEP_PRODUCT) goToStep(STEP_STOCKS);
    else if (currentStep === STEP_STOCKS) goToStep(STEP_AMOUNT);
  });

  // ─── Event: Prev ───
  prevBtn.addEventListener('click', function() {
    if (currentStep === STEP_AMOUNT) goToStep(STEP_STOCKS);
    else if (currentStep === STEP_STOCKS) goToStep(STEP_PRODUCT);
  });

  // ─── Form Submit ───
  const form = document.getElementById('productForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      stockIdsInput.value = JSON.stringify(selectedStocks.map(s => s.id));
      e.preventDefault();
      form.querySelectorAll('.input-skeleton').forEach(function(el) { el.classList.add('is-loading'); });
      const btn = form.querySelector('.btn-loading');
      if (btn) { btn.classList.add('is-loading'); btn.disabled = true; }
      requestAnimationFrame(function() { setTimeout(function() { form.submit(); }, 400); });
    });
  }

  // ─── Init ───
  stockIdsInput.value = JSON.stringify(selectedStocks.map(s => s.id));
});
</script>
@endpush
