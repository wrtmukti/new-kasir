@extends('admin.layouts.app')

@section('title', 'Tambah Stok')

@php $activeMenu = 'stock' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Tambah Stok</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.stock.index') }}">Stok</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Tambah</span>
    </div>
  </div>
</div>

<form action="{{ route('admin.stock.store') }}" method="POST" class="form-submit-loading">
  @csrf
  <div class="card mb-4">
    <div class="card-header-flex"><h6><i class="bi bi-box-seam me-2"></i>Informasi Stok</h6></div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label-modern">Perusahaan</label>
          <div class="input-skeleton">
            <select name="outlet_id" class="form-select-modern">
              <option value="">-- Pilih Perusahaan --</option>
              @foreach($outlets as $c)
                <option value="{{ $c->outlet_id }}" {{ old('outlet_id') == $c->outlet_id ? 'selected' : '' }}>{{ $c->outlet_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Kode Stok</label>
          <div class="input-skeleton">
            <input type="text" name="stock_code" class="form-control-modern" value="{{ old('stock_code') }}" placeholder="STK-001">
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Nama Stok <span class="text-danger">*</span></label>
          <div class="input-skeleton">
            <input type="text" name="stock_name" id="stock_name" class="form-control-modern @error('stock_name') is-invalid @enderror" value="{{ old('stock_name') }}" placeholder="Nama stok produk">
            @error('stock_name')<span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>@enderror
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label-modern">Deskripsi</label>
          <div class="input-skeleton">
            <textarea name="stock_description" class="form-control-modern" rows="3" placeholder="Deskripsi">{{ old('stock_description') }}</textarea>
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Tipe</label>
          <div class="input-skeleton">
            <input type="text" name="stock_type" class="form-control-modern" value="{{ old('stock_type') }}" placeholder="bahan baku">
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Unit</label>
          <div class="input-skeleton">
            <select name="stock_unit" id="stock_unit" class="form-select-modern">
              <option value="">-- Pilih --</option>
              <option value="pcs" {{ old('stock_unit') == 'pcs' ? 'selected' : '' }}>Pcs</option>
              <option value="kg" {{ old('stock_unit') == 'kg' ? 'selected' : '' }}>Kg</option>
              <option value="gr" {{ old('stock_unit') == 'gr' ? 'selected' : '' }}>Gr</option>
              <option value="liter" {{ old('stock_unit') == 'liter' ? 'selected' : '' }}>Liter</option>
              <option value="ml" {{ old('stock_unit') == 'ml' ? 'selected' : '' }}>Ml</option>
              <option value="box" {{ old('stock_unit') == 'box' ? 'selected' : '' }}>Box</option>
              <option value="pack" {{ old('stock_unit') == 'pack' ? 'selected' : '' }}>Pack</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Jumlah Stok (Qty Dibuat)</label>
          <div class="input-skeleton">
            <input type="number" name="stock_amount" id="stock_amount" class="form-control-modern" value="{{ old('stock_amount', 0) }}" min="0">
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Harga Satuan</label>
          <div class="input-skeleton">
            <input type="number" name="stock_price" class="form-control-modern" value="{{ old('stock_price') }}" min="0" step="0.01" placeholder="0">
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Status</label>
          <div class="input-skeleton">
            <select name="stock_status" class="form-select-modern">
              <option value="1" {{ old('stock_status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
              <option value="0" {{ old('stock_status') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Optional Raw Material Deduction Section -->
  <div class="card mb-4">
    <div class="card-header-flex">
      <h6><i class="bi bi-diagram-3 me-2 text-warning"></i>Potong Stok Bahan Mentah (Raw Stock) — Opsional</h6>
    </div>
    <div class="card-body">
      <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" name="deduct_raw_material" id="deduct_raw_material" value="1" {{ old('deduct_raw_material') ? 'checked' : '' }}>
        <label class="form-check-label fw-bold" for="deduct_raw_material" style="color: var(--text-primary);">Potong Stok Bahan Mentah COGS Otomatis Saat Membuat Stok Ini</label>
      </div>

      <div id="rawDeductionFields" style="display: none;">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label-modern">Pilih Bahan Mentah (Raw Material)</label>
            <select name="cogs_raw_material_id" id="cogs_raw_material_id" class="form-select-modern">
              <option value="">-- Pilih Bahan Mentah --</option>
              @foreach($cogsRawMaterials as $raw)
                <option value="{{ $raw->cogs_raw_material_id }}" data-name="{{ $raw->name }}" data-unit="{{ $raw->unit }}" data-amount="{{ $raw->amount }}">
                  {{ $raw->name }} (Stok Fisik: {{ number_format($raw->amount, 2) }} {{ $raw->unit }})
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label-modern">Takaran Bahan Mentah Per 1 Unit Stok</label>
            <div class="input-group">
              <input type="number" step="0.0001" name="raw_qty_per_unit" id="raw_qty_per_unit" class="form-control-modern" value="{{ old('raw_qty_per_unit', 0) }}" placeholder="Contoh: 0.2 (untuk 200gr per 1 unit)">
              <span class="input-group-text bg-transparent rawUnitLabel" style="border-color: var(--border-subtle); color: var(--text-muted); font-size:0.85rem;">-</span>
            </div>
            <span class="text-muted-c d-block mt-1" style="font-size: 0.78rem;">
              Contoh: Bikin 5 pcs Steak @200gr (0.2 kg), isi `0.2`. Total bahan terpakai = 0.2 kg x 5 pcs = 1 kg.
            </span>
          </div>
        </div>

        <div class="alert alert-info border-0 mt-3 mb-0" style="background: var(--info-bg); color: var(--info);" id="deductionPreview">
          <i class="bi bi-info-circle me-2"></i>Pilih bahan mentah dan masukkan takaran per unit untuk melihat simulasi kalkulasi pemotongan stok.
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary-grad btn-loading" id="btnSubmit">Simpan Stok</button>
    <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-soft">Batal</a>
  </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const deductCheckbox = document.getElementById('deduct_raw_material');
  const rawFields = document.getElementById('rawDeductionFields');
  const selectRaw = document.getElementById('cogs_raw_material_id');
  const inputQtyPerUnit = document.getElementById('raw_qty_per_unit');
  const inputStockAmount = document.getElementById('stock_amount');
  const previewEl = document.getElementById('deductionPreview');

  function toggleFields() {
    if (deductCheckbox && rawFields) {
      rawFields.style.display = deductCheckbox.checked ? 'block' : 'none';
      updatePreview();
    }
  }

  function updatePreview() {
    if (!deductCheckbox || !deductCheckbox.checked) return;

    const selectedOpt = selectRaw ? selectRaw.options[selectRaw.selectedIndex] : null;
    const rawName = selectedOpt ? selectedOpt.dataset.name || '-' : '-';
    const rawUnit = selectedOpt ? selectedOpt.dataset.unit || '-' : '-';
    const currentRawStock = selectedOpt ? parseFloat(selectedOpt.dataset.amount || 0) : 0;

    const qtyPerUnit = inputQtyPerUnit ? parseFloat(inputQtyPerUnit.value || 0) : 0;
    const stockQty = inputStockAmount ? parseFloat(inputStockAmount.value || 0) : 0;
    const totalDeducted = qtyPerUnit * stockQty;

    document.querySelectorAll('.rawUnitLabel').forEach(el => el.textContent = rawUnit);

    if (selectedOpt && selectedOpt.value && qtyPerUnit > 0 && stockQty > 0) {
      const remaining = currentRawStock - totalDeducted;
      let warningHtml = '';
      if (remaining < 0) {
        warningHtml = `<strong class="text-danger ms-2"><i class="bi bi-exclamation-triangle-fill me-1"></i>Peringatan: Stok bahan mentah tidak mencukupi (Kurang ${Math.abs(remaining).toFixed(2)} ${rawUnit})!</strong>`;
      }
      previewEl.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i>Kalkulasi: Pembuatan <strong>${stockQty} unit stok</strong> x <strong>${qtyPerUnit} ${rawUnit}</strong> = Akan memotong <strong>${totalDeducted.toFixed(2)} ${rawUnit} ${rawName}</strong>. (Sisa stok bahan: ${remaining.toFixed(2)} ${rawUnit})${warningHtml}`;
    } else {
      previewEl.innerHTML = `<i class="bi bi-info-circle me-2"></i>Pilih bahan mentah dan masukkan takaran per unit untuk melihat simulasi kalkulasi pemotongan stok.`;
    }
  }

  if (deductCheckbox) {
    deductCheckbox.addEventListener('change', toggleFields);
  }
  if (selectRaw) {
    selectRaw.addEventListener('change', updatePreview);
  }
  if (inputQtyPerUnit) {
    inputQtyPerUnit.addEventListener('input', updatePreview);
  }
  if (inputStockAmount) {
    inputStockAmount.addEventListener('input', updatePreview);
  }

  toggleFields();

  const form = document.querySelector('.form-submit-loading');
  if (form) {
    form.addEventListener('submit', function(e) {
      form.querySelectorAll('.input-skeleton').forEach(function(el) {
        el.classList.add('is-loading');
      });
      const btn = document.getElementById('btnSubmit');
      if (btn) {
        btn.classList.add('is-loading');
        btn.disabled = true;
      }
    });
  }
});
</script>
@endpush
@endsection
