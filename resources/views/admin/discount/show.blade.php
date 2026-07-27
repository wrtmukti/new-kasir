@extends('admin.layouts.app')

@section('title', 'Detail Diskon')

@php $activeMenu = 'discount' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>{{ $discount->discount_name }}</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.discount.index') }}">Diskon</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>{{ $discount->discount_name }}</span>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.discount.edit', $discount) }}" class="btn btn-primary-grad">
      <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('admin.discount.index') }}" class="btn btn-outline-soft">Kembali</a>
  </div>
</div>

<div class="row g-3">
  {{-- Info Diskon --}}
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header-flex"><h6><i class="bi bi-info-circle me-2"></i>Informasi Diskon</h6></div>
      <div class="card-body p-0">
        <table class="detail-table">
          <tr>
            <td class="detail-label">Perusahaan</td>
            <td class="detail-value">{{ $discount->company?->company_name ?? '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Nama</td>
            <td class="detail-value fw-semibold">{{ $discount->discount_name }}</td>
          </tr>
          <tr>
            <td class="detail-label">Tipe</td>
            <td class="detail-value">
              @if($discount->discount_type == 'percentage')
                <span class="pill pill-info">Persen</span>
              @else
                <span class="pill pill-warning">Nominal</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Nilai</td>
            <td class="detail-value">
              @if($discount->discount_type == 'percentage')
                <strong>{{ number_format($discount->discount_value, 0) }}%</strong>
              @else
                <strong>Rp{{ number_format($discount->discount_value, 0) }}</strong>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Maksimal Potongan</td>
            <td class="detail-value">
              @if($discount->discount_max_amount)
                Rp{{ number_format($discount->discount_max_amount, 0) }}
              @else
                <span class="text-muted-c">Tanpa batas</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Deskripsi</td>
            <td class="detail-value">{{ $discount->discount_description ?? '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Status</td>
            <td class="detail-value">
              @if($discount->discount_status)
                <span class="pill pill-success">Aktif</span>
              @else
                <span class="pill pill-neutral">Nonaktif</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Periode Berlaku</td>
            <td class="detail-value">
              @if($discount->start_date)
                {{ $discount->start_date->format('d M Y H:i') }}
                @if($discount->end_date)
                  — {{ $discount->end_date->format('d M Y H:i') }}
                @endif
              @else
                <span class="text-muted-c">-</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="detail-label">Dibuat</td>
            <td class="detail-value">{{ $discount->created_at ? $discount->created_at->format('d M Y H:i') : '-' }}</td>
          </tr>
          <tr>
            <td class="detail-label">Diupdate</td>
            <td class="detail-value">{{ $discount->updated_at ? $discount->updated_at->format('d M Y H:i') : '-' }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  {{-- Produk Terkait --}}
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header-flex">
        <h6><i class="bi bi-box me-2"></i>Produk Terkait</h6>
        <span class="chip-tag">{{ $discount->products->count() }} produk</span>
      </div>
      <div class="card-body p-0" style="max-height:400px;overflow-y:auto;">
        @if($discount->products->count() > 0)
          <div class="discount-product-list">
            @foreach($discount->products as $product)
              <div class="discount-product-item">
                <div class="discount-product-info">
                  <div class="discount-product-name">{{ $product->product_name }}</div>
                  <div class="discount-product-code">{{ $product->product_code }}</div>
                </div>
                <button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger detach-product"
                  data-product-id="{{ $product->product_id }}" data-product-name="{{ $product->product_name }}"
                  title="Lepaskan diskon">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center text-muted-c py-4" style="font-size:0.85rem;">
            <i class="bi bi-inbox" style="font-size:1.5rem;display:block;margin-bottom:0.5rem;"></i>
            Belum ada produk terkait
          </div>
        @endif
      </div>
      <div class="card-footer border-0">
        <button type="button" class="btn btn-outline-soft btn-sm w-100" data-bs-toggle="modal" data-bs-target="#attachProductModal">
          <i class="bi bi-plus-lg me-1"></i>Hubungkan ke Produk
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Modal Attach Product --}}
<div class="modal fade" id="attachProductModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="mb-0">Hubungkan Diskon ke Produk</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted-c" style="font-size:0.85rem;">Pilih produk yang ingin diberi diskon <strong>{{ $discount->discount_name }}</strong>.</p>
        <div class="input-skeleton">
          <select id="productSelect" class="form-select-modern" style="width:100%;">
            <option value="">-- Pilih Produk --</option>
            @foreach($products as $product)
              <option value="{{ $product->product_id }}"
                {{ $discount->products->contains('product_id', $product->product_id) ? 'disabled' : '' }}>
                [{{ $product->product_code }}] {{ $product->product_name }}
                @if($product->category)
                  ({{ $product->category->category_name }})
                @endif
              </option>
            @endforeach
          </select>
        </div>
        <div id="attachError" class="text-danger mt-2" style="font-size:0.85rem;display:none;"></div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-soft" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary-grad" id="confirmAttachBtn">Hubungkan</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal Detach Confirm --}}
<div class="modal fade" id="detachModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="mb-0">Lepaskan Diskon</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-4">
        <i class="bi bi-exclamation-triangle-fill" style="font-size:2rem;color:var(--danger);"></i>
        <p class="mt-2 mb-0">Lepaskan diskon dari produk <strong id="detachProductName"></strong>?</p>
      </div>
      <div class="modal-footer justify-content-center border-0 pt-0">
        <button type="button" class="btn btn-outline-soft" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmDetachBtn">Ya, Lepaskan</button>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
.discount-product-list { display:flex; flex-direction:column; }
.discount-product-item { display:flex; align-items:center; gap:0.75rem; padding:0.65rem 1.35rem; border-bottom:1px solid var(--border-subtle); }
.discount-product-item:last-child { border-bottom:none; }
.discount-product-info { flex:1; min-width:0; }
.discount-product-name { font-size:0.85rem; font-weight:600; color:var(--text-primary); }
.discount-product-code { font-size:0.75rem; color:var(--text-muted); margin-top:0.1rem; }
</style>
@endpush

{{-- Toast --}}
@if(session('success'))
  <script>document.addEventListener('DOMContentLoaded', function() { NexoraToast('{{ session('success') }}', 'success'); });</script>
@endif
@if(session('error'))
  <script>document.addEventListener('DOMContentLoaded', function() { NexoraToast('{{ session('error') }}', 'danger'); });</script>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const csrf = '{{ csrf_token() }}';
  const attachUrl = '{{ route('admin.discount.attach-product', $discount) }}';
  const detachUrl = '{{ route('admin.discount.detach-product', $discount) }}';
  let detachProductId = null;

  // ===== Attach =====
  document.getElementById('confirmAttachBtn').addEventListener('click', function() {
    const productId = document.getElementById('productSelect').value;
    const errorEl = document.getElementById('attachError');

    if (!productId) {
      errorEl.textContent = 'Pilih produk terlebih dahulu.';
      errorEl.style.display = 'block';
      return;
    }
    errorEl.style.display = 'none';

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';

    fetch(attachUrl, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
      body: '_token=' + csrf + '&product_id=' + productId
    })
    .then(res => res.json())
    .then(data => {
      NexoraToast(data.success || 'Berhasil.', 'success');
      var modal = bootstrap.Modal.getInstance(document.getElementById('attachProductModal'));
      if (modal) modal.hide();
      setTimeout(() => location.reload(), 500);
    })
    .catch(() => {
      NexoraToast('Gagal menghubungkan diskon.', 'danger');
      btn.disabled = false;
      btn.innerHTML = 'Hubungkan';
    });
  });

  // ===== Detach =====
  document.querySelectorAll('.detach-product').forEach(function(btn) {
    btn.addEventListener('click', function() {
      detachProductId = this.dataset.productId;
      document.getElementById('detachProductName').textContent = this.dataset.productName;
      var modal = new bootstrap.Modal(document.getElementById('detachModal'));
      modal.show();
    });
  });

  document.getElementById('confirmDetachBtn').addEventListener('click', function() {
    if (!detachProductId) return;

    var modal = bootstrap.Modal.getInstance(document.getElementById('detachModal'));
    if (modal) modal.hide();

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>...';

    fetch(detachUrl, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
      body: '_token=' + csrf + '&product_id=' + detachProductId
    })
    .then(res => res.json())
    .then(data => {
      NexoraToast(data.success || 'Berhasil.', 'success');
      setTimeout(() => location.reload(), 500);
    })
    .catch(() => {
      NexoraToast('Gagal melepas diskon.', 'danger');
      btn.disabled = false;
      btn.innerHTML = 'Ya, Lepaskan';
    });
  });
});
</script>
@endpush
