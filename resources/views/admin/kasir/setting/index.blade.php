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
              <span class="nav-label-text fw-semibold">Jam Buka Kasir</span>
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
        <!-- KARTU FULL WIDTH FORM OPERASIONAL -->
        <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-subtle) !important; border-radius: 1rem;">
          <div class="card-header-flex py-3 px-4" style="border-bottom: 1px solid var(--border-subtle);">
            <div class="d-flex align-items-center gap-2">
              <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:38px; height:38px; background: rgba(99, 102, 241, 0.15); color: var(--accent-1, #6366f1);">
                <i class="bi bi-shop-window fs-5"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-bold">Pengaturan Jam Buka Kasir &amp; Modal Laci</h6>
                <small class="text-muted-c" style="font-size:0.75rem;">Atur jam buka kasir (Toko 24 Jam atau Jam Tertentu), modal uang kembalian di laci, dan cut-off harian</small>
              </div>
            </div>

            <div class="d-flex align-items-center gap-2.5">
              @if($activeShift)
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.72rem; cursor: pointer;" data-bs-toggle="offcanvas" data-bs-target="#drawerStatusKasir" title="Klik untuk lihat Status Kasir">
                  <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i> KASIR BUKA
                </span>
              @else
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.72rem; cursor: pointer;" data-bs-toggle="offcanvas" data-bs-target="#drawerStatusKasir" title="Klik untuk lihat Status Kasir">
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
                    <div class="mode-box-card @if(($shiftSetting->shift_mode ?? 'auto_master') !== 'manual') active @endif" onclick="selectShiftMode('auto_master')">
                      <div class="d-flex align-items-center justify-content-between mb-2.5">
                        <div class="mode-icon-circle" style="background: rgba(99, 102, 241, 0.15); color: var(--accent-1, #6366f1);">
                          <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                          <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-0.5" style="font-size:0.68rem; font-weight:600;">
                            24 Jam / Jam Tertentu
                          </span>
                          <input type="radio" name="shift_mode" value="auto_master" id="mode_auto_master" class="form-check-input" @if(($shiftSetting->shift_mode ?? 'auto_master') !== 'manual') checked @endif style="cursor: pointer;">
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
              <div id="autoScheduleBox" class="p-3.5 rounded-3 mb-4" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle); display: {{ (($shiftSetting->shift_mode ?? 'auto_master') !== 'manual') ? 'block' : 'none' }};">
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
              <div id="manualScheduleBox" class="p-3.5 rounded-3 mb-4" style="background: var(--bg-elevated-2, rgba(255,255,255,0.03)); border: 1px solid var(--border-subtle); display: {{ (($shiftSetting->shift_mode ?? 'auto_master') === 'manual') ? 'block' : 'none' }};">
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
                    <input type="time" name="daily_cutoff_time" id="daily_cutoff_time" class="form-control form-control-modern" value="{{ \Carbon\Carbon::parse($shiftSetting->daily_cutoff_time ?? '03:00')->format('H:i') }}" required>
                  </div>
                  <div class="text-muted-c mt-1" style="font-size: 0.76rem;">
                    Untuk toko 24 jam maupun resto yang buka sampai larut/subuh, transaksi setelah jam ini otomatis dihitung sebagai <strong>Tanggal Bisnis Baru</strong> (Rekomendasi resto: <code>03:00</code> Subuh).
                  </div>
                </div>

                <div class="col-md-7">
                  <div class="form-check form-switch pt-1 mb-2">
                    <input class="form-check-input" type="checkbox" name="auto_lock_unclosed" id="auto_lock_unclosed" value="1" @if($shiftSetting->auto_lock_unclosed ?? 1) checked @endif style="width: 2.4em; height: 1.2em; cursor: pointer;">
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
      </div>

