@extends('admin.layouts.app')

@section('title', 'Detail PO')

@php $activeMenu = 'purchase-order' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail PO: {{ $order->po_code }}</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.purchase-order.index') }}">Purchase Order</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>{{ $order->po_code }}</span>
    </div>
  </div>
  <div class="d-flex gap-2">
    @if($order->po_status === 'draft')
      <a href="{{ route('admin.purchase-order.edit', $order) }}" class="btn btn-primary-grad">
        <i class="bi bi-pencil me-1"></i>Edit
      </a>
      <button type="button" class="btn btn-success-grad" id="confirmBtn">
        <i class="bi bi-check-lg me-1"></i>Konfirmasi PO
      </button>
      <button type="button" class="btn btn-danger-grad" id="cancelBtn">
        <i class="bi bi-x-lg me-1"></i>Batalkan
      </button>
    @endif
    @if($order->po_status === 'ordered')
      <button type="button" class="btn btn-danger-grad" id="cancelBtn">
        <i class="bi bi-x-lg me-1"></i>Batalkan
      </button>
      <a href="{{ route('admin.purchase-order.receiving.create', $order) }}" class="btn btn-success-grad">
        <i class="bi bi-box-arrow-in-down me-1"></i>Terima Barang
      </a>
    @endif
    @if(in_array($order->po_status, ['partial', 'completed']))
      <button type="button" class="btn btn-warning-grad" id="returnBtn">
        <i class="bi bi-arrow-return-left me-1"></i>Return Barang
      </button>
    @endif
    @if(in_array($order->po_status, ['partial']))
      <a href="{{ route('admin.purchase-order.receiving.create', $order) }}" class="btn btn-success-grad">
        <i class="bi bi-box-arrow-in-down me-1"></i>Terima Barang
      </a>
    @endif
    <a href="{{ route('admin.purchase-order.index') }}" class="btn btn-outline-soft">Kembali</a>
  </div>
</div>

