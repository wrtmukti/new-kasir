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
              <i class="bi bi-shop-window me-2 text-warning"></i>
              <span class="nav-label-text fw-semibold">Buka Tutup & Kasir</span>
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

      {{-- PANEL 5: PENGATURAN JAM OPERASIONAL & BUKA TUTUP KASIR --}}
      <div id="settings-shift" class="settings-panel" style="display:none;">
        <div class="row g-4 align-items-start">
          
          <!-- SIDEBAR KIRI: STATUS LIVE KASIR & AKSES CEPAT PANDUAN (col-xl-4 col-lg-5) -->
          <div class="col-xl-4 col-lg-5">
            <!-- KARTU 1: STATUS KASIR HARI INI -->
            <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-subtle) !important; border-radius: 1rem;">
              <div class="card-header-flex py-3 px-3.5" style="border-bottom: 1px solid var(--border-subtle);">
                <div class="d-flex align-items-center gap-2">
                  <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:34px; height:34px; background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                    <i class="bi bi-activity fs-5"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-bold" style="font-size:0.9rem;">Status Kasir Hari Ini</h6>
                    <small class="text-muted-c" style="font-size:0.72rem;">Live monitoring sesi POS cabang</small>
                  </div>
                </div>
                @if($activeShift)
                  <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                    <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i> KASIR BUKA
                  </span>
                @else
                  <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.72rem;">
                    <i class="bi bi-dash-circle me-1"></i> KASIR TUTUP
                  </span>
                @endif
              </div>

              <div class="card-body p-3.5">
                <!-- Session Info Details -->
                <div class="mb-3 p-2.5 rounded-3" style="background: var(--bg-elevated, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle);">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted-c small" style="font-size:0.75rem;">Sesi Bertugas</span>
                    <span class="fw-semibold text-truncate" style="font-size:0.8rem; max-width:160px; color:var(--text-primary);">
                      {{ $activeShift ? $activeShift->shift_name : 'Belum Ada Sesi' }}
                    </span>
                  </div>
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted-c small" style="font-size:0.75rem;">Waktu Buka</span>
                    <span class="fw-bold font-monospace text-success" style="font-size:0.8rem;">
                      {{ $activeShift ? \Carbon\Carbon::parse($activeShift->opened_at)->format('H:i') . ' WIB' : '-' }}
                    </span>
                  </div>
                </div>

                <!-- Parameters Vertical List -->
                <div class="d-flex flex-column gap-2.5 mb-3.5">
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted-c small" style="font-size:0.78rem;">Mode Toko:</span>
                    <span id="modeSummaryBadge" class="badge {{ (($shiftSetting->shift_mode ?? 'auto_master') === 'manual') ? 'bg-warning-subtle text-warning border border-warning-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                      <i class="bi {{ (($shiftSetting->shift_mode ?? 'auto_master') === 'manual') ? 'bi-sliders' : 'bi-shop' }} me-1"></i>
                      {{ (($shiftSetting->shift_mode ?? 'auto_master') === 'manual') ? 'Manual (Bebas)' : 'Otomatis (Jadwal)' }}
                    </span>
                  </div>

                  <div class="d-flex justify-content-between align-items-start">
                    <span class="text-muted-c small" style="font-size:0.78rem;">Jam Operasional:</span>
                    <div class="text-end">
                      <span class="fw-bold font-monospace" id="scheduleSummaryText" style="font-size: 0.82rem; color: var(--text-primary);">
                        @if(($shiftSetting->shift_mode ?? 'auto_master') === 'manual')
                          Fleksibel (Bebas Kapan Saja)
                        @else
                          {{ \Carbon\Carbon::parse($primaryShift->start_time ?? '08:00')->format('H:i') }} - {{ \Carbon\Carbon::parse($primaryShift->end_time ?? '22:00')->format('H:i') }} WIB
                        @endif
                      </span>
                      <small class="text-muted-c d-block" style="font-size:0.68rem;">Cut-off: {{ \Carbon\Carbon::parse($shiftSetting->daily_cutoff_time ?? '03:00')->format('H:i') }} WIB</small>
                    </div>
                  </div>

                  <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted-c small" style="font-size:0.78rem;">Modal Laci Kasir:</span>
                    <span class="fw-bold text-success font-monospace" id="modalSummaryText" style="font-size: 0.82rem;">
                      @if(($shiftSetting->shift_mode ?? 'auto_master') === 'manual')
                        Diinput Kasir Saat Buka
                      @else
                        Rp {{ number_format($primaryShift->default_starting_cash ?? 300000, 0, ',', '.') }}
                      @endif
                    </span>
                  </div>
                </div>

                <!-- Action Button 1: Shortcut ke POS -->
                <a href="{{ route('admin.keuangan.shift-operational.index') }}" class="btn btn-primary-grad w-100 py-2.5 rounded-3 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2 mb-2">
                  <i class="bi bi-cash-stack"></i>
                  <span>Ke Layar Buka / Tutup Kasir</span>
                </a>

                <!-- Action Button 2: Buka Modal SOP -->
                <button type="button" class="btn btn-outline-primary w-100 py-2 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-2" style="font-size:0.82rem;" data-bs-toggle="modal" data-bs-target="#modalPanduanKasir">
                  <i class="bi bi-journal-check"></i>
                  <span>Buka Panduan SOP Alur Kasir</span>
                </button>
              </div>
            </div>

            <!-- KARTU 2: ALUR KERJA RINGKAS KASIR (VERTICAL STACK) -->
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-subtle) !important; border-radius: 1rem;">
              <div class="card-header-flex py-2.5 px-3.5" style="border-bottom: 1px solid var(--border-subtle);">
                <span class="fw-bold small d-flex align-items-center gap-1.5" style="color:var(--text-primary); font-size:0.82rem;">
                  <i class="bi bi-diagram-3-fill text-primary"></i>
                  <span>Alur Buka Tutup F&amp;B</span>
                </span>
                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none text-primary fw-semibold" style="font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#modalPanduanKasir">
                  Detail <i class="bi bi-box-arrow-up-right ms-0.5" style="font-size:0.65rem;"></i>
                </button>
              </div>

              <div class="card-body p-3">
                <div class="d-flex flex-column gap-2.5">
                  <!-- Step 1 -->
                  <div class="d-flex align-items-start gap-2.5 p-2 rounded-2" style="background: var(--bg-elevated, rgba(255,255,255,0.02)); border: 1px solid var(--border-subtle);">
                    <div class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width:22px; height:22px; font-size:0.72rem;">1</div>
                    <div>
                      <div class="fw-bold" style="font-size: 0.8rem; color: var(--text-primary);">Buka Kasir</div>
                      <small class="text-muted-c" style="font-size: 0.72rem; line-height: 1.35; display:block;">Kasir hitung modal laci (misal Rp 300rb) dan tekan Buka Kasir.</small>
                    </div>
                  </div>

                  <!-- Step 2 -->
                  <div class="d-flex align-items-start gap-2.5 p-2 rounded-2" style="background: var(--bg-elevated, rgba(255,255,255,0.02)); border: 1px solid var(--border-subtle);">
                    <div class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width:22px; height:22px; font-size:0.72rem;">2</div>
                    <div>
                      <div class="fw-bold" style="font-size: 0.8rem; color: var(--text-primary);">Transaksi POS</div>
                      <small class="text-muted-c" style="font-size: 0.72rem; line-height: 1.35; display:block;">Pencatatan order. Pembayaran cash terkunci jika belum buka.</small>
                    </div>
                  </div>

                  <!-- Step 3 -->
                  <div class="d-flex align-items-start gap-2.5 p-2 rounded-2" style="background: var(--bg-elevated, rgba(255,255,255,0.02)); border: 1px solid var(--border-subtle);">
                    <div class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width:22px; height:22px; font-size:0.72rem;">3</div>
                    <div>
                      <div class="fw-bold" style="font-size: 0.8rem; color: var(--text-primary);">Tutup Kasir</div>
                      <small class="text-muted-c" style="font-size: 0.72rem; line-height: 1.35; display:block;">Di akhir hari, kasir hitung fisik uang di laci dan input nominalnya.</small>
                    </div>
                  </div>

                  <!-- Step 4 -->
                  <div class="d-flex align-items-start gap-2.5 p-2 rounded-2" style="background: var(--bg-elevated, rgba(255,255,255,0.02)); border: 1px solid var(--border-subtle);">
                    <div class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width:22px; height:22px; font-size:0.72rem;">4</div>
                    <div>
                      <div class="fw-bold" style="font-size: 0.8rem; color: var(--text-primary);">Audit Selisih</div>
                      <small class="text-muted-c" style="font-size: 0.72rem; line-height: 1.35; display:block;">Sistem hitung selisih kas fisik vs transaksi di struk Z-Report.</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- KOLOM KANAN: FORM PENGATURAN OPERASIONAL (col-xl-8 col-lg-7) -->
          <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-subtle) !important; border-radius: 1rem;">
              <div class="card-header-flex py-3 px-4" style="border-bottom: 1px solid var(--border-subtle);">
                <div class="d-flex align-items-center gap-2">
                  <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:38px; height:38px; background: rgba(99, 102, 241, 0.15); color: var(--accent-1, #6366f1);">
                    <i class="bi bi-shop-window fs-5"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-bold">Konfigurasi Jam Operasional &amp; Mode Kasir</h6>
                    <small class="text-muted-c" style="font-size:0.75rem;">Atur metode kerja kasir (Manual vs Otomatis), modal awal, dan cut-off harian</small>
                  </div>
                </div>
                <span class="chip-tag px-3 py-1 rounded-pill" style="background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25); font-weight:600; font-size:0.75rem;">
                  <i class="bi bi-shield-check me-1"></i>Kasir &amp; Laci POS
                </span>
              </div>

              <div class="card-body p-4">
                <form action="{{ route('admin.keuangan.setting-shift.update-cutoff') }}" method="POST" id="formCutoff">
                  @csrf
                  
                  <!-- SECTION 1: PILIH MODE PENGOPERASIAN KASIR -->
                  <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <label class="form-label-modern mb-0 fw-bold text-uppercase" style="font-size:0.8rem; letter-spacing:0.5px;">1. Pilih Mode Pengoperasian Kasir</label>
                      <span class="text-muted-c" style="font-size:0.75rem;">Pilih alur kerja kasir yang paling cocok</span>
                    </div>

                    <div class="row g-3">
                      <!-- Mode 1: Manual (Buka & Tutup Bebas) -->
                      <div class="col-md-6">
                        <div class="mode-box-card @if(($shiftSetting->shift_mode ?? 'auto_master') === 'manual') active @endif" onclick="selectShiftMode('manual')">
                          <div class="d-flex align-items-center justify-content-between mb-2.5">
                            <div class="mode-icon-circle" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                              <i class="bi bi-sliders"></i>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                              <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-0.5" style="font-size:0.68rem; font-weight:600;">
                                Bebas &amp; Fleksibel
                              </span>
                              <input type="radio" name="shift_mode" value="manual" id="mode_manual" class="form-check-input" @if(($shiftSetting->shift_mode ?? 'auto_master') === 'manual') checked @endif style="cursor: pointer;">
                            </div>
                          </div>
                          <div class="fw-bold mb-1 fs-6" style="color: var(--text-primary);">Manual (Buka &amp; Tutup Bebas)</div>
                          <p class="text-muted-c mb-2.5" style="font-size:0.82rem; line-height: 1.4;">Kasir buka dan tutup kasir secara fleksibel kapan saja tanpa dibatasi jam kaku.</p>
                          <ul class="list-unstyled mb-0 text-muted-c" style="font-size:0.77rem; line-height: 1.65;">
                            <li><i class="bi bi-check-circle-fill text-warning me-1.5"></i>Buka &amp; tutup kasir bebas jam berapa saja</li>
                            <li><i class="bi bi-check-circle-fill text-warning me-1.5"></i>Kasir menginput modal awal saat buka kasir</li>
                            <li><i class="bi bi-check-circle-fill text-warning me-1.5"></i>Pas closing, kasir isi uang fisik aktual di laci</li>
                            <li><i class="bi bi-check-circle-fill text-warning me-1.5"></i>Owner memantau selisih fisik vs sistem</li>
                          </ul>
                        </div>
                      </div>

                      <!-- Mode 2: Otomatis (Jadwal Tetap & Standar Modal) -->
                      <div class="col-md-6">
                        <div class="mode-box-card @if(($shiftSetting->shift_mode ?? 'auto_master') !== 'manual') active @endif" onclick="selectShiftMode('auto_master')">
                          <div class="d-flex align-items-center justify-content-between mb-2.5">
                            <div class="mode-icon-circle" style="background: rgba(99, 102, 241, 0.15); color: var(--accent-1, #6366f1);">
                              <i class="bi bi-shop"></i>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                              <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-0.5" style="font-size:0.68rem; font-weight:600;">
                                Terjadwal (Rekomendasi)
                              </span>
                              <input type="radio" name="shift_mode" value="auto_master" id="mode_auto_master" class="form-check-input" @if(($shiftSetting->shift_mode ?? 'auto_master') !== 'manual') checked @endif style="cursor: pointer;">
                            </div>
                          </div>
                          <div class="fw-bold mb-1 fs-6" style="color: var(--text-primary);">Otomatis (Jadwal Tetap)</div>
                          <p class="text-muted-c mb-2.5" style="font-size:0.82rem; line-height: 1.4;">Jam buka-tutup diset tetap. Modal laci kasir diset paten (default Rp 300rb) dan otomatis sama setiap hari.</p>
                          <ul class="list-unstyled mb-0 text-muted-c" style="font-size:0.77rem; line-height: 1.65;">
                            <li><i class="bi bi-check-circle-fill text-primary me-1.5"></i>Jam operasional buka &amp; tutup toko terjadwal</li>
                            <li><i class="bi bi-check-circle-fill text-primary me-1.5"></i>Modal kas awal laci diset paten sekali saja</li>
                            <li><i class="bi bi-check-circle-fill text-primary me-1.5"></i>Periode transaksi dihitung otomatis by tanggal</li>
                            <li><i class="bi bi-check-circle-fill text-primary me-1.5"></i>Sistem hitung kebutuhan top-up modal kasir</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- SECTION 2A: PENGATURAN MODE OTOMATIS (JAM OPERASIONAL & MODAL AWAL) -->
                  <div id="autoScheduleBox" class="p-3.5 rounded-3 mb-4" style="background: var(--bg-elevated-2, rgba(255,255,255,0.02)); border: 1px solid var(--border-subtle); display: {{ (($shiftSetting->shift_mode ?? 'auto_master') === 'manual') ? 'none' : 'block' }};">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                      <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-primary fs-5"></i>
                        <div>
                          <h6 class="mb-0 fw-bold" style="font-size: 0.92rem;">Pengaturan Jam Operasional &amp; Modal Kasir</h6>
                          <small class="text-muted-c" style="font-size:0.74rem;">Tentukan jam buka-tutup toko dan modal laci yang selalu sama setiap hari</small>
                        </div>
                      </div>
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill" style="font-size:0.7rem;">Mode Otomatis Aktif</span>
                    </div>

                    <!-- Row 1: Jam Buka & Jam Tutup -->
                    <div class="row g-3 mb-3">
                      <div class="col-md-6">
                        <label for="open_time" class="form-label-modern mb-1 fw-semibold">Jam Buka Toko <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);"><i class="bi bi-door-open"></i></span>
                          <input type="time" name="open_time" id="open_time" class="form-control form-control-modern" value="{{ \Carbon\Carbon::parse($primaryShift->start_time ?? '08:00')->format('H:i') }}" list="list24h" onchange="normalizeTime24(this)">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="close_time" class="form-label-modern mb-1 fw-semibold">Jam Tutup Toko <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);"><i class="bi bi-door-closed"></i></span>
                          <input type="time" name="close_time" id="close_time" class="form-control form-control-modern" value="{{ \Carbon\Carbon::parse($primaryShift->end_time ?? '22:00')->format('H:i') }}" list="list24h" onchange="normalizeTime24(this)">
                        </div>
                      </div>
                    </div>

                    <!-- Preset Jam Operasional -->
                    <div class="mb-3.5">
                      <span class="text-muted-c small d-block mb-1.5" style="font-size:0.74rem;">Preset Jam Operasional Cepat:</span>
                      <div class="d-flex flex-wrap gap-1.5">
                        <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setOperationalPreset('00:00', '23:59')">
                          <i class="bi bi-lightning-charge-fill text-warning me-1"></i>24 Jam (00:00 - 23:59)
                        </button>
                        <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setOperationalPreset('08:00', '22:00')">
                          Pagi (08:00 - 22:00)
                        </button>
                        <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setOperationalPreset('10:00', '23:00')">
                          Siang (10:00 - 23:00)
                        </button>
                        <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setOperationalPreset('16:30', '23:00')">
                          Sore (16:30 - 23:00)
                        </button>
                      </div>
                    </div>

                    <hr style="border-color: var(--border-subtle); opacity: 0.6;" class="my-3">

                    <!-- Row 2: Default Modal Awal Kasir -->
                    <div class="row g-3 align-items-center">
                      <div class="col-md-6">
                        <label for="default_starting_cash" class="form-label-modern mb-1 fw-semibold">
                          Default Modal Kas Awal (Rp) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                          <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary); font-weight:600;">Rp</span>
                          <input type="number" name="default_starting_cash" id="default_starting_cash" class="form-control form-control-modern font-monospace fw-semibold" value="{{ (int) ($primaryShift->default_starting_cash ?? 300000) }}" step="any" min="0">
                        </div>
                        <small class="text-muted-c mt-1 d-block" style="font-size:0.75rem;">Uang modal kembalian kasir di awal hari</small>
                      </div>

                      <div class="col-md-6">
                        <span class="text-muted-c small d-block mb-1.5" style="font-size:0.74rem;">Preset Nominal Modal Kas:</span>
                        <div class="d-flex flex-wrap gap-1.5">
                          <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setStartingCashPreset(100000)">Rp 100.000</button>
                          <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setStartingCashPreset(200000)">Rp 200.000</button>
                          <button type="button" class="btn btn-sm btn-subtle-preset fw-bold text-primary" onclick="setStartingCashPreset(300000)">Rp 300.000 (Rekomendasi)</button>
                          <button type="button" class="btn btn-sm btn-subtle-preset" onclick="setStartingCashPreset(500000)">Rp 500.000</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- SECTION 2B: PENJELASAN KHUSUS MODE MANUAL -->
                  <div id="manualScheduleBox" class="p-3.5 rounded-3 mb-4" style="background: var(--bg-elevated-2, rgba(255,255,255,0.02)); border: 1px solid var(--border-subtle); display: {{ (($shiftSetting->shift_mode ?? 'auto_master') === 'manual') ? 'block' : 'none' }};">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-sliders text-warning fs-5"></i>
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
                    <div class="col-md-5">
                      <label for="daily_cutoff_time" class="form-label-modern mb-1 fw-semibold">
                        Jam Cut-Off Operasional Harian <span class="text-danger">*</span>
                      </label>
                      <div class="input-group">
                        <span class="input-group-text" style="background: var(--bg-elevated-2); border-color: var(--border-subtle); color: var(--text-secondary);"><i class="bi bi-clock"></i></span>
                        <input type="time" name="daily_cutoff_time" id="daily_cutoff_time" class="form-control form-control-modern" value="{{ \Carbon\Carbon::parse($shiftSetting->daily_cutoff_time ?? '03:00')->format('H:i') }}" required>
                      </div>
                      <div class="text-muted-c mt-1" style="font-size: 0.76rem;">
                        Transaksi setelah jam ini dianggap sebagai <strong>Tanggal Bisnis Baru</strong> (Rekomendasi resto: <code>03:00</code> Pagi).
                      </div>
                    </div>

                    <div class="col-md-7">
                      <div class="form-check form-switch pt-1 mb-2">
                        <input class="form-check-input" type="checkbox" name="auto_lock_unclosed" id="auto_lock_unclosed" value="1" @if($shiftSetting->auto_lock_unclosed ?? 1) checked @endif style="width: 2.4em; height: 1.2em; cursor: pointer;">
                        <label class="form-check-label fw-semibold ms-2" for="auto_lock_unclosed">
                          Auto-Lock Kasir Kemarin (Strict Protection)
                        </label>
                      </div>
                      <div class="text-muted-c" style="font-size: 0.76rem;">
                        Kunci layar POS kasir jika sesi hari kemarin belum di-close oleh kasir sebelumnya.
                      </div>
                    </div>

                    <div class="col-12 text-end pt-2">
                      <button type="submit" class="btn btn-primary-grad px-4 py-2.5 rounded-3 btn-loading" id="btnSaveCutoff">
                        <i class="bi bi-check-circle-fill me-1"></i> Simpan Pengaturan Buka Tutup
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

