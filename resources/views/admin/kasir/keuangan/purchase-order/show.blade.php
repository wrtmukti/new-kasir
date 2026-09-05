@extends('admin.layouts.app')

@section('title', 'Detail Purchase Order — ' . $order->po_code)

@php $activeMenu = 'purchase-order' @endphp

@section('content')
<main class="page-content">
  <div class="page-header">
    <div>
      <h1>Detail PO {{ $order->po_code }}</h1>
      <div class="breadcrumb-trail">
        <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
        <a href="{{ route('admin.keuangan.purchase-order.index') }}">Purchase Order</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
        <span>Detail {{ $order->po_code }}</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      @if($order->po_status == 'draft')
        <form action="{{ route('admin.keuangan.purchase-order.confirm', $order) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-lg me-1"></i>Konfirmasi PO</button>
        </form>
        <a href="{{ route('admin.keuangan.purchase-order.edit', $order) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil me-1"></i>Edit PO</a>
      @elseif(in_array($order->po_status, ['ordered', 'partial']))
        <a href="{{ route('admin.keuangan.purchase-order.receiving.create', $order) }}" class="btn btn-primary-grad btn-sm"><i class="bi bi-box-arrow-in-down me-1"></i>Penerimaan Barang (Receiving)</a>
      @endif
      <a href="{{ route('admin.keuangan.purchase-order.index') }}" class="btn btn-outline-soft btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row g-3 mb-4">
    <div class="col-md-8">
      <div class="card h-100">
        <div class="card-header-flex">
          <h6><i class="bi bi-info-circle me-2"></i>Informasi Purchase Order</h6>
          @if($order->po_status == 'draft')
            <span class="badge bg-secondary">Draft</span>
          @elseif($order->po_status == 'ordered')
            <span class="badge bg-info">Ordered</span>
          @elseif($order->po_status == 'partial')
            <span class="badge bg-warning text-dark">Partial Received</span>
          @elseif($order->po_status == 'completed')
            <span class="badge bg-success">Completed</span>
          @elseif($order->po_status == 'cancelled')
            <span class="badge bg-danger">Cancelled</span>
          @endif
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-6 col-md-4">
              <small class="text-muted-c d-block">Kode PO</small>
              <span class="fw-bold text-mono fs-6">{{ $order->po_code }}</span>
            </div>
            <div class="col-6 col-md-4">
              <small class="text-muted-c d-block">Tanggal PO</small>
              <span class="fw-semibold">{{ \Carbon\Carbon::parse($order->po_date)->translatedFormat('d M Y') }}</span>
            </div>
            <div class="col-6 col-md-4">
              <small class="text-muted-c d-block">Supplier</small>
              <span class="fw-semibold text-primary">{{ $order->supplier?->supplier_name ?? '-' }}</span>
            </div>
            <div class="col-6 col-md-4">
              <small class="text-muted-c d-block">Total Nominal PO</small>
              <span class="fw-bold text-success text-mono fs-6">Rp {{ number_format($order->po_total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="col-12">
              <small class="text-muted-c d-block">Catatan PO</small>
              <p class="mb-0 text-secondary" style="white-space: pre-line;">{{ $order->po_notes ?: '-' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-header-flex"><h6><i class="bi bi-truck me-2"></i>Status Supplier</h6></div>
        <div class="card-body">
          <div class="mb-2"><small class="text-muted-c d-block">Kontak Person</small><span class="fw-semibold">{{ $order->supplier?->contact_name ?? '-' }}</span></div>
          <div class="mb-2"><small class="text-muted-c d-block">Telepon</small><span class="fw-semibold">{{ $order->supplier?->supplier_phone ?? '-' }}</span></div>
          <div class="mb-0"><small class="text-muted-c d-block">Alamat</small><span class="text-secondary">{{ $order->supplier?->supplier_address ?? '-' }}</span></div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header-flex"><h6><i class="bi bi-box-seam me-2"></i>Rincian Item Bahan Mentah (`cogs_raw_materials`)</h6></div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table-modern">
          <thead>
            <tr>
              <th class="ps-3">Nama Bahan Mentah</th>
              <th class="text-center">Satuan</th>
              <th class="text-center">Qty Dipesan</th>
              <th class="text-center">Qty Diterima</th>
              <th class="text-end">Harga Beli / Unit</th>
              <th class="text-end pe-3">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $item)
            @php $rm = $item->cogsRawMaterial; @endphp
            <tr>
              <td class="ps-3">
                <div class="fw-bold" style="color:var(--text-primary);">{{ $rm?->name ?? 'Bahan Mentah' }}</div>
                <small class="text-muted-c">Kode: {{ $rm?->raw_material_code ?? '-' }}</small>
              </td>
              <td class="text-center"><span class="chip-tag">{{ $rm?->unit ?? '-' }}</span></td>
              <td class="text-center fw-bold">{{ number_format($item->qty) }} {{ $rm?->unit }}</td>
              <td class="text-center">
                @if($item->received_qty >= $item->qty)
                  <span class="badge bg-success">{{ number_format($item->received_qty) }} {{ $rm?->unit }}</span>
                @elseif($item->received_qty > 0)
                  <span class="badge bg-warning text-dark">{{ number_format($item->received_qty) }} / {{ number_format($item->qty) }} {{ $rm?->unit }}</span>
                @else
                  <span class="badge bg-secondary">0 {{ $rm?->unit }}</span>
                @endif
              </td>
              <td class="text-end text-mono">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
              <td class="text-end pe-3 fw-bold text-mono">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @if($order->receivings->count() > 0)
  <div class="card mb-4">
    <div class="card-header-flex"><h6><i class="bi bi-receipt me-2"></i>Riwayat Penerimaan Bahan Mentah (Receiving)</h6></div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table-modern">
          <thead>
            <tr>
              <th class="ps-3">Kode Receiving</th>
              <th>Tanggal Terima</th>
              <th>Bahan Mentah</th>
              <th class="text-center">Qty Masuk</th>
              <th class="text-end pe-3">Total Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->receivings as $rcv)
              @foreach($rcv->items as $rItem)
              @php $rawMat = $rItem->cogsRawMaterial; @endphp
              <tr>
                <td class="ps-3 fw-bold text-mono">{{ $rcv->receiving_code }}</td>
                <td>{{ \Carbon\Carbon::parse($rcv->receiving_date)->translatedFormat('d M Y H:i') }}</td>
                <td>{{ $rawMat?->name ?? '-' }} ({{ $rawMat?->unit }})</td>
                <td class="text-center fw-bold text-success">+{{ number_format($rItem->received_qty) }} {{ $rawMat?->unit }}</td>
                <td class="text-end pe-3 text-mono">Rp {{ number_format($rItem->subtotal, 0, ',', '.') }}</td>
              </tr>
              @endforeach
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
</main>
@endsection
