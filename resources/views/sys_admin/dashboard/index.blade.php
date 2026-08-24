@extends('sys_admin.layouts.app')

@section('title', 'Executive Platform Dashboard')

@section('content')
<div class="container-fluid p-0">

  {{-- Top Welcome & Filter Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <h4 class="fw-bold mb-1" style="font-size:1.45rem; color:var(--text-primary);">
        <i class="bi bi-speedometer2 me-2 text-primary"></i>Executive SaaS Platform Overview
      </h4>
      <p class="text-muted-c mb-0" style="font-size:0.85rem;">
        Monitoring real-time multi-client POS platform, performa finansial SaaS, dan kesehatan database terisolasi.
      </p>
    </div>

    {{-- Time Range Filters --}}
    <div class="d-flex align-items-center gap-1.5 p-1 rounded-3" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
      <button type="button" class="btn btn-sm btn-range {{ $range === 'today' ? 'btn-primary-grad text-white' : 'btn-ghost text-muted-c' }} rounded-2 px-2.5 py-1" data-range="today">Hari Ini</button>
      <button type="button" class="btn btn-sm btn-range {{ $range === '7days' ? 'btn-primary-grad text-white' : 'btn-ghost text-muted-c' }} rounded-2 px-2.5 py-1" data-range="7days">7 Hari</button>
      <button type="button" class="btn btn-sm btn-range {{ $range === '30days' ? 'btn-primary-grad text-white' : 'btn-ghost text-muted-c' }} rounded-2 px-2.5 py-1" data-range="30days">30 Hari</button>
      <button type="button" class="btn btn-sm btn-range {{ $range === 'this_month' ? 'btn-primary-grad text-white' : 'btn-ghost text-muted-c' }} rounded-2 px-2.5 py-1" data-range="this_month">Bulan Ini</button>
      <button type="button" class="btn btn-sm btn-range {{ $range === 'all_time' ? 'btn-primary-grad text-white' : 'btn-ghost text-muted-c' }} rounded-2 px-2.5 py-1" data-range="all_time">Semua Data</button>
    </div>
  </div>

  {{-- Expiring Subscriptions Alert Banner (if any) --}}
  @if($kpi['expiring_soon_count'] > 0)
    <div class="alert alert-warning border-0 rounded-4 shadow-sm p-3 mb-4 d-flex align-items-center justify-content-between gap-3" style="background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3) !important;">
      <div class="d-flex align-items-center gap-2.5">
        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.2); color: #d97706;">
          <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        </div>
        <div>
          <div class="fw-bold" style="color: #d97706; font-size:0.92rem;">
            Peringatan: Terdapat {{ $kpi['expiring_soon_count'] }} Client Mendekati Masa Kadaluarsa (&le; 7 Hari)!
          </div>
          <small class="text-muted-c" style="font-size:0.8rem;">
            Segera follow-up PIC client untuk perpanjangan lisensi berlangganan sebelum akun ter-suspend otomatis.
          </small>
        </div>
      </div>
      <a href="{{ route('sys_admin.subscriptions.index', ['status' => 'active']) }}" class="btn btn-sm btn-warning fw-semibold rounded-3 px-3 py-1.5 flex-shrink-0">
        Lihat Client Terkait <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  @endif

  {{-- 4 Primary Bento KPI Cards --}}
  <div class="row g-3 mb-4">
    {{-- Card 1: Total & Active Clients --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card p-3 rounded-4 border-0 shadow-sm h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.78rem;">TOTAL CLIENTS</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
            <i class="bi bi-buildings fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-1" id="kpiTotalClients" style="font-size:1.75rem; color:var(--text-primary);">{{ $kpi['total_clients'] }}</h3>
        <div class="d-flex align-items-center gap-2" style="font-size:0.75rem;">
          <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5 rounded-pill">
            <i class="bi bi-check-circle-fill me-0.5"></i>{{ $kpi['active_clients'] }} Aktif
          </span>
          <span class="text-muted-c">&bull; {{ $kpi['trial_clients'] }} Trial</span>
        </div>
      </div>
    </div>

    {{-- Card 2: Financial Metrics (MRR) --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card p-3 rounded-4 border-0 shadow-sm h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.78rem;">MONTHLY RECURRING REVENUE</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(16, 185, 129, 0.15); color: #10b981;">
            <i class="bi bi-cash-stack fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-1 text-success" id="kpiMrr" style="font-size:1.75rem;">Rp {{ number_format($kpi['mrr'], 0, ',', '.') }}</h3>
        <div class="text-muted-c" style="font-size:0.75rem;">
          Proyeksi ARR: <strong class="text-primary">Rp {{ number_format($kpi['arr'], 0, ',', '.') }}</strong>/thn
        </div>
      </div>
    </div>

    {{-- Card 3: Database & Infra Health --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card p-3 rounded-4 border-0 shadow-sm h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.78rem;">INFRASTRUCTURE HEALTH</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
            <i class="bi bi-hdd-network fs-5"></i>
          </div>
        </div>
        <div class="d-flex align-items-baseline gap-2 mb-1">
          <h3 class="fw-bold mb-0 text-primary" id="kpiLatency" style="font-size:1.75rem;">{{ $kpi['avg_latency'] }} ms</h3>
          <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size:0.7rem;">Healthy</span>
        </div>
        <div class="text-muted-c" style="font-size:0.75rem;">
          {{ $kpi['healthy_databases'] }} dari {{ $kpi['total_databases'] }} DB Terhubung
        </div>
      </div>
    </div>

    {{-- Card 4: Subscriptions & Central DB --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card p-3 rounded-4 border-0 shadow-sm h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.78rem;">CENTRAL DB STATUS</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(14, 165, 233, 0.15); color: #0ea5e9;">
            <i class="bi bi-shield-check fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-1 text-info" style="font-size:1.75rem;">{{ $kpi['central_health'] }}</h3>
        <div class="text-muted-c" style="font-size:0.75rem;">
          Ping Latency: <strong>{{ $kpi['central_latency'] }} ms</strong>
        </div>
      </div>
    </div>
  </div>

  {{-- 2 Interactive Charts Row --}}
  <div class="row g-4 mb-4">
    {{-- Chart 1: Acquisition Growth Trend --}}
    <div class="col-lg-8">
      <div class="card rounded-4 border-0 shadow-sm p-4 h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <h6 class="fw-bold mb-0" style="color:var(--text-primary);">
              <i class="bi bi-graph-up-arrow text-primary me-2"></i>Tren Akuisisi Klien Baru
            </h6>
            <small class="text-muted-c" style="font-size:0.78rem;">Jumlah pendaftaran client per bulan (6 Bulan Terakhir)</small>
          </div>
        </div>
        <div style="height: 260px; position: relative;">
          <canvas id="chartGrowth"></canvas>
        </div>
      </div>
    </div>

    {{-- Chart 2: Plan Distribution --}}
    <div class="col-lg-4">
      <div class="card rounded-4 border-0 shadow-sm p-4 h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <h6 class="fw-bold mb-0" style="color:var(--text-primary);">
              <i class="bi bi-pie-chart text-success me-2"></i>Distribusi Paket SaaS
            </h6>
            <small class="text-muted-c" style="font-size:0.78rem;">Proporsi client berdasarkan paket langganan</small>
          </div>
        </div>
        <div style="height: 260px; position: relative;" class="d-flex align-items-center justify-content-center">
          <canvas id="chartPlans"></canvas>
        </div>
      </div>
    </div>
  </div>

  {{-- Bottom Grid: Recent Clients & Live Audit Activity --}}
  <div class="row g-4">
    {{-- Recent Clients --}}
    <div class="col-lg-7">
      <div class="card rounded-4 border-0 shadow-sm h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="card-header bg-transparent border-0 p-4 pb-2 d-flex align-items-center justify-content-between">
          <h6 class="fw-bold mb-0" style="color:var(--text-primary);">
            <i class="bi bi-building-add text-primary me-2"></i>Klien Baru Mendaftar (Recent Onboarding)
          </h6>
          <a href="{{ route('sys_admin.clients.index') }}" class="btn btn-ghost btn-sm text-primary p-0" style="font-size:0.82rem;">
            Lihat Semua <i class="bi bi-arrow-right"></i>
          </a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size:0.85rem;">
              <thead style="background: var(--bg-elevated-2); color: var(--text-secondary);">
                <tr>
                  <th class="ps-4">Client ID & Nama</th>
                  <th>Owner</th>
                  <th>Paket</th>
                  <th>Status</th>
                  <th class="pe-4 text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentClients as $rc)
                  <tr style="border-bottom: 1px solid var(--border-subtle);">
                    <td class="ps-4">
                      <div class="fw-semibold text-primary">{{ $rc->client_name }}</div>
                      <small class="text-muted-c">{{ $rc->client_id }}</small>
                    </td>
                    <td>
                      <div>{{ $rc->owner_name }}</div>
                      <small class="text-muted-c">{{ $rc->owner_email }}</small>
                    </td>
                    <td>
                      <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5">
                        {{ $rc->activeSubscription?->plan?->plan_name ?? 'Trial' }}
                      </span>
                    </td>
                    <td>
                      <span class="badge {{ $rc->status === 'active' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill px-2 py-0.5">
                        {{ ucfirst($rc->status) }}
                      </span>
                    </td>
                    <td class="pe-4 text-end">
                      <a href="{{ route('sys_admin.clients.show', $rc->client_id) }}" class="btn btn-sm btn-outline-primary rounded-2 px-2 py-0.5">
                        Detail
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-4 text-muted-c">Belum ada client terdaftar.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Live Audit Feed --}}
    <div class="col-lg-5">
      <div class="card rounded-4 border-0 shadow-sm p-4 h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="fw-bold mb-0" style="color:var(--text-primary);">
            <i class="bi bi-activity text-danger me-2"></i>Live Audit Log Stream
          </h6>
          <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-0.5">Real-time</span>
        </div>

        <div class="d-flex flex-column gap-3 overflow-auto scroll-thin" style="max-height: 280px;">
          @forelse($recentAuditLogs as $log)
            <div class="p-2.5 rounded-3 d-flex align-items-start gap-2.5" style="background: var(--bg-elevated-2); border: 1px solid var(--border-subtle);">
              <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width:28px; height:28px; background: rgba(59, 130, 246, 0.15); color: #3b82f6; font-size:0.75rem;">
                <i class="bi bi-shield-check"></i>
              </div>
              <div class="w-100">
                <div class="d-flex align-items-center justify-content-between">
                  <span class="fw-semibold text-primary" style="font-size:0.82rem;">{{ $log->actor_name ?? 'System' }}</span>
                  <small class="text-muted-c" style="font-size:0.72rem;">{{ $log->created_at->diffForHumans() }}</small>
                </div>
                <div class="text-secondary-c" style="font-size:0.78rem;">
                  Aksi: <code class="text-primary">{{ $log->action }}</code> &bull; Target: {{ $log->target_type }} #{{ $log->target_id }}
                </div>
              </div>
            </div>
          @empty
            <div class="text-center py-4 text-muted-c" style="font-size:0.85rem;">Belum ada log audit.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
{{-- Include Chart.js via CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  function getThemeColors() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    return {
      textColor: isDark ? '#94a3b8' : '#64748b',
      gridColor: isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.06)'
    };
  }

  let themeColors = getThemeColors();

  // Initial Growth Chart
  const growthCtx = document.getElementById('chartGrowth').getContext('2d');
  const growthLabels = {!! json_encode($chartGrowth['labels']) !!};
  const growthData = {!! json_encode($chartGrowth['data']) !!};

  const chartGrowth = new Chart(growthCtx, {
    type: 'line',
    data: {
      labels: growthLabels,
      datasets: [{
        label: 'Client Baru',
        data: growthData,
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        borderWidth: 2.5,
        fill: true,
        tension: 0.35,
        pointBackgroundColor: '#3b82f6',
        pointRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        x: {
          grid: { color: themeColors.gridColor },
          ticks: { color: themeColors.textColor, font: { size: 11 } }
        },
        y: {
          grid: { color: themeColors.gridColor },
          ticks: { color: themeColors.textColor, font: { size: 11 }, precision: 0 },
          beginAtZero: true
        }
      }
    }
  });

  // Initial Plan Distribution Chart
  const plansCtx = document.getElementById('chartPlans').getContext('2d');
  const planLabels = {!! json_encode($chartPlans['labels']) !!};
  const planData = {!! json_encode($chartPlans['data']) !!};
  const planColors = {!! json_encode($chartPlans['colors']) !!};

  const chartPlans = new Chart(plansCtx, {
    type: 'doughnut',
    data: {
      labels: planLabels,
      datasets: [{
        data: planData.length && planData.some(v => v > 0) ? planData : [1],
        backgroundColor: planColors,
        borderWidth: 0,
        hoverOffset: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { color: themeColors.textColor, font: { size: 11 }, boxWidth: 12 }
        }
      },
      cutout: '70%'
    }
  });

  // Dynamic Theme Switch Listener for Charts
  window.addEventListener('themeChanged', function() {
    const updated = getThemeColors();
    if (chartGrowth) {
      chartGrowth.options.scales.x.grid.color = updated.gridColor;
      chartGrowth.options.scales.x.ticks.color = updated.textColor;
      chartGrowth.options.scales.y.grid.color = updated.gridColor;
      chartGrowth.options.scales.y.ticks.color = updated.textColor;
      chartGrowth.update();
    }
    if (chartPlans) {
      chartPlans.options.plugins.legend.labels.color = updated.textColor;
      chartPlans.update();
    }
  });

  // Handle Range Filter Buttons
  document.querySelectorAll('.btn-range').forEach(btn => {
    btn.onclick = function() {
      const range = this.getAttribute('data-range');
      document.querySelectorAll('.btn-range').forEach(b => {
        b.className = 'btn btn-sm btn-range btn-ghost text-muted-c rounded-2 px-2.5 py-1';
      });
      this.className = 'btn btn-sm btn-range btn-primary-grad text-white rounded-2 px-2.5 py-1';

      // Shimmer loading
      setTimeout(() => {
        fetch(`{{ route('sys_admin.dashboard') }}?range=${range}`, {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            document.getElementById('kpiTotalClients').textContent = data.kpi.total_clients;
            document.getElementById('kpiMrr').textContent = `Rp ${data.kpi.mrr}`;
            document.getElementById('kpiLatency').textContent = `${data.kpi.avg_latency} ms`;

            // Update Chart Growth
            chartGrowth.data.labels = data.chart_growth.labels;
            chartGrowth.data.datasets[0].data = data.chart_growth.data;
            chartGrowth.update();

            // Update Chart Plans
            chartPlans.data.labels = data.chart_plans.labels;
            chartPlans.data.datasets[0].data = data.chart_plans.data;
            chartPlans.update();
          }
        });
      }, 400);
    };
  });
});
</script>
@endpush
