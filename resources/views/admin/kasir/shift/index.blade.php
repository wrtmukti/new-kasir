@extends('admin.layouts.app')

@section('title', 'Buka / Tutup Kasir')

@php $activeMenu = 'shift-operational' @endphp

@section('content')
<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>Buka / Tutup Kasir</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Keuangan</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Buka Kasir</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('admin.keuangan.setting-shift.index') }}" class="btn btn-outline-soft">
      <i class="bi bi-sliders me-1"></i> Pengaturan Shift
    </a>
    <a href="{{ route('admin.reports.shifts') }}" class="btn btn-outline-soft">
      <i class="bi bi-journal-text me-1"></i> Audit Shift
    </a>
  </div>
</div>

<!-- FLASH NOTIFICATIONS -->
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="background: var(--success-bg); border: 1px solid var(--success); color: var(--success); border-radius: var(--radius-md);">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert" style="background: var(--danger-bg); border: 1px solid var(--danger); color: var(--danger); border-radius: var(--radius-md);">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<!-- =========================================================================
     KONDISI A: SHIFT SEDANG AKTIF / OPEN (LIVE DASHBOARD & CONTROLS)
     ========================================================================= -->
@if($activeShift)

  <!-- ACTIVE SHIFT TOP BAR -->
  <div class="card mb-3">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3 py-3">
      <div class="d-flex align-items-center gap-3">
        <span class="status-dot live" style="width:12px; height:12px;"></span>
        <div>
          <div class="d-flex align-items-center gap-2">
            <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">Kasir Aktif: {{ $activeShift->cashier?->name ?? 'Kasir Utama' }}</h6>
            <span class="badge badge-success">BUKA</span>
          </div>
          <span class="text-muted-c" style="font-size:0.82rem;">
            Mulai: <strong>{{ \Carbon\Carbon::parse($activeShift->opened_at)->format('H:i') }} WIB</strong> &bull; 
            Durasi Bertugas: <strong class="text-info">{{ $liveStats['runtime_duration'] }}</strong>
          </span>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-soft" data-bs-toggle="modal" data-bs-target="#modalCashIn">
          <i class="bi bi-plus-circle text-success me-1"></i> + Kas Masuk (Top-up)
        </button>
        <button type="button" class="btn btn-outline-soft" data-bs-toggle="modal" data-bs-target="#modalCashOut">
          <i class="bi bi-dash-circle text-danger me-1"></i> - Kas Keluar (Petty Cash)
        </button>
      </div>
    </div>
  </div>

  <!-- STAT CARDS (4 COLUMNS - NEXORA STYLE) -->
  <div class="row g-3 mb-3">
    <!-- 1. Modal Awal -->
    <div class="col-6 col-xl-3">
      <div class="card card-glow h-100">
        <div class="card-inner card-body stat-card">
          <div class="stat-icon" style="background: var(--warning-bg); color: var(--warning);">
            <i class="bi bi-cash-stack"></i>
          </div>
          <div class="stat-value">Rp {{ number_format($liveStats['starting_cash'], 0, ',', '.') }}</div>
          <div class="stat-label">Modal Awal Laci</div>
          <span class="text-muted-c mt-1" style="font-size:0.75rem;">Saldo awal saat buka kasir</span>
        </div>
      </div>
    </div>

    <!-- 2. Sales Tunai -->
    <div class="col-6 col-xl-3">
      <div class="card card-glow h-100">
        <div class="card-inner card-body stat-card">
          <div class="stat-icon" style="background: var(--success-bg); color: var(--success);">
            <i class="bi bi-receipt"></i>
          </div>
          <div class="stat-value">Rp {{ number_format($liveStats['cash_sales'], 0, ',', '.') }}</div>
          <div class="stat-label">Penjualan Tunai Kasir</div>
          <span class="text-muted-c mt-1" style="font-size:0.75rem;">{{ $liveStats['order_count'] }} Pesanan Terikat</span>
        </div>
      </div>
    </div>

    <!-- 3. Kas Laci (In / Out) -->
    <div class="col-6 col-xl-3">
      <div class="card card-glow h-100">
        <div class="card-inner card-body stat-card">
          <div class="stat-icon" style="background: rgba(99, 102, 241, 0.12); color: var(--accent-1);">
            <i class="bi bi-arrow-left-right"></i>
          </div>
          <div class="stat-value" style="font-size:1.15rem;">
            <span class="text-success">+{{ number_format($liveStats['drawer_cash_in'] ?? 0, 0, ',', '.') }}</span> / 
            <span class="text-danger">-{{ number_format($liveStats['drawer_cash_out'] ?? 0, 0, ',', '.') }}</span>
          </div>
          <div class="stat-label">Kas Laci (Topup / Petty)</div>
          <span class="text-muted-c mt-1" style="font-size:0.75rem;">Mutasi buku kas laci</span>
        </div>
      </div>
    </div>

    <!-- 4. Ekspektasi Uang di Laci -->
    <div class="col-6 col-xl-3">
      <div class="card card-glow h-100">
        <div class="card-inner card-body stat-card">
          <div class="stat-icon" style="background: var(--info-bg); color: var(--accent-cyan);">
            <i class="bi bi-wallet2"></i>
          </div>
          <div class="stat-value text-info">Rp {{ number_format($liveStats['expected_cash'], 0, ',', '.') }}</div>
          <div class="stat-label">Ekspektasi Uang di Laci</div>
          <span class="text-muted-c mt-1" style="font-size:0.75rem;">Modal + Tunai + In - Out</span>
        </div>
      </div>
    </div>
  </div>

  <!-- MIDDLE SECTION: TUTUP SHIFT & LIVE DRAWER LOGS -->
  <div class="row g-3 mb-3">
    
    <!-- LEFT: FORM TUTUP SHIFT & HANDOVER KASIR -->
    <div class="col-lg-7">
      <div class="card h-100">
        <div class="card-header-flex">
          <div>
            <h6><i class="bi bi-box-arrow-right text-danger me-2"></i>Tutup Kasir &amp; Setoran Laci</h6>
            <span class="text-muted-c" style="font-size:0.78rem;">Verifikasi uang fisik kasir dan alokasi setoran brankas</span>
          </div>
        </div>

        <div class="card-body">
          <form action="{{ route('admin.keuangan.shift-operational.close') }}" method="POST" id="formCloseShift">
            @csrf

            <div class="row g-3">
              <!-- 1. Hitungan Fisik Uang di Laci -->
              <div class="col-md-6">
                <label for="actual_cash_counted" class="form-label-modern">
                  Total Uang Fisik di Laci (Rp) <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-elevated border-strong text-muted-c fw-bold">Rp</span>
                  <input type="number" name="actual_cash_counted" id="actual_cash_counted" class="form-control-modern fw-bold text-warning fs-5" value="{{ $liveStats['expected_cash'] }}" step="any" min="0" oninput="calculateVariance()" required>
                </div>
                <span class="text-muted-c" style="font-size:0.75rem;">Total seluruh lembaran uang &amp; koin di laci.</span>
              </div>

              <!-- 2. Tinggalkan di Laci Kasir (Modal Shift Berikutnya) -->
              <div class="col-md-6">
                <label for="retained_cash_float" class="form-label-modern">
                  Tinggalkan di Laci Kasir (Rp)
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-elevated border-strong text-muted-c fw-bold">Rp</span>
                  <input type="number" name="retained_cash_float" id="retained_cash_float" class="form-control-modern fw-bold text-info fs-5" value="{{ $liveStats['starting_cash'] }}" step="any" min="0" oninput="calculateDeposit()">
                </div>
                <span class="text-muted-c" style="font-size:0.75rem;">Modal kas awal untuk kasir shift selanjutnya.</span>
              </div>

              <!-- 3. Uang Setor ke Brankas / Owner -->
              <div class="col-md-6">
                <label class="form-label-modern">Uang Setor ke Brankas / Owner</label>
                <div class="card p-2.5" style="background: var(--success-bg); border: 1px solid rgba(52, 211, 153, 0.3);">
                  <div class="fs-5 fw-bold text-success" id="displayDepositSafe">
                    Rp {{ number_format(max(0, $liveStats['expected_cash'] - $liveStats['starting_cash']), 0, ',', '.') }}
                  </div>
                </div>
                <span class="text-muted-c" style="font-size:0.75rem;">Uang fisik laci dikurangi modal yang ditinggal.</span>
              </div>

              <!-- 4. Status Audit Selisih Kas -->
              <div class="col-md-6">
                <label class="form-label-modern">Status Selisih Kas (Variance)</label>
                <div id="varianceContainer">
                  <div class="card p-2.5" style="background: var(--success-bg); border: 1px solid rgba(52, 211, 153, 0.3);">
                    <div class="fw-bold text-success" style="font-size:0.9rem;">
                      <i class="bi bi-check-circle-fill me-1"></i> Kas PAS (Rp 0)
                    </div>
                  </div>
                </div>
                <span class="text-muted-c" style="font-size:0.75rem;">Perbandingan fisik kasir vs hitungan sistem.</span>
              </div>

              <!-- 5. Catatan Kasir -->
              <div class="col-12">
                <label for="cashier_note" class="form-label-modern">Catatan Kasir / Kendala Shift (Opsional)</label>
                <input type="text" name="cashier_note" id="cashier_note" class="form-control-modern" placeholder="Tuliskan catatan selisih kas atau informasi serah terima shift..." value="{{ $activeShift->notes }}">
              </div>

              <!-- 6. Action Submit Button -->
              <div class="col-12 mt-3">
                <button type="submit" class="btn btn-danger-grad w-100 py-2.5 fw-semibold" id="btnCloseShift">
                  <i class="bi bi-stop-circle-fill me-1"></i> Tutup Shift &amp; Cetak Z-Report
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- RIGHT: LIVE BUKU KAS LACI SHIFT BERJALAN -->
    <div class="col-lg-5">
      <div class="card h-100">
        <div class="card-header-flex">
          <div>
            <h6><i class="bi bi-journal-text text-primary me-2"></i>Buku Kas Laci Shift Ini</h6>
            <span class="text-muted-c" style="font-size:0.78rem;">Riwayat kas masuk &amp; keluar laci</span>
          </div>
          <span class="badge badge-primary">{{ $drawerLogs->count() }} Mutasi</span>
        </div>

        <div class="card-body p-0">
          @if($drawerLogs->count() > 0)
            <div class="table-responsive" style="max-height: 380px;">
              <table class="table-modern mb-0">
                <thead>
                  <tr>
                    <th>Waktu</th>
                    <th>Tipe / Kategori</th>
                    <th>Alasan</th>
                    <th class="text-end">Nominal</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($drawerLogs as $log)
                    <tr>
                      <td class="text-muted-c" style="font-size:0.8rem;">
                        {{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }}
                      </td>
                      <td>
                        @if($log->type === 'in')
                          <span class="badge badge-success">IN</span>
                        @else
                          <span class="badge badge-danger">OUT</span>
                        @endif
                        <span class="text-muted-c ms-1" style="font-size:0.75rem;">{{ ucwords(str_replace('_', ' ', $log->category)) }}</span>
                      </td>
                      <td style="font-size:0.82rem;" class="text-truncate" style="max-width:140px;" title="{{ $log->reason }}">
                        {{ $log->reason }}
                      </td>
                      <td class="text-end fw-semibold {{ $log->type === 'in' ? 'text-success' : 'text-danger' }}" style="font-size:0.85rem;">
                        {{ $log->type === 'in' ? '+' : '-' }}{{ number_format($log->amount, 0, ',', '.') }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-5 text-muted-c">
              <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
              <p class="mb-0" style="font-size:0.85rem;">Belum ada mutasi kas masuk/keluar laci pada shift ini.</p>
              <span style="font-size:0.75rem;">Gunakan tombol <strong>+ Kas Masuk</strong> atau <strong>- Kas Keluar</strong> di atas jika ada top-up atau petty cash.</span>
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>

<!-- =========================================================================
     KONDISI B: SHIFT BELUM DIBUKA / TUTUP (CLOCK-IN FORM)
     ========================================================================= -->
@else

  <div class="row g-3 mb-3 justify-content-center">
    <div class="col-lg-6 col-md-8">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header-flex py-3 px-4" style="border-bottom: 1px solid var(--border-subtle);">
          <div class="d-flex align-items-center gap-2.5">
            <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 36px; height: 36px; font-size: 1.1rem;">
              <i class="bi bi-shop"></i>
            </div>
            <div>
              <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Buka Kasir Baru</h6>
              <span class="text-muted-c" style="font-size:0.75rem;">Masukkan modal uang kembalian laci sebelum mulai transaksi</span>
            </div>
          </div>
          <span class="badge badge-warning rounded-pill px-2.5 py-1">KASIR TUTUP</span>
        </div>

        <div class="card-body p-4">
          <form action="{{ route('admin.keuangan.shift-operational.open') }}" method="POST" id="formOpenShift">
            @csrf
            <input type="hidden" name="shift_name" id="shift_name" value="Sesi Kasir">
            <input type="hidden" name="shift_number" id="shift_number" value="1">

            <div class="mb-3">
              <label for="starting_cash" class="form-label-modern fw-semibold mb-2">
                Saldo Modal Awal Kas Laci (Rp) <span class="text-danger">*</span>
              </label>
              <div class="input-group input-group-lg mb-2.5">
                <span class="input-group-text bg-elevated border-strong text-muted-c fw-bold px-3">Rp</span>
                <input type="number" 
                       name="starting_cash" 
                       id="starting_cash" 
                       class="form-control-modern fs-4 fw-bold text-success" 
                       value="{{ $masterShifts->first()->default_starting_cash ?? 200000 }}" 
                       step="any" 
                       min="0" 
                       placeholder="0"
                       required>
              </div>
              
              <!-- Quick Preset Cash Buttons -->
              <div class="d-flex align-items-center gap-1.5 flex-wrap">
                <span class="text-muted-c me-1" style="font-size:0.75rem;">Pilihan Cepat:</span>
                <button type="button" class="btn btn-outline-soft btn-sm py-1 px-2.5 rounded-pill" style="font-size:0.75rem;" onclick="setPresetCash(100000)">Rp 100.000</button>
                <button type="button" class="btn btn-outline-soft btn-sm py-1 px-2.5 rounded-pill" style="font-size:0.75rem;" onclick="setPresetCash(200000)">Rp 200.000</button>
                <button type="button" class="btn btn-outline-soft btn-sm py-1 px-2.5 rounded-pill" style="font-size:0.75rem;" onclick="setPresetCash(300000)">Rp 300.000</button>
                <button type="button" class="btn btn-outline-soft btn-sm py-1 px-2.5 rounded-pill" style="font-size:0.75rem;" onclick="setPresetCash(500000)">Rp 500.000</button>
              </div>
            </div>

            <div class="pt-3 border-top mt-4" style="border-color: var(--border-subtle) !important;">
              <button type="submit" class="btn btn-primary-grad px-4 py-2.5 fw-bold rounded-pill w-100 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                <i class="bi bi-cash-stack fs-5"></i>
                <span>Buka Kasir Sekarang</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

@endif


<!-- =========================================================================
     SECTION AUDIT: HISTORI 5 SHIFT CLOSING TERAKHIR (BOTTOM TABLE)
     ========================================================================= -->
<div class="card mt-3">
  <div class="card-header-flex">
    <div>
      <h6><i class="bi bi-journal-check text-primary me-2"></i>Histori 5 Penutupan Kasir Terakhir</h6>
      <span class="text-muted-c" style="font-size:0.78rem;">Rekapitulasi 5 sesi penutupan laci kasir sebelumnya</span>
    </div>
    <a href="{{ route('admin.reports.shifts') }}" class="btn btn-outline-soft btn-sm">
      Lihat Semua Audit <i class="bi bi-arrow-right ms-1"></i>
    </a>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Petugas Kasir</th>
            <th>Waktu Buka</th>
            <th>Waktu Tutup</th>
            <th>Modal Awal</th>
            <th>Ekspektasi Kas</th>
            <th>Fisik Kasir</th>
            <th>Selisih Kas</th>
            <th class="text-end">Z-Report</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentClosings as $rc)
            <tr>
              <td>
                <span class="fw-semibold" style="color: var(--text-primary);">
                  {{ \Carbon\Carbon::parse($rc->business_date)->format('d/m/Y') }}
                </span>
              </td>
              <td>
                <span class="fw-medium" style="color: var(--text-primary);">
                  {{ $rc->cashier?->name ?? ($rc->shift_name ?: 'Kasir Utama') }}
                </span>
              </td>
              <td class="text-muted-c" style="font-size:0.82rem;">{{ \Carbon\Carbon::parse($rc->opened_at)->format('H:i') }} WIB</td>
              <td class="text-muted-c" style="font-size:0.82rem;">{{ $rc->closed_at ? \Carbon\Carbon::parse($rc->closed_at)->format('H:i') . ' WIB' : '-' }}</td>
              <td>Rp {{ number_format($rc->starting_cash, 0, ',', '.') }}</td>
              <td>Rp {{ number_format($rc->system_expected_cash, 0, ',', '.') }}</td>
              <td class="fw-bold" style="color: var(--text-primary);">Rp {{ number_format($rc->actual_cash_counted, 0, ',', '.') }}</td>
              <td>
                @if($rc->cash_difference == 0)
                  <span class="badge badge-success">PAS</span>
                @elseif($rc->cash_difference > 0)
                  <span class="badge badge-primary">+Rp {{ number_format($rc->cash_difference, 0, ',', '.') }}</span>
                @else
                  <span class="badge badge-danger">-Rp {{ number_format(abs($rc->cash_difference), 0, ',', '.') }}</span>
                @endif
              </td>
              <td class="text-end">
                <a href="{{ route('admin.keuangan.shift-operational.z-report', $rc->id) }}" target="_blank" class="btn btn-outline-soft btn-sm py-1 px-2" title="Cetak Struk Z-Report">
                  <i class="bi bi-printer"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center py-4 text-muted-c">
                Belum ada histori shift closing.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>


