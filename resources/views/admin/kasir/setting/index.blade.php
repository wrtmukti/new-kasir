@extends('admin.layouts.app')

@section('title', 'Setting')

@php $activeMenu = 'setting' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Setting</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Beranda</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Setting</span>
    </div>
  </div>
</div>

<div class="row g-4">
  {{-- KOLOM KIRI: MENU TAB VERTIKAL (MENGIKUTI /docs/settings) --}}
  <div class="col-lg-3">
    <div class="card mb-3">
      <div class="card-body p-2">
        <ul class="list-unstyled m-0 settings-menu">
          <li class="nav-item active">
            <a href="#" class="nav-link py-2.5 px-3 rounded-2" data-settings-target="#settings-profile">
              <i class="bi bi-building-fill me-2 text-success"></i>
              <span class="nav-label-text fw-semibold">Profil Outlet</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link py-2.5 px-3 rounded-2" data-settings-target="#settings-payment">
              <i class="bi bi-credit-card-2-front-fill me-2 text-primary"></i>
              <span class="nav-label-text fw-semibold">Alur Pembayaran</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link py-2.5 px-3 rounded-2" data-settings-target="#settings-theme">
              <i class="bi bi-palette-fill me-2 text-warning"></i>
              <span class="nav-label-text fw-semibold">Tema QR Guest</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link py-2.5 px-3 rounded-2" data-settings-target="#settings-tax">
              <i class="bi bi-percent me-2 text-info"></i>
              <span class="nav-label-text fw-semibold">Pajak & Service</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link py-2.5 px-3 rounded-2" data-settings-target="#settings-shift">
              <i class="bi bi-clock-history me-2 text-warning"></i>
              <span class="nav-label-text fw-semibold">Shift & Cut-off</span>
            </a>
          </li>
        </ul>
      </div>
    </div>

    {{-- Info Card Ringkas --}}
    <div class="card p-3 rounded-3" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px dashed var(--border-subtle, rgba(255,255,255,0.1));">
      <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-info-circle-fill text-primary"></i>
        <span class="fw-bold" style="font-size:0.85rem;">Status Sistem</span>
      </div>
      <div class="text-muted-c" style="font-size:0.78rem;">
        Konfigurasi outlet berlaku langsung ke modul POS Kasir dan halaman QR Menu Meja Tamu.
      </div>
    </div>
  </div>

  {{-- KOLOM KANAN: PANEL PENGATURAN --}}
  <div class="col-lg-9">
    <div class="settings-panels">

      {{-- PANEL 1: PROFIL OUTLET & PERUSAHAAN (DEFAULT AKTIF) --}}
      <div id="settings-profile" class="settings-panel" style="display:block;">
        <div class="card mb-4">
          <div class="card-header-flex">
            <h6><i class="bi bi-building me-2 text-success"></i>Profil Usaha & Informasi Outlet</h6>
            <span class="chip-tag" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; font-weight:600;">
              Outlet Info
            </span>
          </div>
          <div class="card-body">
            <form id="companyProfileForm" action="{{ route('admin.setting.update-profile') }}" method="POST" enctype="multipart/form-data">
              @csrf

              <div class="row g-3 mb-3">
                <div class="col-md-6 input-skeleton">
                  <label for="outlet_name" class="form-label-modern fw-semibold">Nama Usaha / Brand <span class="text-danger">*</span></label>
                  <input type="text" name="outlet_name" id="outlet_name" class="form-control form-control-modern"
                         value="{{ $outlet->outlet_name }}" required placeholder="Contoh: Geprek Gambus / Cafe Kopi">
                </div>
                <div class="col-md-3 input-skeleton">
                  <label for="outlet_code" class="form-label-modern">Kode Singkat</label>
                  <input type="text" name="outlet_code" id="outlet_code" class="form-control form-control-modern text-uppercase"
                         value="{{ $outlet->outlet_code }}" placeholder="Contoh: GGB">
                </div>
                <div class="col-md-3 input-skeleton">
                  <label for="outlet_branch" class="form-label-modern">Cabang</label>
                  <input type="text" name="outlet_branch" id="outlet_branch" class="form-control form-control-modern"
                         value="{{ $outlet->outlet_branch }}" placeholder="Contoh: Pusat / Jogja">
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6 input-skeleton">
                  <label for="outlet_phone" class="form-label-modern">Nomor Telepon / WhatsApp</label>
                  <input type="text" name="outlet_phone" id="outlet_phone" class="form-control form-control-modern"
                         value="{{ $outlet->outlet_phone }}" placeholder="Contoh: 081234567890">
                </div>
                <div class="col-md-6 input-skeleton">
                  <label for="outlet_email" class="form-label-modern">Email Usaha</label>
                  <input type="email" name="outlet_email" id="outlet_email" class="form-control form-control-modern"
                         value="{{ $outlet->outlet_email }}" placeholder="Contoh: info@restocafe.com">
                </div>
              </div>

              <div class="mb-3 input-skeleton">
                <label for="outlet_address" class="form-label-modern">Alamat Lengkap</label>
                <textarea name="outlet_address" id="outlet_address" rows="2" class="form-control form-control-modern"
                          placeholder="Contoh: Jl. Merdeka No. 10, Jakarta Pusat">{{ $outlet->outlet_address }}</textarea>
              </div>

              <div class="mb-4 input-skeleton">
                <label for="outlet_image" class="form-label-modern">Logo Usaha (Opsional)</label>
                <div class="d-flex align-items-center gap-3">
                  @if($outlet->outlet_image)
                    <img src="{{ asset('storage/' . $outlet->outlet_image) }}" id="logoPreview" alt="Logo" class="rounded-3 border border-secondary-subtle object-fit-cover" style="width:60px; height:60px;">
                  @else
                    <div id="logoPlaceholder" class="rounded-3 d-flex align-items-center justify-center text-muted-c" style="width:60px; height:60px; background:var(--bg-elevated-2); border:1px dashed var(--border-subtle);">
                      <i class="bi bi-image fs-4"></i>
                    </div>
                  @endif
                  <input type="file" name="outlet_image" id="outlet_image" class="form-control form-control-modern" accept="image/*">
                </div>
                <small class="text-muted-c mt-1 d-block">Format gambar: PNG, JPG, JPEG, WEBP (Maksimal 2MB).</small>
              </div>

              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary-grad px-4 btn-loading" id="btnSaveCompanyProfile">
                  <i class="bi bi-check2-circle me-1"></i>Simpan Profil Usaha
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      {{-- PANEL 2: ALUR PEMBAYARAN KASIR (BAYAR DI AWAL VS BAYAR DI AKHIR) --}}
      <div id="settings-payment" class="settings-panel" style="display:none;">
        <div class="card mb-4 border-0 shadow-sm" style="border: 1px solid var(--border-subtle) !important; border-radius: 1rem;">
          <div class="card-header-flex py-3 px-4" style="border-bottom: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center gap-2">
              <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
                <i class="bi bi-credit-card-2-front-fill fs-5"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-bold">Alur Kebijakan Pembayaran (Payment Timing)</h6>
                <small class="text-muted-c" style="font-size:0.75rem;">Konfigurasi alur transaksi kasir POS & QR ordering meja tamu</small>
              </div>
            </div>
            <span class="chip-tag px-3 py-1 rounded-pill" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.25); font-weight:600; font-size:0.75rem;">
              <i class="bi bi-shield-check me-1"></i>Kebijakan Kasir
            </span>
          </div>

          <div class="card-body p-4">
            <div class="mb-4">
              <p class="text-secondary-c mb-0" style="font-size:0.9rem; line-height: 1.5;">
                Pilih alur transaksi yang paling sesuai dengan model operasional outlet Anda. Sistem akan menyesuaikan tombol pelunasan di kasir dan instruksi pada menu QR tamu.
              </p>
            </div>

            <form id="paymentTimingForm" action="{{ route('admin.setting.update-payment') }}" method="POST">
              @csrf
              <input type="hidden" name="payment_timing" id="paymentTimingInput" value="{{ $setting->payment_timing ?? 'post_payment' }}">

              <div class="row g-4 mb-4">
                {{-- OPSI 1: BAYAR DI AKHIR (POST-PAYMENT) --}}
                <div class="col-lg-6">
                  <div class="timing-bento-card p-4 rounded-4 cursor-pointer h-100 position-relative transition-all {{ ($setting->payment_timing ?? 'post_payment') === 'post_payment' ? 'active-blue' : '' }}"
                       id="cardPostPayment" onclick="selectPaymentTiming('post_payment')">
                    
                    {{-- Header Bento Card --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <div class="d-flex align-items-center gap-3">
                        <div class="bento-icon-glow rounded-3 d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59,130,246,0.3); color: #3b82f6;">
                          <i class="bi bi-cup-hot-fill fs-4"></i>
                        </div>
                        <div>
                          <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold mb-0">Bayar di Akhir</h5>
                          </div>
                          <span class="badge rounded-pill mt-1" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6; font-size:0.7rem; border: 1px solid rgba(59,130,246,0.25);">
                            Post-Payment / Dine-In Resto
                          </span>
                        </div>
                      </div>

                      {{-- Custom Radio Ring --}}
                      <div class="custom-radio-indicator" id="radioPostPayment">
                        <div class="radio-ring {{ ($setting->payment_timing ?? 'post_payment') === 'post_payment' ? 'checked-blue' : '' }}">
                          <div class="radio-dot"></div>
                        </div>
                      </div>
                    </div>

                    {{-- Deskripsi Ringkas --}}
                    <p class="text-secondary-c mb-3" style="font-size:0.83rem; line-height: 1.45;">
                      Pelanggan memesan dan menikmati hidangan terlebih dahulu. Pelunasan transaksi dilakukan di kasir saat pelanggan selesai makan.
                    </p>

                    {{-- Visual Flow Pipeline --}}
                    <div class="flow-pipeline p-3 rounded-3 mb-3">
                      <div class="d-flex align-items-center justify-content-between text-center position-relative">
                        <div class="flow-step flex-1">
                          <div class="flow-step-num mx-auto mb-1">1</div>
                          <div class="flow-step-label">Pesan Menu</div>
                        </div>
                        <div class="flow-step-arrow"><i class="bi bi-chevron-right text-muted-c"></i></div>
                        <div class="flow-step flex-1">
                          <div class="flow-step-num mx-auto mb-1">2</div>
                          <div class="flow-step-label">Masak & Sajikan</div>
                        </div>
                        <div class="flow-step-arrow"><i class="bi bi-chevron-right text-muted-c"></i></div>
                        <div class="flow-step flex-1">
                          <div class="flow-step-num mx-auto mb-1 step-highlight-blue">3</div>
                          <div class="flow-step-label fw-bold text-primary">Bayar di Kasir</div>
                        </div>
                      </div>
                    </div>

                    {{-- Tag Fitur --}}
                    <div class="d-flex flex-wrap gap-1.5 mt-auto">
                      <span class="feature-pill"><i class="bi bi-check2 text-primary me-1"></i>Layanan Meja</span>
                      <span class="feature-pill"><i class="bi bi-check2 text-primary me-1"></i>Bisa Tambah Pesanan</span>
                      <span class="feature-pill"><i class="bi bi-check2 text-primary me-1"></i>Split Bill Nyaman</span>
                    </div>
                  </div>
                </div>

                {{-- OPSI 2: BAYAR DI AWAL (PRE-PAYMENT) --}}
                <div class="col-lg-6">
                  <div class="timing-bento-card p-4 rounded-4 cursor-pointer h-100 position-relative transition-all {{ ($setting->payment_timing ?? 'post_payment') === 'pre_payment' ? 'active-green' : '' }}"
                       id="cardPrePayment" onclick="selectPaymentTiming('pre_payment')">
                    
                    {{-- Header Bento Card --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <div class="d-flex align-items-center gap-3">
                        <div class="bento-icon-glow rounded-3 d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16,185,129,0.3); color: #10b981;">
                          <i class="bi bi-lightning-charge-fill fs-4"></i>
                        </div>
                        <div>
                          <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold mb-0">Bayar di Awal</h5>
                          </div>
                          <span class="badge rounded-pill mt-1" style="background: rgba(16, 185, 129, 0.12); color: #10b981; font-size:0.7rem; border: 1px solid rgba(16,185,129,0.25);">
                            Pre-Payment / Quick Counter
                          </span>
                        </div>
                      </div>

                      {{-- Custom Radio Ring --}}
                      <div class="custom-radio-indicator" id="radioPrePayment">
                        <div class="radio-ring {{ ($setting->payment_timing ?? 'post_payment') === 'pre_payment' ? 'checked-green' : '' }}">
                          <div class="radio-dot"></div>
                        </div>
                      </div>
                    </div>

                    {{-- Deskripsi Ringkas --}}
                    <p class="text-secondary-c mb-3" style="font-size:0.83rem; line-height: 1.45;">
                      Pelanggan wajib melunasi pesanan terlebih dahulu di kasir sebelum pesanan diproses di dapur atau diserahkan ke pelanggan.
                    </p>

                    {{-- Visual Flow Pipeline --}}
                    <div class="flow-pipeline p-3 rounded-3 mb-3">
                      <div class="d-flex align-items-center justify-content-between text-center position-relative">
                        <div class="flow-step flex-1">
                          <div class="flow-step-num mx-auto mb-1">1</div>
                          <div class="flow-step-label">Pesan Menu</div>
                        </div>
                        <div class="flow-step-arrow"><i class="bi bi-chevron-right text-muted-c"></i></div>
                        <div class="flow-step flex-1">
                          <div class="flow-step-num mx-auto mb-1 step-highlight-green">2</div>
                          <div class="flow-step-label fw-bold text-success">Lunas di Kasir</div>
                        </div>
                        <div class="flow-step-arrow"><i class="bi bi-chevron-right text-muted-c"></i></div>
                        <div class="flow-step flex-1">
                          <div class="flow-step-num mx-auto mb-1">3</div>
                          <div class="flow-step-label">Masak & Ambil</div>
                        </div>
                      </div>
                    </div>

                    {{-- Tag Fitur --}}
                    <div class="d-flex flex-wrap gap-1.5 mt-auto">
                      <span class="feature-pill"><i class="bi bi-check2 text-success me-1"></i>Fast Food & Coffee</span>
                      <span class="feature-pill"><i class="bi bi-check2 text-success me-1"></i>Cegah Order Ghosting</span>
                      <span class="feature-pill"><i class="bi bi-check2 text-success me-1"></i>Antrean Cepat</span>
                    </div>
                  </div>
                </div>
              </div>

              {{-- OPERATIONAL IMPACT LIVE PREVIEW BOX --}}
              <div class="impact-preview-box p-3.5 rounded-3 mb-4 {{ ($setting->payment_timing ?? 'post_payment') === 'pre_payment' ? 'impact-mode-green' : 'impact-mode-blue' }}" id="impactPreviewBox">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <i class="bi {{ ($setting->payment_timing ?? 'post_payment') === 'pre_payment' ? 'bi-lightning-charge-fill text-success' : 'bi-cup-hot-fill text-primary' }}" id="impactIcon"></i>
                  <span class="fw-bold" style="font-size:0.86rem;" id="impactTitle">
                    Dampak Operasional Sistem: {{ ($setting->payment_timing ?? 'post_payment') === 'pre_payment' ? 'Mode Bayar di Awal (Pre-Payment)' : 'Mode Bayar di Akhir (Post-Payment)' }}
                  </span>
                </div>
                <div class="text-secondary-c" style="font-size:0.8rem; line-height:1.5;" id="impactDesc">
                  <div class="row g-2 mt-1">
                    <div class="col-md-6">
                      <div class="impact-preview-item p-2 rounded-2">
                        <strong><i class="bi bi-shop me-1 text-primary"></i>Kasir POS:</strong>
                        <div class="text-muted-c mt-0.5">Tombol <em>"Lanjut ke Pembayaran"</em> dibuka saat tamu selesai makan untuk melunasi tagihan sebelum meja dilepas jadi tersedia.</div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="impact-preview-item p-2 rounded-2">
                        <strong><i class="bi bi-qr-code-scan me-1 text-primary"></i>QR Tamu:</strong>
                        <div class="text-muted-c mt-0.5">Tamu dapat memesan menu langsung dari meja, menambah pesanan susulan, dan memantau status pesanan tanpa perlu bayar di awal.</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Tombol Simpan --}}
              <div class="d-flex justify-content-between align-items-center pt-2">
                <span class="text-muted-c" style="font-size:0.8rem;">
                  <i class="bi bi-info-circle me-1"></i>Perubahan langsung diterapkan secara instan ke POS Kasir & QR Tamu.
                </span>
                <button type="submit" class="btn btn-primary-grad px-4 py-2.5 rounded-3 btn-loading d-flex align-items-center gap-2 shadow-sm" id="btnSavePaymentTiming">
                  <i class="bi bi-check2-circle fs-5"></i>
                  <span class="fw-semibold">Simpan Alur Pembayaran</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      {{-- PANEL 3: TEMA QR GUEST ORDERING --}}
      <div id="settings-theme" class="settings-panel" style="display:none;">
        <div class="card mb-4">
          <div class="card-header-flex">
            <h6><i class="bi bi-palette me-2 text-warning"></i>Tema QR Ordering Tamu (Guest Template)</h6>
            <span class="chip-tag" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; font-weight:600;">
              Live Dynamic Theme
            </span>
          </div>
          <div class="card-body">
            <p class="text-muted-c mb-4" style="font-size:0.88rem;">
              Pilih tampilan antarmuka saat pelanggan memindai QR Code meja. Perubahan tema langsung aktif seketika tanpa perlu reload server.
            </p>

            <form id="guestThemeForm" action="{{ route('admin.setting.update-theme') }}" method="POST">
              @csrf
              <input type="hidden" name="theme" id="selectedThemeInput" value="{{ $setting->theme ?? 'spicy_bites' }}">

              <div class="row g-3 mb-4">
                @foreach($themes as $t)
                  @php $isActive = ($setting->theme ?? 'spicy_bites') === $t['key']; @endphp
                  <div class="col-md-6 col-xl-4">
                    <div class="theme-card p-3 rounded-3 cursor-pointer h-100 {{ $isActive ? 'active' : '' }}"
                         id="themeCard_{{ $t['key'] }}"
                         onclick="selectGuestTheme('{{ $t['key'] }}')"
                         style="border: 2px solid {{ $isActive ? $t['color'] : 'var(--border-subtle, rgba(255,255,255,0.1))' }};">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                          <div class="rounded-circle d-flex align-items-center justify-center text-white"
                               style="width:30px; height:30px; background: {{ $t['color'] }};">
                            <i class="bi {{ $t['icon'] }} fs-6"></i>
                          </div>
                          <span class="fw-bold text-truncate" style="font-size:0.88rem;">{{ $t['name'] }}</span>
                        </div>
                        <span class="theme-active-badge" id="badgeTheme_{{ $t['key'] }}">
                          @if($isActive)
                            <span class="badge" style="background: {{ $t['color'] }}; color:#fff;"><i class="bi bi-check-lg"></i></span>
                          @endif
                        </span>
                      </div>

                      <div class="mb-2">
                        <span class="chip-tag" style="background: rgba(255,255,255,0.06); font-size:0.7rem; border:1px solid var(--border-subtle);">
                          {{ $t['badge'] }}
                        </span>
                      </div>

                      <p class="text-muted-c m-0" style="font-size:0.76rem; line-height:1.35;">
                        {{ $t['desc'] }}
                      </p>
                    </div>
                  </div>
                @endforeach
              </div>

              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary-grad px-4 btn-loading" id="btnSaveTheme">
                  <i class="bi bi-check2-circle me-1"></i>Terapkan Tema Guest
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      {{-- PANEL 4: MASTER SETTING PAJAK & SERVICE CHARGE --}}
      <div id="settings-tax" class="settings-panel" style="display:none;">
        <!-- Card Setting Pajak PB1 & Service Charge -->
        <div class="row g-4 mb-4">
          <!-- Card Setting Pajak PB1 -->
          <div class="col-lg-6">
            <div class="card h-100 p-4 border-0 shadow-sm" style="border: 1px solid var(--border-subtle) !important; border-radius: 1rem;">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:44px; height:44px; background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                    <i class="bi bi-receipt-cutoff fs-4"></i>
                  </div>
                  <div>
                    <h5 class="fw-bold mb-0">Pajak Restoran (PB1)</h5>
                    <small class="text-muted-c" style="font-size:0.75rem;">Pajak Barang & Jasa Tertentu (PBJT Restoran)</small>
                  </div>
                </div>
                <span class="badge {{ optional($tax)->is_active ? 'bg-success' : 'bg-secondary' }} px-3 py-1.5 rounded-pill" id="taxStatusBadge">
                  {{ optional($tax)->is_active ? 'Aktif' : 'Non-Aktif' }}
                </span>
              </div>
              
              <hr style="border-color: var(--border-subtle); opacity: 0.5;">

              <form id="formTax" action="{{ route('admin.keuangan.setting-tax.update-tax') }}" method="POST">
                @csrf
                <div class="mb-3 input-skeleton">
                  <label class="form-label-modern fw-semibold">Nama Label Pajak <span class="text-danger">*</span></label>
                  <input type="text" name="tax_name" id="inputTaxName" class="form-control form-control-modern" value="{{ optional($tax)->tax_name ?? 'PBJT Restoran 10%' }}" placeholder="Contoh: PBJT Restoran 10%" required>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-md-6 input-skeleton">
                    <label class="form-label-modern fw-semibold">Tarif Pajak (%) <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <input type="number" step="0.01" name="rate_percent" id="inputTaxRate" class="form-control form-control-modern" value="{{ optional($tax)->rate_percent ?? 10.00 }}" placeholder="10.00" required>
                      <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);">%</span>
                    </div>
                  </div>
                  <div class="col-md-6 input-skeleton">
                    <label class="form-label-modern fw-semibold">Tipe Pengenaan <span class="text-danger">*</span></label>
                    <select name="type" id="inputTaxType" class="form-select form-select-modern" required>
                      <option value="exclusive" {{ optional($tax)->type == 'exclusive' ? 'selected' : '' }}>Eksklusif (Ditambah dari Subtotal)</option>
                      <option value="inclusive" {{ optional($tax)->type == 'inclusive' ? 'selected' : '' }}>Inklusif (Sudah Termasuk di Harga)</option>
                    </select>
                  </div>
                </div>

                <div class="form-check form-switch mb-4">
                  <input class="form-check-input" type="checkbox" name="is_active" id="switchTaxActive" value="1" {{ optional($tax)->is_active ? 'checked' : '' }} style="cursor:pointer; width:2.4em; height:1.2em;">
                  <label class="form-check-label fw-semibold ms-2" for="switchTaxActive">Aktifkan Pengenaan Pajak pada Order Kasir</label>
                </div>

                <div class="d-flex justify-content-end">
                  <button type="submit" class="btn btn-primary-grad px-4 py-2.5 rounded-3 btn-loading" id="btnSaveTax">
                    <i class="bi bi-check2-circle me-1"></i>Simpan Setting Pajak
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Card Setting Service Charge -->
          <div class="col-lg-6">
            <div class="card h-100 p-4 border-0 shadow-sm" style="border: 1px solid var(--border-subtle) !important; border-radius: 1rem;">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:44px; height:44px; background: rgba(16, 185, 129, 0.15); color: #10b981;">
                    <i class="bi bi-person-hearts fs-4"></i>
                  </div>
                  <div>
                    <h5 class="fw-bold mb-0">Service Charge Pelayanan</h5>
                    <small class="text-muted-c" style="font-size:0.75rem;">Biaya Layanan Karyawan / Tip Layanan Outlet</small>
                  </div>
                </div>
                <span class="badge {{ optional($service)->is_active ? 'bg-success' : 'bg-secondary' }} px-3 py-1.5 rounded-pill" id="serviceStatusBadge">
                  {{ optional($service)->is_active ? 'Aktif' : 'Non-Aktif' }}
                </span>
              </div>
              
              <hr style="border-color: var(--border-subtle); opacity: 0.5;">

              <form id="formService" action="{{ route('admin.keuangan.setting-tax.update-service') }}" method="POST">
                @csrf
                <div class="mb-3 input-skeleton">
                  <label class="form-label-modern fw-semibold">Nama Label Service Charge <span class="text-danger">*</span></label>
                  <input type="text" name="service_name" id="inputServiceName" class="form-control form-control-modern" value="{{ optional($service)->service_name ?? 'Service Charge 5%' }}" placeholder="Contoh: Service Charge 5%" required>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-md-6 input-skeleton">
                    <label class="form-label-modern fw-semibold">Tarif Layanan (%) <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <input type="number" step="0.01" name="rate_percent" id="inputServiceRate" class="form-control form-control-modern" value="{{ optional($service)->rate_percent ?? 5.00 }}" placeholder="5.00" required>
                      <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);">%</span>
                    </div>
                  </div>
                  <div class="col-md-6 d-flex align-items-center">
                    <div class="form-check form-switch mt-2">
                      <input class="form-check-input" type="checkbox" name="is_taxable" id="switchServiceTaxable" value="1" {{ optional($service)->is_taxable ? 'checked' : '' }} style="cursor:pointer; width:2.2em; height:1.1em;">
                      <label class="form-check-label fw-semibold ms-2" for="switchServiceTaxable" style="font-size:0.83rem;">Kena DPP Pajak PB1</label>
                    </div>
                  </div>
                </div>

                <div class="form-check form-switch mb-4">
                  <input class="form-check-input" type="checkbox" name="is_active" id="switchServiceActive" value="1" {{ optional($service)->is_active ? 'checked' : '' }} style="cursor:pointer; width:2.4em; height:1.2em;">
                  <label class="form-check-label fw-semibold ms-2" for="switchServiceActive">Aktifkan Service Charge pada Order Kasir</label>
                </div>

                <div class="d-flex justify-content-end">
                  <button type="submit" class="btn btn-primary-grad px-4 py-2.5 rounded-3 btn-loading" id="btnSaveService" style="background: linear-gradient(135deg, #10b981, #059669); border:none;">
                    <i class="bi bi-check2-circle me-1"></i>Simpan Service Charge
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Simulation Preview Card -->
        <div class="card p-4 border-0 shadow-sm" style="border: 1px solid var(--border-subtle) !important; border-radius: 1rem;">
          <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-calculator-fill text-primary"></i>Simulasi Kalkulasi Checkout Struk Kasir
          </h6>
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0" style="font-size:0.88rem;">
              <thead style="background: var(--bg-elevated); color: var(--text-primary); border-bottom: 2px solid var(--border-subtle);">
                <tr>
                  <th>Contoh Pesanan</th>
                  <th class="text-end">Subtotal</th>
                  <th class="text-end">Diskon Promo</th>
                  <th class="text-end">Service Charge</th>
                  <th class="text-end">Dasar Pajak (DPP)</th>
                  <th class="text-end">Pajak PB1</th>
                  <th class="text-end fw-bold">Grand Total Checkout</th>
                </tr>
              </thead>
              <tbody>
                <tr style="border-bottom: 1px solid var(--border-subtle);">
                  <td>2x Menu Makanan + 2x Minuman</td>
                  <td class="text-end text-muted-c">Rp 100.000</td>
                  <td class="text-end text-danger">- Rp 10.000</td>
                  <td class="text-end text-success" id="simServiceText">+ Rp 4.500</td>
                  <td class="text-end text-muted-c" id="simDppText">Rp 94.500</td>
                  <td class="text-end text-primary" id="simTaxText">+ Rp 9.450</td>
                  <td class="text-end fw-bold text-success fs-6" id="simGrandTotalText">Rp 103.950</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- PANEL 5: MASTER SHIFT & JAM CUT-OFF RESTORAN --}}
      <div id="settings-shift" class="settings-panel" style="display:none;">
        <!-- CARD 1: PENGATURAN JAM CUT-OFF OPERASIONAL RESTO -->
        <div class="card mb-4 border-0 shadow-sm" style="border: 1px solid var(--border-subtle) !important; border-radius: 1rem;">
          <div class="card-header-flex py-3 px-4" style="border-bottom: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center gap-2">
              <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                <i class="bi bi-clock-history fs-5"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-bold">Pengaturan Cut-Off Operasional & Mode Shift</h6>
                <small class="text-muted-c" style="font-size:0.75rem;">Menentukan batas tanggal bisnis dan cara kerja sesi kasir</small>
              </div>
            </div>
            <span class="chip-tag px-3 py-1 rounded-pill" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.25); font-weight:600; font-size:0.75rem;">
              <i class="bi bi-shield-lock me-1"></i>Audit & POS
            </span>
          </div>

          <div class="card-body p-4">
            <form action="{{ route('admin.keuangan.setting-shift.update-cutoff') }}" method="POST" id="formCutoff">
              @csrf
              
              <!-- Option Mode Shift Box -->
              <div class="mb-4">
                <label class="form-label-modern mb-2 fw-bold text-uppercase" style="font-size:0.8rem; letter-spacing:0.5px;">1. Pilih Mode Pengoperasian Shift Kasir</label>
                <div class="row g-3">
                  
                  <!-- Mode 1: Auto Master Shift -->
                  <div class="col-md-4">
                    <div class="mode-box-card @if(($shiftSetting->shift_mode ?? 'auto_master') === 'auto_master') active @endif" onclick="selectShiftMode('auto_master')">
                      <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="mode-icon-circle" style="background: rgba(59,130,246,0.15); color:#3b82f6;">
                          <i class="bi bi-calendar-range"></i>
                        </div>
                        <input type="radio" name="shift_mode" value="auto_master" id="mode_auto_master" class="form-check-input" @if(($shiftSetting->shift_mode ?? 'auto_master') === 'auto_master') checked @endif style="cursor: pointer;">
                      </div>
                      <div class="fw-bold mb-1">Terjadwal (Master Shift)</div>
                      <p class="text-muted-c mb-0" style="font-size:0.82rem; line-height: 1.4;">Kasir memilih shift resmi dari daftar master. Jam kerja dan modal awal terisi otomatis sesuai template.</p>
                    </div>
                  </div>

                  <!-- Mode 2: Manual Shift -->
                  <div class="col-md-4">
                    <div class="mode-box-card @if(($shiftSetting->shift_mode ?? 'auto_master') === 'manual') active @endif" onclick="selectShiftMode('manual')">
                      <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="mode-icon-circle" style="background: rgba(245,158,11,0.15); color:#f59e0b;">
                          <i class="bi bi-pencil-square"></i>
                        </div>
                        <input type="radio" name="shift_mode" value="manual" id="mode_manual" class="form-check-input" @if(($shiftSetting->shift_mode ?? 'auto_master') === 'manual') checked @endif style="cursor: pointer;">
                      </div>
                      <div class="fw-bold mb-1">Manual / Dinamis</div>
                      <p class="text-muted-c mb-0" style="font-size:0.82rem; line-height: 1.4;">Kasir bebas menginput nama shift dan modal kas awal secara fleksibel saat pertama kali bertugas.</p>
                    </div>
                  </div>

                  <!-- Mode 3: Single Daily Shift -->
                  <div class="col-md-4">
                    <div class="mode-box-card @if(($shiftSetting->shift_mode ?? 'auto_master') === 'single_daily') active @endif" onclick="selectShiftMode('single_daily')">
                      <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="mode-icon-circle" style="background: rgba(16,185,129,0.15); color:#10b981;">
                          <i class="bi bi-sun-fill"></i>
                        </div>
                        <input type="radio" name="shift_mode" value="single_daily" id="mode_single_daily" class="form-check-input" @if(($shiftSetting->shift_mode ?? 'auto_master') === 'single_daily') checked @endif style="cursor: pointer;">
                      </div>
                      <div class="fw-bold mb-1">Single Daily Shift (Full Day)</div>
                      <p class="text-muted-c mb-0" style="font-size:0.82rem; line-height: 1.4;">Hanya 1 sesi shift per hari. Kasir buka 1x di pagi hari dan tutup 1x saat toko selesai beroperasi.</p>
                    </div>
                  </div>

                </div>
              </div>

              <!-- Jam Cut-Off & Auto Lock -->
              <div class="row g-4 align-items-end pt-2">
                <div class="col-md-4 input-skeleton">
                  <label for="daily_cutoff_time" class="form-label-modern mb-1 fw-semibold">
                    Jam Cut-Off Operasional Harian <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);"><i class="bi bi-clock"></i></span>
                    <input type="time" name="daily_cutoff_time" id="daily_cutoff_time" class="form-control form-control-modern" value="{{ \Carbon\Carbon::parse($shiftSetting->daily_cutoff_time ?? '03:00')->format('H:i') }}" required>
                  </div>
                  <div class="text-muted-c mt-1" style="font-size: 0.78rem;">
                    Transaksi setelah jam ini dianggap sebagai <strong>Tanggal Bisnis Baru</strong> (Rekomendasi resto: <code>03:00</code> Pagi).
                  </div>
                </div>

                <div class="col-md-5">
                  <div class="form-check form-switch pt-2">
                    <input class="form-check-input" type="checkbox" name="auto_lock_unclosed" id="auto_lock_unclosed" value="1" @if($shiftSetting->auto_lock_unclosed ?? 1) checked @endif style="width: 2.4em; height: 1.2em; cursor: pointer;">
                    <label class="form-check-label fw-semibold ms-2" for="auto_lock_unclosed">
                      Auto-Lock Shift Kemarin (Strict Protection)
                    </label>
                  </div>
                  <div class="text-muted-c mt-1" style="font-size: 0.78rem;">
                    Kunci layar POS jika shift hari kemarin belum di-close oleh kasir sebelumnya.
                  </div>
                </div>

                <div class="col-md-3 text-end">
                  <button type="submit" class="btn btn-primary-grad w-100 py-2.5 rounded-3 btn-loading" id="btnSaveCutoff">
                    <i class="bi bi-save me-1"></i> Simpan Pengaturan
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- CARD 2: DAFTAR MASTER SHIFT RESTORAN -->
        <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-subtle) !important; border-radius: 1rem;">
          <div class="card-header-flex py-3 px-4" style="border-bottom: 1px solid var(--border-subtle);">
            <div>
              <h6 class="mb-0 fw-bold"><i class="bi bi-list-stars text-primary me-2"></i>Daftar Master Shift Restoran</h6>
              <div class="text-muted-c" style="font-size:0.75rem;">Template shift yang dipilih kasir saat membuka kasir di POS.</div>
            </div>
            <button type="button" class="btn btn-primary-grad btn-sm px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#modalAddShift">
              <i class="bi bi-plus-lg me-1"></i>Tambah Master Shift
            </button>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0" style="font-size:0.88rem;">
                <thead style="background: var(--bg-elevated); color: var(--text-primary); border-bottom: 2px solid var(--border-subtle);">
                  <tr>
                    <th style="width: 70px;" class="ps-4">No</th>
                    <th>Nama Shift</th>
                    <th>Jam Kerja Operasional</th>
                    <th>Default Modal Awal Kasir</th>
                    <th>Status Active</th>
                    <th class="text-end pe-4" style="width: 140px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($shifts as $index => $shift)
                    <tr style="border-bottom: 1px solid var(--border-subtle);">
                      <td class="fw-bold text-secondary ps-4">#{{ $shift->shift_number }}</td>
                      <td>
                        <span class="fw-bold">{{ $shift->shift_name }}</span>
                      </td>
                      <td>
                        <span class="badge px-2.5 py-1 rounded-pill" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.25);">
                          <i class="bi bi-clock me-1"></i>
                          {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }} WIB
                        </span>
                      </td>
                      <td>
                        <span class="fw-bold text-success">
                          Rp {{ number_format($shift->default_starting_cash, 0, ',', '.') }}
                        </span>
                      </td>
                      <td>
                        @if($shift->is_active)
                          <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Aktif</span>
                        @else
                          <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill"><i class="bi bi-x-circle-fill me-1"></i>Non-Aktif</span>
                        @endif
                      </td>
                      <td class="text-end pe-4">
                        <button type="button" class="btn btn-sm btn-outline-warning rounded-2 me-1" title="Edit Shift" onclick="editShift({{ json_encode($shift) }})">
                          <i class="bi bi-pencil-fill"></i>
                        </button>
                        <form action="{{ route('admin.keuangan.setting-shift.destroy-shift', $shift->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Master Shift ini?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger rounded-2" title="Hapus Shift">
                            <i class="bi bi-trash-fill"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center py-4 text-muted-c">
                        <i class="bi bi-exclamation-circle d-block fs-3 mb-2 text-secondary"></i>
                        Belum ada data Master Shift. Klik tombol <strong>"Tambah Master Shift"</strong> di kanan atas.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- MODAL 1: TAMBAH MASTER SHIFT -->
