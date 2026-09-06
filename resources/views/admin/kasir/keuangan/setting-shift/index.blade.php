@extends('admin.layouts.app')

@section('title', 'Pengaturan Jam Buka Kasir & Modal Laci')

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

  /* Drawer Trigger Floating Tab on RIGHT EDGE */
  .kasir-drawer-tab {
    position: fixed;
    right: 0;
    left: auto;
    top: 45%;
    transform: translateY(-50%);
    z-index: 1040;
    background: var(--bg-surface, #1e293b);
    border: 1.5px solid var(--border-subtle, rgba(255, 255, 255, 0.15));
    border-right: none;
    border-radius: 12px 0 0 12px;
    padding: 10px 14px;
    box-shadow: -4px 6px 20px rgba(0, 0, 0, 0.35);
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    color: var(--text-primary, #f8fafc);
    font-size: 0.8rem;
  }
  @media (max-width: 991.98px) {
    .kasir-drawer-tab {
      right: 0;
      top: 55%;
    }
  }
  .kasir-drawer-tab:hover {
    background: var(--bg-elevated, #334155);
    border-color: #3b82f6;
    box-shadow: -6px 8px 25px rgba(59, 130, 246, 0.3);
    transform: translateY(-50%) translateX(-4px);
    color: #3b82f6;
  }
  [data-theme="light"] .kasir-drawer-tab {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #1e293b;
    box-shadow: -4px 6px 15px rgba(0, 0, 0, 0.08);
  }
  [data-theme="light"] .kasir-drawer-tab:hover {
    background: #eff6ff;
    border-color: #3b82f6;
    color: #2563eb;
  }
  .kasir-drawer-tab .pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    display: inline-block;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.35);
    animation: pulseKasirDot 2s infinite;
  }
  @keyframes pulseKasirDot {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
  }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <h1>Pengaturan Jam Buka Kasir & Modal Laci</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Beranda</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Keuangan & Setting</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Jam Buka Kasir</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('admin.keuangan.shift-operational.index') }}" class="btn btn-outline-primary rounded-pill px-3 py-2 btn-sm fw-semibold">
      <i class="bi bi-cash-stack me-1"></i> Buka / Tutup Kasir
    </a>
    <a href="{{ route('admin.reports.shifts') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm">
      <i class="bi bi-shield-lock me-1"></i> Audit Buka-Tutup (Z-Report)
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

