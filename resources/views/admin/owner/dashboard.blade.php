@extends('admin.layouts.app')

@section('title', 'Dashboard — Portal Owner')

@php $activeMenu = 'owner-dashboard' @endphp

@section('content')
<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>Dashboard</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Home</a>
      <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Owner</span>
      <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Dashboard</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('owner.financial') }}" class="btn btn-outline-soft">
      <i class="bi bi-pie-chart me-1"></i> Laba Rugi
    </a>
    <a href="{{ route('owner.financial.export', ['start_date' => $startDate, 'end_date' => $endDate, 'outlet_ids' => $selectedOutletIds]) }}" class="btn btn-primary-grad">
      <i class="bi bi-download me-1"></i> Ekspor Laporan
    </a>
  </div>
</div>

<!-- FILTER BAR -->
<div class="card mb-3">
  <div class="card-body py-2 px-3">
    <form method="GET" action="{{ route('owner.dashboard') }}" class="row g-2 align-items-center">
      <div class="col-lg-4 col-md-5 col-12">
        <div class="d-flex align-items-center gap-2">
          <span class="text-muted-c small fw-medium text-nowrap"><i class="bi bi-shop me-1"></i>Cabang:</span>
          <select name="outlet_ids[]" class="form-select-modern" style="padding: 0.4rem 2rem 0.4rem 0.75rem; font-size: 0.82rem;">
            <option value="">Semua Cabang ({{ $activeOutlets->count() }})</option>
            @foreach($activeOutlets as $ot)
              <option value="{{ $ot->outlet_id }}" {{ in_array($ot->outlet_id, $selectedOutletIds) ? 'selected' : '' }}>
                {{ $ot->outlet_name }} ({{ $ot->outlet_branch ?? 'Cabang' }})
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-6">
        <div class="d-flex align-items-center gap-2">
          <span class="text-muted-c small fw-medium text-nowrap"><i class="bi bi-calendar3 me-1"></i>Dari:</span>
          <input type="date" name="start_date" value="{{ $startDate }}" class="form-control-modern" style="padding: 0.4rem 0.65rem; font-size: 0.82rem;">
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-6">
        <div class="d-flex align-items-center gap-2">
          <span class="text-muted-c small fw-medium text-nowrap">Sampai:</span>
          <input type="date" name="end_date" value="{{ $endDate }}" class="form-control-modern" style="padding: 0.4rem 0.65rem; font-size: 0.82rem;">
        </div>
      </div>

      <div class="col-lg-2 col-md-1 col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary-grad btn-sm flex-fill" style="padding: 0.45rem 0.75rem;">
          <i class="bi bi-funnel me-1"></i> Filter
        </button>
        <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-soft btn-sm" title="Reset Filter" style="padding: 0.45rem 0.65rem;">
          <i class="bi bi-arrow-counterclockwise"></i>
        </a>
      </div>
    </form>
  </div>
</div>

