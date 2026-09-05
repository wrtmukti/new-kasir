<div class="table-responsive">
  <table class="table-modern">
    <thead>
      <tr>
        <th class="ps-3">Kode PO</th>
        <th>Tanggal</th>
        <th>Supplier</th>
        <th>Total PO</th>
        <th class="text-center">Status</th>
        <th class="text-end pe-3">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($orders as $po)
      <tr>
        <td class="ps-3 fw-bold text-mono">
          <a href="{{ route('admin.keuangan.purchase-order.show', $po) }}" class="text-primary">{{ $po->po_code }}</a>
        </td>
        <td>{{ \Carbon\Carbon::parse($po->po_date)->translatedFormat('d M Y') }}</td>
        <td>
          <div class="fw-semibold">{{ $po->supplier?->supplier_name ?? '-' }}</div>
          <small class="text-muted-c">{{ $po->supplier?->supplier_phone ?? '' }}</small>
        </td>
        <td class="fw-bold text-mono">Rp {{ number_format($po->po_total_amount, 0, ',', '.') }}</td>
        <td class="text-center">
          @if($po->po_status == 'draft')
            <span class="badge bg-secondary">Draft</span>
          @elseif($po->po_status == 'ordered')
            <span class="badge bg-info">Ordered</span>
          @elseif($po->po_status == 'partial')
            <span class="badge bg-warning text-dark">Partial Received</span>
          @elseif($po->po_status == 'completed')
            <span class="badge bg-success">Completed</span>
          @elseif($po->po_status == 'cancelled')
            <span class="badge bg-danger">Cancelled</span>
          @endif
        </td>
        <td class="text-end pe-3">
          <a href="{{ route('admin.keuangan.purchase-order.show', $po) }}" class="btn btn-ghost btn-icon-sq btn-sm text-info" title="Detail PO">
            <i class="bi bi-eye"></i>
          </a>
          @if($po->po_status == 'draft')
            <a href="{{ route('admin.keuangan.purchase-order.edit', $po) }}" class="btn btn-ghost btn-icon-sq btn-sm text-warning" title="Edit PO">
              <i class="bi bi-pencil"></i>
            </a>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center py-4 text-muted-c">
          <i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada data Purchase Order Bahan Mentah.
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
@if($orders->hasPages())
  <div class="px-3 py-3 border-top d-flex justify-content-between align-items-center">
    <div>Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} entries</div>
    <div>{{ $orders->links('vendor.pagination.modern') }}</div>
  </div>
@endif