<!-- KARTU FULL WIDTH FORM OPERASIONAL -->
<div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-subtle) !important; border-radius: 1rem;">
  <div class="card-header-flex py-3 px-4" style="border-bottom: 1px solid var(--border-subtle);">
    <div class="d-flex align-items-center gap-2">
      <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:38px; height:38px; background: rgba(99, 102, 241, 0.15); color: var(--accent-1, #6366f1);">
        <i class="bi bi-shop-window fs-5"></i>
      </div>
      <div>
        <h6 class="mb-0 fw-bold">Pengaturan Jam Buka Kasir &amp; Modal Laci</h6>
        <small class="text-muted-c" style="font-size:0.75rem;">Atur jam buka kasir (24 jam nonstop atau jam tertentu), modal uang kembalian di laci, dan cut-off harian</small>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2.5">
      @if($activeShift)
        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
          <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i> KASIR BUKA
        </span>
      @else
        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.72rem;">
          <i class="bi bi-dash-circle me-1"></i> KASIR TUTUP
        </span>
      @endif

      <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1.5 fw-semibold d-flex align-items-center gap-1.5 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#drawerStatusKasir" id="btnToggleStatusDrawer">
        <i class="bi bi-activity"></i>
        <span>Status &amp; Panduan Kasir</span>
      </button>
    </div>
  </div>

  <div class="card-body p-4">
    <form action="{{ route('admin.keuangan.setting-shift.update-cutoff') }}" method="POST" id="formCutoff">
      @csrf
      
      <!-- SECTION 1: PILIH JAM OPERASIONAL KASIR -->
      <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <label class="form-label-modern mb-0 fw-bold text-uppercase" style="font-size:0.8rem; letter-spacing:0.5px;">1. Pilih Jam Operasional Kasir</label>
          <span class="text-muted-c" style="font-size:0.75rem;">Pilih model jam buka kasir yang paling sesuai untuk toko / resto Anda</span>
        </div>

        <div class="row g-3">
          <!-- Mode 1: Terjadwal / 24 Jam -->
          <div class="col-md-6">
            <div class="mode-box-card @if(($setting->shift_mode ?? 'auto_master') !== 'manual') active @endif" onclick="selectShiftMode('auto_master')">
              <div class="d-flex align-items-center justify-content-between mb-2.5">
                <div class="mode-icon-circle" style="background: rgba(99, 102, 241, 0.15); color: var(--accent-1, #6366f1);">
                  <i class="bi bi-clock-history"></i>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-0.5" style="font-size:0.68rem; font-weight:600;">
                    24 Jam / Jam Tertentu
                  </span>
                  <input type="radio" name="shift_mode" value="auto_master" id="mode_auto_master" class="form-check-input" @if(($setting->shift_mode ?? 'auto_master') !== 'manual') checked @endif style="cursor: pointer;">
                </div>
              </div>
              <div class="fw-bold mb-1 fs-6" style="color: var(--text-primary);">Jam Buka Terjadwal / 24 Jam</div>
              <p class="text-muted-c mb-2.5" style="font-size:0.82rem; line-height: 1.4;">Kasir buka 24 jam nonstop atau diatur dari jam berapa sampai jam berapa, modal kembalian siap otomatis.</p>
              <ul class="list-unstyled mb-0 text-muted-c" style="font-size:0.77rem; line-height: 1.65;">
                <li><i class="bi bi-check-circle-fill text-primary me-1.5"></i>Bisa diset untuk <strong>Toko 24 Jam</strong> (00:00 - 23:59)</li>
                <li><i class="bi bi-check-circle-fill text-primary me-1.5"></i>Bisa diset jam buka tertentu (contoh: <strong>08:00 - 22:00</strong>)</li>
                <li><i class="bi bi-check-circle-fill text-primary me-1.5"></i>Modal uang kembalian otomatis disiapkan setiap hari</li>
                <li><i class="bi bi-check-circle-fill text-primary me-1.5"></i>Sistem otomatis menghitung kebutuhan setoran saat tutup kasir</li>
              </ul>
            </div>
          </div>

          <!-- Mode 2: Manual (Buka Bebas) -->
          <div class="col-md-6">
            <div class="mode-box-card @if(($setting->shift_mode ?? 'auto_master') === 'manual') active @endif" onclick="selectShiftMode('manual')">
              <div class="d-flex align-items-center justify-content-between mb-2.5">
                <div class="mode-icon-circle" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                  <i class="bi bi-sliders"></i>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-0.5" style="font-size:0.68rem; font-weight:600;">
                    Bebas &amp; Fleksibel
                  </span>
                  <input type="radio" name="shift_mode" value="manual" id="mode_manual" class="form-check-input" @if(($setting->shift_mode ?? 'auto_master') === 'manual') checked @endif style="cursor: pointer;">
                </div>
              </div>
              <div class="fw-bold mb-1 fs-6" style="color: var(--text-primary);">Buka Bebas (Manual)</div>
              <p class="text-muted-c mb-2.5" style="font-size:0.82rem; line-height: 1.4;">Kasir buka dan tutup toko secara bebas sesuai jam riil user tanpa jadwal operasional tetap.</p>
              <ul class="list-unstyled mb-0 text-muted-c" style="font-size:0.77rem; line-height: 1.65;">
                <li><i class="bi bi-check-circle-fill text-warning me-1.5"></i>Buka &amp; tutup kasir bebas kapan saja saat mulai jualan</li>
                <li><i class="bi bi-check-circle-fill text-warning me-1.5"></i>Kasir menginput modal uang kembalian saat buka kasir</li>
                <li><i class="bi bi-check-circle-fill text-warning me-1.5"></i>Saat tutup kasir, kasir menghitung uang fisik di laci</li>
                <li><i class="bi bi-check-circle-fill text-warning me-1.5"></i>Owner memantau selisih fisik kas vs sistem di Z-Report</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 2: DYNAMIC SETTINGS SESUAI MODE -->
      <!-- BOX A: KONFIGURASI JAM BUKA TERJADWAL / 24 JAM -->
      <div id="autoScheduleBox" class="p-3.5 rounded-3 mb-4" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle); display: {{ (($setting->shift_mode ?? 'auto_master') !== 'manual') ? 'block' : 'none' }};">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-calendar-check text-primary fs-5"></i>
            <div>
              <h6 class="mb-0 fw-bold" style="font-size: 0.92rem;">Pengaturan Jam Buka Kasir &amp; Modal Kembalian</h6>
              <small class="text-muted-c" style="font-size:0.74rem;">Atur apakah kasir buka 24 jam nonstop atau dari jam berapa sampai jam berapa kasir buka</small>
            </div>
          </div>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill" style="font-size:0.7rem;">Jadwal Jam Buka Aktif</span>
        </div>

        <div class="row g-3 align-items-end">
          <!-- Jam Buka Kasir -->
          <div class="col-md-3">
            <label for="open_time" class="form-label-modern mb-1 fw-semibold" style="font-size: 0.8rem;">
              Jam Buka Kasir <span class="text-danger">*</span>
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

          <!-- Jam Tutup Kasir -->
          <div class="col-md-3">
            <label for="close_time" class="form-label-modern mb-1 fw-semibold" style="font-size: 0.8rem;">
              Jam Tutup Kasir <span class="text-danger">*</span>
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
              Modal Uang Kembalian Kasir (Rp) <span class="text-danger">*</span>
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
            <span class="text-muted-c me-1" style="font-size:0.73rem;">Pilihan Cepat Jam:</span>
            <button type="button" class="btn btn-sm btn-subtle-preset fw-bold text-warning border-warning-subtle" onclick="setOperationalPreset('00:00', '23:59')">
              <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Toko 24 Jam (00:00 - 23:59)
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
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-sliders text-warning fs-5"></i>
            <div>
              <h6 class="mb-0 fw-bold" style="font-size: 0.92rem;">Alur Pengoperasian Buka Bebas (Manual)</h6>
              <small class="text-muted-c" style="font-size:0.74rem;">Kasir bebas menentukan kapan buka kasir dan langsung memasukkan uang modal kembalian di laci</small>
            </div>
          </div>
          <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill" style="font-size:0.7rem;">Mode Bebas Aktif</span>
        </div>
        <div class="p-3 rounded-2" style="background: rgba(245, 158, 11, 0.08); border-left: 3px solid #f59e0b;">
          <p class="mb-1 text-muted-c" style="font-size:0.8rem; line-height:1.5;">
            Pada <strong>Mode Bebas</strong>, kasir tidak dibatasi oleh jam operasional tetap. Kasir dapat langsung menekan tombol <strong>"Buka Kasir"</strong> kapan saja saat mulai jualan dan mengetikkan uang modal kembalian di laci secara langsung di layar POS.
          </p>
          <small class="text-warning fw-semibold" style="font-size:0.75rem;">
            <i class="bi bi-lightbulb me-1"></i> Tips Owner: Saat tutup kasir (closing), kasir tetap wajib menghitung uang fisik aktual di laci agar owner dapat melihat selisih kas di laporan Z-Report.
          </small>
        </div>
      </div>

      <!-- SECTION 3: JAM CUT-OFF OPERASIONAL & PERLINDUNGAN STRICT -->
      <div class="row g-4 align-items-end pt-2">
        <div class="col-md-5">
          <label for="daily_cutoff_time" class="form-label-modern mb-1 fw-semibold">
            Jam Cut-Off Operasional Harian <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);"><i class="bi bi-clock"></i></span>
            <input type="time" name="daily_cutoff_time" id="daily_cutoff_time" class="form-control form-control-modern" value="{{ \Carbon\Carbon::parse($setting->daily_cutoff_time ?? '03:00')->format('H:i') }}" required>
          </div>
          <div class="text-muted-c mt-1" style="font-size: 0.76rem;">
            Untuk toko 24 jam maupun resto yang buka sampai larut/subuh, transaksi setelah jam ini otomatis dihitung sebagai <strong>Tanggal Bisnis Baru</strong> (Rekomendasi resto: <code>03:00</code> Subuh).
          </div>
        </div>

        <div class="col-md-7">
          <div class="form-check form-switch pt-1 mb-2">
            <input class="form-check-input" type="checkbox" name="auto_lock_unclosed" id="auto_lock_unclosed" value="1" @if($setting->auto_lock_unclosed ?? 1) checked @endif style="width: 2.4em; height: 1.2em; cursor: pointer;">
            <label class="form-check-label fw-semibold ms-2" for="auto_lock_unclosed">
              Auto-Lock Kasir Kemarin (Cegah Lupa Tutup Kasir)
            </label>
          </div>
          <div class="text-muted-c" style="font-size: 0.76rem;">
            Kunci layar POS kasir jika kasir hari kemarin belum melakukan penutupan kasir.
          </div>
        </div>

        <div class="col-12 text-end pt-2">
          <button type="submit" class="btn btn-primary-grad px-4 py-2.5 rounded-3 btn-loading" id="btnSaveCutoff">
            <i class="bi bi-check-circle-fill me-1"></i> Simpan Pengaturan Jam Buka Kasir
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- FLOATING DRAWER TAB ON LEFT EDGE (HOVER / CLICK TO SLIDE DRAWER) -->
<div id="drawerTriggerTab" class="kasir-drawer-tab d-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#drawerStatusKasir" title="Arahkan kursor atau klik untuk melihat Status Kasir">
  <span class="pulse-dot"></span>
  <i class="bi bi-activity text-primary fs-6"></i>
  <span class="tab-text fw-bold">Status Kasir</span>
  <i class="bi bi-chevron-left text-muted-c ms-0.5" style="font-size: 0.65rem;"></i>
