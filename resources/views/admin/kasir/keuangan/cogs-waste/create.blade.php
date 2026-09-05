@extends('admin.layouts.app')

@section('title', 'Catat Bahan Terbuang (Waste Log)')

@php $activeMenu = 'cogs-waste' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Catat Bahan Terbuang / Busuk (Waste Log)</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.keuangan.cogs-waste.index') }}">Waste Log</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Catat</span>
    </div>
  </div>
</div>

<div class="card" style="max-width: 720px;">
  <div class="card-header-flex">
    <h6>Form Catat Bahan Terbuang</h6>
  </div>
  <div class="card-body p-4">
    <form action="{{ route('admin.keuangan.cogs-waste.store') }}" method="POST" id="wasteForm">
      @csrf
      
      <div class="mb-3">
        <label for="cogs_raw_material_id" class="form-label-modern">Pilih Bahan Mentah <span class="text-danger">*</span></label>
        <select name="cogs_raw_material_id" id="cogs_raw_material_id" class="form-select-modern @error('cogs_raw_material_id') is-invalid @enderror" required>
          <option value="">-- Pilih Bahan Mentah --</option>
          @foreach($rawMaterials as $raw)
            <option value="{{ $raw->cogs_raw_material_id }}" data-price="{{ $raw->effective_price }}" data-unit="{{ $raw->unit }}">
              {{ $raw->name }} (Stok: {{ number_format($raw->amount, 2) }} {{ $raw->unit }} | Rp {{ number_format($raw->effective_price, 2) }}/{{ $raw->unit }})
            </option>
          @endforeach
        </select>
        @error('cogs_raw_material_id')
          <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
        @enderror
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label for="qty_lost" class="form-label-modern">Jumlah Terbuang <span class="text-danger">*</span></label>
          <div class="input-group">
            <input type="number" step="0.0001" name="qty_lost" id="qty_lost" class="form-control-modern @error('qty_lost') is-invalid @enderror" value="{{ old('qty_lost') }}" placeholder="2" required>
            <span class="input-group-text bg-transparent unit-label" style="border-color: var(--border-subtle); color: var(--text-muted); font-size:0.85rem;">-</span>
          </div>
          @error('qty_lost')
            <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="reason" class="form-label-modern">Alasan Waste <span class="text-danger">*</span></label>
          <select name="reason" id="reason" class="form-select-modern @error('reason') is-invalid @enderror" required>
            <option value="Basi/Rotten" {{ old('reason') == 'Basi/Rotten' ? 'selected' : '' }}>Basi / Busuk (Rotten)</option>
            <option value="Expired" {{ old('reason') == 'Expired' ? 'selected' : '' }}>Kadaluarsa (Expired)</option>
            <option value="Tumpah/Rusak" {{ old('reason') == 'Tumpah/Rusak' ? 'selected' : '' }}>Tumpah / Rusak</option>
            <option value="Trial/Gagal Masak" {{ old('reason') == 'Trial/Gagal Masak' ? 'selected' : '' }}>Gagal Masak / Trial</option>
          </select>
          @error('reason')
            <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="mb-3">
        <label for="loss_date" class="form-label-modern">Tanggal Kejadiaan <span class="text-danger">*</span></label>
        <input type="date" name="loss_date" id="loss_date" class="form-control-modern @error('loss_date') is-invalid @enderror" value="{{ old('loss_date', date('Y-m-d')) }}" required>
        @error('loss_date')
          <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
        @enderror
      </div>

      <div class="mb-4">
        <label for="notes" class="form-label-modern">Catatan Opsional</label>
        <textarea name="notes" id="notes" rows="2" class="form-control-modern" placeholder="Detail kronologi terbuangnya bahan..."></textarea>
      </div>

      <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('admin.keuangan.cogs-waste.index') }}" class="btn btn-outline-soft px-4">Batal</a>
        <button type="submit" class="btn btn-primary-grad px-4 btn-loading" id="btnSubmit">Catat Waste Log</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const selectMat = document.getElementById('cogs_raw_material_id');
  if (selectMat) {
    selectMat.addEventListener('change', function() {
      const opt = this.options[this.selectedIndex];
      const unit = opt ? opt.dataset.unit || '-' : '-';
      document.querySelectorAll('.unit-label').forEach(el => el.textContent = unit);
    });
  }

  const wasteForm = document.getElementById('wasteForm');
  if (wasteForm) {
    wasteForm.addEventListener('submit', function() {
      const btn = document.getElementById('btnSubmit');
      if (btn) {
        btn.classList.add('disabled');
        setTimeout(() => { btn.classList.remove('disabled'); }, 400);
      }
    });
  }
});
</script>
@endpush
