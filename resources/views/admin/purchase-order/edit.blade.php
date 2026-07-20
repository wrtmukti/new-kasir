@extends('admin.layouts.app')

@section('title', 'Edit Purchase Order')

@php $activeMenu = 'purchase-order' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Edit PO: {{ $order->po_code }}</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.purchase-order.index') }}">Purchase Order</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Edit</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex"><h6><i class="bi bi-cart-plus me-2"></i>Informasi PO</h6></div>
  <div class="card-body">
    <form action="{{ route('admin.purchase-order.update', $order) }}" method="POST" id="poForm" class="form-submit-loading">
      @csrf @method('PUT')
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
        <div class="col-md-3">
          <label class="form-label-modern">Kode PO</label>
          <input type="text" class="form-control-modern" value="{{ $order->po_code }}" disabled>
        </div>
      </div>

      <div class="card-header-flex mb-3 px-0 border-0">
        <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Item Bahan</h6>
        <button type="button" class="btn btn-outline-soft btn-sm" id="addItemRow">
          <i class="bi bi-plus-lg me-1"></i>Tambah Item
        </button>
      </div>

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
            @foreach($order->items as $i => $item)
            <tr class="item-row">
              <td>
                <select name="items[{{ $i }}][stock_id]" class="form-select-modern form-select-sm">
                  <option value="">-- Pilih --</option>
                  @foreach($stocks as $st)
                    <option value="{{ $st->stock_id }}" {{ $item->stock_id == $st->stock_id ? 'selected' : '' }}>{{ $st->stock_name }} ({{ $st->stock_unit ?? '-' }})</option>
                  @endforeach
                </select>
              </td>
              <td><input type="number" name="items[{{ $i }}][qty]" class="form-control-modern form-control-sm item-qty" value="{{ $item->qty }}" min="1"></td>
              <td><input type="number" name="items[{{ $i }}][price]" class="form-control-modern form-control-sm item-price" value="{{ $item->price }}" min="0" step="100"></td>
              <td><span class="item-subtotal text-mono">Rp {{ number_format($item->qty * $item->price, 0) }}</span></td>
              <td><button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger remove-item" style="display:none;"><i class="bi bi-x-lg"></i></button></td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="fw-bold">
              <td colspan="3" class="text-end">Grand Total</td>
              <td><span id="grandTotal" class="text-mono">Rp {{ number_format($order->po_total_amount, 0) }}</span></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="row mt-3">
        <div class="col-12">
          <label class="form-label-modern">Catatan</label>
          <div class="input-skeleton">
            <textarea name="po_notes" class="form-control-modern" rows="2" placeholder="Catatan PO">{{ old('po_notes', $order->po_notes) }}</textarea>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary-grad btn-loading">Simpan Perubahan</button>
        <a href="{{ route('admin.purchase-order.show', $order) }}" class="btn btn-outline-soft">Kembali</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Form submit loading
  const form = document.querySelector('.form-submit-loading');
  if (form) {
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
  }

  let rowIndex = {{ count($order->items) }};

  function calcRow(row) {
    const qty = parseInt(row.querySelector('.item-qty').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    row.querySelector('.item-subtotal').textContent = 'Rp ' + (qty * price).toLocaleString('id-ID');
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
      if (document.querySelectorAll('.item-row').length > 1) { row.remove(); calcTotal(); }
    });
  }

  document.querySelectorAll('.item-row').forEach(row => attachRowEvents(row));
});
</script>
@endpush