</div>

<!-- OFFCANVAS SIDEBAR KIRI: STATUS LIVE & PANDUAN KASIR (SLIDE-OVER ON HOVER/CLICK) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="drawerStatusKasir" aria-labelledby="drawerStatusKasirLabel" style="width: 410px; max-width: 90vw; background: var(--bg-surface, #1e293b); color: var(--text-primary); border-left: 1px solid var(--border-subtle); z-index: 1085;">
  <div class="offcanvas-header py-3 px-3.5 border-bottom" style="border-color: var(--border-subtle) !important;">
    <div class="d-flex align-items-center gap-2">
      <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:34px; height:34px; background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
        <i class="bi bi-activity fs-5"></i>
      </div>
      <div>
        <h6 class="mb-0 fw-bold" id="drawerStatusKasirLabel" style="font-size:0.92rem;">Status &amp; Alur Kasir</h6>
        <small class="text-muted-c" style="font-size:0.72rem;">Live monitoring sesi POS cabang</small>
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      @if($activeShift)
        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.7rem;">
          <i class="bi bi-circle-fill me-1" style="font-size: 0.4rem;"></i> BUKA
        </span>
      @else
        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5 rounded-pill fw-semibold" style="font-size: 0.7rem;">
          <i class="bi bi-dash-circle me-1"></i> TUTUP
        </span>
      @endif
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
  </div>

  <div class="offcanvas-body p-3.5 scroll-thin">
    <!-- 1. LIVE MONITORING KASIR CARD -->
    <div class="card border-0 shadow-sm mb-3.5" style="background: var(--bg-elevated, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle) !important; border-radius: 0.85rem;">
      <div class="card-body p-3">
        <!-- Kasir Details -->
        <div class="mb-2.5 p-2 rounded-2" style="background: var(--bg-elevated-2, rgba(255,255,255,0.02)); border: 1px solid var(--border-subtle);">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-muted-c small" style="font-size:0.75rem;">Kasir Bertugas</span>
            <span class="fw-semibold text-truncate" style="font-size:0.8rem; max-width:180px; color:var(--text-primary);">
              {{ $activeShift ? ($activeShift->cashier?->name ?? ($activeShift->shift_name ?: 'Kasir Utama')) : 'Kasir Belum Dibuka' }}
            </span>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted-c small" style="font-size:0.75rem;">Waktu Buka Kasir</span>
            <span class="fw-bold font-monospace text-success" style="font-size:0.8rem;">
              {{ $activeShift ? \Carbon\Carbon::parse($activeShift->opened_at)->format('H:i') . ' WIB' : '-' }}
            </span>
          </div>
        </div>

        <!-- Mode & Parameters -->
        <div class="d-flex flex-column gap-2 mb-3">
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted-c small" style="font-size:0.78rem;">Mode Kasir:</span>
            <span id="modeSummaryBadge" class="badge {{ (($setting->shift_mode ?? 'auto_master') === 'manual') ? 'bg-warning-subtle text-warning border border-warning-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;">
              <i class="bi {{ (($setting->shift_mode ?? 'auto_master') === 'manual') ? 'bi-sliders' : 'bi-clock-history' }} me-1"></i>
              {{ (($setting->shift_mode ?? 'auto_master') === 'manual') ? 'Buka Bebas (Manual)' : 'Terjadwal / 24 Jam' }}
            </span>
          </div>

          <div class="d-flex justify-content-between align-items-start">
            <span class="text-muted-c small" style="font-size:0.78rem;">Jam Buka:</span>
            <div class="text-end">
              <span class="fw-bold font-monospace" id="scheduleSummaryText" style="font-size: 0.8rem; color: var(--text-primary);">
                @if(($setting->shift_mode ?? 'auto_master') === 'manual')
                  Fleksibel (Buka Bebas Kapan Saja)
                @elseif(($primaryShift->start_time ?? '') == '00:00:00' && ($primaryShift->end_time ?? '') >= '23:59:00')
                  24 Jam Nonstop (00:00 - 23:59)
                @else
                  {{ \Carbon\Carbon::parse($primaryShift->start_time ?? '08:00')->format('H:i') }} - {{ \Carbon\Carbon::parse($primaryShift->end_time ?? '22:00')->format('H:i') }} WIB
                @endif
              </span>
              <small class="text-muted-c d-block" style="font-size:0.68rem;">Cut-off: {{ \Carbon\Carbon::parse($setting->daily_cutoff_time ?? '03:00')->format('H:i') }} WIB</small>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted-c small" style="font-size:0.78rem;">Target Modal Laci:</span>
            <span class="fw-bold text-success font-monospace" id="modalSummaryText" style="font-size: 0.8rem;">
              @if(($setting->shift_mode ?? 'auto_master') === 'manual')
                Diinput Kasir Saat Buka
              @else
                Rp {{ number_format($primaryShift->default_starting_cash ?? 300000, 0, ',', '.') }}
              @endif
            </span>
          </div>
        </div>

        <a href="{{ route('admin.keuangan.shift-operational.index') }}" class="btn btn-primary-grad w-100 py-2 rounded-3 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2" style="font-size:0.83rem;">
          <i class="bi bi-cash-stack"></i>
          <span>Ke Layar Buka / Tutup Kasir</span>
        </a>
      </div>
    </div>

    <!-- 2. ALUR KERJA RINGKAS KASIR (1 s/d 4) -->
    <div class="card border-0 shadow-sm" style="background: var(--bg-elevated, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle) !important; border-radius: 0.85rem;">
      <div class="card-header-flex py-2 px-3 border-bottom" style="border-color: var(--border-subtle) !important;">
        <span class="fw-bold small d-flex align-items-center gap-1.5" style="color:var(--text-primary); font-size:0.82rem;">
          <i class="bi bi-diagram-3-fill text-primary"></i>
          <span>Alur Buka Tutup Kasir</span>
        </span>
        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none text-primary fw-semibold" style="font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#modalPanduanKasir">
          Detail SOP <i class="bi bi-box-arrow-up-right ms-0.5" style="font-size:0.65rem;"></i>
        </button>
      </div>

      <div class="card-body p-2.5">
        <div class="d-flex flex-column gap-2">
          <!-- Step 1 -->
          <div class="d-flex align-items-start gap-2 p-2 rounded-2" style="background: var(--bg-elevated-2, rgba(255,255,255,0.02)); border: 1px solid var(--border-subtle);">
            <div class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width:20px; height:20px; font-size:0.68rem;">1</div>
            <div>
              <div class="fw-bold" style="font-size: 0.78rem; color: var(--text-primary);">Buka Kasir</div>
              <small class="text-muted-c" style="font-size: 0.71rem; line-height: 1.3; display:block;">Kasir siapkan modal uang kembalian di laci dan tekan Buka Kasir.</small>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="d-flex align-items-start gap-2 p-2 rounded-2" style="background: var(--bg-elevated-2, rgba(255,255,255,0.02)); border: 1px solid var(--border-subtle);">
            <div class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width:20px; height:20px; font-size:0.68rem;">2</div>
            <div>
              <div class="fw-bold" style="font-size: 0.78rem; color: var(--text-primary);">Transaksi POS</div>
              <small class="text-muted-c" style="font-size: 0.71rem; line-height: 1.3; display:block;">Pencatatan pesanan. Pembayaran cash terkunci jika kasir belum buka.</small>
            </div>
          </div>

          <!-- Step 3 -->
          <div class="d-flex align-items-start gap-2 p-2 rounded-2" style="background: var(--bg-elevated-2, rgba(255,255,255,0.02)); border: 1px solid var(--border-subtle);">
            <div class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width:20px; height:20px; font-size:0.68rem;">3</div>
            <div>
              <div class="fw-bold" style="font-size: 0.78rem; color: var(--text-primary);">Tutup Kasir</div>
              <small class="text-muted-c" style="font-size: 0.71rem; line-height: 1.3; display:block;">Di akhir hari / jam tutup, kasir hitung fisik uang di laci dan ketik nominalnya.</small>
            </div>
          </div>

          <!-- Step 4 -->
          <div class="d-flex align-items-start gap-2 p-2 rounded-2" style="background: var(--bg-elevated-2, rgba(255,255,255,0.02)); border: 1px solid var(--border-subtle);">
            <div class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width:20px; height:20px; font-size:0.68rem;">4</div>
            <div>
              <div class="fw-bold" style="font-size: 0.78rem; color: var(--text-primary);">Audit Selisih</div>
              <small class="text-muted-c" style="font-size: 0.71rem; line-height: 1.3; display:block;">Sistem hitung selisih kas fisik vs transaksi di struk Z-Report.</small>
            </div>
          </div>
        </div>

        <button type="button" class="btn btn-outline-primary w-100 py-2 mt-2.5 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-2" style="font-size:0.8rem;" data-bs-toggle="modal" data-bs-target="#modalPanduanKasir">
          <i class="bi bi-journal-check"></i>
          <span>Buka Panduan Buka &amp; Tutup Kasir</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL PANDUAN LENGKAP ALUR KERJA KASIR F&B (LEBAR, BESAR & JELAS) -->
