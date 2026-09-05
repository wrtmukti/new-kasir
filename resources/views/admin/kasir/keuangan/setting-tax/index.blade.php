@extends('admin.layouts.app')

@section('title', 'Setting Pajak (PB1) & Service Charge')

@php $activeMenu = 'setting-tax' @endphp

@section('content')
<!-- Header Page & Breadcrumb -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
  <div>
    <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary);">Setting Pajak (PB1) & Service Charge</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Keuangan</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Master Setting Pajak & Service</span>
    </div>
  </div>
</div>

<!-- Main Row Cards -->
<div class="row g-4">
  <!-- Card Setting Pajak PB1 -->
  <div class="col-lg-6">
    <div class="card h-100 p-4" style="background: var(--bg-surface); border: 1.5px solid rgba(59, 130, 246, 0.3); border-radius: 16px; box-shadow: 0 4px 20px rgba(59, 130, 246, 0.08);">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
            <i class="bi bi-receipt-cutoff fs-4"></i>
          </div>
          <div>
            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">Pajak Restoran (PB1)</h5>
            <small class="text-muted">Pajak Pertambahan Barang & Jasa Daerah (10%)</small>
          </div>
        </div>
        <span class="badge {{ optional($tax)->is_active ? 'bg-success' : 'bg-secondary' }}" id="taxStatusBadge">
          {{ optional($tax)->is_active ? 'Aktif' : 'Non-Aktif' }}
        </span>
      </div>
      
      <hr style="border-color: var(--border-color); opacity: 0.2;">

      <form id="formTax" action="{{ route('admin.keuangan.setting-tax.update-tax') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label class="form-label fw-semibold" style="font-size:0.85rem; color: var(--text-primary);">Nama Label Pajak</label>
          <input type="text" name="tax_name" class="form-control" value="{{ optional($tax)->tax_name ?? 'PBJT Restoran 10%' }}" placeholder="Contoh: PBJT Restoran 10%" required>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold" style="font-size:0.85rem; color: var(--text-primary);">Tarif Pajak (%)</label>
            <div class="input-group">
              <input type="number" step="0.01" name="rate_percent" class="form-control" value="{{ optional($tax)->rate_percent ?? 10.00 }}" placeholder="10.00" required>
              <span class="input-group-text">%</span>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" style="font-size:0.85rem; color: var(--text-primary);">Tipe Pengenaan</label>
            <select name="type" class="form-select" required>
              <option value="exclusive" {{ optional($tax)->type == 'exclusive' ? 'selected' : '' }}>Eksklusif (Ditambah dari Subtotal)</option>
              <option value="inclusive" {{ optional($tax)->type == 'inclusive' ? 'selected' : '' }}>Inklusif (Sudah Termasuk di Harga)</option>
            </select>
          </div>
        </div>

        <div class="form-check form-switch mb-4">
          <input class="form-check-input" type="checkbox" name="is_active" id="switchTaxActive" value="1" {{ optional($tax)->is_active ? 'checked' : '' }}>
          <label class="form-check-label fw-semibold" for="switchTaxActive" style="color: var(--text-primary);">Aktifkan Pengenaan Pajak pada Order Kasir</label>
        </div>

        <div class="d-flex justify-content-end">
          <button type="submit" class="btn btn-primary px-4 btn-loading" id="btnSaveTax">
            <i class="bi bi-check2-circle me-1"></i>Simpan Setting Pajak
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Card Setting Service Charge -->
  <div class="col-lg-6">
    <div class="card h-100 p-4" style="background: var(--bg-surface); border: 1.5px solid rgba(16, 185, 129, 0.3); border-radius: 16px; box-shadow: 0 4px 20px rgba(16, 185, 129, 0.08);">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
            <i class="bi bi-person-hearts fs-4"></i>
          </div>
          <div>
            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">Service Charge Kasir / Pelayanan</h5>
            <small class="text-muted">Biaya Layanan Karyawan / Tip Restoran (5%)</small>
          </div>
        </div>
        <span class="badge {{ optional($service)->is_active ? 'bg-success' : 'bg-secondary' }}" id="serviceStatusBadge">
          {{ optional($service)->is_active ? 'Aktif' : 'Non-Aktif' }}
        </span>
      </div>
      
      <hr style="border-color: var(--border-color); opacity: 0.2;">

      <form id="formService" action="{{ route('admin.keuangan.setting-tax.update-service') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label class="form-label fw-semibold" style="font-size:0.85rem; color: var(--text-primary);">Nama Label Service Charge</label>
          <input type="text" name="service_name" class="form-control" value="{{ optional($service)->service_name ?? 'Service Charge 5%' }}" placeholder="Contoh: Service Charge 5%" required>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold" style="font-size:0.85rem; color: var(--text-primary);">Tarif Layanan (%)</label>
            <div class="input-group">
              <input type="number" step="0.01" name="rate_percent" class="form-control" value="{{ optional($service)->rate_percent ?? 5.00 }}" placeholder="5.00" required>
              <span class="input-group-text">%</span>
            </div>
          </div>
          <div class="col-md-6 d-flex align-items-center">
            <div class="form-check form-switch mt-4">
              <input class="form-check-input" type="checkbox" name="is_taxable" id="switchServiceTaxable" value="1" {{ optional($service)->is_taxable ? 'checked' : '' }}>
              <label class="form-check-label fw-semibold" for="switchServiceTaxable" style="color: var(--text-primary);">Service Charge Dena Pajak (Kena DPP PB1)</label>
            </div>
          </div>
        </div>

        <div class="form-check form-switch mb-4">
          <input class="form-check-input" type="checkbox" name="is_active" id="switchServiceActive" value="1" {{ optional($service)->is_active ? 'checked' : '' }}>
          <label class="form-check-label fw-semibold" for="switchServiceActive" style="color: var(--text-primary);">Aktifkan Service Charge pada Order Kasir</label>
        </div>

        <div class="d-flex justify-content-end">
          <button type="submit" class="btn btn-success px-4 btn-loading" id="btnSaveService">
            <i class="bi bi-check2-circle me-1"></i>Simpan Service Charge
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Simulation Preview Card -->
<div class="card mt-4 p-4" style="background: var(--bg-surface); border: 1.5px solid var(--border-color); border-radius: 16px;">
  <h6 class="fw-bold mb-3" style="color: var(--text-primary);"><i class="bi bi-calculator me-2">="color:#3b82f6;"></i>Simulasi Kalkulasi Checkout Struk Kasir</h6>
  <div class="table-responsive">
    <table class="table table-sm align-middle mb-0" style="font-size:0.88rem; color: var(--text-primary);">
      <thead style="background: var(--bg-elevated); color: var(--text-primary); border-bottom: 2px solid var(--border-subtle);">
        <tr>
          <th style="color: var(--text-primary);">Item Makanan / Minuman</th>
          <th class="text-end" style="color: var(--text-primary);">Subtotal Pesanan</th>
          <th class="text-end" style="color: var(--text-primary);">Diskon</th>
          <th class="text-end" style="color: var(--text-primary);">Service Charge (5%)</th>
          <th class="text-end" style="color: var(--text-primary);">Dasar Pajak (DPP)</th>
          <th class="text-end" style="color: var(--text-primary);">Pajak PB1 (10%)</th>
          <th class="text-end fw-bold" style="color: var(--text-primary);">Grand Total Struk</th>
        </tr>
      </thead>
      <tbody>
        <tr style="border-bottom: 1px solid var(--border-subtle);">
          <td style="color: var(--text-primary);">2x Ayam Geprek + 2x Ice Tea</td>
          <td class="text-end" style="color: var(--text-secondary);">Rp 100.000</td>
          <td class="text-end text-danger">- Rp 10.000</td>
          <td class="text-end text-success">+ Rp 4.500</td>
          <td class="text-end" style="color: var(--text-secondary);">Rp 94.500</td>
          <td class="text-end text-primary">+ Rp 9.450</td>
          <td class="text-end fw-bold text-success fs-6">Rp 103.950</td>
        </tr>
      </tbody>
    </table>
  </div>