<!-- =========================================================================
     MODAL: KAS MASUK (TOP-UP MODAL LACI)
     ========================================================================= -->
<div class="modal fade" id="modalCashIn" tabindex="-1" aria-labelledby="modalCashInLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg);">
      <div class="modal-header">
        <h6 class="modal-title fw-bold" id="modalCashInLabel" style="color: var(--text-primary);">
          <i class="bi bi-plus-circle text-success me-2"></i>Catat Kas Masuk (Top-Up Laci)
        </h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('admin.keuangan.shift-operational.cash-in') }}" method="POST" id="formModalCashIn">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label-modern">Nominal Kas Masuk (Rp) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-elevated border-strong text-muted-c fw-bold">Rp</span>
              <input type="number" name="amount" class="form-control-modern fw-bold text-success fs-5" placeholder="Contoh: 150000" step="any" min="1" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label-modern">Kategori Kas Masuk <span class="text-danger">*</span></label>
            <input type="text" name="category" class="form-control-modern" placeholder="Contoh: Top-up Owner / Tambah Pecahan Kembalian" value="Top-up Owner" required>
          </div>

          <div class="mb-2">
            <label class="form-label-modern">Alasan / Keterangan <span class="text-danger">*</span></label>
            <input type="text" name="reason" class="form-control-modern" placeholder="Contoh: Tambah uang kembalian pecahan Rp 2.000 &amp; Rp 5.000" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-soft" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-grad">Simpan Kas Masuk</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- =========================================================================
     MODAL: KAS KELUAR (PETTY CASH LACI)
     ========================================================================= -->
