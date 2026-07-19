@extends('admin.layouts.app')

@section('title', 'Edit Kategori')

@php $activeMenu = 'category' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Edit Kategori</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.category.index') }}">Kategori</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Edit</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex"><h6><i class="bi bi-tags-fill me-2"></i>Informasi Kategori</h6></div>
  <div class="card-body">
    <form action="{{ route('admin.category.update', $category) }}" method="POST" class="form-submit-loading" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label-modern">Perusahaan</label>
          <div class="input-skeleton">
            <select name="company_id" class="form-select-modern">
              <option value="">-- Pilih Perusahaan --</option>
              @foreach($companies as $c)
                <option value="{{ $c->company_id }}" {{ old('company_id', $category->company_id) == $c->company_id ? 'selected' : '' }}>{{ $c->company_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Nama Kategori <span class="text-danger">*</span></label>
          <div class="input-skeleton">
            <input type="text" name="category_name" class="form-control-modern @error('category_name') is-invalid @enderror" value="{{ old('category_name', $category->category_name) }}" placeholder="Nama kategori">
            @error('category_name')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Tipe</label>
          <div class="input-skeleton">
            <input type="text" name="category_type" class="form-control-modern" value="{{ old('category_type', $category->category_type) }}" placeholder="misal: makanan, minuman">
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label-modern">Deskripsi</label>
          <div class="input-skeleton">
            <textarea name="category_description" class="form-control-modern" rows="3" placeholder="Deskripsi kategori">{{ old('category_description', $category->category_description) }}</textarea>
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Status</label>
          <div class="input-skeleton">
            <select name="category_status" class="form-select-modern">
              <option value="1" {{ old('category_status', $category->category_status) == '1' ? 'selected' : '' }}>Aktif</option>
              <option value="0" {{ old('category_status', $category->category_status) === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Remark</label>
          <div class="input-skeleton">
            <input type="text" name="category_remark" class="form-control-modern" value="{{ old('category_remark', $category->category_remark) }}" placeholder="Catatan">
          </div>
        </div>

        {{-- Image Upload --}}
        <div class="col-12">
          <label class="form-label-modern">Gambar Kategori</label>
          <div class="input-skeleton">
            <div class="image-upload-wrapper">
              <div class="image-upload-preview {{ $category->category_image ? 'has-image' : '' }}" id="imagePreview">
                @if($category->category_image)
                  <img src="{{ asset('storage/' . $category->category_image) }}" alt="{{ $category->category_name }}">
                @else
                  <i class="bi bi-image" style="font-size:2.5rem; color:var(--text-muted);"></i>
                  <span class="text-muted-c" style="font-size:0.85rem;">Belum ada gambar</span>
                @endif
              </div>
              <div class="image-upload-actions">
                <label class="btn btn-outline-soft" for="categoryImageInput">
                  <i class="bi bi-upload me-1"></i>Ganti Gambar
                </label>
                <button type="button" class="btn btn-ghost btn-sm text-danger" id="removeImageBtn" style="{{ $category->category_image ? '' : 'display:none;' }}">
                  <i class="bi bi-trash3 me-1"></i>Hapus
                </button>
                <div class="text-muted-c mt-1" style="font-size:0.75rem;">
                  Format: JPEG, PNG, WebP, SVG. Maks 2MB.
                </div>
              </div>
              <input type="file" name="category_image" id="categoryImageInput"
                     accept="image/jpeg,image/png,image/webp,image/svg+xml"
                     style="display:none;">
            </div>
            @error('category_image')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary-grad btn-loading">Simpan Perubahan</button>
          <a href="{{ route('admin.category.index') }}" class="btn btn-outline-soft">Batal</a>
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
  const fileInput = document.getElementById('categoryImageInput');
  const preview = document.getElementById('imagePreview');
  const removeBtn = document.getElementById('removeImageBtn');

  if (!fileInput) return;

  // Preview saat pilih gambar
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

  // Hapus gambar
  removeBtn.addEventListener('click', function() {
    fileInput.value = '';
    preview.innerHTML = '<i class="bi bi-image" style="font-size:2.5rem; color:var(--text-muted);"></i><span class="text-muted-c" style="font-size:0.85rem;">Belum ada gambar</span>';
    preview.classList.remove('has-image');
    removeBtn.style.display = 'none';
  });

  // Form submit delay + shimmer
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