<div class="modal fade" id="modalAddShift" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-content-modern" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle); border-radius: 1rem; color: var(--text-primary);">
      <div class="modal-header border-0 px-4 py-3" style="border-bottom: 1px solid var(--border-subtle) !important;">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-plus-circle text-primary me-2"></i>Tambah Master Shift Baru
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.keuangan.setting-shift.store-shift') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3 input-skeleton">
            <label for="shift_name" class="form-label-modern fw-semibold">Nama Shift <span class="text-danger">*</span></label>
            <input type="text" name="shift_name" class="form-control form-control-modern" placeholder="Contoh: Shift 1 Pagi" required>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6 input-skeleton">
              <label for="start_time" class="form-label-modern fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
              <input type="time" name="start_time" class="form-control form-control-modern" value="08:00" required>
            </div>
            <div class="col-6 input-skeleton">
              <label for="end_time" class="form-label-modern fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
              <input type="time" name="end_time" class="form-control form-control-modern" value="16:00" required>
            </div>
          </div>
          <div class="mb-3 input-skeleton">
            <label for="default_starting_cash" class="form-label-modern fw-semibold">Default Modal Awal Kasir (Rp) <span class="text-danger">*</span></label>
            <input type="number" name="default_starting_cash" class="form-control form-control-modern" value="300000" step="1000" min="0" required>
          </div>
          <div class="form-check form-switch pt-1">
            <input class="form-check-input" type="checkbox" name="is_active" id="add_is_active" value="1" checked style="cursor: pointer; width:2.2em; height:1.1em;">
            <label class="form-check-label fw-semibold ms-2" for="add_is_active">Aktifkan Shift Ini</label>
          </div>
        </div>
        <div class="modal-footer border-0 px-4 py-3" style="border-top: 1px solid var(--border-subtle) !important;">
          <button type="button" class="btn btn-outline-secondary rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-grad rounded-3 px-4 btn-loading">Simpan Shift</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL 2: EDIT MASTER SHIFT -->
