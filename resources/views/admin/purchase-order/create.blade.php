@extends('admin.layouts.app')

@section('title', 'Buat Purchase Order')

@php $activeMenu = 'purchase-order' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Buat Purchase Order</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.purchase-order.index') }}">Purchase Order</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Buat</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex"><h6><i class="bi bi-cart-plus me-2"></i>Informasi PO</h6></div>
  <div class="card-body">
    <form action="{{ route('admin.purchase-order.store') }}" method="POST" id="poForm">
      @csrf
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label-modern">Supplier <span class="text-danger">*</span></label>
          <select name="supplier_id" class="form-select-modern @error('supplier_id') is-invalid @enderror">
            <option value="">-- Pilih Supplier --</option>
            @foreach($suppliers as $s)
              <option value="{{ $s->supplier_id }}" {{ old('supplier_id') == $s->supplier_id ? 'selected' : '' }}>{{ $s->supplier_name }}</option>
            @endforeach
          </select>
          @error('supplier_id')<div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Status</label>
          <select name="po_status" class="form-select-modern">
            <option value="draft" {{ old('po_status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="ordered" {{ old('po_status') == 'ordered' ? 'selected' : '' }}>Langsung Pesan</option>
          </select>
        </div>
      </div>

      <div class="card-header-flex mb-3 px-0 border-0">
        <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Item Bahan</h6>
        <button type="button" class="btn btn-outline-soft btn-sm" id="addItemRow">
          <i class="bi bi-plus-lg me-1"></i>Tambah Item
        </button>
      </div>
      @error('items')<div class="text-danger mb-2" style="font-size:0.8rem;">{{ $message }}</div>@enderror

      <div class="table-responsive">
        <table class="table-modern" id="itemsTable">
          <thead>
            <tr>
              <th style="width:35%;">Bahan <span class="text-danger">*</span></th>
              <th style="width:15%;">Qty <span class="text-danger">*</span></th>
              <th style="width:20%;">Harga Satuan <span class="text-danger">*</span></th>
              <th style="width:20%;">Subtotal</th>
              <th style="width:10%;"></th>
            </tr>
          </thead>
          <tbody id="itemsBody">
            @if(old('items'))
              @foreach(old('items') as $i => $item)
              <tr class="item-row">
                <td>
                  <select name="items[{{ $i }}][stock_id]" class="form-select-modern form-select-sm">
                    <option value="">-- Pilih --</option>
                    @foreach($stocks as $st)
                      <option value="{{ $st->stock_id }}" {{ $item['stock_id'] == $st->stock_id ? 'selected' : '' }}>{{ $st->stock_name }}</option>
                    @endforeach
                  </select>
                  @error("items.{$i}.stock_id")<div class="text-danger" style="font-size:0.75rem;">{{ $message }}</div>@enderror
                </td>
                <td><input type="number" name="items[{{ $i }}][qty]" class="form-control-modern form-control-sm item-qty" value="{{ $item['qty'] }}" min="1"></td>
                <td><input type="number" name="items[{{ $i }}][price]" class="form-control-modern form-control-sm item-price" value="{{ $item['price'] }}" min="0" step="100"></td>
                <td><span class="item-subtotal text-mono">Rp 0</span></td>
                <td><button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger remove-item" style="display:{{ $loop->first ? 'none' : '' }};"><i class="bi bi-x-lg"></i></button></td>
              </tr>
              @endforeach
            @else
            <tr class="item-row">
              <td>
                <select name="items[0][stock_id]" class="form-select-modern form-select-sm">
                  <option value="">-- Pilih --</option>
                  @foreach($stocks as $st)
                    <option value="{{ $st->stock_id }}">{{ $st->stock_name }} ({{ $st->stock_unit ?? '-' }})</option>
                  @endforeach
                </select>
              </td>
              <td><input type="number" name="items[0][qty]" class="form-control-modern form-control-sm item-qty" value="1" min="1"></td>
              <td><input type="number" name="items[0][price]" class="form-control-modern form-control-sm item-price" value="0" min="0" step="100"></td>
              <td><span class="item-subtotal text-mono">Rp 0</span></td>
              <td><button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger remove-item" style="display:none;"><i class="bi bi-x-lg"></i></button></td>
            </tr>
            @endif
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
          <label class="form-label-modern">Catatan</label>
          <textarea name="po_notes" class="form-control-modern" rows="2" placeholder="Catatan PO">{{ old('po_notes') }}</textarea>
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary-grad">Simpan PO</button>
        <a href="{{ route('admin.purchase-order.index') }}" class="btn btn-outline-soft">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  let rowIndex = {{ old('items') ? count(old('items')) : 1 }};

  function calcRow(row) {
    const qty = parseInt(row.querySelector('.item-qty').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const sub = qty * price;
    row.querySelector('.item-subtotal').textContent = 'Rp ' + sub.toLocaleString('id-ID');
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
        <select name="items[${rowIndex}][stock_id]" class="form-select-modern form-select-sm">
          <option value="">-- Pilih --</option>
          @foreach($stocks as $st)
            <option value="{{ $st->stock_id }}">{{ $st->stock_name }} ({{ $st->stock_unit ?? '-' }})</option>
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
    row.querySelector('.item-qty').addEventListener('input', function() { calcRow(row); });
    row.querySelector('.item-price').addEventListener('input', function() { calcRow(row); });
    row.querySelector('.remove-item').addEventListener('click', function() {
      if (document.querySelectorAll('.item-row').length > 1) {
        row.remove();
        calcTotal();
      }
    });
  }

  document.querySelectorAll('.item-row').forEach(row => attachRowEvents(row));
});
</script>
@endpush
