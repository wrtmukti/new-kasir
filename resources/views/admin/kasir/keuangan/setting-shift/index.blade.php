@extends('admin.layouts.app')

@section('title', 'Pengaturan Jam Operasional & Buka Tutup Kasir')

@php $activeMenu = 'setting-shift' @endphp

@push('styles')
<style>
  .mode-box-card {
    background: var(--bg-subtle, rgba(255, 255, 255, 0.03));
    border: 1.5px solid var(--border-subtle, rgba(255, 255, 255, 0.1));
    border-radius: 14px;
    padding: 1.25rem;
    cursor: pointer;
    transition: all 0.25s ease;
    height: 100%;
    position: relative;
  }

  .mode-box-card:hover {
    border-color: #3b82f6;
    background: rgba(59, 130, 246, 0.05);
    transform: translateY(-2px);
  }

  .mode-box-card.active {
    border-color: #3b82f6;
    background: rgba(59, 130, 246, 0.12);
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.25);
  }

  .mode-icon-circle {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    margin-bottom: 0.75rem;
  }

  /* Subtle Shift Preset Buttons */
  .btn-subtle-preset {
    background: var(--bg-surface, rgba(255, 255, 255, 0.04));
    border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.1));
    color: var(--text-secondary, #94a3b8);
    border-radius: 6px;
    font-size: 0.76rem;
    padding: 0.25rem 0.65rem;
    transition: all 0.2s ease;
    font-weight: 500;
  }
  .btn-subtle-preset:hover {
    background: rgba(59, 130, 246, 0.12);
    border-color: rgba(59, 130, 246, 0.35);
    color: var(--text-primary, #f8fafc);
  }
  [data-theme="light"] .btn-subtle-preset {
    background: #f8fafc;
    border-color: #e2e8f0;
    color: #64748b;
  }
  [data-theme="light"] .btn-subtle-preset:hover {
    background: #eff6ff;
    border-color: #93c5fd;
    color: #1d4ed8;
  }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <h1>Pengaturan Jam Operasional & Buka Tutup Kasir</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Beranda</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Keuangan & Setting</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Buka Tutup & Kasir</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('admin.keuangan.shift-operational.index') }}" class="btn btn-outline-primary rounded-pill px-3 py-2 btn-sm fw-semibold">
      <i class="bi bi-cash-stack me-1"></i> Buka / Tutup Kasir
    </a>
    <a href="{{ route('admin.reports.shifts') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm">
      <i class="bi bi-shield-lock me-1"></i> Audit Closing (Z-Report)
    </a>
  </div>
</div>