<!-- STAT CARDS (NEXORA GLOW) -->
<div class="row g-3 mb-3">
  <!-- KPI 1: Total Omzet -->
  <div class="col-6 col-xl-3">
    <div class="card card-glow h-100">
      <div class="card-inner card-body stat-card">
        <div class="stat-icon" style="background: rgba(99,102,241,0.12); color: var(--accent-1);">
          <i class="bi bi-currency-dollar"></i>
        </div>
        <div class="stat-value">Rp {{ number_format($kpis['total_revenue'], 0, ',', '.') }}</div>
        <div class="stat-label">Total Omzet</div>
        <span class="stat-trend up mt-2">
          <i class="bi bi-receipt"></i> {{ number_format($kpis['total_orders_count']) }} transaksi
        </span>
      </div>
    </div>
  </div>

  <!-- KPI 2: Total Laba Bersih -->
  <div class="col-6 col-xl-3">
    <div class="card card-glow h-100">
      <div class="card-inner card-body stat-card">
        <div class="stat-icon" style="background: var(--success-bg); color: var(--success);">
          <i class="bi bi-graph-up-arrow"></i>
        </div>
        <div class="stat-value" style="color: var(--success);">Rp {{ number_format($kpis['net_profit'], 0, ',', '.') }}</div>
        <div class="stat-label">Laba Bersih</div>
        <span class="stat-trend up mt-2">
          <i class="bi bi-percent"></i> Margin {{ number_format($kpis['net_margin_percent'], 1) }}%
        </span>
      </div>
    </div>
  </div>

  <!-- KPI 3: Setoran Brankas Kasir -->
  <div class="col-6 col-xl-3">
    <div class="card card-glow h-100">
      <div class="card-inner card-body stat-card">
        <div class="stat-icon" style="background: var(--warning-bg); color: var(--warning);">
          <i class="bi bi-safe"></i>
        </div>
        <div class="stat-value" style="color: var(--warning);">Rp {{ number_format($kpis['total_safe_deposit'], 0, ',', '.') }}</div>
        <div class="stat-label">Setoran Kas</div>
        <span class="stat-trend mt-2" style="background: var(--warning-bg); color: var(--warning);">
          <i class="bi bi-wallet2"></i> Fisik Brankas
        </span>
      </div>
    </div>
  </div>

  <!-- KPI 4: Hutang PO Supplier Tempo -->
  <div class="col-6 col-xl-3">
    <div class="card card-glow h-100">
      <div class="card-inner card-body stat-card">
        <div class="stat-icon" style="background: var(--danger-bg); color: var(--danger);">
          <i class="bi bi-clock-history"></i>
        </div>
        <div class="stat-value" style="color: var(--danger);">Rp {{ number_format($kpis['total_po_unpaid'], 0, ',', '.') }}</div>
        <div class="stat-label">Hutang Pembelian</div>
        <span class="stat-trend down mt-2">
          <i class="bi bi-exclamation-circle"></i> Tempo Belum Lunas
        </span>
      </div>
    </div>
  </div>
</div>

<!-- TREN PENJUALAN HARIAN -->
<div class="card mb-3">
  <div class="card-header-flex">
    <div>
      <h6>Tren Penjualan Harian</h6>
      <span class="text-muted-c" style="font-size: 0.78rem;">Grafik perbandingan volume omzet harian antar cabang</span>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="status-dot live"></span>
      <span class="text-muted-c" style="font-size: 0.78rem;">Live</span>
    </div>
  </div>
  <div class="card-body" style="height: 320px; position: relative;">
    <canvas id="multiBranchTrendChart"></canvas>
  </div>
</div>

