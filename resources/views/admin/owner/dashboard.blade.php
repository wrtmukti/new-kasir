@extends('admin.layouts.app')

@section('title', 'Konsolidasi Multi-Cabang — Portal Owner')

@php $activeMenu = 'owner-dashboard' @endphp

@push('styles')
<style>
  .owner-hero-card {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.14) 0%, rgba(59, 130, 246, 0.10) 100%);
    border: 1.5px solid rgba(245, 158, 11, 0.35);
    border-radius: 18px;
    padding: 1.5rem 1.75rem;
    margin-bottom: 1.5rem;
  }
  .filter-panel-card {
    background: var(--bg-surface, #1A1E27);
    border: 1px solid var(--border-subtle, #2F3748);
    border-radius: 14px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
  }
  .chart-card-custom {
    background: var(--bg-surface, #1A1E27);
    border: 1px solid var(--border-subtle, #2F3748);
    border-radius: 16px;
    padding: 1.5rem;
  }
</style>
@endpush

@section('content')
<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>👑 Konsolidasi Eksekutif Multi-Cabang</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Portal Owner</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Konsolidasi Semua Cabang</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('admin.owner.financial') }}" class="btn btn-outline-soft">
      <i class="bi bi-pie-chart me-1"></i> Laba Rugi Holding
    </a>
    <a href="{{ route('admin.owner.financial.export', ['start_date' => $startDate, 'end_date' => $endDate, 'outlet_ids' => $selectedOutletIds]) }}" class="btn btn-primary">
      <i class="bi bi-file-earmark-excel me-1"></i> Export Excel Konsolidasi
    </a>
  </div>
</div>

<!-- HERO BANNER -->
<div class="owner-hero-card">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <div class="d-flex align-items-center gap-2 mb-1">
        <span class="badge badge-warning px-2.5 py-1 fw-bold">PORTAL OWNER EXECUTIVE</span>
        <span class="text-muted-c small"><i class="bi bi-buildings text-warning me-1"></i>Menganalisis <strong>{{ $kpis['outlet_count_analyzed'] }}</strong> Cabang Aktif</span>
      </div>
      <h3 class="fw-bold mb-1" style="color: var(--text-primary);">Ringkasan Performa Seluruh Jaringan Restoran</h3>
      <p class="text-secondary-c mb-0 small">
        Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong>
      </p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <div class="text-end d-none d-md-block">
        <div class="text-muted-c small">Total Transaksi</div>
        <h4 class="fw-bold mb-0 text-primary">{{ number_format($kpis['total_orders_count']) }} Struk</h4>
      </div>
    </div>
  </div>
</div>

<!-- FILTER PANEL -->
<div class="filter-panel-card">
  <form method="GET" action="{{ route('admin.owner.dashboard') }}" class="row g-2 align-items-end">
    <div class="col-md-3">
      <label class="form-label small fw-bold text-secondary-c">Filter Cabang / Outlet:</label>
      <select name="outlet_ids[]" class="form-select form-select-sm" style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
        <option value="">-- Semua Cabang (Konsolidasi) --</option>
        @foreach($activeOutlets as $ot)
          <option value="{{ $ot->outlet_id }}" {{ in_array($ot->outlet_id, $selectedOutletIds) ? 'selected' : '' }}>
            {{ $ot->outlet_name }} ({{ $ot->outlet_branch ?? 'Cabang' }})
          </option>
        @endforeach
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label small fw-bold text-secondary-c">Dari Tanggal:</label>
      <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm" style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
    </div>

    <div class="col-md-3">
      <label class="form-label small fw-bold text-secondary-c">Sampai Tanggal:</label>
      <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm" style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
    </div>

    <div class="col-md-3 d-flex gap-2">
      <button type="submit" class="btn btn-primary btn-sm flex-fill">
        <i class="bi bi-funnel-fill me-1"></i> Terapkan
      </button>
      <a href="{{ route('admin.owner.dashboard') }}" class="btn btn-outline-soft btn-sm">
        <i class="bi bi-arrow-counterclockwise"></i> Reset
      </a>
    </div>
  </form>
</div>

<!-- 4 KARTU KPI KONSOLIDASI (NEXORA GLOW) -->
<div class="row g-3 mb-4">
  <!-- KPI 1: Total Omzet Konsolidasi -->
  <div class="col-xl-3 col-md-6">
    <div class="stat-card card-glow" style="--card-glow-color: rgba(59, 130, 246, 0.4);">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-secondary-c small fw-bold text-uppercase">Total Omzet Gabungan</span>
        <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
          <i class="bi bi-currency-dollar fs-5"></i>
        </div>
      </div>
      <h3 class="fw-bold mb-1" style="color: var(--text-primary);">Rp {{ number_format($kpis['total_revenue'], 0, ',', '.') }}</h3>
      <div class="text-muted-c small">
        <span class="text-success"><i class="bi bi-receipt me-1"></i>{{ number_format($kpis['total_orders_count']) }}</span> transaksi sukses
      </div>
    </div>
  </div>

  <!-- KPI 2: Total Laba Bersih Holding -->
  <div class="col-xl-3 col-md-6">
    <div class="stat-card card-glow" style="--card-glow-color: rgba(16, 185, 129, 0.4);">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-secondary-c small fw-bold text-uppercase">Laba Bersih Konsolidasi</span>
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
          <i class="bi bi-graph-up-arrow fs-5"></i>
        </div>
      </div>
      <h3 class="fw-bold mb-1 text-success">Rp {{ number_format($kpis['net_profit'], 0, ',', '.') }}</h3>
      <div class="text-muted-c small">
        Margin Bersih: <strong class="text-success">{{ number_format($kpis['net_margin_percent'], 1) }}%</strong>
      </div>
    </div>
  </div>

  <!-- KPI 3: Setoran Brankas Kasir -->
  <div class="col-xl-3 col-md-6">
    <div class="stat-card card-glow" style="--card-glow-color: rgba(245, 158, 11, 0.4);">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-secondary-c small fw-bold text-uppercase">Kas Setoran Brankas</span>
        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
          <i class="bi bi-safe fs-5"></i>
        </div>
      </div>
      <h3 class="fw-bold mb-1 text-warning">Rp {{ number_format($kpis['total_safe_deposit'], 0, ',', '.') }}</h3>
      <div class="text-muted-c small">
        <i class="bi bi-wallet2 text-warning me-1"></i>Kas fisik disetor kasir cabang
      </div>
    </div>
  </div>

  <!-- KPI 4: Hutang PO Supplier Tempo -->
  <div class="col-xl-3 col-md-6">
    <div class="stat-card card-glow" style="--card-glow-color: rgba(239, 68, 68, 0.4);">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-secondary-c small fw-bold text-uppercase">Hutang PO Supplier Tempo</span>
        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
          <i class="bi bi-clock-history fs-5"></i>
        </div>
      </div>
      <h3 class="fw-bold mb-1 text-danger">Rp {{ number_format($kpis['total_po_unpaid'], 0, ',', '.') }}</h3>
      <div class="text-muted-c small">
        <span class="text-danger fw-bold"><i class="bi bi-exclamation-circle me-1"></i>Tagihan Belum Lunas</span>
      </div>
    </div>
  </div>
</div>

<!-- MULTI-BRANCH TREND CHART -->
<div class="chart-card-custom mb-4">
  <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
      <h5 class="fw-bold mb-1" style="color: var(--text-primary);"><i class="bi bi-graph-up me-2 text-primary"></i>Tren Omzet Komparatif Antar Cabang</h5>
      <span class="text-muted-c small">Perbandingan volume penjualan harian di setiap outlet</span>
    </div>
    <span class="badge badge-soft-primary">Multi-Series Real-Time</span>
  </div>
  <div style="height: 320px; position: relative;">
    <canvas id="multiBranchTrendChart"></canvas>
  </div>
</div>

<!-- LEADERBOARD CABANG -->
<div class="card" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 16px;">
  <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3" style="border-bottom: 1px solid var(--border-subtle);">
    <div>
      <h5 class="fw-bold mb-0" style="color: var(--text-primary);"><i class="bi bi-trophy-fill text-warning me-2"></i>Peringkat &amp; Performa Outlet (Leaderboard)</h5>
      <span class="text-muted-c small">Diurutkan berdasarkan total omzet penjualan tertinggi</span>
    </div>
    <a href="{{ route('admin.owner.benchmark') }}" class="btn btn-sm btn-outline-soft">
      Lihat Analitik Benchmark <i class="bi bi-arrow-right ms-1"></i>
    </a>
  </div>

  <div class="table-responsive">
    <table class="table table-custom mb-0">
      <thead>
        <tr>
          <th style="width: 50px;">Rank</th>
          <th>Nama Cabang</th>
          <th class="text-end">Omzet</th>
          <th class="text-center">Pesanan</th>
          <th class="text-end">Modal HPP</th>
          <th class="text-center">Gross Margin</th>
          <th class="text-end">Kerugian Waste</th>
          <th class="text-end">Laba Bersih</th>
          <th class="text-center">Status Shift</th>
        </tr>
      </thead>
      <tbody>
        @forelse($leaderboard as $index => $row)
          <tr>
            <td>
              @if($index === 0)
                <span class="badge badge-warning fw-bold">🥇 1</span>
              @elseif($index === 1)
                <span class="badge badge-secondary fw-bold">🥈 2</span>
              @elseif($index === 2)
                <span class="badge badge-danger fw-bold" style="background: #cd7f32; color: #fff;">🥉 3</span>
              @else
                <span class="text-muted-c fw-bold ms-2">{{ $index + 1 }}</span>
              @endif
            </td>
            <td>
              <strong style="color: var(--text-primary);">{{ $row['outlet_name'] }}</strong>
              <div class="text-muted-c small">{{ $row['outlet_code'] }} &bull; {{ $row['outlet_branch'] }}</div>
            </td>
            <td class="text-end fw-bold" style="color: var(--text-primary);">
              Rp {{ number_format($row['revenue'], 0, ',', '.') }}
            </td>
            <td class="text-center">
              <span class="badge badge-soft-primary">{{ number_format($row['orders_count']) }}</span>
            </td>
            <td class="text-end text-secondary-c">
              Rp {{ number_format($row['cogs'], 0, ',', '.') }}
            </td>
            <td class="text-center">
              <span class="badge {{ $row['gross_margin_percent'] >= 60 ? 'badge-success' : ($row['gross_margin_percent'] >= 40 ? 'badge-info' : 'badge-warning') }}">
                {{ number_format($row['gross_margin_percent'], 1) }}%
              </span>
            </td>
            <td class="text-end text-danger">
              Rp {{ number_format($row['waste_loss'], 0, ',', '.') }}
            </td>
            <td class="text-end fw-bold {{ $row['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
              Rp {{ number_format($row['net_profit'], 0, ',', '.') }}
              <div class="small text-muted-c">({{ number_format($row['net_margin_percent'], 1) }}%)</div>
            </td>
            <td class="text-center">
              @if($row['has_active_shift'])
                <span class="badge badge-success"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>{{ $row['active_shift_name'] ?? 'Buka' }}</span>
              @else
                <span class="badge badge-secondary">Tutup</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center py-4 text-muted-c">
              Belum ada data cabang pada periode yang dipilih.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
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

    const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    new Chart(ctx, {
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
              font: { weight: 600, size: 12 }
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
  });
</script>
@endpush