<!-- Session Flash Notification Listener -->
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 12px;">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<!-- CARD 1: PENGATURAN MODE BUKA TUTUP KASIR & JAM OPERASIONAL -->
<div class="card mb-4 border-0 shadow-sm" style="border: 1px solid var(--border-subtle) !important; border-radius: 1rem;">
  <div class="card-header-flex py-3 px-4" style="border-bottom: 1px solid var(--border-subtle);">
    <div class="d-flex align-items-center gap-2">
      <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:38px; height:38px; background: rgba(99, 102, 241, 0.15); color: var(--accent-1, #6366f1);">
        <i class="bi bi-shop-window fs-5"></i>
      </div>
      <div>
        <h6 class="mb-0 fw-bold">Pengaturan Mode Operasional Toko & Cut-Off Kasir</h6>
        <small class="text-muted-c" style="font-size:0.75rem;">Atur metode pengoperasian kasir (Manual Bebas vs Terjadwal Otomatis), modal awal tetap, dan tanggal cut-off bisnis</small>
      </div>
    </div>
    <span class="chip-tag px-3 py-1 rounded-pill" style="background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25); font-weight:600; font-size:0.75rem;">
      <i class="bi bi-shield-check me-1"></i>Kasir & Tata Kelola Laci
    </span>
  </div>

  <div class="card-body p-4">
    <form action="{{ route('admin.keuangan.setting-shift.update-cutoff') }}" method="POST" id="formCutoff">
      @csrf
      
      <!-- SECTION 1: PILIH MODE PENGOPERASIAN KASIR -->
      <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <label class="form-label-modern mb-0 fw-bold text-uppercase" style="font-size:0.8rem; letter-spacing:0.5px;">1. Pilih Mode Pengoperasian Kasir</label>
          <span class="text-muted-c" style="font-size:0.75rem;">Pilih alur kerja kasir yang paling cocok untuk outlet Anda</span>
        </div>

        <div class="row g-3">
          <!-- Mode 1: Manual (Buka & Tutup Bebas) -->
          <div class="col-md-6">
            <div class="mode-box-card @if(($setting->shift_mode ?? 'auto_master') === 'manual') active @endif" onclick="selectShiftMode('manual')">
              <div class="d-flex align-items-center justify-content-between mb-2.5">
                <div class="mode-icon-circle" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                  <i class="bi bi-sliders"></i>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-0.5" style="font-size:0.68rem; font-weight:600;">
                    Bebas & Fleksibel
                  </span>
                  <input type="radio" name="shift_mode" value="manual" id="mode_manual" class="form-check-input" @if(($setting->shift_mode ?? 'auto_master') === 'manual') checked @endif style="cursor: pointer;">
                </div>
              </div>
              <div class="fw-bold mb-1 fs-6" style="color: var(--text-primary);">Manual (Buka & Tutup Bebas)</div>
              <p class="text-muted-c mb-2.5" style="font-size:0.82rem; line-height: 1.4;">Kasir buka dan tutup toko secara bebas sesuai jam riil user datang dan pulang tanpa batasan jadwal kaku.</p>
              <ul class="list-unstyled mb-0 text-muted-c" style="font-size:0.77rem; line-height: 1.65;">
                <li><i class="bi bi-check-circle-fill text-warning me-1.5"></i>Buka & tutup kasir bebas jam berapa saja</li>
                <li><i class="bi bi-check-circle-fill text-warning me-1.5"></i>Kasir mengisi modal kas awal setiap kali buka kasir</li>
                <li><i class="bi bi-check-circle-fill text-warning me-1.5"></i>Pas closing, kasir isi uang fisik aktual di laci</li>
                <li><i class="bi bi-check-circle-fill text-warning me-1.5"></i>Owner tinggal cek selisih fisik vs transaksi sistem</li>
              </ul>
            </div>
          </div>

          <!-- Mode 2: Otomatis (Jadwal Tetap & Standar Modal) -->
          <div class="col-md-6">
            <div class="mode-box-card @if(($setting->shift_mode ?? 'auto_master') !== 'manual') active @endif" onclick="selectShiftMode('auto_master')">
              <div class="d-flex align-items-center justify-content-between mb-2.5">
                <div class="mode-icon-circle" style="background: rgba(99, 102, 241, 0.15); color: var(--accent-1, #6366f1);">
                  <i class="bi bi-shop"></i>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-0.5" style="font-size:0.68rem; font-weight:600;">
                    Terjadwal & Rekomendasi
                  </span>
                  <input type="radio" name="shift_mode" value="auto_master" id="mode_auto_master" class="form-check-input" @if(($setting->shift_mode ?? 'auto_master') !== 'manual') checked @endif style="cursor: pointer;">
                </div>
              </div>
              <div class="fw-bold mb-1 fs-6" style="color: var(--text-primary);">Otomatis (Jadwal Tetap & Standar Modal)</div>
              <p class="text-muted-c mb-2.5" style="font-size:0.82rem; line-height: 1.4;">Jam operasional toko diset tetap. Modal kas awal sudah diset paten (misal Rp 300rb) dan otomatis selalu sama setiap hari.</p>
              <ul class="list-unstyled mb-0 text-muted-c" style="font-size:0.77rem; line-height: 1.65;">
                <li><i class="bi bi-check-circle-fill text-primary me-1.5"></i>Setting jam buka dan tutup toko operasional</li>
                <li><i class="bi bi-check-circle-fill text-primary me-1.5"></i>Modal kas awal otomatis terisi nominal tetap setiap hari</li>
                <li><i class="bi bi-check-circle-fill text-primary me-1.5"></i>Periode transaksi dihitung otomatis by tanggal</li>
                <li><i class="bi bi-check-circle-fill text-primary me-1.5"></i>Sistem otomatis hitung kebutuhan top-up modal kasir esok hari</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 2: DYNAMIC SETTINGS SESUAI MODE -->
      <!-- BOX A: KONFIGURASI MODE OTOMATIS (TERJADWAL & MODAL TETAP) -->
      <div id="autoScheduleBox" class="p-3.5 rounded-3 mb-4" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle); display: {{ (($setting->shift_mode ?? 'auto_master') !== 'manual') ? 'block' : 'none' }};">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-calendar-check text-primary fs-5"></i>
            <div>
              <h6 class="mb-0 fw-bold" style="font-size: 0.92rem;">Konfigurasi Jadwal Toko & Modal Awal Kasir</h6>
              <small class="text-muted-c" style="font-size:0.74rem;">Jam operasional toko dan nominal kas laci yang selalu disiapkan</small>
            </div>
          </div>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill" style="font-size:0.7rem;">Mode Otomatis Aktif</span>
        </div>

        <div class="row g-3 align-items-end">
          <!-- Jam Buka Toko -->
          <div class="col-md-3">
            <label for="open_time" class="form-label-modern mb-1 fw-semibold" style="font-size: 0.8rem;">
              Jam Buka Kasir / Toko <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text border-end-0" style="background: var(--bg-surface, rgba(255,255,255,0.04)); border-color: var(--border-subtle); color: var(--text-secondary);">
                <i class="bi bi-door-open"></i>
              </span>
              <input type="text" name="open_time" id="open_time" 
                     class="form-control form-control-modern border-start-0 font-monospace" 
                     value="{{ \Carbon\Carbon::parse($primaryShift->start_time ?? '08:00')->format('H:i') }}" 
                     placeholder="08:00" maxlength="5" 
                     oninput="formatTime24(this)" onblur="normalizeTime24(this)" list="list24h">
            </div>
          </div>

          <!-- Jam Tutup Toko -->
          <div class="col-md-3">
            <label for="close_time" class="form-label-modern mb-1 fw-semibold" style="font-size: 0.8rem;">
              Jam Tutup Kasir / Toko <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text border-end-0" style="background: var(--bg-surface, rgba(255,255,255,0.04)); border-color: var(--border-subtle); color: var(--text-secondary);">
                <i class="bi bi-door-closed"></i>
              </span>
              <input type="text" name="close_time" id="close_time" 
                     class="form-control form-control-modern border-start-0 font-monospace" 
                     value="{{ \Carbon\Carbon::parse($primaryShift->end_time ?? '22:00')->format('H:i') }}" 
                     placeholder="22:00" maxlength="5" 
                     oninput="formatTime24(this)" onblur="normalizeTime24(this)" list="list24h">
            </div>
          </div>

          <!-- Modal Kas Awal Tetap -->
          <div class="col-md-6">
            <label for="default_starting_cash" class="form-label-modern mb-1 fw-semibold" style="font-size: 0.8rem;">
              Modal Kas Awal Tetap Kasir (Rp) <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text border-end-0" style="background: var(--bg-surface, rgba(255,255,255,0.04)); border-color: var(--border-subtle); color: #10b981; font-weight:bold;">
                Rp
              </span>
              <input type="number" name="default_starting_cash" id="default_starting_cash" 
                     class="form-control form-control-modern border-start-0 font-monospace fw-bold" 
                     value="{{ (int)($primaryShift->default_starting_cash ?? 300000) }}" 
                     step="any" min="0" required style="color: var(--text-primary);">
            </div>
          </div>
        </div>

        <!-- Presets Cepat Jam & Modal -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-2.5 border-top" style="border-color: var(--border-subtle) !important;">
          <div class="d-flex flex-wrap gap-1.5 align-items-center">
            <span class="text-muted-c me-1" style="font-size:0.73rem;">Preset Jam:</span>
            <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setOperationalPreset('00:00', '23:59')">
              <i class="bi bi-lightning-charge-fill text-warning me-1"></i>24 Jam (00:00 - 23:59)
            </button>
            <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setOperationalPreset('08:00', '22:00')">
              Pagi - Malam (08:00 - 22:00)
            </button>
            <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setOperationalPreset('10:00', '23:00')">
              Siang - Malam (10:00 - 23:00)
            </button>
            <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setOperationalPreset('16:30', '23:00')">
              Sore - Malam (16:30 - 23:00)
            </button>
          </div>
          <div class="d-flex flex-wrap gap-1.5 align-items-center">
            <span class="text-muted-c me-1" style="font-size:0.73rem;">Preset Modal:</span>
            <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setStartingCashPreset(100000)">100rb</button>
            <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setStartingCashPreset(200000)">200rb</button>
            <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setStartingCashPreset(300000)">300rb</button>
            <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setStartingCashPreset(500000)">500rb</button>
          </div>
        </div>
      </div>

      <!-- BOX B: KONFIGURASI MODE MANUAL (BEBAS & FLEKSIBEL) -->
      <div id="manualScheduleBox" class="p-3.5 rounded-3 mb-4" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle); display: {{ (($setting->shift_mode ?? 'auto_master') === 'manual') ? 'block' : 'none' }};">
        <div class="d-flex align-items-center justify-content-between mb-2.5">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle-fill text-warning fs-5"></i>
            <div>
              <h6 class="mb-0 fw-bold" style="font-size: 0.92rem;">Alur Pengoperasian Manual (Bebas)</h6>
              <small class="text-muted-c" style="font-size:0.74rem;">Kasir bebas menentukan waktu buka-tutup dan menginput modal kas awal saat bertugas</small>
            </div>
          </div>
          <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill" style="font-size:0.7rem;">Mode Manual Aktif</span>
        </div>
        <div class="p-3 rounded-2" style="background: rgba(245, 158, 11, 0.08); border-left: 3px solid #f59e0b;">
          <p class="mb-1 text-muted-c" style="font-size:0.8rem; line-height:1.5;">
            Pada <strong>Mode Manual</strong>, kasir tidak dibatasi oleh jam buka/tutup toko. Kasir dapat langsung mengklik menu <strong>"Buka Kasir"</strong> kapan saja dan menginput uang modal awal laci kasir secara langsung di layar kasir POS.
          </p>
          <small class="text-warning fw-semibold" style="font-size:0.75rem;">
            <i class="bi bi-lightbulb me-1"></i> Tips Owner: Saat tutup kasir (closing), kasir tetap wajib menghitung uang fisik aktual di laci agar owner dapat melihat selisih kas di laporan Z-Report.
          </small>
        </div>
      </div>

      <!-- SECTION 3: JAM CUT-OFF OPERASIONAL & PERLINDUNGAN STRICT -->
      <div class="row g-4 align-items-end pt-2">
        <div class="col-md-4">
          <label for="daily_cutoff_time" class="form-label-modern mb-1 fw-semibold">
            Jam Cut-Off Operasional Harian <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);"><i class="bi bi-clock"></i></span>
            <input type="time" name="daily_cutoff_time" id="daily_cutoff_time" class="form-control form-control-modern" value="{{ \Carbon\Carbon::parse($setting->daily_cutoff_time ?? '03:00')->format('H:i') }}" required>
          </div>
          <div class="text-muted-c mt-1" style="font-size: 0.78rem;">
            Transaksi setelah jam ini dianggap sebagai <strong>Tanggal Bisnis Baru</strong> (Rekomendasi resto: <code>03:00</code> Pagi).
          </div>
        </div>

        <div class="col-md-5">
          <div class="form-check form-switch pt-2">
            <input class="form-check-input" type="checkbox" name="auto_lock_unclosed" id="auto_lock_unclosed" value="1" @if($setting->auto_lock_unclosed ?? 1) checked @endif style="width: 2.4em; height: 1.2em; cursor: pointer;">
            <label class="form-check-label fw-semibold ms-2" for="auto_lock_unclosed">
              Auto-Lock Kasir Kemarin (Strict Protection)
            </label>
          </div>
          <div class="text-muted-c mt-1" style="font-size: 0.78rem;">
            Kunci layar POS kasir jika sesi hari kemarin belum di-close oleh kasir sebelumnya.
          </div>
        </div>

        <div class="col-md-3 text-end">
          <button type="submit" class="btn btn-primary-grad w-100 py-2.5 rounded-3 btn-loading" id="btnSaveCutoff">
            <i class="bi bi-check-circle-fill me-1"></i> Simpan Pengaturan
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- CARD 2: STATUS & PANDUAN ALUR KERJA KASIR SAAT INI -->
<div class="card border-0 shadow-sm" style="border: 1px solid var(--border-subtle) !important; border-radius: 1rem;">
  <div class="card-header-flex py-3 px-4" style="border-bottom: 1px solid var(--border-subtle);">
    <div class="d-flex align-items-center gap-2">
      <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
        <i class="bi bi-activity fs-5"></i>
      </div>
      <div>
        <h6 class="mb-0 fw-bold">Status & Panduan Operasional Kasir</h6>
        <small class="text-muted-c" style="font-size:0.75rem;">Ringkasan konfigurasi aktif cabang dan panduan alur kerja kasir POS</small>
      </div>
    </div>
    <a href="{{ route('admin.keuangan.shift-operational.index') }}" class="btn btn-sm btn-outline-primary rounded-3 px-3 py-1.5 fw-semibold d-flex align-items-center gap-1.5">
      <i class="bi bi-cash-stack"></i>
      <span>Ke Layar Buka / Tutup Kasir</span>
    </a>
  </div>

  <div class="card-body p-4">
    <!-- LIVE OVERVIEW BAR -->
    <div class="row g-3 mb-4">
      <!-- Item 1: Status Sesi Kasir Hari Ini -->
      <div class="col-md-3">
        <div class="p-3 rounded-3 h-100" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle);">
          <span class="text-muted-c fw-semibold text-uppercase d-block mb-1" style="font-size: 0.65rem; letter-spacing: 0.4px;">Status Kasir Hari Ini</span>
          @if($activeShift)
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">
                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> KASIR BUKA
              </span>
            </div>
            <small class="text-muted-c d-block mt-1 text-truncate" style="font-size: 0.72rem;">
              {{ $activeShift->shift_name }} • {{ \Carbon\Carbon::parse($activeShift->opened_at)->format('H:i') }} WIB
            </small>
          @else
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                <i class="bi bi-dash-circle me-1"></i> KASIR TUTUP
              </span>
            </div>
            <small class="text-muted-c d-block mt-1" style="font-size: 0.72rem;">Belum ada sesi kasir aktif</small>
          @endif
        </div>
      </div>

      <!-- Item 2: Mode Kasir Aktif -->
      <div class="col-md-3">
        <div class="p-3 rounded-3 h-100" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle);">
          <span class="text-muted-c fw-semibold text-uppercase d-block mb-1" style="font-size: 0.65rem; letter-spacing: 0.4px;">Mode Pengoperasian</span>
          <div class="fw-bold" style="font-size: 0.88rem; color: var(--text-primary);">
            <span id="modeSummaryBadge" class="badge {{ (($setting->shift_mode ?? 'auto_master') === 'manual') ? 'bg-warning-subtle text-warning border border-warning-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} px-2.5 py-1 rounded-pill" style="font-size: 0.75rem;">
              <i class="bi {{ (($setting->shift_mode ?? 'auto_master') === 'manual') ? 'bi-sliders' : 'bi-shop' }} me-1"></i>
              {{ (($setting->shift_mode ?? 'auto_master') === 'manual') ? 'Manual (Buka Bebas)' : 'Otomatis (Terjadwal)' }}
            </span>
          </div>
          <small class="text-muted-c d-block mt-1" style="font-size: 0.72rem;">Sesuai konfigurasi toko aktif</small>
        </div>
      </div>

      <!-- Item 3: Jam Operasional -->
      <div class="col-md-3">
        <div class="p-3 rounded-3 h-100" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle);">
          <span class="text-muted-c fw-semibold text-uppercase d-block mb-1" style="font-size: 0.65rem; letter-spacing: 0.4px;">Jam Operasional Toko</span>
          <div class="fw-bold font-monospace" id="scheduleSummaryText" style="font-size: 0.86rem; color: var(--text-primary);">
            @if(($setting->shift_mode ?? 'auto_master') === 'manual')
              Fleksibel (Bebas Kapan Saja)
            @else
              {{ \Carbon\Carbon::parse($primaryShift->start_time ?? '08:00')->format('H:i') }} - {{ \Carbon\Carbon::parse($primaryShift->end_time ?? '22:00')->format('H:i') }} WIB
            @endif
          </div>
          <small class="text-muted-c d-block mt-1" style="font-size: 0.72rem;">Cut-off: {{ \Carbon\Carbon::parse($setting->daily_cutoff_time ?? '03:00')->format('H:i') }} WIB</small>
        </div>
      </div>

      <!-- Item 4: Modal Kas Awal Standar -->
      <div class="col-md-3">
        <div class="p-3 rounded-3 h-100" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle);">
          <span class="text-muted-c fw-semibold text-uppercase d-block mb-1" style="font-size: 0.65rem; letter-spacing: 0.4px;">Standar Modal Laci Kasir</span>
          <div class="fw-bold text-success font-monospace" id="modalSummaryText" style="font-size: 0.88rem;">
            @if(($setting->shift_mode ?? 'auto_master') === 'manual')
              Diinput Kasir Saat Buka
            @else
              Otomatis Rp {{ number_format($primaryShift->default_starting_cash ?? 300000, 0, ',', '.') }}
            @endif
          </div>
          <small class="text-muted-c d-block mt-1" style="font-size: 0.72rem;">Target laci kasir setiap hari</small>
        </div>
      </div>
    </div>

    <!-- PANDUAN 4 LANGKAH ALUR BUKA TUTUP KASIR (SEDERHANA & TO THE POINT) -->
    <div class="p-3.5 rounded-3" style="background: var(--bg-elevated-2, rgba(255,255,255,0.02)); border: 1px dashed var(--border-subtle);">
      <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size: 0.86rem; color: var(--text-primary);">
        <i class="bi bi-info-circle text-primary"></i>
        <span>Alur Kerja Buka Tutup Kasir Toko F&amp;B</span>
      </h6>
      <div class="row g-3">
        <div class="col-md-3">
          <div class="p-2.5 rounded-2 h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center gap-2 mb-1.5">
              <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:20px; height:20px; font-size:0.7rem;">1</span>
              <span class="fw-bold" style="font-size: 0.8rem; color: var(--text-primary);">Buka Kasir</span>
            </div>
            <p class="text-muted-c mb-0" style="font-size: 0.74rem; line-height: 1.4;">
              Kasir menghitung fisik modal laci kasir (misal Rp 300rb) dan menekan tombol Buka Kasir di awal jam kerja.
            </p>
          </div>
        </div>

        <div class="col-md-3">
          <div class="p-2.5 rounded-2 h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center gap-2 mb-1.5">
              <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:20px; height:20px; font-size:0.7rem;">2</span>
              <span class="fw-bold" style="font-size: 0.8rem; color: var(--text-primary);">Transaksi POS</span>
            </div>
            <p class="text-muted-c mb-0" style="font-size: 0.74rem; line-height: 1.4;">
              Sistem mencatat transaksi tunai & non-tunai. Pembayaran cash terkunci otomatis jika kasir belum dibuka.
            </p>
          </div>
        </div>

        <div class="col-md-3">
          <div class="p-2.5 rounded-2 h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center gap-2 mb-1.5">
              <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:20px; height:20px; font-size:0.7rem;">3</span>
              <span class="fw-bold" style="font-size: 0.8rem; color: var(--text-primary);">Tutup Kasir</span>
            </div>
            <p class="text-muted-c mb-0" style="font-size: 0.74rem; line-height: 1.4;">
              Di akhir hari, kasir menghitung seluruh uang fisik riil di laci dan menginput nominalnya pada form Tutup Kasir.
            </p>
          </div>
        </div>

        <div class="col-md-3">
          <div class="p-2.5 rounded-2 h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center gap-2 mb-1.5">
              <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:20px; height:20px; font-size:0.7rem;">4</span>
              <span class="fw-bold" style="font-size: 0.8rem; color: var(--text-primary);">Audit Selisih</span>
            </div>
            <p class="text-muted-c mb-0" style="font-size: 0.74rem; line-height: 1.4;">
              Sistem menghitung selisih kas fisik vs transaksi sistem di struk Z-Report; Owner memantau keakuratan kas.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<datalist id="list24h">
  <option value="00:00">00:00 (Tengah Malam)</option>
  <option value="06:00">06:00 (Pagi)</option>
  <option value="07:00">07:00</option>
  <option value="08:00">08:00</option>
  <option value="09:00">09:00</option>
  <option value="10:00">10:00</option>
  <option value="12:00">12:00 (Siang)</option>
  <option value="15:00">15:00</option>
  <option value="16:00">16:00</option>
  <option value="16:30">16:30 (Sore)</option>
  <option value="17:00">17:00</option>
  <option value="20:00">20:00</option>
  <option value="22:00">22:00 (Malam)</option>
  <option value="23:00">23:00</option>
  <option value="23:59">23:59 (Akhir Hari)</option>