<!-- PERINGKAT CABANG -->
<div class="card">
  <div class="card-header-flex">
    <div>
      <h6>Peringkat &amp; Performa Cabang</h6>
      <span class="text-muted-c" style="font-size: 0.78rem;">Urutan performa berdasarkan total omzet penjualan tertinggi</span>
    </div>
    <a href="{{ route('owner.benchmark') }}" class="btn btn-sm btn-outline-soft">
      Detail Benchmark <i class="bi bi-arrow-right ms-1"></i>
    </a>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern striped mb-0">
        <thead>
          <tr>
            <th style="width: 55px;" class="text-center">#</th>
            <th>Cabang</th>
            <th class="text-end">Omzet</th>
            <th class="text-center">Pesanan</th>
            <th class="text-end">HPP</th>
            <th class="text-center">Margin</th>
            <th class="text-end">Waste</th>
            <th class="text-end">Laba Bersih</th>
            <th class="text-center">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($leaderboard as $index => $row)
            <tr>
              <td class="text-center">
                @if($index === 0)
                  <span class="pill pill-warning fw-bold">#1</span>
                @elseif($index === 1)
                  <span class="pill pill-secondary fw-bold">#2</span>
                @elseif($index === 2)
                  <span class="pill pill-danger fw-bold" style="background: rgba(205,127,50,0.15); color: #cd7f32; border: 1px solid rgba(205,127,50,0.3);">#3</span>
                @else
                  <span class="text-muted-c fw-semibold">{{ $index + 1 }}</span>
                @endif
              </td>
              <td>
                <div class="cell-primary">{{ $row['outlet_name'] }}</div>
                <div class="text-muted-c" style="font-size: 0.75rem;">{{ $row['outlet_branch'] ?? $row['outlet_code'] }}</div>
              </td>
              <td class="text-end text-mono fw-bold" style="color: var(--text-primary);">
                Rp {{ number_format($row['revenue'], 0, ',', '.') }}
              </td>
              <td class="text-center">
                <span class="pill pill-primary text-mono">{{ number_format($row['orders_count']) }}</span>
              </td>
              <td class="text-end text-mono text-muted-c">
                Rp {{ number_format($row['cogs'], 0, ',', '.') }}
              </td>
              <td class="text-center">
                <span class="pill {{ $row['gross_margin_percent'] >= 60 ? 'pill-success' : ($row['gross_margin_percent'] >= 40 ? 'pill-info' : 'pill-warning') }} text-mono">
                  {{ number_format($row['gross_margin_percent'], 1) }}%
                </span>
              </td>
              <td class="text-end text-mono text-danger">
                Rp {{ number_format($row['waste_loss'], 0, ',', '.') }}
              </td>
              <td class="text-end text-mono fw-bold {{ $row['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                Rp {{ number_format($row['net_profit'], 0, ',', '.') }}
                <div class="text-muted-c" style="font-size: 0.72rem; font-weight: normal;">({{ number_format($row['net_margin_percent'], 1) }}%)</div>
              </td>
              <td class="text-center">
                @if($row['has_active_shift'])
                  <span class="pill pill-success d-inline-flex align-items-center gap-1">
                    <span class="status-dot live" style="width: 6px; height: 6px;"></span>
                    {{ $row['active_shift_name'] ?? 'Buka' }}
                  </span>
                @else
                  <span class="pill pill-secondary">Tutup</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center py-4 text-muted-c">
                Belum ada data transaksi pada periode yang dipilih.
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('multiBranchTrendChart');
    if (!ctx) return;

    const chartData = @json($trendChart);
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

    const gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.06)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    // Enhance datasets with smooth curves and point styles
    if (chartData.datasets) {
      chartData.datasets.forEach((ds) => {
        ds.tension = 0.35;
        ds.pointRadius = 3;
        ds.pointHoverRadius = 6;
      });
    }

    const chartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels: chartData.labels,
        datasets: chartData.datasets
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',
          intersect: false,
        },
        plugins: {
          legend: {
            position: 'top',
            labels: {
              color: textColor,
              font: { weight: 600, size: 12 },
              usePointStyle: true,
              padding: 16
            }
          },
          tooltip: {
            backgroundColor: isDark ? '#1e293b' : '#ffffff',
            titleColor: isDark ? '#ffffff' : '#0f172a',
            bodyColor: isDark ? '#cbd5e1' : '#334155',
            borderColor: isDark ? '#334155' : '#e2e8f0',
            borderWidth: 1,
            padding: 10,
            callbacks: {
              label: function (context) {
                let label = context.dataset.label || '';
                if (label) label += ': ';
                if (context.parsed.y !== null) {
                  label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                }
                return label;
              }
            }
          }
        },
        scales: {
          x: {
            grid: { color: gridColor },
            ticks: { color: textColor }
          },
          y: {
            grid: { color: gridColor },
            ticks: {
              color: textColor,
              callback: function (value) {
                return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
              }
            }
          }
        }
      }
    });

    // Auto update colors on theme toggle
    const observer = new MutationObserver(() => {
      const darkNow = document.documentElement.getAttribute('data-theme') === 'dark';
      const newGrid = darkNow ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.06)';
      const newText = darkNow ? '#94a3b8' : '#64748b';

      chartInstance.options.scales.x.grid.color = newGrid;
      chartInstance.options.scales.x.ticks.color = newText;
      chartInstance.options.scales.y.grid.color = newGrid;
      chartInstance.options.scales.y.ticks.color = newText;
      chartInstance.options.plugins.legend.labels.color = newText;
      chartInstance.options.plugins.tooltip.backgroundColor = darkNow ? '#1e293b' : '#ffffff';
      chartInstance.options.plugins.tooltip.titleColor = darkNow ? '#ffffff' : '#0f172a';
      chartInstance.options.plugins.tooltip.bodyColor = darkNow ? '#cbd5e1' : '#334155';
      chartInstance.options.plugins.tooltip.borderColor = darkNow ? '#334155' : '#e2e8f0';
      chartInstance.update();
    });
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
  });
</script>
@endpush