<!-- FLOATING DRAWER TAB ON LEFT EDGE (HOVER / CLICK TO SLIDE DRAWER) -->
<div id="drawerTriggerTab" class="kasir-drawer-tab d-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#drawerStatusKasir" title="Arahkan kursor atau klik untuk melihat Status Kasir" style="display:none;">
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
            <span id="modeSummaryBadge" class="badge {{ (($shiftSetting->shift_mode ?? 'auto_master') === 'manual') ? 'bg-warning-subtle text-warning border border-warning-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;">
              <i class="bi {{ (($shiftSetting->shift_mode ?? 'auto_master') === 'manual') ? 'bi-sliders' : 'bi-clock-history' }} me-1"></i>
              {{ (($shiftSetting->shift_mode ?? 'auto_master') === 'manual') ? 'Buka Bebas (Manual)' : 'Terjadwal / 24 Jam' }}
            </span>
          </div>

          <div class="d-flex justify-content-between align-items-start">
            <span class="text-muted-c small" style="font-size:0.78rem;">Jam Buka:</span>
            <div class="text-end">
              <span class="fw-bold font-monospace" id="scheduleSummaryText" style="font-size: 0.8rem; color: var(--text-primary);">
                @if(($shiftSetting->shift_mode ?? 'auto_master') === 'manual')
                  Fleksibel (Buka Bebas Kapan Saja)
                @elseif(($primaryShift->start_time ?? '') == '00:00:00' && ($primaryShift->end_time ?? '') >= '23:59:00')
                  24 Jam Nonstop (00:00 - 23:59)
                @else
                  {{ \Carbon\Carbon::parse($primaryShift->start_time ?? '08:00')->format('H:i') }} - {{ \Carbon\Carbon::parse($primaryShift->end_time ?? '22:00')->format('H:i') }} WIB
                @endif
              </span>
              <small class="text-muted-c d-block" style="font-size:0.68rem;">Cut-off: {{ \Carbon\Carbon::parse($shiftSetting->daily_cutoff_time ?? '03:00')->format('H:i') }} WIB</small>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted-c small" style="font-size:0.78rem;">Target Modal Laci:</span>
            <span class="fw-bold text-success font-monospace" id="modalSummaryText" style="font-size: 0.8rem;">
              @if(($shiftSetting->shift_mode ?? 'auto_master') === 'manual')
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
                <i class="bi bi-moon-stars me-1"></i> Selesai Jualan / Cut-Off
              </span>
            </div>
            <p class="text-muted-c mb-3" style="font-size: 0.88rem; line-height: 1.65;">
              Saat jam operasional selesai (atau saat cut-off harian untuk toko 24 jam), kasir mengeluarkan dan menghitung seluruh uang tunai yang tersisa di laci (kertas &amp; koin). Setelah dihitung, kasir membuka menu <strong>"Tutup Kasir"</strong> dan mengetik total uang fisiknya.
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
            NexoraToast(data.message || 'Pengaturan jam buka kasir & modal laci berhasil diperbarui.', 'success');
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
    scheduleSummaryText.textContent = openVal + ' - ' + closeVal + ' WIB';
  }
}

  // Drawer Status Kasir: Click trigger & Tab Integration
  const drawerTriggerTab = document.getElementById("drawerTriggerTab");
  const drawerStatusKasirEl = document.getElementById("drawerStatusKasir");

  // Tab Menu Switching integration: Muncul di Kanan saat menu "Buka Tutup & Kasir" di-klik
  document.querySelectorAll(".settings-menu .nav-link[data-settings-target]").forEach(link => {
    link.addEventListener("click", function() {
      const target = this.getAttribute("data-settings-target");
      if (target === "#settings-shift") {
        if (drawerTriggerTab) drawerTriggerTab.style.display = "flex";
        if (drawerStatusKasirEl) {
          bootstrap.Offcanvas.getOrCreateInstance(drawerStatusKasirEl).show();
        }
      } else {
        if (drawerTriggerTab) drawerTriggerTab.style.display = "none";
        if (drawerStatusKasirEl) {
          bootstrap.Offcanvas.getOrCreateInstance(drawerStatusKasirEl).hide();
        }
      }
    });
  });
</script>
@endpush