<div class="modal fade" id="modalEditShift" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-content-modern" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle); border-radius: 1rem; color: var(--text-primary);">
      <div class="modal-header border-0 px-4 py-3" style="border-bottom: 1px solid var(--border-subtle) !important;">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-pencil-square text-warning me-2"></i>Edit Master Shift
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditShift" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3 input-skeleton">
            <label for="edit_shift_name" class="form-label-modern fw-semibold">Nama Shift <span class="text-danger">*</span></label>
            <input type="text" name="shift_name" id="edit_shift_name" class="form-control form-control-modern" required>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6 input-skeleton">
              <label for="edit_start_time" class="form-label-modern fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
              <input type="time" name="start_time" id="edit_start_time" class="form-control form-control-modern" required>
            </div>
            <div class="col-6 input-skeleton">
              <label for="edit_end_time" class="form-label-modern fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
              <input type="time" name="end_time" id="edit_end_time" class="form-control form-control-modern" required>
            </div>
          </div>
          <div class="mb-3 input-skeleton">
            <label for="edit_default_starting_cash" class="form-label-modern fw-semibold">Default Modal Awal Kasir (Rp) <span class="text-danger">*</span></label>
            <input type="number" name="default_starting_cash" id="edit_default_starting_cash" class="form-control form-control-modern" step="1000" min="0" required>
          </div>
          <div class="form-check form-switch pt-1">
            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1" style="cursor: pointer; width:2.2em; height:1.1em;">
            <label class="form-check-label fw-semibold ms-2" for="edit_is_active">Aktifkan Shift Ini</label>
          </div>
        </div>
        <div class="modal-footer border-0 px-4 py-3" style="border-top: 1px solid var(--border-subtle) !important;">
          <button type="button" class="btn btn-outline-secondary rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-grad rounded-3 px-4 btn-loading">Update Shift</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.settings-menu .nav-item .nav-link {
  color: var(--text-secondary, #94a3b8);
  transition: all 0.2s ease-in-out;
  display: flex;
  align-items: center;
}
.settings-menu .nav-item .nav-link:hover {
  color: var(--text-primary, #f8fafc);
  background: var(--bg-elevated-2, rgba(255, 255, 255, 0.05));
}
.settings-menu .nav-item.active .nav-link {
  color: #fff;
  background: var(--accent-1, #3b82f6);
}

/* Timing Bento Card Design */
.timing-bento-card {
  border: 2px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
  background: var(--bg-elevated, #222834);
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  user-select: none;
  display: flex;
  flex-direction: column;
}
.timing-bento-card:hover {
  transform: translateY(-3px);
  border-color: var(--border-strong, rgba(255, 255, 255, 0.2));
}

/* Light Theme Adaptations */
[data-theme="light"] .timing-bento-card {
  background: var(--bg-surface, #ffffff);
  border-color: var(--border-subtle, #dce6f5);
}
[data-theme="light"] .timing-bento-card:hover {
  background: var(--bg-elevated-2, #eef4ff);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
}
[data-theme="light"] .timing-bento-card.active-blue {
  background: #f0f7ff !important;
  border-color: #3b82f6 !important;
  box-shadow: 0 0 0 1px #3b82f6, 0 12px 30px -10px rgba(59, 130, 246, 0.2) !important;
}
[data-theme="light"] .timing-bento-card.active-green {
  background: #f0fdf4 !important;
  border-color: #10b981 !important;
  box-shadow: 0 0 0 1px #10b981, 0 12px 30px -10px rgba(16, 185, 129, 0.2) !important;
}
[data-theme="light"] .flow-pipeline {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}
[data-theme="light"] .flow-step-num {
  background: #ffffff;
  border-color: #cbd5e1;
  color: #475569;
}
[data-theme="light"] .feature-pill {
  background: #f1f5f9;
  border-color: #e2e8f0;
  color: #334155;
}
[data-theme="light"] .impact-preview-box.impact-mode-blue {
  background: #eff6ff !important;
  border: 1px dashed #93c5fd !important;
}
[data-theme="light"] .impact-preview-box.impact-mode-green {
  background: #f0fdf4 !important;
  border: 1px dashed #86efac !important;
}
[data-theme="light"] .impact-preview-item {
  background: #ffffff;
  border: 1px solid #e2e8f0;
}
[data-theme="light"] .theme-card {
  background: var(--bg-surface, #ffffff);
  border-color: var(--border-subtle, #dce6f5);
}
[data-theme="light"] .theme-card:hover {
  background: var(--bg-elevated-2, #eef4ff);
}

/* Dark Theme Adaptations */
[data-theme="dark"] .timing-bento-card {
  background: var(--bg-elevated, #222834);
  border-color: var(--border-subtle, #2f3748);
}
[data-theme="dark"] .timing-bento-card:hover {
  background: var(--bg-elevated-2, #2a3140);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
}
[data-theme="dark"] .timing-bento-card.active-blue {
  background: linear-gradient(180deg, rgba(59, 130, 246, 0.12) 0%, rgba(59, 130, 246, 0.03) 100%) !important;
  border-color: #3b82f6 !important;
  box-shadow: 0 0 0 1px #3b82f6, 0 12px 30px -10px rgba(59, 130, 246, 0.3) !important;
}
[data-theme="dark"] .timing-bento-card.active-green {
  background: linear-gradient(180deg, rgba(16, 185, 129, 0.12) 0%, rgba(16, 185, 129, 0.03) 100%) !important;
  border-color: #10b981 !important;
  box-shadow: 0 0 0 1px #10b981, 0 12px 30px -10px rgba(16, 185, 129, 0.3) !important;
}
[data-theme="dark"] .flow-pipeline {
  background: rgba(0, 0, 0, 0.25);
  border: 1px solid var(--border-subtle, #2f3748);
}
[data-theme="dark"] .flow-step-num {
  background: var(--bg-elevated-2, #2a3140);
  border-color: var(--border-subtle, #2f3748);
  color: var(--text-secondary, #c8d0dc);
}
[data-theme="dark"] .feature-pill {
  background: rgba(255, 255, 255, 0.04);
  border-color: var(--border-subtle, #2f3748);
  color: #cbd5e1;
}
[data-theme="dark"] .impact-preview-box.impact-mode-blue {
  background: rgba(59, 130, 246, 0.06) !important;
  border: 1px dashed rgba(59, 130, 246, 0.3) !important;
}
[data-theme="dark"] .impact-preview-box.impact-mode-green {
  background: rgba(16, 185, 129, 0.06) !important;
  border: 1px dashed rgba(16, 185, 129, 0.3) !important;
}
[data-theme="dark"] .impact-preview-item {
  background: rgba(0, 0, 0, 0.25);
  border: 1px solid rgba(255, 255, 255, 0.05);
}
[data-theme="dark"] .theme-card {
  background: var(--bg-elevated, #222834);
  border-color: var(--border-subtle, #2f3748);
}
[data-theme="dark"] .theme-card:hover {
  background: var(--bg-elevated-2, #2a3140);
}

/* Custom Radio Indicator */
.radio-ring {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  border: 2px solid var(--border-subtle, rgba(255, 255, 255, 0.2));
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease-in-out;
}
.radio-ring.checked-blue {
  border-color: #3b82f6;
  background: #3b82f6;
}
.radio-ring.checked-green {
  border-color: #10b981;
  background: #10b981;
}
.radio-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #ffffff;
  opacity: 0;
  transform: scale(0.5);
  transition: all 0.2s ease-in-out;
}
.radio-ring.checked-blue .radio-dot,
.radio-ring.checked-green .radio-dot {
  opacity: 1;
  transform: scale(1);
}

/* Workflow Flow Step */
.step-highlight-blue {
  background: #3b82f6 !important;
  color: #ffffff !important;
  box-shadow: 0 0 10px rgba(59, 130, 246, 0.4);
}
.step-highlight-green {
  background: #10b981 !important;
  color: #ffffff !important;
  box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
}
.flow-step-num {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.72rem;
  font-weight: 700;
}
.flow-step-label {
  font-size: 0.72rem;
  font-weight: 600;
  color: var(--text-secondary, #94a3b8);
}
.flow-step-arrow {
  font-size: 0.8rem;
  opacity: 0.6;
}

/* Feature Pills */
.feature-pill {
  font-size: 0.72rem;
  font-weight: 500;
  padding: 4px 10px;
  border-radius: 9999px;
}

/* Theme Card */
.theme-card {
  transition: all 0.2s ease-in-out;
  user-select: none;
}
.theme-card.active {
  box-shadow: 0 0 0 1px var(--accent-1, #3b82f6);
}
.cursor-pointer {
  cursor: pointer;
}

/* Shift Mode Box Card */
.mode-box-card {
  border: 1.5px solid var(--border-subtle);
  background: var(--bg-elevated);
  border-radius: 14px;
  padding: 1.25rem;
  cursor: pointer;
  transition: all 0.25s ease;
  height: 100%;
  position: relative;
}
.mode-box-card:hover {
  border-color: #3b82f6;
  background: var(--bg-elevated-2);
  transform: translateY(-2px);
}
.mode-box-card.active {
  border-color: #3b82f6 !important;
  background: rgba(59, 130, 246, 0.08) !important;
  box-shadow: 0 4px 16px rgba(59, 130, 246, 0.2) !important;
}
.mode-icon-circle {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
}
</style>
@endpush

@push('scripts')
<script>
// Tab Switching (Mengikuti /docs/settings)
document.addEventListener('DOMContentLoaded', function() {
  const tabLinks = document.querySelectorAll('.settings-menu .nav-link[data-settings-target]');
  const panels = document.querySelectorAll('.settings-panel');

  tabLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      tabLinks.forEach(l => l.parentElement.classList.remove('active'));
      this.parentElement.classList.add('active');

      const targetSelector = this.getAttribute('data-settings-target');
      if (!targetSelector) return;
      panels.forEach(p => p.style.display = 'none');

      const targetPanel = document.querySelector(targetSelector);
      if (targetPanel) {
        targetPanel.style.display = 'block';
      }
    });
  });

  // Handle Form Payment Timing via AJAX
  const paymentTimingForm = document.getElementById('paymentTimingForm');
  const btnSavePaymentTiming = document.getElementById('btnSavePaymentTiming');

  paymentTimingForm.addEventListener('submit', function(e) {
    e.preventDefault();
    btnSavePaymentTiming.disabled = true;
    btnSavePaymentTiming.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

    const formData = new FormData(paymentTimingForm);
    setTimeout(() => {
      fetch(paymentTimingForm.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        btnSavePaymentTiming.disabled = false;
        btnSavePaymentTiming.innerHTML = '<i class="bi bi-check2-circle fs-5"></i><span class="fw-semibold">Simpan Alur Pembayaran</span>';
        if (data.success) {
          NexoraToast(data.message, 'success');
        } else {
          NexoraToast(data.message || 'Gagal menyimpan pengaturan.', 'danger');
        }
      })
      .catch(() => {
        btnSavePaymentTiming.disabled = false;
        btnSavePaymentTiming.innerHTML = '<i class="bi bi-check2-circle fs-5"></i><span class="fw-semibold">Simpan Alur Pembayaran</span>';
        NexoraToast('Terjadi kesalahan jaringan.', 'danger');
      });
    }, 400);
  });

  // Handle Form Theme via AJAX
  const guestThemeForm = document.getElementById('guestThemeForm');
  const btnSaveTheme = document.getElementById('btnSaveTheme');

  guestThemeForm.addEventListener('submit', function(e) {
    e.preventDefault();
    btnSaveTheme.disabled = true;
    btnSaveTheme.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menerapkan Tema...';

    const formData = new FormData(guestThemeForm);
    setTimeout(() => {
      fetch(guestThemeForm.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        btnSaveTheme.disabled = false;
        btnSaveTheme.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Terapkan Tema Guest';
        if (data.success) {
          NexoraToast(data.message, 'success');
        } else {
          NexoraToast(data.message || 'Gagal mengubah tema.', 'danger');
        }
      })
      .catch(() => {
        btnSaveTheme.disabled = false;
        btnSaveTheme.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Terapkan Tema Guest';
        NexoraToast('Terjadi kesalahan jaringan.', 'danger');
      });
    }, 400);
  });

  // Handle Form Tax via AJAX
  const formTax = document.getElementById('formTax');
  const btnSaveTax = document.getElementById('btnSaveTax');
  if (formTax) {
    formTax.addEventListener('submit', function(e) {
      e.preventDefault();
      btnSaveTax.disabled = true;
      btnSaveTax.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

      const formData = new FormData(formTax);
      setTimeout(() => {
        fetch(formTax.action, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          btnSaveTax.disabled = false;
          btnSaveTax.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Simpan Setting Pajak';
          if (data.status === 'success' || data.success) {
            NexoraToast(data.message || 'Pengaturan Pajak berhasil disimpan!', 'success');
            const isActive = document.getElementById('switchTaxActive').checked;
            const badge = document.getElementById('taxStatusBadge');
            badge.className = isActive ? 'badge bg-success px-3 py-1.5 rounded-pill' : 'badge bg-secondary px-3 py-1.5 rounded-pill';
            badge.textContent = isActive ? 'Aktif' : 'Non-Aktif';
            recalculateSimulation();
          } else {
            NexoraToast(data.message || 'Gagal menyimpan pengaturan pajak.', 'danger');
          }
        })
        .catch(() => {
          btnSaveTax.disabled = false;
          btnSaveTax.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Simpan Setting Pajak';
          NexoraToast('Terjadi kesalahan jaringan.', 'danger');
        });
      }, 400);
    });
  }

  // Handle Form Service Charge via AJAX
  const formService = document.getElementById('formService');
  const btnSaveService = document.getElementById('btnSaveService');
  if (formService) {
    formService.addEventListener('submit', function(e) {
      e.preventDefault();
      btnSaveService.disabled = true;
      btnSaveService.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

      const formData = new FormData(formService);
      setTimeout(() => {
        fetch(formService.action, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          btnSaveService.disabled = false;
          btnSaveService.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Simpan Service Charge';
          if (data.status === 'success' || data.success) {
            NexoraToast(data.message || 'Pengaturan Service Charge berhasil disimpan!', 'success');
            const isActive = document.getElementById('switchServiceActive').checked;
            const badge = document.getElementById('serviceStatusBadge');
            badge.className = isActive ? 'badge bg-success px-3 py-1.5 rounded-pill' : 'badge bg-secondary px-3 py-1.5 rounded-pill';
            badge.textContent = isActive ? 'Aktif' : 'Non-Aktif';
            recalculateSimulation();
          } else {
            NexoraToast(data.message || 'Gagal menyimpan service charge.', 'danger');
          }
        })
        .catch(() => {
          btnSaveService.disabled = false;
          btnSaveService.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Simpan Service Charge';
          NexoraToast('Terjadi kesalahan jaringan.', 'danger');
        });
      }, 400);
    });
  }

  // Live Simulation Calculation for Tax & Service Charge
  function recalculateSimulation() {
    const subtotal = 100000;
    const discount = 10000;
    const netOrder = subtotal - discount; // 90000

    const serviceActive = document.getElementById('switchServiceActive') ? document.getElementById('switchServiceActive').checked : true;
    const serviceRate = parseFloat(document.getElementById('inputServiceRate')?.value || 5) / 100;
    const serviceTaxable = document.getElementById('switchServiceTaxable') ? document.getElementById('switchServiceTaxable').checked : true;
    
    const taxActive = document.getElementById('switchTaxActive') ? document.getElementById('switchTaxActive').checked : true;
    const taxRate = parseFloat(document.getElementById('inputTaxRate')?.value || 10) / 100;

    const serviceAmount = serviceActive ? Math.round(netOrder * serviceRate) : 0;
    const dpp = serviceTaxable ? (netOrder + serviceAmount) : netOrder;
    const taxAmount = taxActive ? Math.round(dpp * taxRate) : 0;
    const grandTotal = netOrder + serviceAmount + taxAmount;

    if (document.getElementById('simServiceText')) {
      document.getElementById('simServiceText').textContent = serviceActive ? `+ Rp ${serviceAmount.toLocaleString('id-ID')}` : 'Rp 0 (Nonaktif)';
      document.getElementById('simDppText').textContent = `Rp ${dpp.toLocaleString('id-ID')}`;
      document.getElementById('simTaxText').textContent = taxActive ? `+ Rp ${taxAmount.toLocaleString('id-ID')}` : 'Rp 0 (Nonaktif)';
      document.getElementById('simGrandTotalText').textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;
    }
  }

  // Attach live calculation triggers
  ['inputTaxRate', 'inputServiceRate', 'switchTaxActive', 'switchServiceActive', 'switchServiceTaxable'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener('input', recalculateSimulation);
      el.addEventListener('change', recalculateSimulation);
    }
  });

  // Handle Form Company Profile via AJAX
  const companyProfileForm = document.getElementById('companyProfileForm');
  const btnSaveCompanyProfile = document.getElementById('btnSaveCompanyProfile');
  if (companyProfileForm) {
    companyProfileForm.addEventListener('submit', function(e) {
      e.preventDefault();
      btnSaveCompanyProfile.disabled = true;
      btnSaveCompanyProfile.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan Profil...';

      const formData = new FormData(companyProfileForm);
      setTimeout(() => {
        fetch(companyProfileForm.action, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          btnSaveCompanyProfile.disabled = false;
          btnSaveCompanyProfile.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Simpan Profil Usaha';
          if (data.success) {
            NexoraToast(data.message, 'success');
          } else {
            NexoraToast(data.message || 'Gagal memperbarui profil usaha.', 'danger');
          }
        })
        .catch(() => {
          btnSaveCompanyProfile.disabled = false;
          btnSaveCompanyProfile.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Simpan Profil Usaha';
          NexoraToast('Terjadi kesalahan saat mengunggah data.', 'danger');
        });
      }, 400);
    });
  }

  // Handle URL Hash navigation on page load (e.g. /admin/setting#tax or #shift)
  const hash = window.location.hash;
  if (hash) {
    const targetTabLink = document.querySelector(`.settings-menu .nav-link[data-settings-target="${hash}"]`) ||
                          document.querySelector(`.settings-menu .nav-link[data-settings-target="#settings-${hash.replace('#', '')}"]`);
    if (targetTabLink) {
      targetTabLink.click();
    }
  }
});

// Selection Helpers
function selectPaymentTiming(timing) {
  document.getElementById('paymentTimingInput').value = timing;
  const cardPost = document.getElementById('cardPostPayment');
  const cardPre = document.getElementById('cardPrePayment');
  const ringPost = document.querySelector('#radioPostPayment .radio-ring');
  const ringPre = document.querySelector('#radioPrePayment .radio-ring');
  
  const impactBox = document.getElementById('impactPreviewBox');
  const impactIcon = document.getElementById('impactIcon');
  const impactTitle = document.getElementById('impactTitle');
  const impactDesc = document.getElementById('impactDesc');

  if (timing === 'post_payment') {
    cardPost.classList.add('active-blue');
    cardPre.classList.remove('active-green');
    ringPost.classList.add('checked-blue');
    ringPre.classList.remove('checked-green');

    impactBox.classList.remove('impact-mode-green');
    impactBox.classList.add('impact-mode-blue');
    impactIcon.className = 'bi bi-cup-hot-fill text-primary';
    impactTitle.textContent = 'Dampak Operasional Sistem: Mode Bayar di Akhir (Post-Payment)';
    impactDesc.innerHTML = `
      <div class="row g-2 mt-1">
        <div class="col-md-6">
          <div class="impact-preview-item p-2 rounded-2">
            <strong><i class="bi bi-shop me-1 text-primary"></i>Kasir POS:</strong>
            <div class="text-muted-c mt-0.5">Tombol <em>"Lanjut ke Pembayaran"</em> dibuka saat tamu selesai makan untuk melunasi tagihan sebelum meja dilepas jadi tersedia.</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="impact-preview-item p-2 rounded-2">
            <strong><i class="bi bi-qr-code-scan me-1 text-primary"></i>QR Tamu:</strong>
            <div class="text-muted-c mt-0.5">Tamu dapat memesan menu langsung dari meja, menambah pesanan susulan, dan memantau status pesanan tanpa perlu bayar di awal.</div>
          </div>
        </div>
      </div>
    `;
  } else {
    cardPre.classList.add('active-green');
    cardPost.classList.remove('active-blue');
    ringPre.classList.add('checked-green');
    ringPost.classList.remove('checked-blue');

    impactBox.classList.remove('impact-mode-blue');
    impactBox.classList.add('impact-mode-green');
    impactIcon.className = 'bi bi-lightning-charge-fill text-success';
    impactTitle.textContent = 'Dampak Operasional Sistem: Mode Bayar di Awal (Pre-Payment)';
    impactDesc.innerHTML = `
      <div class="row g-2 mt-1">
        <div class="col-md-6">
          <div class="impact-preview-item p-2 rounded-2">
            <strong><i class="bi bi-shop me-1 text-success"></i>Kasir POS:</strong>
            <div class="text-muted-c mt-0.5">Pesanan baru langsung diproses pembayarannya lunas (Cash/Debit) di kasir sebelum pesanan dikirim dan dimasak dapur.</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="impact-preview-item p-2 rounded-2">
            <strong><i class="bi bi-qr-code-scan me-1 text-success"></i>QR Tamu:</strong>
            <div class="text-muted-c mt-0.5">Setelah konfirmasi pesanan dari meja, tamu diarahkan untuk menyelesaikan pembayaran ke kasir agar pesanan segera dimasak.</div>
          </div>
        </div>
      </div>
    `;
  }
}

function selectGuestTheme(themeKey) {
  document.getElementById('selectedThemeInput').value = themeKey;
  const themeCards = document.querySelectorAll('.theme-card');
  const themeBadges = document.querySelectorAll('.theme-active-badge');

  themeCards.forEach(c => {
    c.classList.remove('active');
    c.style.borderColor = 'var(--border-subtle, rgba(255,255,255,0.1))';
  });
  themeBadges.forEach(b => b.innerHTML = '');

  const selectedCard = document.getElementById('themeCard_' + themeKey);
  const selectedBadge = document.getElementById('badgeTheme_' + themeKey);
  if (selectedCard) {
    selectedCard.classList.add('active');
    selectedCard.style.borderColor = 'var(--accent-1, #3b82f6)';
  }
  if (selectedBadge) {
    selectedBadge.innerHTML = '<span class="badge bg-primary text-white"><i class="bi bi-check-lg"></i></span>';
  }
}

function selectShiftMode(mode) {
  document.querySelectorAll('.mode-box-card').forEach(box => box.classList.remove('active'));
  const radio = document.getElementById('mode_' + mode);
  if (radio) {
    radio.checked = true;
    radio.closest('.mode-box-card').classList.add('active');
  }
}

function editShift(shift) {
  const form = document.getElementById('formEditShift');
  form.action = `/admin/keuangan/setting-shift/${shift.id}/update`;

  document.getElementById('edit_shift_name').value = shift.shift_name;
  document.getElementById('edit_start_time').value = shift.start_time.substring(0, 5);
  document.getElementById('edit_end_time').value = shift.end_time.substring(0, 5);
  document.getElementById('edit_default_starting_cash').value = shift.default_starting_cash;
  document.getElementById('edit_is_active').checked = (shift.is_active == 1);

  const editModal = new bootstrap.Modal(document.getElementById('modalEditShift'));
  editModal.show();
}
</script>
@endpush