<div class="row g-3">
  {{-- Info PO --}}
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-info-circle me-2"></i>Informasi PO</h6></div>
      <div class="card-body p-0">
        <table class="detail-table">
          @php
            $sClass = match($order->po_status) { 'draft'=>'pill-neutral', 'ordered'=>'pill-warning', 'partial'=>'pill-info', 'completed'=>'pill-success', 'cancelled'=>'pill-danger', default=>'pill-danger' };
            $sLabel = match($order->po_status) { 'draft'=>'Draft', 'ordered'=>'Dipesan', 'partial'=>'Sebagian', 'completed'=>'Selesai', 'cancelled'=>'Batal', default=>'Batal' };
          @endphp
          <tr><td class="detail-label">Kode PO</td><td class="detail-value text-mono fw-semibold">{{ $order->po_code }}</td></tr>
          <tr><td class="detail-label">Supplier</td><td class="detail-value">{{ $order->supplier?->supplier_name ?? '-' }}</td></tr>
          <tr><td class="detail-label">Tanggal</td><td class="detail-value">{{ $order->po_date ? $order->po_date->format('d M Y H:i') : '-' }}</td></tr>
          <tr><td class="detail-label">Status</td><td class="detail-value"><span class="pill {{ $sClass }}">{{ $sLabel }}</span></td></tr>
          <tr><td class="detail-label">Total</td><td class="detail-value text-mono fw-semibold">Rp {{ number_format($order->po_total_amount, 0, ',', '.') }}</td></tr>
          <tr><td class="detail-label">Catatan</td><td class="detail-value" style="white-space:pre-wrap;">{{ $order->po_notes ?? '-' }}</td></tr>
          @if($order->po_status === 'cancelled')
          <tr><td class="detail-label text-danger">Alasan Batal</td><td class="detail-value" style="white-space:pre-wrap;">{{ $order->po_notes ? preg_replace('/^\[DIBATALKAN:.*?\] /', '', $order->po_notes) : '-' }}</td></tr>
          @endif
        </table>
      </div>
    </div>
  </div>

  {{-- Items PO --}}
  <div class="col-12">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-box-seam me-2"></i>Item Pesanan</h6></div>
      <div class="card-body p-0">
        <table class="table-modern" id="itemsTable">
          <thead>
            <tr>
              <th>Bahan</th>
              <th>Qty Pesan</th>
              <th>Harga</th>
              <th>Subtotal</th>
              <th>Qty Diterima</th>
              <th>Qty Return</th>
              <th>Sisa</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $item)
            @php
              $ret = $returnLogs[$item->stock_id] ?? 0;
              $remaining = $item->qty - $item->received_qty;
            @endphp
            <tr>
              <td class="fw-semibold">{{ $item->stock?->stock_name ?? '-' }}</td>
              <td class="text-mono">{{ $item->qty }} {{ $item->stock?->stock_unit ?? '' }}</td>
              <td class="text-mono">Rp {{ number_format($item->price, 0) }}</td>
              <td class="text-mono">Rp {{ number_format($item->subtotal, 0) }}</td>
              <td class="text-mono fw-semibold">{{ $item->received_qty }}</td>
              <td class="text-mono {{ $ret > 0 ? 'text-danger fw-semibold' : '' }}">{{ $ret }}</td>
              <td class="text-mono fw-semibold {{ $remaining > 0 ? 'text-warning' : 'text-success' }}">
                {{ max(0, $remaining) }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Receiving History --}}
  @if($order->receivings->count())
  <div class="col-12">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-box-arrow-in-down me-2"></i>Riwayat Penerimaan</h6></div>
      <div class="card-body p-0">
        <table class="table-modern">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Tanggal</th>
              <th>Status</th>
              <th>Catatan</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->receivings as $rcv)
            <tr>
              <td class="text-mono fw-semibold">{{ $rcv->receiving_code }}</td>
              <td>{{ $rcv->receiving_date ? $rcv->receiving_date->format('d M Y H:i') : '-' }}</td>
              <td><span class="pill pill-success">Selesai</span></td>
              <td>{{ $rcv->receiving_notes ?? '-' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  {{-- Return History --}}
  @php
    $returnHistories = \App\Models\Admin\StockLog::where('reference_type', 'purchase_return')
      ->where('reference_code', $order->po_code)
      ->with('stock')
      ->get();
  @endphp
  @if($returnHistories->count())
  <div class="col-12">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-arrow-return-left me-2"></i>Riwayat Return</h6></div>
      <div class="card-body p-0">
        <table class="table-modern">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Bahan</th>
              <th>Qty</th>
              <th>Alasan</th>
            </tr>
          </thead>
          <tbody>
            @foreach($returnHistories as $log)
            <tr>
              <td style="font-size:0.85rem;">{{ $log->created_at ? $log->created_at->format('d M Y H:i') : '-' }}</td>
              <td class="fw-semibold">{{ $log->stock?->stock_name ?? '-' }}</td>
              <td class="text-mono text-danger fw-semibold">-{{ $log->qty }}</td>
              <td>{{ $log->notes ? preg_replace('/^Return dari ' . $order->po_code . ': /', '', $log->notes) : '-' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
</div>

{{-- ===================== MODAL CONFIRM ===================== --}}
<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content" style="background:var(--bg-surface);border:1px solid var(--border-subtle);">
      <div class="modal-body text-center py-4">
        <i class="bi bi-check-circle text-success" style="font-size:2rem;display:block;margin-bottom:0.75rem;"></i>
        <p class="fw-semibold mb-1" style="color:var(--text-primary);">Konfirmasi PO ini?</p>
        <small class="text-muted-c">PO akan dikirim ke supplier. Status → Ordered.</small>
      </div>
      <div class="modal-footer border-0 pt-0 justify-content-center">
        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-success-grad" id="confirmSubmit">Ya, Konfirmasi</button>
      </div>
    </div>
  </div>
</div>

{{-- ===================== MODAL CANCEL ===================== --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background:var(--bg-surface);border:1px solid var(--border-subtle);">
      <div class="modal-header">
        <h6 class="mb-0"><i class="bi bi-x-circle text-danger me-2"></i>Batalkan PO</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="input-skeleton">
          <label class="form-label-modern">Alasan Pembatalan <span class="text-danger">*</span></label>
          <textarea id="cancelReason" class="form-control-modern" rows="3" placeholder="Wajib diisi alasan pembatalan..."></textarea>
        </div>
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger-grad" id="cancelSubmit">Ya, Batalkan</button>
      </div>
    </div>
  </div>
</div>

{{-- ===================== MODAL RETURN ===================== --}}
<div class="modal fade" id="returnModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background:var(--bg-surface);border:1px solid var(--border-subtle);">
      <div class="modal-header">
        <h6 class="mb-0"><i class="bi bi-arrow-return-left text-warning me-2"></i>Return Barang</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="returnForm">
          <div class="input-skeleton mb-3">
            <label class="form-label-modern">Bahan <span class="text-danger">*</span></label>
            <select id="returnStockId" class="form-select-modern" required>
              <option value="">-- Pilih Bahan --</option>
              @foreach($order->items as $item)
                @php
                  $ret = $returnLogs[$item->stock_id] ?? 0;
                  $available = $item->received_qty - $ret;
                @endphp
                @if($available > 0)
                <option value="{{ $item->stock_id }}" data-max="{{ $available }}">
                  {{ $item->stock?->stock_name ?? '-' }} (sisa return: {{ $available }})
                </option>
                @endif
              @endforeach
            </select>
          </div>
          <div class="input-skeleton mb-3">
            <label class="form-label-modern">Jumlah Return <span class="text-danger">*</span></label>
            <input type="number" id="returnQty" class="form-control-modern" value="1" min="1" required>
            <small class="text-muted-c" id="returnMaxHint"></small>
          </div>
          <div class="input-skeleton">
            <label class="form-label-modern">Alasan Return <span class="text-danger">*</span></label>
            <textarea id="returnReason" class="form-control-modern" rows="2" placeholder="Alasan return..."></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-warning-grad" id="returnSubmit">Konfirmasi Return</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.btn-danger-grad { background:linear-gradient(135deg,#dc2626,#ef4444); color:#fff; border:none; }
.btn-danger-grad:hover { background:linear-gradient(135deg,#b91c1c,#dc2626); color:#fff; }
.btn-warning-grad { background:linear-gradient(135deg,#d97706,#f59e0b); color:#fff; border:none; }
.btn-warning-grad:hover { background:linear-gradient(135deg,#b45309,#d97706); color:#fff; }
.btn-success-grad { background:linear-gradient(135deg,#059669,#10B981); color:#fff; border:none; }
.btn-success-grad:hover { background:linear-gradient(135deg,#047857,#059669); color:#fff; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const csrf = '{{ csrf_token() }}';

  // Flash → NexoraToast
  @if(session('success'))
    NexoraToast('{{ session('success') }}', 'success');
  @endif
  @if(session('error'))
    NexoraToast('{{ session('error') }}', 'danger');
  @endif

  // ========== AUTO CONFIRM MODAL ==========
  @if(request('confirm'))
    var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    confirmModal.show();
  @endif

  // ========== CONFIRM ==========
  const confirmBtn = document.getElementById('confirmBtn');
  const confirmSubmit = document.getElementById('confirmSubmit');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', function() {
      var m = new bootstrap.Modal(document.getElementById('confirmModal'));
      m.show();
    });
  }
  if (confirmSubmit) {
    confirmSubmit.addEventListener('click', function() {
      const btn = this;
      btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengonfirmasi...';
      fetch('{{ route('admin.purchase-order.confirm', $order) }}', {
        method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf }
      })
      .then(r => r.json()).then(d => {
        if (d.success) {
          var m = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
          if (m) m.hide();
          NexoraToast(d.success, 'success');
          setTimeout(function() { location.reload(); }, 500);
        } else {
          NexoraToast(d.error || 'Gagal konfirmasi.', 'danger');
          btn.disabled = false; btn.innerHTML = 'Ya, Konfirmasi';
        }
      }).catch(function() { NexoraToast('Gagal konfirmasi PO.', 'danger'); btn.disabled = false; btn.innerHTML = 'Ya, Konfirmasi'; });
    });
  }

  // ========== CANCEL ==========
  const cancelBtn = document.getElementById('cancelBtn');
  const cancelSubmit = document.getElementById('cancelSubmit');
  if (cancelBtn) {
    cancelBtn.addEventListener('click', function() {
      document.getElementById('cancelReason').value = '';
      var m = new bootstrap.Modal(document.getElementById('cancelModal'));
      m.show();
    });
  }
  if (cancelSubmit) {
    cancelSubmit.addEventListener('click', function() {
      const reason = document.getElementById('cancelReason').value.trim();
      if (!reason) { NexoraToast('Alasan pembatalan wajib diisi.', 'warning'); return; }
      const btn = this; btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Membatalkan...';
      fetch('{{ route('admin.purchase-order.cancel', $order) }}', {
        method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'cancellation_reason=' + encodeURIComponent(reason)
      })
      .then(r => r.json()).then(d => {
        if (d.success) {
          var m = bootstrap.Modal.getInstance(document.getElementById('cancelModal'));
          if (m) m.hide();
          NexoraToast(d.success, 'success');
          setTimeout(function() { location.reload(); }, 500);
        } else {
          NexoraToast(d.error || Object.values(d.errors || {}).flat()[0] || 'Gagal membatalkan.', 'danger');
          btn.disabled = false; btn.innerHTML = 'Ya, Batalkan';
        }
      }).catch(function() { NexoraToast('Gagal membatalkan PO.', 'danger'); btn.disabled = false; btn.innerHTML = 'Ya, Batalkan'; });
    });
  }

  // ========== RETURN ==========
  const returnBtn = document.getElementById('returnBtn');
  const returnSubmit = document.getElementById('returnSubmit');
  if (returnBtn) {
    returnBtn.addEventListener('click', function() {
      document.getElementById('returnReason').value = '';
      document.getElementById('returnQty').value = 1;
      document.getElementById('returnStockId').value = '';
      document.getElementById('returnMaxHint').textContent = '';
      var m = new bootstrap.Modal(document.getElementById('returnModal'));
      m.show();
    });
  }

  document.getElementById('returnStockId')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const max = opt ? opt.dataset.max : 0;
    document.getElementById('returnMaxHint').textContent = max ? 'Maksimal return: ' + max : '';
    document.getElementById('returnQty').max = max || 1;
  });

  if (returnSubmit) {
    returnSubmit.addEventListener('click', function() {
      const stockId = document.getElementById('returnStockId').value;
      const qty = parseInt(document.getElementById('returnQty').value) || 0;
      const reason = document.getElementById('returnReason').value.trim();
      if (!stockId) { NexoraToast('Pilih bahan yang akan diretur.', 'warning'); return; }
      if (qty < 1) { NexoraToast('Jumlah return minimal 1.', 'warning'); return; }
      if (!reason) { NexoraToast('Alasan return wajib diisi.', 'warning'); return; }
      const btn = this; btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';
      fetch('{{ route('admin.purchase-order.return', $order) }}', {
        method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'stock_id=' + encodeURIComponent(stockId) + '&qty=' + qty + '&reason=' + encodeURIComponent(reason)
      })
      .then(r => r.json()).then(d => {
        if (d.success) {
          var m = bootstrap.Modal.getInstance(document.getElementById('returnModal'));
          if (m) m.hide();
          NexoraToast(d.success, 'success');
          setTimeout(function() { location.reload(); }, 500);
        } else {
          NexoraToast(d.error || Object.values(d.errors || {}).flat()[0] || 'Gagal return.', 'danger');
          btn.disabled = false; btn.innerHTML = 'Konfirmasi Return';
        }
      }).catch(function() { NexoraToast('Gagal return.', 'danger'); btn.disabled = false; btn.innerHTML = 'Konfirmasi Return'; });
    });
  }
});
</script>
@endpush
