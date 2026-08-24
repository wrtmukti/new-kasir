@extends('admin.layouts.app')

@section('title', 'Dashboard')

@php $activeMenu = 'dashboard' @endphp

@push('styles')
<style>
/* ==========================================================================
   CSS VARIABLES: CHART COLOR PALETTE (LIGHT & DARK MODE)
   ========================================================================== */
:root, [data-theme="dark"] {
  --chart-card-bg: #1E293B;        /* Slate 800 */
  --chart-card-border: #334155;    /* Slate 700 */
  --chart-title-color: #F8FAFC;    /* Slate 50 */
  --chart-grid-color: rgba(255, 255, 255, 0.08);
  --chart-text-color: #94A3B8;    /* Slate 400 */
  --chart-tooltip-bg: #1E293B;
  --chart-tooltip-text: #F8FAFC;
  --chart-tooltip-border: #334155;
  --chart-donut-border: #1E293B;   /* Match Card Background for seamless gaps */

  /* Kategori Colors (Dark Mode) */
  --color-makanan: #3B82F6;        /* Blue 500 */
  --color-minuman: #A855F7;        /* Purple 500 */
  --color-snack: #06B6D4;          /* Cyan 500 */
  --color-bundle: #34D399;         /* Emerald 400 */
  --color-other: #FBBF24;          /* Amber 400 */
}

[data-theme="light"] {
  --chart-card-bg: #FFFFFF;        /* Pure White */
  --chart-card-border: #E2E8F0;    /* Slate 200 */
  --chart-title-color: #0F172A;    /* Slate 900 */
  --chart-grid-color: #E2E8F0;    /* Slate 200 (1px solid clear grid line) */
  --chart-text-color: #475569;    /* Slate 600 */
  --chart-tooltip-bg: #FFFFFF;
  --chart-tooltip-text: #0F172A;
  --chart-tooltip-border: #CBD5E1;
  --chart-donut-border: #FFFFFF;   /* Match Card Background for white gaps */

  /* Kategori Colors (Light Mode) */
  --color-makanan: #2563EB;        /* Royal Blue */
  --color-minuman: #7C3AED;        /* Purple */
  --color-snack: #0891B2;          /* Cyan */
  --color-bundle: #10B981;         /* Emerald */
  --color-other: #F59E0B;          /* Amber */
}
</style>
@endpush