</div>
@endsection

{{-- Flash session notifications --}}
@if(session('success'))
  <script>document.addEventListener('DOMContentLoaded', function() { if (typeof NexoraToast !== 'undefined') NexoraToast('{{ session('success') }}', 'success'); });</script>
@endif
@if(session('error'))
  <script>document.addEventListener('DOMContentLoaded', function() { if (typeof NexoraToast !== 'undefined') NexoraToast('{{ session('error') }}', 'danger'); });</script>
@endif

@push('scripts')
<script>
  $(document).ready(function() {
    // Form Tax Submit Handler
    $('#formTax').on('submit', function(e) {
      e.preventDefault();
      var btn = $('#btnSaveTax');
      var originalHtml = btn.html();
      btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...');

      setTimeout(function() {
        $.ajax({
          url: $('#formTax').attr('action'),
          method: 'POST',
          data: $('#formTax').serialize(),
          success: function(res) {
            btn.prop('disabled', false).html(originalHtml);
            if (typeof NexoraToast !== 'undefined') {
              NexoraToast(res.message || 'Pengaturan Pajak berhasil disimpan!', 'success');
            } else {
              alert(res.message);
            }
            $('#taxStatusBadge')
              .toggleClass('bg-success', $('#switchTaxActive').is(':checked'))
              .toggleClass('bg-secondary', !$('#switchTaxActive').is(':checked'))
              .text($('#switchTaxActive').is(':checked') ? 'Aktif' : 'Non-Aktif');
          },
          error: function(xhr) {
            btn.prop('disabled', false).html(originalHtml);
            var errMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
            if (typeof NexoraToast !== 'undefined') {
              NexoraToast(errMessage, 'danger');
            } else {
              alert(errMessage);
            }
          }
        });
      }, 400); // 400ms feedback latency rule
    });

    // Form Service Submit Handler
    $('#formService').on('submit', function(e) {
      e.preventDefault();
      var btn = $('#btnSaveService');
      var originalHtml = btn.html();
      btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...');

      setTimeout(function() {
        $.ajax({
          url: $('#formService').attr('action'),
          method: 'POST',
          data: $('#formService').serialize(),
          success: function(res) {
            btn.prop('disabled', false).html(originalHtml);
            if (typeof NexoraToast !== 'undefined') {
              NexoraToast(res.message || 'Pengaturan Service Charge berhasil disimpan!', 'success');
            } else {
              alert(res.message);
            }
            $('#serviceStatusBadge')
              .toggleClass('bg-success', $('#switchServiceActive').is(':checked'))
              .toggleClass('bg-secondary', !$('#switchServiceActive').is(':checked'))
              .text($('#switchServiceActive').is(':checked') ? 'Aktif' : 'Non-Aktif');
          },
          error: function(xhr) {
            btn.prop('disabled', false).html(originalHtml);
            var errMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
            if (typeof NexoraToast !== 'undefined') {
              NexoraToast(errMessage, 'danger');
            } else {
              alert(errMessage);
            }
          }
        });
      }, 400); // 400ms feedback latency rule
    });
  });
</script>
@endpush

