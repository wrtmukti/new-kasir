@extends('admin.layouts.app')

@section('title', 'Tambah Supplier')

@php $activeMenu = 'supplier' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Tambah Supplier</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.supplier.index') }}">Supplier</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Tambah</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex"><h6><i class="bi bi-truck me-2"></i>Informasi Supplier</h6></div>
  <div class="card-body">
    <form action="{{ route('admin.supplier.store') }}" method="POST" class="form-submit-loading">
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
          <label class="form-label-modern">Kode Supplier</label>
          <div class="input-skeleton">
            <input type="text" name="supplier_code" class="form-control-modern" value="{{ old('supplier_code') }}" placeholder="SUP-001">
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Nama Supplier <span class="text-danger">*</span></label>
          <div class="input-skeleton">
            <input type="text" name="supplier_name" class="form-control-modern @error('supplier_name') is-invalid @enderror" value="{{ old('supplier_name') }}" placeholder="Nama supplier">
            @error('supplier_name')<span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>@enderror
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Kontak Person</label>
          <div class="input-skeleton">
            <input type="text" name="supplier_contact" class="form-control-modern" value="{{ old('supplier_contact') }}" placeholder="Contact person">
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Telepon</label>
          <div class="input-skeleton">
            <input type="text" name="supplier_phone" class="form-control-modern" value="{{ old('supplier_phone') }}" placeholder="021-xxxxxxx">
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Status</label>
          <div class="input-skeleton">
            <select name="supplier_status" class="form-select-modern">
              <option value="1" {{ old('supplier_status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
              <option value="0" {{ old('supplier_status') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="col-12">
          <label class="form-label-modern">Alamat</label>
          <div class="input-skeleton">
            <textarea name="supplier_address" class="form-control-modern" rows="3" placeholder="Alamat lengkap">{{ old('supplier_address') }}</textarea>
          </div>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary-grad btn-loading">Simpan</button>
          <a href="{{ route('admin.supplier.index') }}" class="btn btn-outline-soft">Batal</a>
        </div>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
      setTimeout(function() { form.submit(); }, 400);
    });
  });
});
</script>
@endpush
@endsection
