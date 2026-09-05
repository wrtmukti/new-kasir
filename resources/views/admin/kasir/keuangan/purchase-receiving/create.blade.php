@extends('admin.layouts.app')

@section('title', 'Penerimaan Bahan Mentah (Receiving) — ' . $order->po_code)

@php $activeMenu = 'purchase-order' @endphp

@section('content')
<main class="page-content">
  <div class="page-header">
    <div>
      <h1>Penerimaan Bahan Mentah (Receiving)</h1>
      <div class="breadcrumb-trail">
        <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
        <a href="{{ route('admin.keuangan.purchase-order.index') }}">Purchase Order</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
        <a href="{{ route('admin.keuangan.purchase-order.show', $order) }}">{{ $order->po_code }}</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
        <span>Receiving</span>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header-flex">
      <h6><i class="bi bi-box-arrow-in-down me-2"></i>Penerimaan Barang Dari {{ $order->po_code }} ({{ $order->supplier?->supplier_name }})</h6>
      <span class="badge bg-primary fs-6">{{ $receivingCode }}</span>
    </div>
    <div class="card-body">
      <form action="{{ route('admin.keuangan.purchase-order.receiving.store', $order) }}" method="POST" class="form-submit-loading">
        @csrf
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label-modern">Tanggal Penerimaan <span class="text-danger">*</span></label>
            <div class="input-skeleton">
              <input type="datetime-local" name="receiving_date" class="form-control-modern @error('receiving_date') is-invalid @enderror" value="{{ old('receiving_date', now()->format('Y-m-d\TH:i')) }}">
              @error('receiving_date')<span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>@enderror
            </div>
          </div>
        </div>

        <h6 class="mb-3"><i class="bi bi-list-check me-2"></i>Item Bahan Mentah Diterima</h6>
        @error('items')<div class="text-danger mb-2" style="font-size:0.8rem;">{{ $message }}</div>@enderror

        <div class="table-responsive mb-4">
          <table class="table-modern">
            <thead>
              <tr>
                <th class="ps-3">Nama Bahan Mentah</th>
                <th class="text-center">Sisa Pesanan</th>
                <th class="text-center" style="width:20%;">Jumlah Diterima Saat Ini <span class="text-danger">*</span></th>
                <th class="text-end" style="width:22%;">Harga Beli per Unit (Rp) <span class="text-danger">*</span></th>
              </tr>
            </thead>
            <tbody>
              @foreach($order->items as $i => $item)
              @php
                $remaining = $item->qty - $item->received_qty;
                $rawMat = $item->cogsRawMaterial;
              @endphp
              @if($remaining > 0)
              <tr>
                <td class="ps-3">
                  <div class="fw-bold" style="color:var(--text-primary);">{{ $rawMat?->name ?? 'Bahan Mentah' }}</div>
                  <small class="text-muted-c">Kode: {{ $rawMat?->raw_material_code ?? '-' }} | Satuan: {{ $rawMat?->unit }}</small>
                  <input type="hidden" name="items[{{ $i }}][po_item_id]" value="{{ $item->po_item_id }}">
                  <input type="hidden" name="items[{{ $i }}][cogs_raw_material_id]" value="{{ $item->cogs_raw_material_id }}">
                </td>
                <td class="text-center fw-semibold text-warning">
                  {{ number_format($remaining) }} {{ $rawMat?->unit }}
                </td>
                <td class="text-center">
                  <input type="number" name="items[{{ $i }}][received_qty]" class="form-control-modern form-control-sm text-center" value="{{ old("items.$i.received_qty", $remaining) }}" min="0" max="{{ $remaining }}">
                  @error("items.$i.received_qty")<div class="text-danger" style="font-size:0.75rem;">{{ $message }}</div>@enderror
                </td>
                <td class="text-end">
                  <input type="number" name="items[{{ $i }}][received_price]" class="form-control-modern form-control-sm text-end" value="{{ old("items.$i.received_price", $item->price) }}" min="0" step="100">
                  @error("items.$i.received_price")<div class="text-danger" style="font-size:0.75rem;">{{ $message }}</div>@enderror
                </td>
              </tr>
              @endif
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="row mb-4">
          <div class="col-12">
            <label class="form-label-modern">Catatan Penerimaan (Keterangan Kondisi Barang)</label>
            <div class="input-skeleton">
              <textarea name="receiving_notes" class="form-control-modern" rows="2" placeholder="Contoh: Bahan dalam kondisi segar dan kemasan rapi.">{{ old('receiving_notes') }}</textarea>
            </div>
          </div>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary-grad btn-loading">Simpan & Update Raw Stock (`cogs_raw_materials`)</button>
          <a href="{{ route('admin.keuangan.purchase-order.show', $order) }}" class="btn btn-outline-soft">Batal</a>
        </div>
      </form>
    </div>
  </div>
</main>
@endsection