</datalist>
@endsection

@push('scripts')
<script>
  function selectShiftMode(mode) {
    document.querySelectorAll('.mode-box-card').forEach(box => box.classList.remove('active'));
    const radio = document.getElementById('mode_' + mode);
    if (radio) {
      radio.checked = true;
      radio.closest('.mode-box-card').classList.add('active');
    }

    const autoBox = document.getElementById('autoScheduleBox');
    const manualBox = document.getElementById('manualScheduleBox');
    const modeSummaryBadge = document.getElementById('modeSummaryBadge');
    const scheduleSummaryText = document.getElementById('scheduleSummaryText');
    const modalSummaryText = document.getElementById('modalSummaryText');

    if (mode === 'manual') {
      if (autoBox) autoBox.style.display = 'none';
      if (manualBox) manualBox.style.display = 'block';
      if (modeSummaryBadge) {
        modeSummaryBadge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill';
        modeSummaryBadge.innerHTML = '<i class="bi bi-sliders me-1"></i> Manual (Buka Bebas)';
      }
      if (scheduleSummaryText) scheduleSummaryText.textContent = 'Fleksibel (Bebas Jam Berapa Saja)';
      if (modalSummaryText) modalSummaryText.textContent = 'Diinput Kasir Saat Buka';
    } else {
      if (autoBox) autoBox.style.display = 'block';
      if (manualBox) manualBox.style.display = 'none';
      if (modeSummaryBadge) {
        modeSummaryBadge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill';
        modeSummaryBadge.innerHTML = '<i class="bi bi-shop me-1"></i> Otomatis (Terjadwal)';
      }
      if (scheduleSummaryText) {
        const openVal = document.getElementById('open_time')?.value || '08:00';
        const closeVal = document.getElementById('close_time')?.value || '22:00';
        scheduleSummaryText.textContent = openVal + ' - ' + closeVal + ' WIB';
      }
      if (modalSummaryText) {
        const cashVal = document.getElementById('default_starting_cash')?.value || '300000';
        modalSummaryText.textContent = 'Otomatis Rp ' + Number(cashVal).toLocaleString('id-ID');
      }
    }
  }

  function setOperationalPreset(openTime, closeTime) {
    const openInput = document.getElementById('open_time');
    const closeInput = document.getElementById('close_time');
    if (openInput) openInput.value = openTime;
    if (closeInput) closeInput.value = closeTime;
    const scheduleSummaryText = document.getElementById('scheduleSummaryText');
    if (scheduleSummaryText) scheduleSummaryText.textContent = openTime + ' - ' + closeTime + ' WIB';
    if (typeof NexoraToast === 'function') {
      NexoraToast({ title: 'Preset Terpilih', message: `Jam Operasional diset ke ${openTime} - ${closeTime}`, type: 'info' });
    }
  }

  function setStartingCashPreset(amount) {
    const cashInput = document.getElementById('default_starting_cash');
    if (cashInput) cashInput.value = amount;
    const modalSummaryText = document.getElementById('modalSummaryText');
    if (modalSummaryText) modalSummaryText.textContent = 'Otomatis Rp ' + Number(amount).toLocaleString('id-ID');
    if (typeof NexoraToast === 'function') {
      NexoraToast({ title: 'Modal Terpilih', message: `Modal Awal diset ke Rp ${Number(amount).toLocaleString('id-ID')}`, type: 'info' });
    }
  }

  function formatTime24(input) {
    let val = input.value.replace(/[^0-9]/g, '');
    if (val.length >= 3) {
      val = val.substring(0, 2) + ':' + val.substring(2, 4);
    }
    input.value = val;
  }

  function normalizeTime24(input) {
    let val = input.value.trim().replace(/[^0-9:]/g, '');
    if (!val) return;
    if (/^\d{1,2}$/.test(val)) {
      let h = Math.min(23, Math.max(0, parseInt(val, 10)));
      val = (h < 10 ? '0' : '') + h + ':00';
    } else if (/^\d{1,2}:\d{1,2}$/.test(val)) {
      let parts = val.split(':');
      let h = Math.min(23, Math.max(0, parseInt(parts[0], 10)));
      let m = Math.min(59, Math.max(0, parseInt(parts[1], 10)));
      val = (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
    }
    input.value = val;
    const scheduleSummaryText = document.getElementById('scheduleSummaryText');
    const openVal = document.getElementById('open_time')?.value || '08:00';
    const closeVal = document.getElementById('close_time')?.value || '22:00';
    if (scheduleSummaryText && document.getElementById('mode_auto_master')?.checked) {
      scheduleSummaryText.textContent = openVal + ' - ' + closeVal + ' WIB';
    }
  }

  document.querySelectorAll('.btn-loading').forEach(btn => {
    btn.closest('form')?.addEventListener('submit', function() {
      btn.disabled = true;
      btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...`;
    });
  });
</script>
@endpush
