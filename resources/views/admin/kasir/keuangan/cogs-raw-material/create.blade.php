@extends('admin.layouts.app')

@section('title', 'Tambah Bahan Mentah')

@php $activeMenu = 'cogs-raw-material' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Tambah Bahan Mentah</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.keuangan.cogs-raw-material.index') }}">Bahan Mentah</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Tambah</span>
    </div>
  </div>
</div>

<div class="card" style="max-width: 720px;">
  <div class="card-header-flex">
    <h6>Form Tambah Bahan Mentah</h6>
  </div>
  <div class="card-body p-4">
    <form action="{{ route('admin.keuangan.cogs-raw-material.store') }}" method="POST" id="rawMaterialForm">
      @csrf
      
      <div class="mb-3">
        <label for="name" class="form-label-modern">Nama Bahan Mentah <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control-modern @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Daging Ayam Utuh, Minyak Goreng, Beras" required>
        @error('name')
          <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
        @enderror
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label for="unit" class="form-label-modern">Satuan Unit <span class="text-danger">*</span></label>
          <select name="unit" id="unit" class="form-select-modern @error('unit') is-invalid @enderror" required>
            <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
            <option value="gr" {{ old('unit') == 'gr' ? 'selected' : '' }}>Gram (gr)</option>
            <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter (l)</option>
            <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>MiliLiter (ml)</option>
            <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pcs / Buah</option>
            <option value="butir" {{ old('unit') == 'butir' ? 'selected' : '' }}>Butir</option>
          </select>
          @error('unit')
            <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="amount" class="form-label-modern">Stok Fisik Awal <span class="text-danger">*</span></label>
          <input type="number" step="0.0001" name="amount" id="amount" class="form-control-modern @error('amount') is-invalid @enderror" value="{{ old('amount', 0) }}" required>
          @error('amount')
            <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label for="price_per_unit" class="form-label-modern">Harga Beli Per Unit (Rp) <span class="text-danger">*</span></label>
          <input type="number" step="0.01" name="price_per_unit" id="price_per_unit" class="form-control-modern @error('price_per_unit') is-invalid @enderror" value="{{ old('price_per_unit') }}" placeholder="40000" required>
          @error('price_per_unit')
            <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="loss_percent" class="form-label-modern">Persentase Susut / Loss (%)</label>
          <input type="number" step="0.1" name="loss_percent" id="loss_percent" class="form-control-modern @error('loss_percent') is-invalid @enderror" value="{{ old('loss_percent', 0) }}" placeholder="20">
          <span class="text-muted-c d-block mt-1" style="font-size: 0.78rem;">Persentase terbuang saat dibersihkan/dipotong.</span>
          @error('loss_percent')
            <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="mb-3">
        <label for="min_amount" class="form-label-modern">Stok Minimal Alert</label>
        <input type="number" step="0.0001" name="min_amount" id="min_amount" class="form-control-modern @error('min_amount') is-invalid @enderror" value="{{ old('min_amount', 0) }}" placeholder="Contoh: 5">
        <span class="text-muted-c d-block mt-1" style="font-size: 0.78rem;">Tampilkan peringatan jika stok di bawah nilai ini.</span>
      </div>

      <div class="mb-4">
        <label for="notes" class="form-label-modern">Catatan Opsional</label>
        <textarea name="notes" id="notes" rows="2" class="form-control-modern" placeholder="Catatan tambahan..."></textarea>
      </div>

      <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('admin.keuangan.cogs-raw-material.index') }}" class="btn btn-outline-soft px-4">Batal</a>
        <button type="submit" class="btn btn-primary-grad px-4 btn-loading" id="btnSubmit">Simpan Bahan Mentah</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
$('#rawMaterialForm').on('submit', function() {
  const btn = $('#btnSubmit');
  btn.addClass('disabled');
  setTimeout(() => { btn.removeClass('disabled'); }, 400);
});
</script>
@endpush