@section('content')
<main class="page-content">
  <!-- Header Page with Inline Filters -->
  <div class="page-header">
    <div>
      <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary);">
        <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard
      </h1>
      <div class="breadcrumb-trail">
        <a href="{{ route('admin.dashboard') }}" class="text-muted-c text-decoration-none">Beranda</a>
        <i class="bi bi-chevron-right text-muted-c" style="font-size:0.6rem;"></i>
        <span style="color: var(--text-primary); font-weight: 600;">Dashboard</span>
      </div>
    </div>

    <!-- Inline Filter Form -->
    <form action="{{ route('admin.keuangan.menu-analytics.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
      <select name="month" class="form-select-modern" style="width:auto; min-width:130px;">
        @for($m = 1; $m <= 12; $m++)
          <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
          </option>
        @endfor
      </select>
      <select name="year" class="form-select-modern" style="width:auto; min-width:95px;">
        @for($y = date('Y'); $y >= 2024; $y--)
          <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endfor
      </select>
      <button type="submit" class="btn btn-primary-grad btn-sm px-3">
        <i class="bi bi-filter me-1"></i>Filter Analitik
      </button>
    </form>
  </div>

  <!-- 4 Top KPI Stat Cards (Matching laravel-admin stat-card style) -->
  <div class="row g-3 mb-3">
    <div class="col-6 col-xl-3">
      <div class="card card-glow h-100">
        <div class="card-inner card-body stat-card">
          <div class="stat-icon" style="background:rgba(59,130,246,0.12); color:var(--color-makanan);">
            <i class="bi bi-box-seam-fill"></i>
          </div>
          <div class="stat-value">{{ number_format($totalItemsSold) }} <span style="font-size:0.9rem; font-weight:normal; color:var(--chart-text-color);">Pcs</span></div>
          <div class="stat-label">Total Pcs Terjual ({{ $totalPaidTransactions }} Transaksi)</div>
        </div>
      </div>
    </div>

    <div class="col-6 col-xl-3">
      <div class="card card-glow h-100">
        <div class="card-inner card-body stat-card">
          <div class="stat-icon" style="background:rgba(16,185,129,0.12); color:var(--color-bundle);">
            <i class="bi bi-wallet2"></i>
          </div>
          <div class="stat-value" style="color:var(--color-bundle);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
          <div class="stat-label">Total Omzet Penjualan</div>
        </div>
      </div>
    </div>

    <div class="col-6 col-xl-3">
      <div class="card card-glow h-100">
        <div class="card-inner card-body stat-card">
          <div class="stat-icon" style="background:var(--danger-bg); color:var(--danger);">
            <i class="bi bi-fire"></i>
          </div>
          <div class="stat-value text-truncate" style="font-size:1.2rem; color:var(--danger);" title="{{ $topProduct1['name'] ?? '-' }}">
            {{ $topProduct1['name'] ?? '-' }}
          </div>
          <div class="stat-label">Menu Terlaris #1 🔥 ({{ isset($topProduct1['qty_sold']) ? number_format($topProduct1['qty_sold']) : 0 }} Pcs)</div>
        </div>
      </div>
    </div>

    <div class="col-6 col-xl-3">
      <div class="card card-glow h-100">
        <div class="card-inner card-body stat-card">
          <div class="stat-icon" style="background:rgba(6,182,212,0.12); color:var(--color-snack);">
            <i class="bi bi-pie-chart-fill"></i>
          </div>
          <div class="stat-value text-truncate" style="font-size:1.2rem; color:var(--color-snack);" title="{{ $topCategoryName }}">
            {{ $topCategoryName }}
          </div>
          <div class="stat-label">Kategori Terfavorit 🏷️</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Section Grid -->
  <div class="row g-3 mb-3">
    <!-- Bar Chart: Top 10 Products & Bundles -->
    <div class="col-lg-7">
      <div class="card h-100">
        <div class="card-header-flex">
          <div>
            <h6 style="color: var(--chart-title-color);"><i class="bi bi-bar-chart-line-fill me-2" style="color:var(--color-makanan);"></i>Top 10 Menu & Bundle Terlaris</h6>
            <span class="text-muted-c" style="font-size:0.78rem;">Qty Terjual (Pcs) Periode {{ \Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}</span>
          </div>
          <span class="status-dot live"></span>
        </div>
        <div class="card-body" style="height:320px; position:relative;">
          <canvas id="barChartTopProducts"></canvas>
        </div>
      </div>
    </div>

    <!-- Doughnut Chart: Category Contribution -->
    <div class="col-lg-5">
      <div class="card h-100">
        <div class="card-header-flex">
          <div>
            <h6 style="color: var(--chart-title-color);"><i class="bi bi-pie-chart-fill me-2" style="color:var(--color-snack);"></i>Kontribusi Omzet per Kategori</h6>
            <span class="text-muted-c" style="font-size:0.78rem;">Persentase kontribusi omzet</span>
          </div>
        </div>
        <div class="card-body" style="height:320px; position:relative;">
          <canvas id="donutChartCategory"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Daily Sales Trend Line Chart -->
  <div class="card mb-3">
    <div class="card-header-flex">
      <div>
        <h6 style="color: var(--chart-title-color);"><i class="bi bi-graph-up-arrow me-2" style="color:var(--color-bundle);"></i>Grafik Tren Penjualan Harian</h6>
        <span class="text-muted-c" style="font-size:0.78rem;">Omzet Harian (Rp) Periode {{ \Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}</span>
      </div>
      <span class="chip-tag">Live Omzet</span>
    </div>
    <div class="card-body" style="height:280px; position:relative;">
      <canvas id="lineChartDailyTrend"></canvas>
    </div>
  </div>

  <!-- Table Ranking All Products & Bundles -->
  <div class="card mb-4">
    <div class="card-header-flex">
      <h6 style="color: var(--chart-title-color);">Matriks Peringkat Performa Seluruh Menu & Bundle</h6>
      <span class="chip-tag">{{ count($rankedItems) }} item terjual</span>
    </div>

    <div class="px-3 pt-3 pb-2">
      <div class="input-group">
        <span class="input-group-text bg-transparent border-end-0 text-muted-c" style="border-color: var(--border-subtle);"><i class="bi bi-search"></i></span>
        <input type="text" id="searchTableInput" class="form-control-modern border-start-0 ps-0" placeholder="Cari nama menu, paket bundle, atau kategori...">
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table-modern" id="rankingsTable">
          <thead>
            <tr>
              <th class="ps-3 text-center" style="width: 70px;">Rank</th>
              <th>Nama Menu / Paket Bundle</th>
              <th>Kategori</th>
              <th class="text-end">Harga Satuan</th>
              <th class="text-center">QTY Terjual</th>
              <th class="text-end">Total Omzet Penjualan</th>
              <th class="text-center">Kontribusi (%)</th>
              <th class="text-center pe-3">Status Performa</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rankedItems as $row)
            <tr>
              <td class="ps-3 text-center fw-bold">
                @if($row['rank'] == 1)
                  <span class="badge rounded-circle p-2" style="background: rgba(251, 191, 36, 0.2); color: #fbbf24; width:30px; height:30px;">🥇</span>
                @elseif($row['rank'] == 2)
                  <span class="badge rounded-circle p-2" style="background: rgba(203, 213, 225, 0.2); color: #cbd5e1; width:30px; height:30px;">🥈</span>
                @elseif($row['rank'] == 3)
                  <span class="badge rounded-circle p-2" style="background: rgba(217, 119, 6, 0.2); color: #d97706; width:30px; height:30px;">🥉</span>
                @else
                  <span class="text-muted-c">#{{ $row['rank'] }}</span>
                @endif
              </td>
              <td>
                <div class="fw-bold" style="color: var(--text-primary);">{{ $row['name'] }}</div>
                @if($row['type'] == 'bundle')
                  <small class="text-info"><i class="bi bi-box2-heart-fill me-1"></i>Paket Combo / Bundle</small>
                @else
                  <small class="text-muted-c"><i class="bi bi-tag-fill me-1"></i>Menu Porsi Standar</small>
                @endif
              </td>
              <td><span class="chip-tag">{{ $row['category'] }}</span></td>
              <td class="text-end" style="color: var(--text-secondary);">Rp {{ number_format($row['unit_price'], 0, ',', '.') }}</td>
              <td class="text-center fw-bold">
                <span class="chip-tag px-2 py-1" style="background: rgba(59, 130, 246, 0.12); color: var(--color-makanan); font-size:0.85rem;">
                  {{ number_format($row['qty_sold']) }} Pcs
                </span>
              </td>
              <td class="text-end fw-bold text-success">Rp {{ number_format($row['total_omzet'], 0, ',', '.') }}</td>
              <td class="text-center fw-semibold" style="color: var(--text-primary);">
                {{ number_format($row['omzet_share'], 1) }}%
              </td>
              <td class="text-center pe-3">
                <span class="badge px-2 py-1" style="{{ $row['badge_style'] }}">
                  {{ $row['badge_label'] }}
                </span>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center py-4 text-muted-c">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada data penjualan menu pada bulan terpilih.
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // 1. Data Inject from PHP to JS
  const rawBarLabels = @json($chartBarLabels);
  const barQtyData = @json($chartBarQtyData);

  const categoryLabels = @json($chartCategoryLabels);
  const categoryData = @json($chartCategoryData);

  const lineLabels = @json($dailyTrendLabels);
  const lineOmzetData = @json($dailyTrendOmzet);

  // Truncate long labels for Bar Chart to prevent collision
  const barLabels = rawBarLabels.map(function(label) {
    return label.length > 14 ? label.substring(0, 12) + '...' : label;
  });

  // Helper membaca CSS Variables secara dinamis
  function getCssVar(varName, fallback) {
    const val = getComputedStyle(document.documentElement).getPropertyValue(varName).trim();
    return val || fallback;
  }

  // Get Dynamic Theme Colors based on user color palette spec
  function getChartThemeConfig() {
    const isLight = document.documentElement.getAttribute('data-theme') === 'light';
    return {
      gridColor: getCssVar('--chart-grid-color', isLight ? '#E2E8F0' : 'rgba(255, 255, 255, 0.08)'),
      textColor: getCssVar('--chart-text-color', isLight ? '#475569' : '#94A3B8'),
      tooltipBg: getCssVar('--chart-tooltip-bg', isLight ? '#FFFFFF' : '#1E293B'),
      tooltipText: getCssVar('--chart-tooltip-text', isLight ? '#0F172A' : '#F8FAFC'),
      tooltipBorder: getCssVar('--chart-tooltip-border', isLight ? '#CBD5E1' : '#334155'),
      donutBorder: getCssVar('--chart-donut-border', isLight ? '#FFFFFF' : '#1E293B'),
      palette: [
        getCssVar('--color-makanan', isLight ? '#2563EB' : '#3B82F6'),
        getCssVar('--color-minuman', isLight ? '#7C3AED' : '#A855F7'),
        getCssVar('--color-snack', isLight ? '#0891B2' : '#06B6D4'),
        getCssVar('--color-bundle', isLight ? '#10B981' : '#34D399'),
        getCssVar('--color-other', isLight ? '#F59E0B' : '#FBBF24'),
      ]
    };
  }

  let chartTheme = getChartThemeConfig();
  let chartBarInstance = null;
  let chartDonutInstance = null;
  let chartLineInstance = null;

  // Render All Charts
  function renderAllCharts() {
    chartTheme = getChartThemeConfig();

    // 2. Bar Chart Setup (Sumbu Y & Grid dipertegas #E2E8F0 / rgba(255,255,255,0.08))
    const ctxBar = document.getElementById('barChartTopProducts');
    if (ctxBar && barLabels.length > 0) {
      if (chartBarInstance) chartBarInstance.destroy();
      chartBarInstance = new Chart(ctxBar, {
        type: 'bar',
        data: {
          labels: barLabels,
          datasets: [{
            label: 'Qty Terjual (Pcs)',
            data: barQtyData,
            backgroundColor: chartTheme.palette.slice(0, barQtyData.length),
            borderColor: 'transparent',
            borderRadius: 6,
            maxBarThickness: 28,
            categoryPercentage: 0.75,
            barPercentage: 0.7
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: chartTheme.tooltipBg,
              titleColor: chartTheme.tooltipText,
              bodyColor: chartTheme.tooltipText,
              borderColor: chartTheme.tooltipBorder,
              borderWidth: 1,
              padding: 10,
              displayColors: false,
              callbacks: {
                title: function(context) {
                  const idx = context[0].dataIndex;
                  return rawBarLabels[idx] || context[0].label;
                },
                label: function(context) {
                  return 'Terjual: ' + context.parsed.y.toLocaleString('id-ID') + ' Pcs';
                }
              }
            }
          },
          scales: {
            x: {
              grid: { display: false },
              ticks: {
                color: chartTheme.textColor, // Neutral slate (#475569 / #94A3B8)
                font: { size: 11, weight: '500' },
                maxRotation: 30,
                minRotation: 0
              }
            },
            y: {
              grid: {
                color: chartTheme.gridColor, // #E2E8F0 di Light Mode & rgba(255,255,255,0.08) di Dark Mode
                borderDash: [3, 3]
              },
              ticks: {
                color: chartTheme.textColor, // Neutral slate (#475569 / #94A3B8)
                font: { size: 11, weight: '500' }
              }
            }
          }
        }
      });
    }

    // 3. Doughnut Chart Setup (borderColor = Warna Card Background (#FFFFFF / #1E293B), borderWidth: 3)
    const ctxDonut = document.getElementById('donutChartCategory');
    if (ctxDonut && categoryLabels.length > 0) {
      if (chartDonutInstance) chartDonutInstance.destroy();
      chartDonutInstance = new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
          labels: categoryLabels,
          datasets: [{
            data: categoryData,
            backgroundColor: chartTheme.palette.slice(0, categoryLabels.length),
            borderColor: chartTheme.donutBorder, // Warna background kartu (#FFFFFF di Light / #1E293B di Dark)
            borderWidth: 3,                      // Gap pemisah halus tanpa border hitam
            hoverOffset: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '72%',
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                color: chartTheme.textColor,
                font: { size: 11, weight: '500' },
                padding: 14,
                usePointStyle: true,
                pointStyle: 'rectRounded'
              }
            },
            tooltip: {
              backgroundColor: chartTheme.tooltipBg,
              titleColor: chartTheme.tooltipText,
              bodyColor: chartTheme.tooltipText,
              borderColor: chartTheme.tooltipBorder,
              borderWidth: 1,
              padding: 10,
              callbacks: {
                label: function(context) {
                  const val = context.parsed;
                  return ' Omzet: Rp ' + val.toLocaleString('id-ID');
                }
              }
            }
          }
        }
      });
    }

    // 4. Line Chart Setup (Daily Sales Trend)
    const ctxLine = document.getElementById('lineChartDailyTrend');
    if (ctxLine && lineLabels.length > 0) {
      if (chartLineInstance) chartLineInstance.destroy();
      const chartContext = ctxLine.getContext('2d');
      const gradientFill = chartContext.createLinearGradient(0, 0, 0, 260);
      gradientFill.addColorStop(0, 'rgba(16, 185, 129, 0.22)');
      gradientFill.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

      chartLineInstance = new Chart(ctxLine, {
        type: 'line',
        data: {
          labels: lineLabels,
          datasets: [{
            label: 'Omzet Harian (Rp)',
            data: lineOmzetData,
            borderColor: '#10b981',
            backgroundColor: gradientFill,
            fill: true,
            tension: 0.4,
            borderWidth: 2.5,
            pointBackgroundColor: '#10b981',
            pointBorderColor: chartTheme.donutBorder,
            pointBorderWidth: 2,
            pointRadius: 3,
            pointHoverRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: chartTheme.tooltipBg,
              titleColor: chartTheme.tooltipText,
              bodyColor: chartTheme.tooltipText,
              borderColor: chartTheme.tooltipBorder,
              borderWidth: 1,
              padding: 10,
              callbacks: {
                label: function(context) {
                  return ' Omzet: Rp ' + context.parsed.y.toLocaleString('id-ID');
                }
              }
            }
          },
          scales: {
            x: {
              grid: { display: false },
              ticks: { color: chartTheme.textColor, font: { size: 11, weight: '500' } }
            },
            y: {
              grid: {
                color: chartTheme.gridColor,
                borderDash: [3, 3]
              },
              ticks: {
                color: chartTheme.textColor,
                font: { size: 11, weight: '500' },
                callback: function(val) { return 'Rp ' + (val/1000) + 'k'; }
              }
            }
          }
        }
      });
    }
  }

  // Initial Render
  renderAllCharts();

  // Dynamic Theme Switcher Listener (MutationObserver on data-theme attribute)
  const themeObserver = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
        renderAllCharts();
      }
    });
  });

  themeObserver.observe(document.documentElement, { attributes: true });

  // 5. Table Live Search (Vanilla JS)
  const searchInput = document.getElementById('searchTableInput');
  const tableBody = document.querySelector('#rankingsTable tbody');

  if (searchInput && tableBody) {
    searchInput.addEventListener('keyup', function() {
      const term = this.value.toLowerCase().trim();
      const rows = tableBody.querySelectorAll('tr');

      rows.forEach(function(row) {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
      });
    });
  }
});
</script>
@endpush
