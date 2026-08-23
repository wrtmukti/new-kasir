@extends('admin.layouts.app')

@section('title', 'Buka / Tutup Shift Kasir (Clock-In & Out)')

@php $activeMenu = 'shift-operational' @endphp

@push('styles')
<style>
  .hero-shift-banner {
    background: var(--bg-elevated, #1e293b);
    border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
    border-radius: 16px;
    padding: 1.75rem;
    margin-bottom: 1.75rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    position: relative;
    overflow: hidden;
  }

  .hero-shift-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
  }

  .hero-shift-banner.closed::before {
    background: linear-gradient(180deg, #f59e0b, #d97706);
  }

  .hero-shift-banner.active::before {
    background: linear-gradient(180deg, #22c55e, #16a34a);
  }

  .kpi-stat-box {
    background: var(--bg-subtle, rgba(255, 255, 255, 0.03));
    border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
    border-radius: 12px;
    padding: 1rem 1.1rem;
    height: 100%;
    transition: all 0.2s ease;
  }

  .kpi-stat-box:hover {
    border-color: #3b82f6;
    background: rgba(59, 130, 246, 0.05);
  }

  .kpi-stat-title {
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--text-secondary, #94a3b8);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.35rem;
  }

  .kpi-stat-value {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--text-primary, #f8fafc);
  }

  .btn-preset-chip {
    background: var(--bg-subtle, rgba(255, 255, 255, 0.05));
    border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.12));
    color: var(--text-primary, #f8fafc);
    border-radius: 20px;
    padding: 0.3rem 0.8rem;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s ease;
  }

  .btn-preset-chip:hover {
    background: rgba(59, 130, 246, 0.2);
    border-color: #3b82f6;
    color: #60a5fa;
  }

  .btn-clock-in {
    background: linear-gradient(135deg, #16a34a, #15803d);
    color: #ffffff;
    border: none;
    border-radius: 12px;
    padding: 0.8rem 2.25rem;
    font-weight: 700;
    font-size: 1rem;
    box-shadow: 0 4px 16px rgba(22, 163, 74, 0.35);
    transition: all 0.2s ease;
  }

  .btn-clock-in:hover {
    background: linear-gradient(135deg, #15803d, #166534);
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(22, 163, 74, 0.45);
  }

  .btn-clock-out {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #ffffff;
    border: none;
    border-radius: 12px;
    padding: 0.8rem 2.25rem;
    font-weight: 700;
    font-size: 1rem;
    box-shadow: 0 4px 16px rgba(220, 38, 38, 0.35);
    transition: all 0.2s ease;
  }

  .btn-clock-out:hover {
    background: linear-gradient(135deg, #b91c1c, #991b1b);
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 38, 38, 0.45);
  }

  .variance-tag-pill {
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
  }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <h1>Buka / Tutup Shift (Clock-In & Out)</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Keuangan & Setting</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Buka / Tutup Shift</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('admin.keuangan.setting-shift.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm">
      <i class="bi bi-sliders me-1"></i> Pengaturan Master Shift
    </a>
    <a href="{{ route('admin.reports.shifts') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm">
      <i class="bi bi-shield-lock me-1"></i> Audit Shift Closing
    </a>
  </div>
</div>

<!-- Session Flash Messages -->
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 12px;">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif


<!-- KONDISI A: SHIFT BELUM DIBUKA / TUTUP (CLOCK-IN FORM) -->
@if(!$activeShift)
  <div class="hero-shift-banner closed">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <span class="chip-tag" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; font-weight:700;">
        <i class="bi bi-pause-circle-fill me-1"></i> STATUS SHIFT RESTORAN: BELUM AKTIF / TUTUP
      </span>
      <span class="text-muted-c" style="font-size:0.85rem;">
        <i class="bi bi-clock me-1"></i> Jam Cut-Off Operasional: <strong>{{ \Carbon\Carbon::parse($setting->daily_cutoff_time)->format('H:i') }} WIB</strong>
      </span>
    </div>

    <h4 class="fw-bold mb-1" style="color: var(--text-primary, #f8fafc);">
      Buka Shift Kasir (Clock-In Presensi Kasir)
    </h4>
    <p class="text-muted-c mb-4" style="font-size:0.88rem;">
      Pilih nama shift dan masukkan saldo modal awal kas laci sebelum memulai pelayanan transaksi di POS Kasir.
    </p>

    <form action="{{ route('admin.keuangan.shift-operational.open') }}" method="POST" id="formOpenShift">
      @csrf
      <div class="row g-4">
        
        <!-- Shift Selector -->
        <div class="col-md-5">
          <label class="form-label-modern mb-1 fw-semibold">
            Pilih Master Shift / Nama Shift <span class="text-danger">*</span>
          </label>
          
          @if($setting->shift_mode === 'auto_master' && $masterShifts->count() > 0)
            <select name="shift_name_select" id="shift_name_select" class="form-select-modern" onchange="onSelectMasterShift(this)" required>
              @foreach($masterShifts as $ms)
                <option value="{{ $ms->shift_name }}" data-number="{{ $ms->shift_number }}" data-cash="{{ $ms->default_starting_cash }}">
                  Shift #{{ $ms->shift_number }} — {{ $ms->shift_name }} ({{ \Carbon\Carbon::parse($ms->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($ms->end_time)->format('H:i') }})
                </option>
              @endforeach
            </select>
            <input type="hidden" name="shift_name" id="shift_name" value="{{ $masterShifts->first()->shift_name }}">
            <input type="hidden" name="shift_number" id="shift_number" value="{{ $masterShifts->first()->shift_number }}">
          @else
            <input type="text" name="shift_name" id="shift_name" class="form-control-modern" placeholder="Contoh: Shift 1 Pagi" value="Shift Pagi Kasir" required>
            <input type="hidden" name="shift_number" id="shift_number" value="1">
          @endif
        </div>

        <!-- Starting Cash Input -->
        <div class="col-md-7">
          <label class="form-label-modern mb-1 fw-semibold">
            Saldo Modal Awal Kas Laci (Rp) <span class="text-danger">*</span>
          </label>
          <div class="input-group mb-2">
            <span class="input-group-text bg-dark border-secondary text-success fw-bold">Rp</span>
            <input type="number" name="starting_cash" id="starting_cash" class="form-control-modern fs-5 fw-bold text-success" value="{{ $masterShifts->first()->default_starting_cash ?? 300000 }}" step="1000" min="0" required>
          </div>
          
          <!-- Quick Preset Cash Buttons -->
          <div class="d-flex align-items-center gap-2">
            <span class="text-muted-c" style="font-size:0.8rem;">Preset Modal:</span>
            <button type="button" class="btn-preset-chip" onclick="setPresetCash(100000)">Rp 100rb</button>
            <button type="button" class="btn-preset-chip" onclick="setPresetCash(200000)">Rp 200rb</button>
            <button type="button" class="btn-preset-chip" onclick="setPresetCash(300000)">Rp 300rb</button>
            <button type="button" class="btn-preset-chip" onclick="setPresetCash(500000)">Rp 500rb</button>
          </div>
        </div>

      </div>

      <div class="mt-4 text-end">
        <button type="submit" class="btn btn-clock-in btn-loading">
          <i class="bi bi-play-circle-fill me-2"></i> CLOCK-IN / BUKA SHIFT KASIR SEKARANG
        </button>
      </div>
    </form>
  </div>


<!-- KONDISI B: SHIFT SEDANG AKTIF / OPEN (LIVE DASHBOARD & CLOCK-OUT FORM) -->
@else
  <div class="hero-shift-banner active">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <span class="chip-tag" style="background: rgba(34, 197, 94, 0.2); color: #4ade80; font-weight:700;">
        <i class="bi bi-check-circle-fill me-1"></i> STATUS SHIFT RESTORAN: SEDANG AKTIF / BERJALAN
      </span>
      <span class="text-muted-c" style="font-size:0.85rem;">
        <i class="bi bi-clock-history me-1"></i> Durasi Bertugas: <strong class="text-info">{{ $liveStats['runtime_duration'] }}</strong>
      </span>
    </div>

    <!-- Live KPI Stats Cards -->
    <div class="row g-3 mb-4">
      <div class="col-md-3 col-6">
        <div class="kpi-stat-box">
          <div class="kpi-stat-title">Shift & Kasir</div>
          <div class="kpi-stat-value text-info text-truncate">{{ $activeShift->shift_name }}</div>
          <div class="text-muted-c mt-1" style="font-size:0.78rem;">Sejak {{ \Carbon\Carbon::parse($activeShift->opened_at)->format('H:i') }} WIB</div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="kpi-stat-box">
          <div class="kpi-stat-title">Modal Awal Laci</div>
          <div class="kpi-stat-value text-warning">Rp {{ number_format($liveStats['starting_cash'], 0, ',', '.') }}</div>
          <div class="text-muted-c mt-1" style="font-size:0.78rem;">Dipaketkan saat clock-in</div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="kpi-stat-box">
          <div class="kpi-stat-title">Live Sales Tunai</div>
          <div class="kpi-stat-value text-success">Rp {{ number_format($liveStats['cash_sales'], 0, ',', '.') }}</div>
          <div class="text-muted-c mt-1" style="font-size:0.78rem;">{{ $liveStats['order_count'] }} Pesanan Terikat</div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="kpi-stat-box">
          <div class="kpi-stat-title">Ekspektasi Uang Kas</div>
          <div class="kpi-stat-value text-primary">Rp {{ number_format($liveStats['expected_cash'], 0, ',', '.') }}</div>
          <div class="text-muted-c mt-1" style="font-size:0.78rem;">Modal + Sales Tunai</div>
        </div>
      </div>
    </div>

    <hr class="border-secondary opacity-25 my-4">

    <!-- Form Clock-Out / Tutup Shift & Cash Balancing -->
    <h5 class="fw-bold mb-1" style="color: var(--text-primary, #f8fafc);">
      <i class="bi bi-box-arrow-right text-danger me-2"></i>Tutup Shift & Cash Balancing (Clock-Out)
    </h5>
    <p class="text-muted-c mb-4" style="font-size:0.88rem;">
      Hitung uang fisik di laci kasir dan masukkan totalnya untuk memverifikasi selisih kas sebelum mencetak Z-Report.
    </p>

    <form action="{{ route('admin.keuangan.shift-operational.close') }}" method="POST" id="formCloseShift">
      @csrf
      
      <div class="row g-4 align-items-center">
        <div class="col-md-5">
          <label for="actual_cash_counted" class="form-label-modern mb-1 fw-semibold">
            Hitungan Fisik Uang Tunai di Laci (Rp) <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-dark border-secondary text-warning fw-bold">Rp</span>
            <input type="number" name="actual_cash_counted" id="actual_cash_counted" class="form-control-modern fs-4 fw-bold text-warning" value="{{ $liveStats['expected_cash'] }}" step="1000" min="0" oninput="calculateVariance()" required>
          </div>
          <div class="text-muted-c mt-1" style="font-size:0.78rem;">
            Hitung seluruh lembaran uang kertas dan koin di dalam laci kasir.
          </div>
        </div>

        <div class="col-md-4">
          <label class="form-label-modern mb-1 fw-semibold">Status Selisih Kas (Audit Variance)</label>
          <div id="varianceContainer">
            <div class="variance-tag-pill" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3);">
              <i class="bi bi-check-circle-fill"></i> Kas PAS (Rp 0)
            </div>
          </div>
        </div>

        <div class="col-md-3 text-end">
          <button type="submit" class="btn btn-clock-out w-100 btn-loading" id="btnCloseShift">
            <i class="bi bi-stop-circle-fill me-1"></i> CLOCK-OUT & Z-REPORT
          </button>
        </div>
      </div>

      <div class="mt-3">
        <label for="notes" class="form-label-modern mb-1 fw-semibold text-secondary">Catatan Shift (Opsional)</label>
        <input type="text" name="notes" id="notes" class="form-control-modern" placeholder="Tuliskan catatan selisih kas atau kendala operasional shift..." value="{{ $activeShift->notes }}">
      </div>
    </form>
  </div>
@endif


<!-- SECTION AUDIT: HISTORI 5 SHIFT CLOSING TERAKHIR -->
<div class="card">
  <div class="card-header-flex">
    <div>
      <h6><i class="bi bi-journal-check text-primary me-2"></i>Histori 5 Shift Closing Terakhir</h6>
      <div class="text-muted-c" style="font-size:0.8rem;">Daftar 5 sesi penutupan shift kasir terbaru.</div>
    </div>
    <a href="{{ route('admin.reports.shifts') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
      Lihat Audit Shift <i class="bi bi-arrow-right ms-1"></i>
    </a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>Tanggal Bisnis</th>
            <th>Nama Shift</th>
            <th>Waktu Clock-In</th>
            <th>Waktu Clock-Out</th>
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
                <span class="fw-bold" style="color: var(--text-primary, #f8fafc);">{{ \Carbon\Carbon::parse($rc->business_date)->format('d/m/Y') }}</span>
              </td>
              <td>{{ $rc->shift_name }}</td>
              <td class="text-muted-c" style="font-size:0.85rem;">{{ \Carbon\Carbon::parse($rc->opened_at)->format('H:i') }} WIB</td>
              <td class="text-muted-c" style="font-size:0.85rem;">{{ $rc->closed_at ? \Carbon\Carbon::parse($rc->closed_at)->format('H:i') . ' WIB' : '-' }}</td>
              <td>Rp {{ number_format($rc->starting_cash, 0, ',', '.') }}</td>
              <td>Rp {{ number_format($rc->system_expected_cash, 0, ',', '.') }}</td>
              <td class="fw-bold text-white">Rp {{ number_format($rc->actual_cash_counted, 0, ',', '.') }}</td>
              <td>
                @if($rc->cash_difference == 0)
                  <span class="chip-tag" style="background: rgba(34, 197, 94, 0.15); color: #4ade80;">PAS</span>
                @elseif($rc->cash_difference > 0)
                  <span class="chip-tag" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">+Rp {{ number_format($rc->cash_difference, 0, ',', '.') }}</span>
                @else
                  <span class="chip-tag" style="background: rgba(239, 68, 68, 0.15); color: #f87171;">-Rp {{ number_format(abs($rc->cash_difference), 0, ',', '.') }}</span>
                @endif
              </td>
              <td class="text-end">
                <a href="{{ route('admin.keuangan.shift-operational.z-report', $rc->id) }}" target="_blank" class="btn btn-action-icon text-info" title="Cetak Struk Z-Report">
                  <i class="bi bi-printer-fill"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center py-3 text-muted-c">
                Belum ada histori shift closing.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function setPresetCash(amount) {
    document.getElementById('starting_cash').value = amount;
  }

  function onSelectMasterShift(selectEl) {
    const selectedOption = selectEl.options[selectEl.selectedIndex];
    const shiftName = selectedOption.value;
    const shiftNumber = selectedOption.getAttribute('data-number');
    const startingCash = selectedOption.getAttribute('data-cash');

    document.getElementById('shift_name').value = shiftName;
    document.getElementById('shift_number').value = shiftNumber;
    if (startingCash) {
      document.getElementById('starting_cash').value = startingCash;
    }
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
        <div class="variance-tag-pill" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3);">
          <i class="bi bi-check-circle-fill"></i> Kas PAS (Rp 0)
        </div>
      `;
    } else if (diff > 0) {
      container.innerHTML = `
        <div class="variance-tag-pill" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">
          <i class="bi bi-plus-circle-fill"></i> Kelebihan Kas: +Rp ${new Intl.NumberFormat('id-ID').format(diff)}
        </div>
      `;
    } else {
      container.innerHTML = `
        <div class="variance-tag-pill" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3);">
          <i class="bi bi-exclamation-triangle-fill"></i> Kekurangan Kas: -Rp ${new Intl.NumberFormat('id-ID').format(Math.abs(diff))}
        </div>
      `;
    }
  }

  document.querySelectorAll('.btn-loading').forEach(btn => {
    btn.closest('form')?.addEventListener('submit', function() {
      btn.disabled = true;
      btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses...`;
      setTimeout(() => {}, 400);
    });
  });
</script>
@endpush
