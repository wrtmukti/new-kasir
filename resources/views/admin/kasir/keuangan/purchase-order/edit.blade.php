@extends('admin.layouts.app')

@section('title', 'Edit Purchase Order Bahan Mentah')

@php $activeMenu = 'purchase-order' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Edit Purchase Order {{ $order->po_code }}</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.keuangan.purchase-order.index') }}">Purchase Order</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Edit</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex"><h6><i class="bi bi-pencil-square me-2"></i>Edit PO {{ $order->po_code }}</h6></div>
  <div class="card-body">
    <form action="{{ route('admin.keuangan.purchase-order.update', $order) }}" method="POST" id="poForm" class="form-submit-loading">
      @csrf
      @method('PUT')
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label-modern">Supplier <span class="text-danger">*</span></label>
          <div class="input-skeleton">
            <select name="supplier_id" class="form-select-modern @error('supplier_id') is-invalid @enderror">
              <option value="">-- Pilih Supplier --</option>
              @foreach($suppliers as $s)
                <option value="{{ $s->supplier_id }}" {{ old('supplier_id', $order->supplier_id) == $s->supplier_id ? 'selected' : '' }}>{{ $s->supplier_name }}</option>
              @endforeach
            </select>
            @error('supplier_id')<span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>@enderror
          </div>
        </div>
      </div>

      <div class="card-header-flex mb-3 px-0 border-0">
        <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Item Bahan Mentah (`cogs_raw_materials`)</h6>
        <button type="button" class="btn btn-outline-soft btn-sm" id="addItemRow">
          <i class="bi bi-plus-lg me-1"></i>Tambah Bahan Mentah
        </button>
      </div>
      @error('items')<div class="text-danger mb-2" style="font-size:0.8rem;">{{ $message }}</div>@enderror

      <div class="table-responsive">
        <table class="table-modern" id="itemsTable">
          <thead>
            <tr>
              <th style="width:35%;">Bahan Mentah <span class="text-danger">*</span></th>
              <th style="width:15%;">Qty <span class="text-danger">*</span></th>
              <th style="width:20%;">Harga Beli / Unit <span class="text-danger">*</span></th>
              <th style="width:20%;">Subtotal</th>
              <th style="width:10%;"></th>
            </tr>
          </thead>
          <tbody id="itemsBody">
            @php $oldOrExisting = old('items', $order->items->toArray()); @endphp
            @foreach($oldOrExisting as $i => $item)
            <tr class="item-row">
              <td>
                <select name="items[{{ $i }}][cogs_raw_material_id]" class="form-select-modern form-select-sm raw-mat-select">
                  <option value="">-- Pilih Bahan Mentah --</option>
                  @foreach($rawMaterials as $rm)
                    <option value="{{ $rm->cogs_raw_material_id }}" data-price="{{ $rm->price_per_unit }}" {{ ($item['cogs_raw_material_id'] ?? '') == $rm->cogs_raw_material_id ? 'selected' : '' }}>
                      {{ $rm->name }} ({{ $rm->unit }}) - Rp {{ number_format($rm->price_per_unit, 0, ',', '.') }}
                    </option>
                  @endforeach
                </select>
                @error("items.{$i}.cogs_raw_material_id")<div class="text-danger" style="font-size:0.75rem;">{{ $message }}</div>@enderror
              </td>
              <td><input type="number" name="items[{{ $i }}][qty]" class="form-control-modern form-control-sm item-qty" value="{{ $item['qty'] }}" min="1"></td>
              <td><input type="number" name="items[{{ $i }}][price]" class="form-control-modern form-control-sm item-price" value="{{ $item['price'] }}" min="0" step="100"></td>
              <td><span class="item-subtotal text-mono">Rp 0</span></td>
              <td><button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger remove-item" style="display:{{ $loop->first && count($oldOrExisting) == 1 ? 'none' : '' }};"><i class="bi bi-x-lg"></i></button></td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="fw-bold">
              <td colspan="3" class="text-end">Grand Total</td>
              <td><span id="grandTotal" class="text-mono">Rp 0</span></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="row mt-3">
        <div class="col-12">
          <label class="form-label-modern">Catatan PO</label>
          <div class="input-skeleton">
            <textarea name="po_notes" class="form-control-modern" rows="2">{{ old('po_notes', $order->po_notes) }}</textarea>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary-grad btn-loading">Update PO Bahan Mentah</button>
        <a href="{{ route('admin.keuangan.purchase-order.show', $order) }}" class="btn btn-outline-soft">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  let rowIndex = {{ count($oldOrExisting) }};

  function calcRow(row) {
    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const sub = qty * price;
    row.querySelector('.item-subtotal').textContent = 'Rp ' + Math.round(sub).toLocaleString('id-ID');
    calcTotal();
  }

  function calcTotal() {
    let total = 0;
    document.querySelectorAll('.item-subtotal').forEach(el => {
      total += parseInt(el.textContent.replace(/[^0-9]/g, '')) || 0;
    });
    document.getElementById('grandTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
  }

  document.getElementById('addItemRow').addEventListener('click', function() {
    const tbody = document.getElementById('itemsBody');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.innerHTML = `
      <td>
        <select name="items[${rowIndex}][cogs_raw_material_id]" class="form-select-modern form-select-sm raw-mat-select">
          <option value="">-- Pilih Bahan Mentah --</option>
          @foreach($rawMaterials as $rm)
            <option value="{{ $rm->cogs_raw_material_id }}" data-price="{{ $rm->price_per_unit }}">
              {{ $rm->name }} ({{ $rm->unit }}) - Rp {{ number_format($rm->price_per_unit, 0, ',', '.') }}
            </option>
          @endforeach
        </select>
      </td>
      <td><input type="number" name="items[${rowIndex}][qty]" class="form-control-modern form-control-sm item-qty" value="1" min="1"></td>
      <td><input type="number" name="items[${rowIndex}][price]" class="form-control-modern form-control-sm item-price" value="0" min="0" step="100"></td>
      <td><span class="item-subtotal text-mono">Rp 0</span></td>
      <td><button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger remove-item"><i class="bi bi-x-lg"></i></button></td>
    `;
    tbody.appendChild(row);
    attachRowEvents(row);
    rowIndex++;
  });

  function attachRowEvents(row) {
    const select = row.querySelector('.raw-mat-select');
    if (select) {
      select.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const defaultPrice = opt ? parseFloat(opt.getAttribute('data-price')) || 0 : 0;
        const priceInput = row.querySelector('.item-price');
        if (priceInput && (!priceInput.value || parseFloat(priceInput.value) === 0)) {
          priceInput.value = defaultPrice;
        }
        calcRow(row);
      });
    }

    row.querySelector('.item-qty').addEventListener('input', function() { calcRow(row); });
    row.querySelector('.item-price').addEventListener('input', function() { calcRow(row); });
    row.querySelector('.remove-item').addEventListener('click', function() {
      if (document.querySelectorAll('.item-row').length > 1) {
        row.remove();
        calcTotal();
      }
    });

    calcRow(row);
  }

  document.querySelectorAll('.item-row').forEach(row => attachRowEvents(row));
});
</script>
@endpush
