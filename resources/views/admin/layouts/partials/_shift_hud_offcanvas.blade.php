{{-- ==============================================================================
     OFFCANVAS DRAWER UTILITY & SHIFT HUD (MILESTONE V2)
     Frontline POS Cash Drawer & Shift Utility — Zero Cart Disruption
     ============================================================================== --}}

<style>
  #cashierDrawerHud {
    width: 440px;
    max-width: 95vw;
    background: var(--bg-surface, #1e293b);
    color: var(--text-primary, #f8fafc);
    border-left: 1px solid var(--border-subtle, rgba(255,255,255,0.08)) !important;
    z-index: 1080;
  }
  [data-theme="light"] #cashierDrawerHud {
    background: #ffffff !important;
    color: #0f172a !important;
    border-left: 1px solid #e2e8f0 !important;
  }
  .hud-stat-card {
    background: var(--bg-elevated, #0f172a);
    border: 1px solid var(--border-subtle, rgba(255,255,255,0.06));
    border-radius: 12px;
    padding: 0.85rem;
    transition: transform 0.15s ease, border-color 0.15s ease;
  }
  [data-theme="light"] .hud-stat-card {
    background: #f8fafc;
    border-color: #e2e8f0;
  }
  .hud-stat-card:hover {
    border-color: rgba(99, 102, 241, 0.4);
    transform: translateY(-2px);
  }
  .hud-action-btn {
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.82rem;
    padding: 0.55rem 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    transition: all 0.2s ease;
  }
  .denomination-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.35rem 0;
    border-bottom: 1px dashed var(--border-subtle, rgba(255,255,255,0.08));
    font-size: 0.82rem;
  }
  [data-theme="light"] .denomination-row {
    border-bottom: 1px dashed #e2e8f0;
  }
  .denomination-input {
    width: 68px;
    text-align: center;
    padding: 0.25rem;
    font-weight: 600;
  }
</style>