<div class="modal fade" id="modalCashOut" tabindex="-1" aria-labelledby="modalCashOutLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg);">
      <div class="modal-header">
        <h6 class="modal-title fw-bold" id="modalCashOutLabel" style="color: var(--text-primary);">
          <i class="bi bi-dash-circle text-danger me-2"></i>Catat Kas Keluar (Petty Cash Laci)
        </h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('admin.keuangan.shift-operational.cash-out') }}" method="POST" id="formModalCashOut">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label-modern">Nominal Kas Keluar (Rp) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-elevated border-strong text-muted-c fw-bold">Rp</span>
              <input type="number" name="amount" class="form-control-modern fw-bold text-danger fs-5" placeholder="Contoh: 45000" step="any" min="1" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label-modern">Kategori Pengeluaran <span class="text-danger">*</span></label>
            <input type="text" name="category" class="form-control-modern" placeholder="Contoh: Petty Cash / Beli Gas LPG / Es Batu / Galon" value="Petty Cash" required>
          </div>

          <div class="mb-2">
            <label class="form-label-modern">Alasan / Keterangan <span class="text-danger">*</span></label>
            <input type="text" name="reason" class="form-control-modern" placeholder="Contoh: Beli es batu kristal 2 kantong &amp; galon" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-soft" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger-grad">Simpan Kas Keluar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function setPresetCash(amount) {
    document.getElementById('starting_cash').value = amount;
  }

  function calculateVariance() {
    const expected = parseFloat('{{ $liveStats["expected_cash"] ?? 0 }}') || 0;
    const actualInput = document.getElementById('actual_cash_counted');
    if (!actualInput) return;

    const actual = parseFloat(actualInput.value) || 0;
    const diff = actual - expected;

    const container = document.getElementById('varianceContainer');
    if (!container) return;

    if (diff === 0) {
      container.innerHTML = `
        <div class="card p-2.5" style="background: var(--success-bg); border: 1px solid rgba(52, 211, 153, 0.3);">
          <div class="fw-bold text-success" style="font-size:0.9rem;">
            <i class="bi bi-check-circle-fill me-1"></i> Kas PAS (Rp 0)
          </div>
        </div>
      `;
    } else if (diff > 0) {
      container.innerHTML = `
        <div class="card p-2.5" style="background: var(--info-bg); border: 1px solid rgba(34, 211, 238, 0.3);">
          <div class="fw-bold text-info" style="font-size:0.9rem;">
            <i class="bi bi-plus-circle-fill me-1"></i> Kelebihan Kas: +Rp ${new Intl.NumberFormat('id-ID').format(diff)}
          </div>
        </div>
      `;
    } else {
      container.innerHTML = `
        <div class="card p-2.5" style="background: var(--danger-bg); border: 1px solid rgba(248, 113, 113, 0.3);">
          <div class="fw-bold text-danger" style="font-size:0.9rem;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> Kekurangan Kas: -Rp ${new Intl.NumberFormat('id-ID').format(Math.abs(diff))}
          </div>
        </div>
      `;
    }

    calculateDeposit();
  }

  function calculateDeposit() {
    const actualInput = document.getElementById('actual_cash_counted');
    const retainedInput = document.getElementById('retained_cash_float');
    const depositDisplay = document.getElementById('displayDepositSafe');
    if (!actualInput || !retainedInput || !depositDisplay) return;

    const actual = parseFloat(actualInput.value) || 0;
    const retained = parseFloat(retainedInput.value) || 0;
    const deposit = Math.max(0, actual - retained);

    depositDisplay.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(deposit);
  }
</script>
@endpush
