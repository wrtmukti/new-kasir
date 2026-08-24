@extends('sys_admin.layouts.app')

@section('title', 'Tambah Outlet Baru')

@section('content')
<div class="container-fluid p-0" style="max-width: 960px;">

  {{-- Breadcrumb & Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1" style="font-size:0.82rem;">
          <li class="breadcrumb-item"><a href="{{ route('sys_admin.dashboard') }}" class="text-decoration-none text-muted-c">Dashboard</a></li>
          @if($fromClient && $selectedClient)
            <li class="breadcrumb-item"><a href="{{ route('sys_admin.clients.index') }}" class="text-decoration-none text-muted-c">Clients</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sys_admin.clients.show', $selectedClient->client_id) }}" class="text-decoration-none text-muted-c">{{ $selectedClient->client_name }}</a></li>
          @else
            <li class="breadcrumb-item"><a href="{{ route('sys_admin.outlets.index') }}" class="text-decoration-none text-muted-c">Outlets</a></li>
          @endif
          <li class="breadcrumb-item active fw-semibold" aria-current="page" style="color: var(--accent-1);">Tambah Outlet</li>
        </ol>
      </nav>
      <h4 class="fw-bold mb-0" style="font-size:1.45rem; color:var(--text-primary);">
        <i class="bi bi-shop-window me-2 text-primary"></i>Tambah Cabang Outlet Baru
      </h4>
    </div>
    <a href="{{ $returnUrl }}" class="btn btn-outline-soft rounded-3 px-3 py-2 fw-semibold" style="font-size:0.85rem;">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>

  @if(session('error'))
    <div class="alert alert-danger rounded-3 border-0 mb-4 shadow-sm">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    </div>
  @endif

  @if(isset($errors) && $errors->any())
    <div class="alert alert-danger rounded-3 border-0 mb-4 shadow-sm">
      <div class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-1.5"></i>Terdapat kesalahan pengisian form:</div>
      <ul class="mb-0 ps-3" style="font-size:0.85rem;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('sys_admin.outlets.store') }}" method="POST" id="formCreateOutlet">
    @csrf
    @if($fromClient)
      <input type="hidden" name="redirect_to" value="client">
    @endif

    {{-- SECTION 1: KLIEN --}}
    <div class="card rounded-4 border-0 shadow-sm mb-4" style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
      <div class="card-header bg-transparent border-0 p-4 pb-2">
        <h6 class="fw-bold mb-1" style="color:var(--text-primary);">
          <i class="bi bi-building me-2 text-primary"></i>1. Klien / Perusahaan Pemilik
        </h6>
        <p class="text-muted-c mb-0" style="font-size:0.8rem;">Pilih database klien tempat cabang outlet baru ini akan didaftarkan.</p>
      </div>
      <div class="card-body p-4 pt-2">
        <div class="mb-2">
          <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">
            Klien / Brand <span class="text-danger">*</span>
          </label>
          <select name="client_id" id="clientSelect" class="form-select-modern @error('client_id') is-invalid @enderror" required>
            <option value="" disabled {{ !$selectedClientId ? 'selected' : '' }}>-- Pilih Klien / Brand --</option>
            @foreach($clients as $c)
              <option value="{{ $c->client_id }}" 
                      data-brand="{{ $c->business_name ?? $c->client_name }}"
                      data-code="{{ $c->client_code }}"
                      data-db="{{ $c->database_name }}"
                      {{ old('client_id', $selectedClientId) === $c->client_id ? 'selected' : '' }}>
                [{{ $c->client_code }}] {{ $c->client_name }} ({{ $c->business_name ?? 'Brand' }})
              </option>
            @endforeach
          </select>
          @error('client_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
          <div id="clientInfoBox" class="mt-2.5 p-3 rounded-3 border" style="background: var(--bg-elevated); border-color: var(--border-subtle) !important; font-size:0.82rem; display:none;">
            <div class="d-flex align-items-center gap-2" style="color: var(--text-primary);">
              <i class="bi bi-database-check text-success fs-6"></i>
              <span>Target Database Fisik: <code id="clientTargetDb" class="fw-bold" style="color: var(--accent-1); background: var(--bg-elevated-2); padding: 2px 6px; border-radius: 4px;"></code></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- SECTION 2: INFORMASI CABANG --}}
    <div class="card rounded-4 border-0 shadow-sm mb-4" style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
      <div class="card-header bg-transparent border-0 p-4 pb-2">
        <h6 class="fw-bold mb-1" style="color:var(--text-primary);">
          <i class="bi bi-shop me-2 text-primary"></i>2. Informasi Cabang & Outlet
        </h6>
        <p class="text-muted-c mb-0" style="font-size:0.8rem;">Data identitas gerai cabang yang akan ditampilkan pada sistem POS & struk.</p>
      </div>
      <div class="card-body p-4 pt-2">
        <div class="row g-3">
          <div class="col-md-7">
            <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">
              Nama Lengkap Outlet <span class="text-danger">*</span>
            </label>
            <input type="text" name="outlet_name" id="outletNameInput" class="form-control-modern @error('outlet_name') is-invalid @enderror" 
                   placeholder="Contoh: Geprek Gambos - Bekasi" value="{{ old('outlet_name') }}" required>
            <small class="text-muted-c" style="font-size:0.75rem;">Format standar: <code>[Nama Brand] - [Kota / Area]</code></small>
            @error('outlet_name')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-5">
            <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">Kode Singkatan Cabang</label>
            <input type="text" name="outlet_code" id="outletCodeInput" class="form-control-modern @error('outlet_code') is-invalid @enderror" 
                   placeholder="Contoh: GGB-BKS" value="{{ old('outlet_code') }}" style="text-transform:uppercase;">
            <small class="text-muted-c" style="font-size:0.75rem;">Singkatan unik untuk kode meja & struk.</small>
            @error('outlet_code')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">Nama Wilayah / Cabang</label>
            <input type="text" name="outlet_branch" class="form-control-modern @error('outlet_branch') is-invalid @enderror" 
                   placeholder="Contoh: Bekasi Barat / Summarecon" value="{{ old('outlet_branch') }}">
            @error('outlet_branch')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">Status Operasional</label>
            <select name="outlet_status" class="form-select-modern">
              <option value="1" {{ old('outlet_status', '1') == '1' ? 'selected' : '' }}>🟢 Aktif (Siap Operasional)</option>
              <option value="0" {{ old('outlet_status') == '0' ? 'selected' : '' }}>⚪ Non-Aktif (Tutup / Maintenance)</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">Nomor Telepon Cabang</label>
            <input type="text" name="outlet_phone" class="form-control-modern @error('outlet_phone') is-invalid @enderror" 
                   placeholder="Contoh: 021-88997766 / 08123456789" value="{{ old('outlet_phone') }}">
            @error('outlet_phone')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">Email Kontak Cabang</label>
            <input type="email" name="outlet_email" class="form-control-modern @error('outlet_email') is-invalid @enderror" 
                   placeholder="Contoh: bekasi@brand.com" value="{{ old('outlet_email') }}">
            @error('outlet_email')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12">
            <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">Alamat Lengkap Gerai Cabang</label>
            <textarea name="outlet_address" rows="3" class="form-control-modern @error('outlet_address') is-invalid @enderror" 
                      placeholder="Masukkan alamat lengkap cabang...">{{ old('outlet_address') }}</textarea>
            @error('outlet_address')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- SECTION 3: SYSTEM AUTOMATION --}}
    <div class="card rounded-4 border-0 shadow-sm mb-4" style="background: var(--bg-elevated-2); border: 1px dashed var(--border-strong) !important;">
      <div class="card-body p-3.5 d-flex align-items-start gap-3">
        <div class="rounded-circle p-2 bg-primary-subtle text-primary mt-0.5">
          <i class="bi bi-magic fs-5"></i>
        </div>
        <div>
          <h6 class="fw-bold mb-1" style="font-size:0.9rem; color:var(--text-primary);">Otomatisasi Sistem Multi-Tenant</h6>
          <p class="text-muted-c mb-0" style="font-size:0.82rem; line-height:1.45;">
            Saat cabang disimpan, sistem akan otomatis menginisialisasi:
            <strong style="color: var(--text-primary);">Setting Outlet</strong>, <strong style="color: var(--text-primary);">Shift Harian (Cutoff 03:00)</strong>, <strong style="color: var(--text-primary);">Pajak PB1 (10%)</strong>, dan <strong style="color: var(--text-primary);">Service Charge (5%)</strong> pada database klien.
          </p>
        </div>
      </div>
    </div>

    {{-- FORM ACTIONS --}}
    <div class="d-flex align-items-center justify-content-end gap-2.5 mb-5">
      <a href="{{ $returnUrl }}" class="btn btn-outline-soft rounded-3 px-4 py-2.5 fw-semibold" style="font-size:0.88rem;">
        Batal
      </a>
      <button type="submit" class="btn btn-primary rounded-3 px-4 py-2.5 fw-semibold" id="btnSubmitOutlet" style="font-size:0.88rem;">
        <i class="bi bi-check2-circle me-1.5"></i>Simpan & Daftarkan Cabang
      </button>
    </div>

  </form>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const clientSelect = document.getElementById('clientSelect');
  const clientInfoBox = document.getElementById('clientInfoBox');
  const clientTargetDb = document.getElementById('clientTargetDb');
  const outletNameInput = document.getElementById('outletNameInput');
  const outletCodeInput = document.getElementById('outletCodeInput');

  function updateClientInfo() {
    const selectedOpt = clientSelect.options[clientSelect.selectedIndex];
    if (selectedOpt && selectedOpt.value) {
      const db = selectedOpt.getAttribute('data-db');
      const brand = selectedOpt.getAttribute('data-brand');
      const code = selectedOpt.getAttribute('data-code');
      
      clientTargetDb.textContent = db;
      clientInfoBox.style.display = 'block';

      if (!outletNameInput.value.trim() && brand) {
        outletNameInput.placeholder = brand + ' - [Kota/Area]';
      }
      if (!outletCodeInput.value.trim() && code) {
        outletCodeInput.placeholder = code + '-XXX';
      }
    } else {
      clientInfoBox.style.display = 'none';
    }
  }

  clientSelect.addEventListener('change', updateClientInfo);
  updateClientInfo();
});
</script>
@endpush
@endsection