<!-- OFFCANVAS CONTAINER -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cashierDrawerHud" aria-labelledby="cashierDrawerHudLabel">
  
  <!-- OFFCANVAS HEADER -->
  <div class="offcanvas-header border-bottom py-3 px-3 d-flex align-items-center justify-content-between" style="border-color: var(--border-subtle, rgba(255,255,255,0.08)) !important;">
    <div class="d-flex align-items-center gap-2">
      <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(99, 102, 241, 0.15); color: var(--brand-primary, #6366f1); font-size: 1.1rem;">
        <i class="bi bi-inboxes-fill"></i>
      </div>
      <div>
        <h6 class="offcanvas-title fw-bold mb-0" id="cashierDrawerHudLabel" style="font-size: 0.92rem;">
          Laci Kasir &amp; Shift HUD
        </h6>
        <span class="text-muted-c" style="font-size: 0.72rem;">Quick Cash Drawer Utility</span>
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-sm btn-icon" id="btnRefreshHud" title="Segarkan Data Laci" style="color: var(--text-secondary); width: 32px; height: 32px; border-radius: 8px;">
        <i class="bi bi-arrow-clockwise fs-6"></i>
      </button>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
  </div>

  <!-- OFFCANVAS BODY -->
  <div class="offcanvas-body p-3 scroll-thin d-flex flex-column justify-content-between">
    
    <div>
      <!-- 1. ACTIVE SHIFT BADGE / BANNER -->
      <div id="hudActiveShiftBanner" class="p-2.5 rounded-3 mb-3 d-flex align-items-center justify-content-between" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25);">
        <div class="d-flex align-items-center gap-2 min-w-0">
          <span class="spinner-grow spinner-grow-sm text-success flex-shrink-0" style="width: 8px; height: 8px;" role="status"></span>
          <div class="text-truncate">
            <div class="fw-bold text-success" id="hudShiftTitle" style="font-size: 0.82rem;">Kasir Aktif</div>
            <div class="text-muted-c text-truncate" style="font-size: 0.72rem;">
              <span id="hudCashierLabel">Kasir</span> • <span id="hudClockInLabel">Buka 08:00</span>
            </div>
          </div>
        </div>
        <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 fw-bold" id="hudDurationBadge" style="font-size: 0.68rem;">
          Aktif
        </span>
      </div>

      <!-- INACTIVE SHIFT BANNER (Hidden by default) -->
      <div id="hudInactiveShiftBanner" class="p-3 rounded-3 mb-3 text-center d-none" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3);">
        <i class="bi bi-exclamation-triangle-fill text-warning fs-4 d-block mb-1"></i>
        <div class="fw-bold text-warning" style="font-size: 0.85rem;">Sesi Kasir Belum Dibuka</div>
        <p class="text-muted-c mb-2" style="font-size: 0.75rem;">Lakukan buka kasir terlebih dahulu untuk mulai mencatat transaksi tunai dan mutasi laci.</p>
        <a href="{{ route('admin.keuangan.shift-operational.index') }}" class="btn btn-sm btn-warning fw-bold px-3 py-1.5 rounded-pill shadow-sm">
          <i class="bi bi-cash-stack me-1"></i>Buka Kasir
        </a>
      </div>

      <!-- 2. 4 LIVE METRIC STAT CARDS -->
      <div id="hudActiveMetricsSection">
        <div class="row g-2 mb-3">
          <!-- Card 1: Modal Awal -->
          <div class="col-6">
            <div class="hud-stat-card">
              <div class="text-muted-c fw-medium mb-1" style="font-size: 0.7rem;">Modal Awal Laci</div>
              <div class="fw-bold text-truncate" id="hudValStartingCash" style="font-size: 0.88rem; color: var(--text-primary);">
                Rp 0
              </div>
            </div>
          </div>

          <!-- Card 2: Penjualan Kasir Tunai -->
          <div class="col-6">
            <div class="hud-stat-card">
              <div class="text-muted-c fw-medium mb-1" style="font-size: 0.7rem;">Penjualan Tunai</div>
              <div class="fw-bold text-success text-truncate" id="hudValCashSales" style="font-size: 0.88rem;">
                +Rp 0
              </div>
            </div>
          </div>

          <!-- Card 3: Mutasi Laci (+In / -Out) -->
          <div class="col-6">
            <div class="hud-stat-card">
              <div class="text-muted-c fw-medium mb-1" style="font-size: 0.7rem;">Mutasi Laci (In/Out)</div>
              <div class="fw-bold text-truncate" style="font-size: 0.78rem;">
                <span class="text-info" id="hudValDrawerIn">+0</span> / 
                <span class="text-danger" id="hudValDrawerOut">-0</span>
              </div>
            </div>
          </div>

          <!-- Card 4: Estimasi Kas Laci Saat Ini -->
          <div class="col-6">
            <div class="hud-stat-card" style="background: rgba(99, 102, 241, 0.08); border-color: rgba(99, 102, 241, 0.25);">
              <div class="text-primary fw-bold mb-1" style="font-size: 0.7rem;">Estimasi Kas Laci</div>
              <div class="fw-bold text-primary text-truncate fs-6" id="hudValExpectedCash">
                Rp 0
              </div>
            </div>
          </div>
        </div>

        <!-- 3. ACTION BUTTONS (TOP-UP, PETTY CASH, X-REPORT) -->
        <div class="d-flex flex-column gap-2 mb-3">
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-success hud-action-btn flex-fill" id="btnToggleCashIn" data-bs-toggle="collapse" data-bs-target="#collapseCashIn" aria-expanded="false">
              <i class="bi bi-plus-circle-fill"></i>+ Top-Up Modal
            </button>
            <button type="button" class="btn btn-outline-danger hud-action-btn flex-fill" id="btnToggleCashOut" data-bs-toggle="collapse" data-bs-target="#collapseCashOut" aria-expanded="false">
              <i class="bi bi-dash-circle-fill"></i>- Petty Cash
            </button>
          </div>

          <!-- Quick Action: Cetak X-Report -->
          <button type="button" class="btn btn-outline-secondary hud-action-btn w-100" id="btnPrintXReportBtn">
            <i class="bi bi-printer-fill text-primary"></i>Cetak Interim X-Report (Thermal 80mm)
          </button>
        </div>

        <!-- COLLAPSIBLE FORM: KAS MASUK (TOP-UP) -->
        <div class="collapse mb-3" id="collapseCashIn">
          <div class="card card-body p-3 border rounded-3" style="background: var(--bg-elevated); border-color: rgba(16, 185, 129, 0.3) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="fw-bold text-success" style="font-size: 0.8rem;">
                <i class="bi bi-arrow-down-left-circle-fill me-1"></i>Suntik Modal / Top-Up Uang Pecahan
              </span>
              <button type="button" class="btn-close btn-close-white" style="font-size: 0.65rem;" data-bs-toggle="collapse" data-bs-target="#collapseCashIn"></button>
            </div>
            <form id="formHudCashIn">
              @csrf
              <div class="mb-2">
                <label class="text-muted-c" style="font-size: 0.72rem;">Nominal (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="amount" class="form-control form-control-sm input-skeleton" placeholder="Contoh: 200000" min="1000" required>
              </div>
              <div class="mb-2">
                <label class="text-muted-c" style="font-size: 0.72rem;">Kategori</label>
                <input type="text" name="category" class="form-control form-control-sm input-skeleton" value="Top-up Uang Pecahan" required>
              </div>
              <div class="mb-2.5">
                <label class="text-muted-c" style="font-size: 0.72rem;">Keterangan / Dari Siapa <span class="text-danger">*</span></label>
                <input type="text" name="reason" class="form-control form-control-sm input-skeleton" placeholder="Contoh: Tambah modal pecahan 2rb & 5rb dari brankas" required>
              </div>
              <button type="submit" class="btn btn-sm btn-success w-100 fw-bold btn-loading d-flex align-items-center justify-content-center gap-1.5 py-1.5">
                <i class="bi bi-check2-circle"></i>Simpan Kas Masuk Laci
              </button>
            </form>
          </div>
        </div>

        <!-- COLLAPSIBLE FORM: KAS KELUAR (PETTY CASH) -->
        <div class="collapse mb-3" id="collapseCashOut">
          <div class="card card-body p-3 border rounded-3" style="background: var(--bg-elevated); border-color: rgba(239, 68, 68, 0.3) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="fw-bold text-danger" style="font-size: 0.8rem;">
                <i class="bi bi-arrow-up-right-circle-fill me-1"></i>Catat Kas Kecil (Petty Cash Keluar)
              </span>
              <button type="button" class="btn-close btn-close-white" style="font-size: 0.65rem;" data-bs-toggle="collapse" data-bs-target="#collapseCashOut"></button>
            </div>
            <form id="formHudCashOut">
              @csrf
              <div class="mb-2">
                <label class="text-muted-c" style="font-size: 0.72rem;">Nominal Pengeluaran (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="amount" class="form-control form-control-sm input-skeleton" placeholder="Contoh: 25000" min="1000" required>
              </div>
              <div class="mb-2">
                <label class="text-muted-c" style="font-size: 0.72rem;">Kategori Beban</label>
                <input type="text" name="category" class="form-control form-control-sm input-skeleton" value="Petty Cash Operasional" required>
              </div>
              <div class="mb-2.5">
                <label class="text-muted-c" style="font-size: 0.72rem;">Keperluan Pengeluaran <span class="text-danger">*</span></label>
                <input type="text" name="reason" class="form-control form-control-sm input-skeleton" placeholder="Contoh: Beli es batu kristal 2 karung" required>
              </div>
              <button type="submit" class="btn btn-sm btn-danger w-100 fw-bold btn-loading d-flex align-items-center justify-content-center gap-1.5 py-1.5">
                <i class="bi bi-check2-circle"></i>Simpan Kas Keluar Laci
              </button>
            </form>
          </div>
        </div>

        <!-- 4. RECENT MUTATIONS LIST -->
        <div class="mb-3">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="fw-bold" style="font-size: 0.78rem; color: var(--text-primary);">
              <i class="bi bi-clock-history me-1 text-primary"></i>5 Mutasi Laci Terakhir
            </span>
            <span class="text-muted-c" style="font-size: 0.7rem;">Shift Ini</span>
          </div>
          <div id="hudRecentLogsContainer" class="d-flex flex-column gap-1.5">
            <!-- Rendered by JavaScript -->
            <div class="text-center py-3 text-muted-c" style="font-size: 0.75rem;">
              Memuat riwayat mutasi...
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 5. FOOTER: BLIND DROP CLOSING BUTTON -->
    <div id="hudFooterSection" class="pt-2 border-top" style="border-color: var(--border-subtle, rgba(255,255,255,0.08)) !important;">
      <button type="button" class="btn btn-danger w-100 fw-bold d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 shadow-sm" id="btnOpenBlindClosingModal">
        <i class="bi bi-lock-fill"></i>Tutup Kasir (Hitung Setoran Laci)
      </button>
      <div class="text-center mt-1.5">
        <small class="text-muted-c" style="font-size: 0.68rem;">
          <i class="bi bi-keyboard me-1"></i>Shortcut: Tekan <kbd class="kbd-hint">F4</kbd> atau <kbd class="kbd-hint">Shift+D</kbd>
        </small>
      </div>
    </div>

  </div>
</div>

<!-- ==============================================================================
     MODAL: ENTERPRISE BLIND DROP CLOSING & DENOMINATION CALCULATOR
     ============================================================================== -->
<div class="modal fade" id="blindClosingModal" tabindex="-1" aria-labelledby="blindClosingModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content rounded-4 border-0 shadow-lg" style="background: var(--bg-surface, #1e293b); color: var(--text-primary); border: 1px solid var(--border-subtle) !important;">
      
      <div class="modal-header border-bottom py-2.5 px-3" style="border-color: var(--border-subtle) !important;">
        <div class="d-flex align-items-center gap-2">
          <div class="rounded-3 d-flex align-items-center justify-content-center bg-danger-subtle text-danger" style="width: 32px; height: 32px;">
            <i class="bi bi-shield-lock-fill"></i>
          </div>
          <div>
            <h6 class="modal-title fw-bold mb-0" id="blindClosingModalLabel" style="font-size: 0.9rem;">
              Tutup Kasir (Hitung Fisik Laci)
            </h6>
            <small class="text-muted-c" style="font-size: 0.72rem;">Hitung fisik uang tunai laci kasir sebelum melihat rekonsiliasi sistem</small>
          </div>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="modal" aria-label="Batal"></button>
      </div>

      <form id="formBlindClosing" action="{{ route('admin.keuangan.shift-operational.close') }}" method="POST">
        @csrf
        <div class="modal-body p-3 scroll-thin" style="max-height: 75vh; overflow-y: auto;">
          
          <!-- Notice Box -->
          <div class="p-2.5 rounded-3 mb-3 d-flex align-items-start gap-2" style="background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.25);">
            <i class="bi bi-info-circle-fill text-primary flex-shrink-0 mt-0.5"></i>
            <div style="font-size: 0.75rem; line-height: 1.35;" class="text-muted-c">
              <strong class="text-primary">Protokol Keamanan Kasir:</strong> Masukkan jumlah lembaran atau koin yang ada di laci. Ekspektasi sistem disembunyikan sampai fisik uang disubmit untuk menjaga integritas pembukuan.
            </div>
          </div>

          <!-- Denomination Breakdown Calculator -->
          <div class="card card-body p-2.5 rounded-3 mb-3 border" style="background: var(--bg-elevated); border-color: var(--border-subtle) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="fw-bold" style="font-size: 0.78rem; color: var(--text-primary);">
                <i class="bi bi-cash-stack me-1 text-success"></i>Kalkulator Denominasi Pecahan Fisik
              </span>
              <button type="button" class="btn btn-sm btn-link text-decoration-none text-muted-c p-0" id="btnResetDenomination" style="font-size: 0.7rem;">
                <i class="bi bi-arrow-counterclockwise me-0.5"></i>Reset Hitungan
              </button>
            </div>

            <!-- Rows of denominations -->
            <div class="denomination-row">
              <span class="fw-semibold">Rp 100.000</span>
              <div class="d-flex align-items-center gap-1.5">
                <span class="text-muted-c">×</span>
                <input type="number" min="0" class="form-control form-control-sm denomination-input" data-nominal="100000" placeholder="0">
                <span class="text-muted-c">=</span>
                <span class="denom-subtotal text-end fw-semibold" style="width: 85px;">Rp 0</span>
              </div>
            </div>

            <div class="denomination-row">
              <span class="fw-semibold">Rp 50.000</span>
              <div class="d-flex align-items-center gap-1.5">
                <span class="text-muted-c">×</span>
                <input type="number" min="0" class="form-control form-control-sm denomination-input" data-nominal="50000" placeholder="0">
                <span class="text-muted-c">=</span>
                <span class="denom-subtotal text-end fw-semibold" style="width: 85px;">Rp 0</span>
              </div>
            </div>

            <div class="denomination-row">
              <span class="fw-semibold">Rp 20.000</span>
              <div class="d-flex align-items-center gap-1.5">
                <span class="text-muted-c">×</span>
                <input type="number" min="0" class="form-control form-control-sm denomination-input" data-nominal="20000" placeholder="0">
                <span class="text-muted-c">=</span>
                <span class="denom-subtotal text-end fw-semibold" style="width: 85px;">Rp 0</span>
              </div>
            </div>

            <div class="denomination-row">
              <span class="fw-semibold">Rp 10.000</span>
              <div class="d-flex align-items-center gap-1.5">
                <span class="text-muted-c">×</span>
                <input type="number" min="0" class="form-control form-control-sm denomination-input" data-nominal="10000" placeholder="0">
                <span class="text-muted-c">=</span>
                <span class="denom-subtotal text-end fw-semibold" style="width: 85px;">Rp 0</span>
              </div>
            </div>

            <div class="denomination-row">
              <span class="fw-semibold">Rp 5.000</span>
              <div class="d-flex align-items-center gap-1.5">
                <span class="text-muted-c">×</span>
                <input type="number" min="0" class="form-control form-control-sm denomination-input" data-nominal="5000" placeholder="0">
                <span class="text-muted-c">=</span>
                <span class="denom-subtotal text-end fw-semibold" style="width: 85px;">Rp 0</span>
              </div>
            </div>

            <div class="denomination-row">
              <span class="fw-semibold">Rp 2.000</span>
              <div class="d-flex align-items-center gap-1.5">
                <span class="text-muted-c">×</span>
                <input type="number" min="0" class="form-control form-control-sm denomination-input" data-nominal="2000" placeholder="0">
                <span class="text-muted-c">=</span>
                <span class="denom-subtotal text-end fw-semibold" style="width: 85px;">Rp 0</span>
              </div>
            </div>

            <div class="denomination-row">
              <span class="fw-semibold">Rp 1.000 / Koin</span>
              <div class="d-flex align-items-center gap-1.5">
                <span class="text-muted-c">×</span>
                <input type="number" min="0" class="form-control form-control-sm denomination-input" data-nominal="1000" placeholder="0">
                <span class="text-muted-c">=</span>
                <span class="denom-subtotal text-end fw-semibold" style="width: 85px;">Rp 0</span>
              </div>
            </div>

            <!-- Manual other coin/cash input -->
            <div class="denomination-row" style="border-bottom: none;">
              <span class="fw-semibold">Koin Lainnya (Rp)</span>
              <div class="d-flex align-items-center gap-1.5">
                <span class="text-muted-c">+</span>
                <input type="number" min="0" class="form-control form-control-sm" id="inputOtherCoins" style="width: 100px; text-align: right;" placeholder="0">
              </div>
            </div>

          </div>

          <!-- Total Actual Cash Counted (Bound with form) -->
          <div class="p-3 rounded-3 mb-3 border text-center" style="background: rgba(16, 185, 129, 0.08); border-color: rgba(16, 185, 129, 0.3) !important;">
            <div class="text-muted-c fw-semibold" style="font-size: 0.72rem;">TOTAL FISIK UANG DI LACI (ACTUAL CASH)</div>
            <div class="fw-bold text-success fs-4" id="displayTotalActualCash">Rp 0</div>
            <input type="hidden" name="actual_cash_counted" id="inputActualCashCounted" value="0" required>
          </div>

          <!-- Retained Cash Float & Deposit to Safe -->
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="text-muted-c fw-medium mb-1" style="font-size: 0.72rem;">Modal Ditinggal di Laci (Rp)</label>
              <input type="number" name="retained_cash_float" id="inputRetainedCashFloat" class="form-control form-control-sm" value="200000" min="0">
              <small class="text-muted-c" style="font-size: 0.65rem;">Untuk kasir shift selanjutnya</small>
            </div>
            <div class="col-6">
              <label class="text-muted-c fw-medium mb-1" style="font-size: 0.72rem;">Uang Masuk Brankas (Rp)</label>
              <div class="form-control form-control-sm bg-light-subtle fw-bold text-primary" id="displayDepositToSafe">
                Rp 0
              </div>
              <small class="text-muted-c" style="font-size: 0.65rem;">Total Fisik − Modal Ditinggal</small>
            </div>
          </div>

          <!-- Notes -->
          <div class="mb-1">
            <label class="text-muted-c fw-medium mb-1" style="font-size: 0.72rem;">Catatan Kasir (Opsional)</label>
            <textarea name="cashier_note" class="form-control form-control-sm" rows="2" placeholder="Catatan khusus shift hari ini jika ada selisih atau kendala..."></textarea>
          </div>

        </div>

        <div class="modal-footer border-top py-2.5 px-3 d-flex align-items-center justify-content-between" style="border-color: var(--border-subtle) !important;">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-sm btn-danger fw-bold btn-loading px-4 py-1.5 shadow-sm" id="btnSubmitBlindClosing">
            <i class="bi bi-lock-fill me-1"></i>Verifikasi &amp; Tutup Shift Final
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- ==============================================================================
     CLIENT-SIDE JAVASCRIPT LOGIC FOR DRAWER HUD & BLIND COUNTING
     ============================================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const liveStatusUrl = "{{ route('admin.keuangan.shift-operational.live-status') }}";
  const cashInUrl = "{{ route('admin.keuangan.shift-operational.cash-in') }}";
  const cashOutUrl = "{{ route('admin.keuangan.shift-operational.cash-out') }}";

  let activeShiftData = null;

  // Format currency IDR helper
  function formatRupiah(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
  }

  // Fetch Live Status from Server
  window.fetchLiveDrawerStatus = function() {
    const refreshIcon = document.querySelector('#btnRefreshHud i');
    if (refreshIcon) refreshIcon.classList.add('bi-spin');

    fetch(liveStatusUrl, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(res => res.json())
    .then(res => {
      if (refreshIcon) refreshIcon.classList.remove('bi-spin');

      const pillBtn = document.getElementById('navbarShiftHudPill');

      if (res.status === 'active' && res.has_active_shift && res.data) {
        activeShiftData = res.data;

        // Update Navbar Pill
        if (pillBtn) {
          pillBtn.className = 'btn btn-sm d-flex align-items-center gap-1.5 rounded-3 px-2.5 py-1.5 shadow-sm border-0';
          pillBtn.style.background = 'rgba(16, 185, 129, 0.12)';
          pillBtn.style.color = '#10b981';
          pillBtn.style.border = '1px solid rgba(16, 185, 129, 0.3) !important';
          pillBtn.innerHTML = `
            <span class="spinner-grow spinner-grow-sm text-success" style="width: 7px; height: 7px;" role="status"></span>
            <span class="fw-semibold text-truncate d-none d-md-inline" style="font-size: 0.78rem;">${res.data.cashier_name || 'Kasir Aktif'}</span>
            <span class="text-muted-c d-none d-md-inline" style="font-size: 0.7rem;">|</span>
            <span class="fw-bold" style="font-size: 0.82rem;">💵 ${res.data.expected_cash_formatted}</span>
          `;
        }

        // Show Active Sections in Offcanvas
        document.getElementById('hudActiveShiftBanner')?.classList.remove('d-none');
        document.getElementById('hudInactiveShiftBanner')?.classList.add('d-none');
        document.getElementById('hudActiveMetricsSection')?.classList.remove('d-none');
        document.getElementById('hudFooterSection')?.classList.remove('d-none');

        // Populate Information
        document.getElementById('hudShiftTitle').innerText = 'Kasir: ' + (res.data.cashier_name || 'Aktif');
        document.getElementById('hudCashierLabel').innerText = res.data.cashier_name;
        document.getElementById('hudClockInLabel').innerText = 'Buka ' + res.data.opened_at_formatted;
        document.getElementById('hudDurationBadge').innerText = res.data.duration;

        // Populate 4 Stat Cards
        document.getElementById('hudValStartingCash').innerText = res.data.starting_cash_formatted;
        document.getElementById('hudValCashSales').innerText = '+' + res.data.cash_sales_formatted;
        document.getElementById('hudValDrawerIn').innerText = '+' + Number(res.data.drawer_cash_in).toLocaleString('id-ID');
        document.getElementById('hudValDrawerOut').innerText = '-' + Number(res.data.drawer_cash_out).toLocaleString('id-ID');
        document.getElementById('hudValExpectedCash').innerText = res.data.expected_cash_formatted;

        // Populate Recent Mutations
        renderRecentLogs(res.data.recent_logs);

      } else {
        activeShiftData = null;

        // Update Navbar Pill for Inactive
        if (pillBtn) {
          pillBtn.className = 'btn btn-sm d-flex align-items-center gap-1.5 rounded-3 px-2.5 py-1.5 shadow-sm border-0';
          pillBtn.style.background = 'rgba(245, 158, 11, 0.12)';
          pillBtn.style.color = '#f59e0b';
          pillBtn.style.border = '1px solid rgba(245, 158, 11, 0.3) !important';
          pillBtn.innerHTML = `
            <i class="bi bi-exclamation-circle-fill text-warning"></i>
            <span class="fw-bold" style="font-size: 0.78rem;">Buka Shift Kasir</span>
          `;
        }

        // Show Inactive Sections in Offcanvas
        document.getElementById('hudActiveShiftBanner')?.classList.add('d-none');
        document.getElementById('hudInactiveShiftBanner')?.classList.remove('d-none');
        document.getElementById('hudActiveMetricsSection')?.classList.add('d-none');
        document.getElementById('hudFooterSection')?.classList.add('d-none');
      }
    })
    .catch(err => {
      if (refreshIcon) refreshIcon.classList.remove('bi-spin');
      console.warn('Error fetching live drawer status:', err);
    });
  };

  // Render recent 5 drawer logs
  function renderRecentLogs(logs) {
    const container = document.getElementById('hudRecentLogsContainer');
    if (!container) return;

    if (!logs || logs.length === 0) {
      container.innerHTML = `
        <div class="text-center py-2 text-muted-c" style="font-size: 0.75rem;">
          Belum ada mutasi kas masuk/keluar pada shift ini.
        </div>
      `;
      return;
    }

    let html = '';
    logs.forEach(log => {
      const isIn = log.type === 'in';
      const badgeClass = isIn ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger';
      const icon = isIn ? 'bi-arrow-down-left' : 'bi-arrow-up-right';
      const sign = isIn ? '+' : '-';

      html += `
        <div class="p-2 rounded-2 d-flex align-items-center justify-content-between" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
          <div class="d-flex align-items-center gap-2 min-w-0">
            <span class="badge ${badgeClass} rounded-pill p-1">
              <i class="bi ${icon}"></i>
            </span>
            <div class="text-truncate" style="line-height: 1.2;">
              <span class="fw-semibold text-truncate d-block" style="font-size: 0.75rem;">${log.reason}</span>
              <small class="text-muted-c" style="font-size: 0.68rem;">${log.time_formatted} • ${log.category}</small>
            </div>
          </div>
          <span class="fw-bold text-nowrap ms-2 ${isIn ? 'text-success' : 'text-danger'}" style="font-size: 0.78rem;">
            ${sign}${log.amount_formatted}
          </span>
        </div>
      `;
    });
    container.innerHTML = html;
  }

  // Refresh Button click
  document.getElementById('btnRefreshHud')?.addEventListener('click', function() {
    window.fetchLiveDrawerStatus();
  });

  // Handle Quick Cash-In Submit
  document.getElementById('formHudCashIn')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    const formData = new FormData(form);

    setTimeout(() => {
      fetch(cashInUrl, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        if (submitBtn) submitBtn.disabled = false;
        if (res.status === 'success') {
          if (typeof NexoraToast === 'function') {
            NexoraToast(res.message, 'success');
          } else {
            alert(res.message);
          }
          form.reset();
          const collapseEl = bootstrap.Collapse.getInstance(document.getElementById('collapseCashIn'));
          if (collapseEl) collapseEl.hide();
          window.fetchLiveDrawerStatus();
        } else {
          alert(res.message || 'Gagal menyimpan kas masuk.');
        }
      })
      .catch(err => {
        if (submitBtn) submitBtn.disabled = false;
        alert('Terjadi kesalahan jaringan.');
      });
    }, 400); // 400ms feedback latency
  });

  // Handle Quick Cash-Out Submit
  document.getElementById('formHudCashOut')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    const formData = new FormData(form);

    setTimeout(() => {
      fetch(cashOutUrl, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        if (submitBtn) submitBtn.disabled = false;
        if (res.status === 'success') {
          if (typeof NexoraToast === 'function') {
            NexoraToast(res.message, 'success');
          } else {
            alert(res.message);
          }
          form.reset();
          const collapseEl = bootstrap.Collapse.getInstance(document.getElementById('collapseCashOut'));
          if (collapseEl) collapseEl.hide();
          window.fetchLiveDrawerStatus();
        } else {
          alert(res.message || 'Gagal menyimpan kas keluar.');
        }
      })
      .catch(err => {
        if (submitBtn) submitBtn.disabled = false;
        alert('Terjadi kesalahan jaringan.');
      });
    }, 400); // 400ms feedback latency
  });

  // Print Interim X-Report
  document.getElementById('btnPrintXReportBtn')?.addEventListener('click', function() {
    if (!activeShiftData || !activeShiftData.x_report_url) {
      alert('Sesi shift aktif tidak ditemukan.');
      return;
    }
    window.open(activeShiftData.x_report_url, '_blank', 'width=420,height=600,menubar=no,toolbar=no,location=no,status=no');
  });

  // Open Blind Closing Modal
  document.getElementById('btnOpenBlindClosingModal')?.addEventListener('click', function() {
    const offcanvasEl = bootstrap.Offcanvas.getInstance(document.getElementById('cashierDrawerHud'));
    if (offcanvasEl) offcanvasEl.hide();

    const modal = new bootstrap.Modal(document.getElementById('blindClosingModal'));
    modal.show();
    calculateDenominations();
  });

  // Denomination breakdown calculator logic
  function calculateDenominations() {
    let total = 0;
    document.querySelectorAll('.denomination-input').forEach(input => {
      const nominal = Number(input.getAttribute('data-nominal')) || 0;
      const count = Number(input.value) || 0;
      const subtotal = nominal * count;
      total += subtotal;

      const row = input.closest('.denomination-row');
      if (row) {
        const subtotalEl = row.querySelector('.denom-subtotal');
        if (subtotalEl) {
          subtotalEl.innerText = formatRupiah(subtotal);
          subtotalEl.style.color = count > 0 ? 'var(--brand-primary, #6366f1)' : 'var(--text-muted)';
        }
      }
    });

    const otherCoins = Number(document.getElementById('inputOtherCoins')?.value) || 0;
    total += otherCoins;

    // Update displays
    const displayTotal = document.getElementById('displayTotalActualCash');
    const hiddenInput = document.getElementById('inputActualCashCounted');
    if (displayTotal) displayTotal.innerText = formatRupiah(total);
    if (hiddenInput) hiddenInput.value = total;

    // Update safe deposit calculation
    const retainedFloat = Number(document.getElementById('inputRetainedCashFloat')?.value) || 0;
    const depositToSafe = Math.max(0, total - retainedFloat);
    const displayDeposit = document.getElementById('displayDepositToSafe');
    if (displayDeposit) displayDeposit.innerText = formatRupiah(depositToSafe);
  }

  // Listen on denomination inputs
  document.querySelectorAll('.denomination-input, #inputOtherCoins, #inputRetainedCashFloat').forEach(el => {
    el.addEventListener('input', calculateDenominations);
  });

  // Reset denomination calculator button
  document.getElementById('btnResetDenomination')?.addEventListener('click', function() {
    document.querySelectorAll('.denomination-input').forEach(inp => inp.value = '');
    const otherCoinsInp = document.getElementById('inputOtherCoins');
    if (otherCoinsInp) otherCoinsInp.value = '';
    calculateDenominations();
  });

  // Global Keyboard Shortcuts (F4 or Shift+D)
  document.addEventListener('keydown', function(e) {
    // If inside input or textarea, avoid overriding unless F4
    const isEditing = ['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName);

    if (e.key === 'F4' || (e.shiftKey && (e.key === 'D' || e.key === 'd') && !isEditing)) {
      e.preventDefault();
      const drawerEl = document.getElementById('cashierDrawerHud');
      if (drawerEl) {
        const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(drawerEl);
        offcanvas.toggle();
      }
    }
  });

  // Initial fetch on page load
  window.fetchLiveDrawerStatus();
});
</script>