<!-- MODAL PANDUAN LENGKAP ALUR KERJA KASIR F&B (LEBAR, BESAR & JELAS) -->
<div class="modal fade" id="modalPanduanKasir" tabindex="-1" aria-labelledby="modalPanduanKasirLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content" style="background: var(--bg-surface, #1e293b); border: 1px solid var(--border-subtle); border-radius: 1.25rem; color: var(--text-primary); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);">
      <div class="modal-header py-3 px-4" style="border-bottom: 1px solid var(--border-subtle);">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(59, 130, 246, 0.15); color: #3b82f6; font-size: 1.4rem;">
            <i class="bi bi-journal-check"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold mb-0" id="modalPanduanKasirLabel">Panduan Alur Buka Tutup Kasir Toko F&amp;B</h5>
            <small class="text-muted-c">Standar Operasional Prosedur (SOP) Pengelolaan Uang Kas &amp; Laci Kasir POS</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <!-- Banner Penjelasan Singkat -->
        <div class="p-3 rounded-3 mb-4 d-flex align-items-center gap-3" style="background: rgba(59, 130, 246, 0.08); border-left: 4px solid #3b82f6;">
          <i class="bi bi-info-circle-fill text-primary fs-4 flex-shrink-0"></i>
          <div style="font-size: 0.85rem; line-height: 1.5; color: var(--text-secondary);">
            Alur buka-tutup kasir dirancang agar <strong>uang fisik kas riil di laci</strong> selalu sinkron dengan <strong>catatan transaksi sistem</strong>, mencegah kebocoran uang kasir, serta memudahkan Owner memantau selisih harian.
          </div>
        </div>

        <!-- 4 Langkah SOP Lengkap -->
        <div class="d-flex flex-column gap-3.5">
          <!-- Step 1 -->
          <div class="p-3.5 rounded-3" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-2.5">
                <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">1</span>
                <h6 class="mb-0 fw-bold fs-6" style="color: var(--text-primary);">Langkah 1: Buka Kasir (Awal Jam Kerja)</h6>
              </div>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">Pagi / Awal Shift</span>
            </div>
            <p class="text-muted-c mb-2" style="font-size: 0.85rem; line-height: 1.5;">
              Sebelum kasir melayani transaksi pertama, kasir mengambil uang modal laci (uang kembalian) dari brankas, menghitung jumlah fisiknya di depan mesin kasir, lalu menekan tombol <strong>"Buka Kasir"</strong> di menu POS.
            </p>
            <div class="p-2.5 rounded-2 d-flex align-items-center gap-2" style="background: rgba(16, 185, 129, 0.08); border: 1px dashed rgba(16, 185, 129, 0.3);">
              <i class="bi bi-check-circle-fill text-success fs-6"></i>
              <small class="text-success fw-semibold" style="font-size: 0.78rem;">
                Jika menggunakan <strong>Mode Otomatis</strong>, modal kas awal (misal Rp 300.000) terisi otomatis dari template master shift toko.
              </small>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="p-3.5 rounded-3" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-2.5">
                <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">2</span>
                <h6 class="mb-0 fw-bold fs-6" style="color: var(--text-primary);">Langkah 2: Transaksi Penjualan POS</h6>
              </div>
              <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">Sepanjang Hari</span>
            </div>
            <p class="text-muted-c mb-2" style="font-size: 0.85rem; line-height: 1.5;">
              Kasir melayani pesanan tamu, mencatat pembayaran tunai (cash) maupun non-tunai (QRIS, Kartu Debit/Kredit, Transfer). Seluruh arus kas masuk otomatis dihitung ke dalam ekspektasi kas laci.
            </p>
            <div class="p-2.5 rounded-2 d-flex align-items-center gap-2" style="background: rgba(59, 130, 246, 0.08); border: 1px dashed rgba(59, 130, 246, 0.3);">
              <i class="bi bi-shield-lock-fill text-primary fs-6"></i>
              <small class="text-primary fw-semibold" style="font-size: 0.78rem;">
                <strong>Sistem Hard-Lock Kas:</strong> Pembayaran tunai otomatis ditolak sistem jika kasir belum melakukan sesi Buka Kasir.
              </small>
            </div>
          </div>

          <!-- Step 3 -->
          <div class="p-3.5 rounded-3" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-2.5">
                <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">3</span>
                <h6 class="mb-0 fw-bold fs-6" style="color: var(--text-primary);">Langkah 3: Tutup Kasir &amp; Hitung Fisik Laci (Closing)</h6>
              </div>
              <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">Malam / Selesai Operasional</span>
            </div>
            <p class="text-muted-c mb-2" style="font-size: 0.85rem; line-height: 1.5;">
              Saat toko selesai beroperasi, kasir menghitung seluruh uang fisik riil di laci kasir (pecahan uang kertas &amp; koin). Kasir memasukkan total uang fisik riil tersebut pada form <strong>Tutup Kasir</strong>.
            </p>
            <div class="p-2.5 rounded-2 d-flex align-items-center gap-2" style="background: rgba(245, 158, 11, 0.08); border: 1px dashed rgba(245, 158, 11, 0.3);">
              <i class="bi bi-eye-slash-fill text-warning fs-6"></i>
              <small class="text-warning fw-semibold" style="font-size: 0.78rem;">
                <strong>Blind Closing:</strong> Kasir menghitung fisik secara jujur dan objektif tanpa dipandu angka ekspektasi sistem terlebih dahulu.
              </small>
            </div>
          </div>

          <!-- Step 4 -->
          <div class="p-3.5 rounded-3" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-2.5">
                <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">4</span>
                <h6 class="mb-0 fw-bold fs-6" style="color: var(--text-primary);">Langkah 4: Rekonsiliasi &amp; Audit Selisih (Z-Report)</h6>
              </div>
              <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">Audit Owner / Spv</span>
            </div>
            <p class="text-muted-c mb-2" style="font-size: 0.85rem; line-height: 1.5;">
              Sistem mencetak struk Z-Report final yang merinci total penjualan, pembayaran non-tunai, uang fisik kasir, dan <strong>Selisih Kas (Selisih Lebih / Kurang)</strong>. Owner dapat langsung mengecek akurasi kasir dari portal laporan.
            </p>
            <div class="p-2.5 rounded-2 d-flex align-items-center gap-2" style="background: rgba(139, 92, 246, 0.08); border: 1px dashed rgba(139, 92, 246, 0.3);">
              <i class="bi bi-bank2 text-purple fs-6"></i>
              <small class="text-purple fw-semibold" style="font-size: 0.78rem;">
                Jika kas kurang dari modal standar (misal tersisa Rp 200rb), Owner mentransfer Rp 100rb ke kasir agar modal besok tetap mulai dari Rp 300rb.
              </small>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer py-3 px-4 d-flex justify-content-between align-items-center" style="border-top: 1px solid var(--border-subtle);">
        <button type="button" class="btn btn-outline-secondary rounded-3 px-3 py-2" data-bs-dismiss="modal">Tutup</button>
        <a href="{{ route('admin.keuangan.shift-operational.index') }}" class="btn btn-primary-grad rounded-3 px-4 py-2 fw-semibold d-flex align-items-center gap-2">
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
  <option value="12:00">12:00 (Siang)</option>
  <option value="15:00">15:00</option>
  <option value="16:00">16:00 (Sore)</option>
  <option value="17:00">17:00</option>
  <option value="20:00">20:00 (Malam)</option>
  <option value="22:00">22:00</option>
  <option value="23:00">23:00</option>
  <option value="23:59">23:59 (Akhir Hari)</option>
