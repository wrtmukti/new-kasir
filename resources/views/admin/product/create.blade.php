@extends('admin.layouts.app')

@section('title', 'Tambah Produk')

@php $activeMenu = 'product' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Tambah Produk</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.product.index') }}">Produk</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Tambah</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex"><h6><i class="bi bi-cup-hot-fill me-2"></i>Informasi Produk</h6></div>
  <div class="card-body">
    <form action="{{ route('admin.product.store') }}" method="POST" class="form-submit-loading" enctype="multipart/form-data">
      @csrf
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
          <label class="form-label-modern">Kode Produk</label>
          <div class="input-skeleton">
            <input type="text" name="product_code" class="form-control-modern" value="{{ old('product_code') }}" placeholder="PRD-001">
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Kategori</label>
          <div class="input-skeleton">
            <select name="category_id" class="form-select-modern">
              <option value="">-- Pilih Kategori --</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->category_id }}" {{ old('category_id') == $cat->category_id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label-modern">Nama Produk <span class="text-danger">*</span></label>
          <div class="input-skeleton">
            <input type="text" name="product_name" class="form-control-modern @error('product_name') is-invalid @enderror" value="{{ old('product_name') }}" placeholder="Nama produk">
            @error('product_name')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label-modern">Harga</label>
          <div class="input-skeleton">
            <input type="number" name="product_price" class="form-control-modern" value="{{ old('product_price') }}" min="0" step="0.01" placeholder="0">
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label-modern">Deskripsi</label>
          <div class="input-skeleton">
            <textarea name="product_description" class="form-control-modern" rows="3" placeholder="Deskripsi produk">{{ old('product_description') }}</textarea>
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Status</label>
          <div class="input-skeleton">
            <select name="product_status" class="form-select-modern">
              <option value="1" {{ old('product_status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
              <option value="0" {{ old('product_status') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Remark</label>
          <div class="input-skeleton">
            <input type="text" name="category_remark" class="form-control-modern" value="{{ old('category_remark') }}" placeholder="Catatan">
          </div>
        </div>

        {{-- Image Upload --}}
        <div class="col-12">
          <label class="form-label-modern">Gambar Produk</label>
          <div class="input-skeleton">
            <div class="image-upload-wrapper">
              <div class="image-upload-preview" id="imagePreview">
                <i class="bi bi-image" style="font-size:2.5rem; color:var(--text-muted);"></i>
                <span class="text-muted-c" style="font-size:0.85rem;">Belum ada gambar</span>
              </div>
              <div class="image-upload-actions">
                <label class="btn btn-outline-soft" for="productImageInput">
                  <i class="bi bi-upload me-1"></i>Pilih Gambar
                </label>
                <button type="button" class="btn btn-ghost btn-sm text-danger" id="removeImageBtn" style="display:none;">
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

        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary-grad btn-loading">Simpan</button>
          <a href="{{ route('admin.product.index') }}" class="btn btn-outline-soft">Batal</a>
        </div>
      </div>
    </form>
  </div>
</div>

@push('styles')
<style>
.image-upload-wrapper {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  padding: 0.75rem 0;
}
.image-upload-preview {
  width: 100px;
  height: 100px;
  border-radius: var(--radius-md);
  border: 2px dashed var(--border-subtle);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.25rem;
  flex-shrink: 0;
  overflow: hidden;
  background: var(--bg-elevated);
  transition: border-color 0.2s, background 0.2s;
}
.image-upload-preview.has-image {
  border-style: solid;
  border-color: var(--border-subtle);
}
.image-upload-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.image-upload-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const fileInput = document.getElementById('productImageInput');
  const preview = document.getElementById('imagePreview');
  const removeBtn = document.getElementById('removeImageBtn');

  if (!fileInput) return;

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
    preview.innerHTML = '<i class="bi bi-image" style="font-size:2.5rem; color:var(--text-muted);"></i><span class="text-muted-c" style="font-size:0.85rem;">Belum ada gambar</span>';
    preview.classList.remove('has-image');
    removeBtn.style.display = 'none';
  });

  const form = document.querySelector('.form-submit-loading');
  if (!form) return;
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    form.querySelectorAll('.input-skeleton').forEach(function(el) {
      el.classList.add('is-loading');
    });
    const btn = form.querySelector('.btn-loading');
    if (btn) {
      btn.classList.add('is-loading');
      btn.disabled = true;
    }
    requestAnimationFrame(function() {
      setTimeout(function() {
        form.submit();
      }, 400);
    });
  });
});
</script>
@endpush
@endsection
