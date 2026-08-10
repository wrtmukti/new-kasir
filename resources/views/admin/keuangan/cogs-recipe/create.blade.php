@extends('admin.layouts.app')

@section('title', 'Buat Resep Standar HPP')

@php $activeMenu = 'cogs-recipe' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Buat Resep Standar & COGS Menu</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.keuangan.cogs-recipe.index') }}">Resep & COGS Menu</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Buat Resep</span>
    </div>
  </div>
</div>

<form action="{{ route('admin.keuangan.cogs-recipe.store') }}" method="POST" id="recipeForm">
  @csrf
  <div class="row g-4">
    <div class="col-md-5">
      <div class="card">
        <div class="card-header-flex">
          <h6>Informasi Utama Resep</h6>
        </div>
        <div class="card-body p-4">
          <div class="mb-3">
            <label for="recipe_name" class="form-label-modern">Nama Resep Standar <span class="text-danger">*</span></label>
            <input type="text" name="recipe_name" id="recipe_name" class="form-control-modern @error('recipe_name') is-invalid @enderror" value="{{ old('recipe_name') }}" placeholder="Contoh: Resep Standar Nasi Ayam Goreng" required>
            @error('recipe_name')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label for="product_id" class="form-label-modern">Hubungkan ke Menu Kasir (Opsional)</label>
            <select name="product_id" id="product_id" class="form-select-modern">
              <option value="">-- Pilih Menu Kasir (Opsional) --</option>
              @foreach($products as $prod)
                <option value="{{ $prod->product_id }}" {{ old('product_id') == $prod->product_id ? 'selected' : '' }}>
                  {{ $prod->product_name }} (Rp {{ number_format($prod->product_price) }})
                </option>
              @endforeach
            </select>
            <span class="text-muted-c d-block mt-1" style="font-size: 0.78rem;">Pilih jika resep ini terikat dengan menu yang dijual di kasir.</span>
          </div>

          <div class="mb-3">
            <label for="target_food_cost" class="form-label-modern">Target Food Cost % <span class="text-danger">*</span></label>
            <input type="number" step="0.1" name="target_food_cost" id="target_food_cost" class="form-control-modern @error('target_food_cost') is-invalid @enderror" value="{{ old('target_food_cost', 30) }}" required>
            <span class="text-muted-c d-block mt-1" style="font-size: 0.78rem;">Standar industri F&B biasanya 30% - 35%.</span>
            @error('target_food_cost')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label for="notes" class="form-label-modern">Catatan Resep</label>
            <textarea name="notes" id="notes" rows="3" class="form-control-modern" placeholder="Catatan instruksi resep..."></textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-7">
      <div class="card">
        <div class="card-header-flex">
          <h6>Bahan Penyusun Resep</h6>
          <button type="button" class="btn btn-sm btn-ghost" id="btnAddRow"><i class="bi bi-plus-lg me-1"></i>Tambah Bahan</button>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive mb-3">
            <table class="table-modern" id="recipeItemsTable">
              <thead>
                <tr>
                  <th>Bahan Mentah COGS</th>
                  <th style="width: 150px;">Takaran Qty</th>
                  <th style="width: 60px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr class="item-row">
                  <td>
                    <select name="items[0][cogs_raw_material_id]" class="form-select-modern select-material" required>
                      <option value="">-- Pilih Bahan Mentah --</option>
                      @foreach($rawMaterials as $raw)
                        <option value="{{ $raw->cogs_raw_material_id }}" data-price="{{ $raw->effective_price }}" data-unit="{{ $raw->unit }}">
                          {{ $raw->name }} (Rp {{ number_format($raw->effective_price, 2) }}/{{ $raw->unit }})
                        </option>
                      @endforeach
                    </select>
                  </td>
                  <td>
                    <div class="input-group">
                      <input type="number" step="0.0001" name="items[0][ingredient_qty]" class="form-control-modern input-qty" placeholder="0" required>
                      <span class="input-group-text bg-transparent unit-label" style="border-color: var(--border-subtle); color: var(--text-muted); font-size:0.8rem;">-</span>
                    </div>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-ghost btn-sm text-danger btn-remove-row"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.keuangan.cogs-recipe.index') }}" class="btn btn-outline-soft px-4">Batal</a>
            <button type="submit" class="btn btn-primary-grad px-4 btn-loading" id="btnSubmit">Simpan Resep Standar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  let rowIdx = 1;
  const btnAddRow = document.getElementById('btnAddRow');
  const tbody = document.querySelector('#recipeItemsTable tbody');

  if (btnAddRow && tbody) {
    btnAddRow.addEventListener('click', function() {
      const rawOptions = `@foreach($rawMaterials as $raw)<option value="{{ $raw->cogs_raw_material_id }}" data-price="{{ $raw->effective_price }}" data-unit="{{ $raw->unit }}">{{ $raw->name }} (Rp {{ number_format($raw->effective_price, 2) }}/{{ $raw->unit }})</option>@endforeach`;
      
      const newTr = document.createElement('tr');
      newTr.className = 'item-row';
      newTr.innerHTML = `
        <td>
          <select name="items[${rowIdx}][cogs_raw_material_id]" class="form-select-modern select-material" required>
            <option value="">-- Pilih Bahan Mentah --</option>
            ${rawOptions}
          </select>
        </td>
        <td>
          <div class="input-group">
            <input type="number" step="0.0001" name="items[${rowIdx}][ingredient_qty]" class="form-control-modern input-qty" placeholder="0" required>
            <span class="input-group-text bg-transparent unit-label" style="border-color: var(--border-subtle); color: var(--text-muted); font-size:0.8rem;">-</span>
          </div>
        </td>
        <td class="text-center">
          <button type="button" class="btn btn-ghost btn-sm text-danger btn-remove-row"><i class="bi bi-trash"></i></button>
        </td>
      `;
      tbody.appendChild(newTr);
      rowIdx++;
    });

    tbody.addEventListener('click', function(e) {
      const removeBtn = e.target.closest('.btn-remove-row');
      if (removeBtn) {
        if (tbody.querySelectorAll('tr.item-row').length > 1) {
          removeBtn.closest('tr').remove();
        } else {
          alert('Minimal 1 bahan dalam resep.');
        }
      }
    });

    tbody.addEventListener('change', function(e) {
      if (e.target.classList.contains('select-material')) {
        const opt = e.target.options[e.target.selectedIndex];
        const unit = opt ? opt.dataset.unit || '-' : '-';
        const tr = e.target.closest('tr');
        if (tr) {
          const unitLabel = tr.querySelector('.unit-label');
          if (unitLabel) unitLabel.textContent = unit;
        }
      }
    });
  }

  const form = document.getElementById('recipeForm');
  if (form) {
    form.addEventListener('submit', function() {
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