<div class="modal fade" id="modalPanduanKasir" tabindex="-1" aria-labelledby="modalPanduanKasirLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" style="max-width: 840px;">
    <div class="modal-content" style="background: var(--bg-surface, #1e293b); border: 1px solid var(--border-subtle); border-radius: 1.35rem; color: var(--text-primary); box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5);">
      
      <!-- Modal Header dengan Padding Nyaman -->
      <div class="modal-header py-3.5 px-4 px-md-5" style="border-bottom: 1px solid var(--border-subtle);">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px; background: rgba(59, 130, 246, 0.14); color: #3b82f6; font-size: 1.4rem;">
            <i class="bi bi-journal-bookmark-fill"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold mb-0.5" id="modalPanduanKasirLabel" style="font-size: 1.15rem; letter-spacing: -0.01em;">Panduan Praktis Buka &amp; Tutup Kasir (24 Jam &amp; Jam Tertentu)</h5>
            <div class="text-muted-c" style="font-size: 0.84rem;">4 Langkah mudah kelola uang di laci kasir agar selalu pas, rapi, dan transparan tanpa ribet</div>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body dengan Padding Luas & Tata Letak Ergonomis -->
      <div class="modal-body px-4 px-md-5 py-4">
        
        <!-- Banner Penjelasan Konsep Awam (Kenapa harus Buka Tutup?) -->
        <div class="p-3.5 p-md-4 rounded-3 mb-4 d-flex align-items-start gap-3.5" style="background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.22); border-left: 5px solid #3b82f6;">
          <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width: 36px; height: 36px; background: rgba(59, 130, 246, 0.18); color: #3b82f6; font-size: 1.15rem;">
            <i class="bi bi-lightbulb-fill"></i>
          </div>
          <div style="font-size: 0.88rem; line-height: 1.6; color: var(--text-secondary);">
            <strong class="d-block mb-1 text-primary" style="font-size: 0.94rem;">Kenapa Toko Perlu Buka &amp; Tutup Kasir?</strong>
            Supaya <strong>uang tunai di laci kasir</strong> selalu cocok dengan <strong>total transaksi di aplikasi kasir</strong>. Baik toko Anda beroperasi <strong>24 jam nonstop</strong> maupun <strong>jam buka tertentu</strong> (misal 08:00 - 22:00), kasir cukup buka kasir di awal dan tutup kasir di akhir hari. Kasir tidak pusing mencari uang kembalian, dan Owner bisa tenang karena selisih uang langsung ketahuan otomatis.
          </div>
        </div>

        <!-- 4 Langkah SOP Lengkap (Bahasa Ramah Pengguna & Padding Lapang) -->
        <div class="d-flex flex-column gap-3.5">
          
          <!-- Langkah 1 -->
          <div class="p-3.5 p-md-4 rounded-3" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center justify-content-between mb-2.5">
              <div class="d-flex align-items-center gap-3">
                <span class="badge text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 0.9rem; background: linear-gradient(135deg, #3b82f6, #1d4ed8);">1</span>
                <h6 class="mb-0 fw-bold" style="font-size: 0.98rem; color: var(--text-primary);">Buka Kasir &amp; Siapkan Uang Kembalian</h6>
              </div>
              <span class="badge px-3 py-1.5 rounded-pill" style="background: rgba(59, 130, 246, 0.12); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.25); font-size: 0.74rem; font-weight: 600;">
                <i class="bi bi-sun me-1"></i> Mulai Jualan
              </span>
            </div>
            <p class="text-muted-c mb-3" style="font-size: 0.88rem; line-height: 1.65;">
              Sebelum mulai melayani pembeli pertama, kasir menyiapkan uang modal (uang receh pecahan kecil untuk kembalian) ke dalam laci kasir, lalu menekan tombol <strong>"Buka Kasir"</strong> di aplikasi dan mengetikkan jumlah uang modalnya.
            </p>
            <div class="p-3 rounded-3 d-flex align-items-center gap-2.5" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25);">
              <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0"></i>
              <div class="text-success fw-medium" style="font-size: 0.82rem; line-height: 1.5;">
                <strong>Tips Praktis:</strong> Jika toko menggunakan <strong>Mode Jam Buka Terjadwal / 24 Jam</strong>, nominal uang modal (misal Rp 300.000) sudah langsung terisi di layar, kasir cukup mencocokkan uang fisiknya saja.
              </div>
            </div>
          </div>

          <!-- Langkah 2 -->
          <div class="p-3.5 p-md-4 rounded-3" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center justify-content-between mb-2.5">
              <div class="d-flex align-items-center gap-3">
                <span class="badge text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 0.9rem; background: linear-gradient(135deg, #06b6d4, #0284c7);">2</span>
                <h6 class="mb-0 fw-bold" style="font-size: 0.98rem; color: var(--text-primary);">Layani Pembeli &amp; Catat Transaksi</h6>
              </div>
              <span class="badge px-3 py-1.5 rounded-pill" style="background: rgba(6, 182, 212, 0.12); color: #38bdf8; border: 1px solid rgba(6, 182, 212, 0.25); font-size: 0.74rem; font-weight: 600;">
                <i class="bi bi-clock-history me-1"></i> Sepanjang Hari
              </span>
            </div>
            <p class="text-muted-c mb-3" style="font-size: 0.88rem; line-height: 1.65;">
              Kasir melayani pesanan pembeli seperti biasa — baik yang membayar tunai, QRIS, kartu debit, maupun transfer. Aplikasi akan otomatis mencatat dan menjumlahkan semua uang yang seharusnya terkumpul di laci kasir.
            </p>
            <div class="p-3 rounded-3 d-flex align-items-center gap-2.5" style="background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.25);">
              <i class="bi bi-shield-check text-primary fs-5 flex-shrink-0"></i>
              <div class="text-primary fw-medium" style="font-size: 0.82rem; line-height: 1.5;">
                <strong>Penting untuk Kasir:</strong> Pembayaran tunai tidak bisa diproses sebelum kasir klik "Buka Kasir", agar semua uang masuk selalu terlacak sejak awal dan tidak ada yang tercecer.
              </div>
            </div>
          </div>

          <!-- Langkah 3 -->
          <div class="p-3.5 p-md-4 rounded-3" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center justify-content-between mb-2.5">
              <div class="d-flex align-items-center gap-3">
                <span class="badge text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 0.9rem; background: linear-gradient(135deg, #f59e0b, #d97706);">3</span>
                <h6 class="mb-0 fw-bold" style="font-size: 0.98rem; color: var(--text-primary);">Tutup Kasir &amp; Hitung Uang di Laci</h6>
              </div>
              <span class="badge px-3 py-1.5 rounded-pill" style="background: rgba(245, 158, 11, 0.12); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.25); font-size: 0.74rem; font-weight: 600;">
                <i class="bi bi-moon-stars me-1"></i> Malam / Tutup Kasir
              </span>
            </div>
            <p class="text-muted-c mb-3" style="font-size: 0.88rem; line-height: 1.65;">
              Saat toko selesai berjualan (atau saat waktu cut-off di toko 24 jam), kasir mengeluarkan dan menghitung seluruh uang tunai yang tersisa di laci (kertas &amp; koin). Setelah dihitung, kasir membuka menu <strong>"Tutup Kasir"</strong> dan mengetik total uang fisiknya.
            </p>
            <div class="p-3 rounded-3 d-flex align-items-center gap-2.5" style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.25);">
              <i class="bi bi-coin text-warning fs-5 flex-shrink-0"></i>
              <div class="text-warning fw-medium" style="font-size: 0.82rem; line-height: 1.5;">
                <strong>Hitung Apa Adanya:</strong> Kasir cukup ketik jumlah uang tunai yang dihitung secara jujur. Jangan khawatir, sistem yang akan menghitung perbandingannya dengan total penjualan.
              </div>
            </div>
          </div>

          <!-- Langkah 4 -->
          <div class="p-3.5 p-md-4 rounded-3" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center justify-content-between mb-2.5">
              <div class="d-flex align-items-center gap-3">
                <span class="badge text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 0.9rem; background: linear-gradient(135deg, #8b5cf6, #6d28d9);">4</span>
                <h6 class="mb-0 fw-bold" style="font-size: 0.98rem; color: var(--text-primary);">Cetak Laporan &amp; Cek Selisih Uang (Z-Report)</h6>
              </div>
              <span class="badge px-3 py-1.5 rounded-pill" style="background: rgba(139, 92, 246, 0.12); color: #c084fc; border: 1px solid rgba(139, 92, 246, 0.25); font-size: 0.74rem; font-weight: 600;">
                <i class="bi bi-file-earmark-check me-1"></i> Laporan Selesai &amp; Setor
              </span>
            </div>
            <p class="text-muted-c mb-3" style="font-size: 0.88rem; line-height: 1.65;">
              Aplikasi langsung mencetak struk ringkasan harian (Z-Report). Di sini langsung terlihat jika ada uang pas, lebih, atau kurang. Kasir dan Owner juga bisa melihat berapa uang yang harus disetor ke brankas dan berapa yang disisakan di laci untuk modal besok.
            </p>
            <div class="p-3 rounded-3 d-flex align-items-center gap-2.5" style="background: rgba(139, 92, 246, 0.08); border: 1px solid rgba(139, 92, 246, 0.25);">
              <i class="bi bi-safe2-fill text-purple fs-5 flex-shrink-0"></i>
              <div class="text-purple fw-medium" style="font-size: 0.82rem; line-height: 1.5;">
                <strong>Contoh Setoran:</strong> Jika uang di laci ada Rp 1.500.000 dan modal besok Rp 300.000, maka Rp 1.200.000 disetor ke brankas toko, dan Rp 300.000 ditinggal di laci kasir untuk modal jualan esok pagi.
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer py-3.5 px-4 px-md-5 d-flex justify-content-between align-items-center" style="border-top: 1px solid var(--border-subtle);">
        <button type="button" class="btn btn-outline-secondary rounded-3 px-4 py-2.5" data-bs-dismiss="modal">Tutup</button>
        <a href="{{ route('admin.keuangan.shift-operational.index') }}" class="btn btn-primary-grad rounded-3 px-4 py-2.5 fw-semibold d-flex align-items-center gap-2 shadow-sm">
          <span>Ke Layar Buka / Tutup Kasir</span>
          <i class="bi bi-arrow-right"></i>
        </a>
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
        modeSummaryBadge.innerHTML = '<i class="bi bi-sliders me-1"></i> Buka Bebas (Manual)';
      }
      if (scheduleSummaryText) scheduleSummaryText.textContent = 'Fleksibel (Buka Bebas Kapan Saja)';
      if (modalSummaryText) modalSummaryText.textContent = 'Diinput Kasir Saat Buka';
    } else {
      if (autoBox) autoBox.style.display = 'block';
      if (manualBox) manualBox.style.display = 'none';
      if (modeSummaryBadge) {
        modeSummaryBadge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill';
        modeSummaryBadge.innerHTML = '<i class="bi bi-clock-history me-1"></i> Terjadwal / 24 Jam';
      }
      if (scheduleSummaryText) {
        const openVal = document.getElementById('open_time')?.value || '08:00';
        const closeVal = document.getElementById('close_time')?.value || '22:00';
        if (openVal === '00:00' && (closeVal === '23:59' || closeVal === '24:00')) {
          scheduleSummaryText.textContent = '24 Jam Nonstop (00:00 - 23:59)';
        } else {
          scheduleSummaryText.textContent = openVal + ' - ' + closeVal + ' WIB';
        }
      }
      if (modalSummaryText) {
        const cashVal = document.getElementById('default_starting_cash')?.value || '300000';
        modalSummaryText.textContent = 'Rp ' + Number(cashVal).toLocaleString('id-ID');
      }
    }
  }

  function setOperationalPreset(openTime, closeTime) {
    const openInput = document.getElementById('open_time');
    const closeInput = document.getElementById('close_time');
    if (openInput) openInput.value = openTime;
    if (closeInput) closeInput.value = closeTime;
    const scheduleSummaryText = document.getElementById('scheduleSummaryText');
    const is24h = (openTime === '00:00' && (closeTime === '23:59' || closeTime === '24:00'));
    if (scheduleSummaryText) {
      scheduleSummaryText.textContent = is24h ? '24 Jam Nonstop (00:00 - 23:59)' : (openTime + ' - ' + closeTime + ' WIB');
    }
    if (typeof NexoraToast === 'function') {
      const msg = is24h ? 'Jam Buka Kasir diset Toko 24 Jam (00:00 - 23:59)' : `Jam Buka Kasir diset ${openTime} - ${closeTime}`;
      NexoraToast({ title: 'Jam Buka Terpilih', message: msg, type: 'info' });
    }
  }

  function setStartingCashPreset(amount) {
    const cashInput = document.getElementById('default_starting_cash');
    if (cashInput) cashInput.value = amount;
    const modalSummaryText = document.getElementById('modalSummaryText');
    if (modalSummaryText) modalSummaryText.textContent = 'Rp ' + Number(amount).toLocaleString('id-ID');
    if (typeof NexoraToast === 'function') {
      NexoraToast({ title: 'Modal Terpilih', message: `Modal Uang Kembalian diset ke Rp ${Number(amount).toLocaleString('id-ID')}`, type: 'info' });
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
      if (openVal === '00:00' && (closeVal === '23:59' || closeVal === '24:00')) {
        scheduleSummaryText.textContent = '24 Jam Nonstop (00:00 - 23:59)';
      } else {
        scheduleSummaryText.textContent = openVal + ' - ' + closeVal + ' WIB';
      }
    }
  }

  document.querySelectorAll('.btn-loading').forEach(btn => {
    btn.closest('form')?.addEventListener('submit', function() {
      btn.disabled = true;
      btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...`;
    });
  });

  // Drawer Status Kasir (Click to open from right)
  // Triggered natively by data-bs-toggle="offcanvas" data-bs-target="#drawerStatusKasir" on:
  // - #drawerTriggerTab (Floating tab on right edge)
  // - #btnToggleStatusDrawer (Header button)
  // - Status badges (KASIR BUKA / KASIR TUTUP)
</script>
@endpush
