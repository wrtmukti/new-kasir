@extends('admin.layouts.app')

@section('title', 'Tambah Cabang Baru')

@php $activeMenu = 'setting' @endphp

@section('content')
<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
  <div>
    <h1 style="color:var(--text-primary); font-size:1.45rem; font-weight:700;">Tambah Cabang Baru</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.order.index') }}" class="text-muted-c text-decoration-none">Beranda</a>
      <i class="bi bi-chevron-right text-muted-c" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.setting.index') }}" class="text-muted-c text-decoration-none">Setting</a>
      <i class="bi bi-chevron-right text-muted-c" style="font-size:0.6rem;"></i>
      <span style="color:var(--text-primary); font-weight:600;">Tambah Cabang</span>
    </div>
  </div>
  <div>
    <a href="{{ route('admin.setting.index') }}" class="btn btn-outline-soft rounded-3 px-3 py-2 fw-semibold" style="font-size:0.88rem;">
      <i class="bi bi-arrow-left me-1"></i>Kembali ke Setting
    </a>
  </div>
</div>

@if(session('error'))
  <div class="alert alert-danger rounded-3 border-0 mb-4 shadow-sm">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
  </div>
@endif

@if(isset($errors) && $errors->any())
  <div class="alert alert-danger rounded-3 border-0 mb-4 shadow-sm">
    <div class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-1.5"></i>Mohon periksa kembali isian form:</div>
    <ul class="mb-0 ps-3" style="font-size:0.85rem;">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="row g-4 justify-content-center">
  <div class="col-lg-8">
    <form action="{{ route('admin.outlets.store') }}" method="POST" id="formAddOutlet">
      @csrf

      <div class="card rounded-4 border-0 shadow-sm mb-4" style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
        <div class="card-header bg-transparent border-0 p-4 pb-2">
          <h5 class="fw-bold mb-1" style="color:var(--text-primary); font-size:1.1rem;">
            <i class="bi bi-shop me-2 text-primary"></i>Informasi Gerai Cabang Baru
          </h5>
          <p class="text-muted-c mb-0" style="font-size:0.83rem;">
            Daftarkan cabang baru untuk brand <strong style="color:var(--text-primary);">{{ $suggestedBrand }}</strong>. Sesi kasir Anda akan langsung dialihkan ke cabang baru setelah berhasil disimpan.
          </p>
        </div>
        <div class="card-body p-4 pt-2">
          <div class="row g-3">
            <div class="col-md-7">
              <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">
                Nama Cabang Outlet <span class="text-danger">*</span>
              </label>
              <input type="text" name="outlet_name" id="outletName" class="form-control-modern @error('outlet_name') is-invalid @enderror" 
                     placeholder="Contoh: {{ $suggestedBrand }} - Bekasi" value="{{ old('outlet_name') }}" required>
              <small class="text-muted-c" style="font-size:0.75rem;">Rekomendasi: <code>{{ $suggestedBrand }} - [Kota / Area]</code></small>
              @error('outlet_name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-5">
              <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">Kode Singkatan Cabang</label>
              <input type="text" name="outlet_code" id="outletCode" class="form-control-modern @error('outlet_code') is-invalid @enderror" 
                     placeholder="Contoh: BKS" value="{{ old('outlet_code') }}" style="text-transform:uppercase;">
              <small class="text-muted-c" style="font-size:0.75rem;">Untuk penomoran meja & struk.</small>
              @error('outlet_code')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">Nama Area / Lokasi Cabang</label>
              <input type="text" name="outlet_branch" class="form-control-modern @error('outlet_branch') is-invalid @enderror" 
                     placeholder="Contoh: Bekasi Summarecon" value="{{ old('outlet_branch') }}">
              @error('outlet_branch')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">Status Operasional</label>
              <select name="outlet_status" class="form-select-modern">
                <option value="1" {{ old('outlet_status', '1') == '1' ? 'selected' : '' }}>🟢 Buka / Aktif (Siap Transaksi)</option>
                <option value="0" {{ old('outlet_status') == '0' ? 'selected' : '' }}>⚪ Tutup Sementara</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">No. Telepon / WhatsApp Cabang</label>
              <input type="text" name="outlet_phone" class="form-control-modern @error('outlet_phone') is-invalid @enderror" 
                     placeholder="Contoh: 021-88991234 / 081298765432" value="{{ old('outlet_phone') }}">
              @error('outlet_phone')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">Email Kontak Cabang</label>
              <input type="email" name="outlet_email" class="form-control-modern @error('outlet_email') is-invalid @enderror" 
                     placeholder="Contoh: bekasi@domain.com" value="{{ old('outlet_email') }}">
              @error('outlet_email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label class="form-label-modern fw-semibold" style="color:var(--text-secondary); font-size:0.85rem;">Alamat Fisik Lengkap Gerai</label>
              <textarea name="outlet_address" rows="3" class="form-control-modern @error('outlet_address') is-invalid @enderror" 
                        placeholder="Masukkan alamat lengkap cabang...">{{ old('outlet_address') }}</textarea>
              @error('outlet_address')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>

      {{-- Automated Setup Card --}}
      <div class="card rounded-4 border-0 shadow-sm mb-4 p-3.5" style="background: var(--bg-elevated-2); border: 1px dashed var(--border-strong) !important;">
        <div class="d-flex align-items-start gap-3">
          <div class="rounded-circle p-2 bg-primary-subtle text-primary mt-0.5">
            <i class="bi bi-shield-check fs-5"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1" style="font-size:0.9rem; color:var(--text-primary);">Setup Otomatis Cabang</h6>
            <p class="text-muted-c mb-0" style="font-size:0.82rem; line-height:1.45;">
              Sistem akan otomatis mengaktifkan <strong style="color:var(--text-primary);">Pajak PB1 (10%)</strong>, <strong style="color:var(--text-primary);">Service Charge (5%)</strong>, dan <strong style="color:var(--text-primary);">Shift Harian (Cutoff 03:00)</strong> untuk cabang baru ini sehingga kasir bisa langsung mulai transaksi.
            </p>
          </div>
        </div>
      </div>

      <div class="d-flex align-items-center justify-content-end gap-2.5 mb-5">
        <a href="{{ route('admin.setting.index') }}" class="btn btn-outline-soft rounded-3 px-4 py-2.5 fw-semibold" style="font-size:0.88rem;">
          Batal
        </a>
        <button type="submit" class="btn btn-primary rounded-3 px-4 py-2.5 fw-semibold" id="btnSaveOutlet" style="font-size:0.88rem;">
          <i class="bi bi-check2-circle me-1.5"></i>Simpan & Aktifkan Cabang
        </button>
      </div>

    </form>
  </div>

  {{-- KOLOM KANAN: DAFTAR CABANG YANG SUDAH ADA --}}
  <div class="col-lg-4">
    <div class="card rounded-4 border-0 shadow-sm p-4 mb-4" style="background: var(--bg-surface); border: 1px solid var(--border-subtle) !important;">
      <h6 class="fw-bold mb-3" style="color:var(--text-primary); font-size:0.95rem;">
        <i class="bi bi-buildings me-2 text-warning"></i>Cabang Terdaftar ({{ $currentOutlets->count() }})
      </h6>
      <div class="d-flex flex-column gap-2.5">
        @foreach($currentOutlets as $ot)
          <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" 
               style="background: var(--bg-elevated); border-color: var(--border-subtle) !important;">
            <div>
              <div class="fw-semibold" style="font-size:0.88rem; color:var(--text-primary);">{{ $ot->outlet_name }}</div>
              <small class="text-muted-c" style="font-size:0.75rem;">Kode: <code style="color:var(--accent-1); background:var(--bg-elevated-2); padding:2px 4px; border-radius:3px;">{{ $ot->outlet_code }}</code></small>
            </div>
            <span class="badge {{ $ot->outlet_status ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-2 py-0.5" style="font-size:0.72rem;">
              {{ $ot->outlet_status ? 'Aktif' : 'Tutup' }}
            </span>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