</datalist>
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

/* 24-Hour Custom Dropdown */
.custom-24h-dropdown .dropdown-item {
  color: var(--text-primary);
  font-size: 0.82rem;
  transition: all 0.15s ease;
  cursor: pointer;
}
.custom-24h-dropdown .dropdown-item:hover {
  background: var(--accent-1, #3b82f6) !important;
  color: #fff !important;
}
.custom-24h-dropdown .dropdown-item:hover small {
  color: rgba(255, 255, 255, 0.85) !important;
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

  // Handle Form Cut-Off & Shift Mode via AJAX
  const formCutoff = document.getElementById('formCutoff');
  const btnSaveCutoff = document.getElementById('btnSaveCutoff');
  if (formCutoff && btnSaveCutoff) {
    formCutoff.addEventListener('submit', function(e) {
      e.preventDefault();
      btnSaveCutoff.disabled = true;
      btnSaveCutoff.classList.add('is-loading');
      btnSaveCutoff.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

      const formData = new FormData(formCutoff);
      setTimeout(() => {
        fetch(formCutoff.action, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          },
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          btnSaveCutoff.disabled = false;
          btnSaveCutoff.classList.remove('is-loading');
          btnSaveCutoff.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Pengaturan';
          if (data.status === 'success' || data.success) {
            NexoraToast(data.message || 'Pengaturan jam cut-off operasional & mode shift berhasil diperbarui.', 'success');
          } else {
            NexoraToast(data.message || 'Gagal menyimpan pengaturan.', 'danger');
          }
        })
        .catch(() => {
          btnSaveCutoff.disabled = false;
          btnSaveCutoff.classList.remove('is-loading');
          btnSaveCutoff.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Pengaturan';
          NexoraToast('Terjadi kesalahan saat menyimpan pengaturan.', 'danger');
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
</script>
@endpush
