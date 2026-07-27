@extends('admin.layouts.app')

@section('title', 'Tambah Voucher')

@php $activeMenu = 'voucher' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Tambah Voucher</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.voucher.index') }}">Voucher</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Tambah</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex"><h6><i class="bi bi-ticket-perforated me-2"></i>Informasi Voucher</h6></div>
  <div class="card-body">
    <form action="{{ route('admin.voucher.store') }}" method="POST" class="form-submit-loading">
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
          <label class="form-label-modern">Kode Voucher <span class="text-danger">*</span></label>
          <div class="input-skeleton">
            <input type="text" name="voucher_code" class="form-control-modern @error('voucher_code') is-invalid @enderror" value="{{ old('voucher_code') }}" placeholder="DISC10, BDAY15">
            @error('voucher_code')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Nama Voucher <span class="text-danger">*</span></label>
          <div class="input-skeleton">
            <input type="text" name="voucher_name" class="form-control-modern @error('voucher_name') is-invalid @enderror" value="{{ old('voucher_name') }}" placeholder="Diskon 10%">
            @error('voucher_name')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Tipe Voucher <span class="text-danger">*</span></label>
          <div class="input-skeleton">
            <select name="voucher_type" class="form-select-modern @error('voucher_type') is-invalid @enderror">
              <option value="">-- Pilih Tipe --</option>
              <option value="percentage" {{ old('voucher_type') == 'percentage' ? 'selected' : '' }}>Persen (%)</option>
              <option value="nominal" {{ old('voucher_type') == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
              <option value="free_item" {{ old('voucher_type') == 'free_item' ? 'selected' : '' }}>Free Item</option>
            </select>
            @error('voucher_type')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Nilai Voucher <span class="text-danger">*</span></label>
          <div class="input-skeleton">
            <input type="number" step="0.01" name="voucher_value" class="form-control-modern @error('voucher_value') is-invalid @enderror" value="{{ old('voucher_value') }}" placeholder="0">
            @error('voucher_value')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Maksimal Potongan (cap)</label>
          <div class="input-skeleton">
            <input type="number" step="0.01" name="voucher_max_discount" class="form-control-modern @error('voucher_max_discount') is-invalid @enderror" value="{{ old('voucher_max_discount') }}" placeholder="0">
            @error('voucher_max_discount')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Minimal Pembelian</label>
          <div class="input-skeleton">
            <input type="number" step="0.01" name="voucher_min_purchase" class="form-control-modern @error('voucher_min_purchase') is-invalid @enderror" value="{{ old('voucher_min_purchase') }}" placeholder="0">
            @error('voucher_min_purchase')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Penerapan</label>
          <div class="input-skeleton">
            <select name="voucher_applicable_to" class="form-select-modern">
              <option value="all" {{ old('voucher_applicable_to') == 'all' ? 'selected' : '' }}>Semua Produk</option>
              <option value="specific_products" {{ old('voucher_applicable_to') == 'specific_products' ? 'selected' : '' }}>Produk Tertentu</option>
              <option value="specific_categories" {{ old('voucher_applicable_to') == 'specific_categories' ? 'selected' : '' }}>Kategori Tertentu</option>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Batas Penggunaan</label>
          <div class="input-skeleton">
            <input type="number" min="0" name="voucher_usage_limit" class="form-control-modern @error('voucher_usage_limit') is-invalid @enderror" value="{{ old('voucher_usage_limit') }}" placeholder="0">
            @error('voucher_usage_limit')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Batas Per Pelanggan</label>
          <div class="input-skeleton">
            <input type="number" min="0" name="voucher_usage_per_customer" class="form-control-modern @error('voucher_usage_per_customer') is-invalid @enderror" value="{{ old('voucher_usage_per_customer') }}" placeholder="0">
            @error('voucher_usage_per_customer')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Status</label>
          <div class="input-skeleton">
            <select name="voucher_status" class="form-select-modern">
              <option value="1" {{ old('voucher_status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
              <option value="0" {{ old('voucher_status') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Tanggal Mulai</label>
          <div class="input-skeleton">
            <input type="datetime-local" name="voucher_start_date" class="form-control-modern @error('voucher_start_date') is-invalid @enderror" value="{{ old('voucher_start_date') }}">
            @error('voucher_start_date')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Tanggal Berakhir</label>
          <div class="input-skeleton">
            <input type="datetime-local" name="voucher_end_date" class="form-control-modern @error('voucher_end_date') is-invalid @enderror" value="{{ old('voucher_end_date') }}">
            @error('voucher_end_date')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="col-12">
          <label class="form-label-modern">Deskripsi</label>
          <div class="input-skeleton">
            <textarea name="voucher_description" class="form-control-modern" rows="3" placeholder="Deskripsi voucher">{{ old('voucher_description') }}</textarea>
          </div>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary-grad btn-loading">Simpan</button>
          <a href="{{ route('admin.voucher.index') }}" class="btn btn-outline-soft">Batal</a>
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
      setTimeout(function() {
        form.submit();
      }, 400);
    });
  });
});
</script>
@endpush
@endsection
