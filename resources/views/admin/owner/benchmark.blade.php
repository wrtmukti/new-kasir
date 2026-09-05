@extends('admin.layouts.app')

@section('title', 'Leaderboard & Benchmark — Portal Owner')

@php $activeMenu = 'owner-benchmark' @endphp

@section('content')
<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>Leaderboard &amp; Benchmark</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Home</a>
      <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Owner</span>
      <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Leaderboard &amp; Benchmark</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-soft">
      <i class="bi bi-grid me-1"></i> Dashboard
    </a>
    <a href="{{ route('owner.financial') }}" class="btn btn-outline-soft">
      <i class="bi bi-pie-chart me-1"></i> Laba Rugi
    </a>
  </div>
</div>

<!-- FILTER BAR -->
<div class="card mb-3">
  <div class="card-body py-2 px-3">
    <form method="GET" action="{{ route('owner.benchmark') }}" class="row g-2 align-items-center">
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
        <a href="{{ route('owner.benchmark') }}" class="btn btn-outline-soft btn-sm" title="Reset Filter" style="padding: 0.45rem 0.65rem;">
          <i class="bi bi-arrow-counterclockwise"></i>
        </a>
      </div>
    </form>
  </div>
</div>

<!-- LEADERBOARD TOP 3 CABANG -->
<div class="row g-3 mb-3">
  @foreach($leaderboard as $index => $row)
    @if($index < 3)
      <div class="col-md-4 col-sm-12">
        <div class="card card-glow h-100">
          <div class="card-inner card-body stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="pill {{ $index === 0 ? 'pill-warning' : ($index === 1 ? 'pill-secondary' : 'pill-danger') }} fw-bold">
                {{ $index === 0 ? '#1 Terlaris' : ($index === 1 ? '#2 Runner Up' : '#3 Peringkat 3') }}
              </span>
              <span class="text-muted-c small">{{ $row['outlet_branch'] ?? $row['outlet_code'] }}</span>
            </div>
            <div class="stat-value text-mono" style="font-size: 1.45rem; color: var(--text-primary);">
              Rp {{ number_format($row['revenue'], 0, ',', '.') }}
            </div>
            <div class="stat-label fw-semibold" style="color: var(--text-primary); font-size: 0.92rem;">
              {{ $row['outlet_name'] }}
            </div>
            <div class="d-flex justify-content-between align-items-center small text-muted-c pt-2 mt-2" style="border-top: 1px solid var(--border-subtle);">
              <span>Margin: <strong class="text-success text-mono">{{ number_format($row['gross_margin_percent'], 1) }}%</strong></span>
              <span>Laba: <strong class="{{ $row['net_profit'] >= 0 ? 'text-success' : 'text-danger' }} text-mono">Rp {{ number_format($row['net_profit'], 0, ',', '.') }}</strong></span>
            </div>
          </div>
        </div>
      </div>
    @endif
  @endforeach
</div>

<!-- TABEL PERINGKAT SELURUH CABANG -->
<div class="card mb-3">
  <div class="card-header-flex">
    <div>
      <h6>Peringkat Kinerja Seluruh Cabang</h6>
      <span class="text-muted-c" style="font-size: 0.78rem;">Evaluasi komparatif omzet, volume transaksi, margin kotor, dan laba bersih per outlet</span>
    </div>
    <span class="pill pill-neutral text-mono">{{ count($leaderboard) }} cabang</span>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern striped mb-0">
        <thead>
          <tr>
            <th class="text-center" style="width: 60px;">Rank</th>
            <th>Cabang</th>
            <th class="text-center">Total Transaksi</th>
            <th class="text-end">Total Omzet</th>
            <th class="text-center">Margin Kotor</th>
            <th class="text-end">Laba Bersih</th>
            <th class="text-center">Status Shift</th>
          </tr>
        </thead>
        <tbody>
          @forelse($leaderboard as $idx => $row)
            <tr>
              <td class="text-center">
                <span class="pill {{ $idx === 0 ? 'pill-warning' : ($idx === 1 ? 'pill-secondary' : ($idx === 2 ? 'pill-danger' : 'pill-neutral')) }} fw-bold text-mono">
                  #{{ $idx + 1 }}
                </span>
              </td>
              <td>
                <div class="cell-primary">{{ $row['outlet_name'] }}</div>
                <div class="text-muted-c" style="font-size: 0.75rem;">{{ $row['outlet_branch'] ?? $row['outlet_code'] }}</div>
              </td>
              <td class="text-center text-mono">
                {{ number_format($row['orders_count']) }} order
              </td>
              <td class="text-end text-mono fw-bold" style="color: var(--text-primary);">
                Rp {{ number_format($row['revenue'], 0, ',', '.') }}
              </td>
              <td class="text-center">
                <span class="pill {{ $row['gross_margin_percent'] >= 60 ? 'pill-success' : ($row['gross_margin_percent'] >= 40 ? 'pill-primary' : 'pill-warning') }} text-mono">
                  {{ number_format($row['gross_margin_percent'], 1) }}%
                </span>
              </td>
              <td class="text-end text-mono fw-bold {{ $row['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                Rp {{ number_format($row['net_profit'], 0, ',', '.') }}
              </td>
              <td class="text-center">
                @if($row['has_active_shift'])
                  <span class="pill pill-success text-mono"><span class="status-dot live me-1"></span>Shift Aktif</span>
                @else
                  <span class="pill pill-neutral text-mono">Shift Selesai</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted-c">
                Tidak ada data cabang untuk ditampilkan.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- BENCHMARK RESEP MENU ANTAR CABANG -->
<div class="card">
  <div class="card-header-flex">
    <div>
      <h6>Benchmark Resep Antar Cabang</h6>
      <span class="text-muted-c" style="font-size: 0.78rem;">Evaluasi konsistensi modal takaran dan HPP per porsi di setiap cabang</span>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="status-dot live"></span>
      <span class="text-muted-c" style="font-size: 0.78rem;">Live</span>
    </div>
  </div>

  <div class="card-body p-3">
    @forelse($benchmarkRecipes as $bRecipe)
      <div class="card mb-3" style="background: var(--bg-surface); border: 1px solid {{ $bRecipe['is_anomaly'] ? 'rgba(245, 158, 11, 0.45)' : 'var(--border-subtle)' }}; border-radius: var(--radius-sm);">
        <div class="p-3">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
              <div class="stat-icon mb-0" style="background: rgba(99,102,241,0.12); color: var(--accent-1); width: 36px; height: 36px; border-radius: var(--radius-sm); font-size: 1rem; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-journal-text"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-semibold" style="color: var(--text-primary);">{{ $bRecipe['recipe_name'] }}</h6>
                <span class="text-muted-c small">Modal Resep Standar: <strong class="text-mono" style="color: var(--text-primary);">Rp {{ number_format($bRecipe['standard_cogs'], 0, ',', '.') }}</strong></span>
              </div>
            </div>

            @if($bRecipe['is_anomaly'])
              <span class="pill pill-warning fw-semibold">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $bRecipe['anomaly_message'] }}
              </span>
            @else
              <span class="pill pill-success fw-semibold">
                <i class="bi bi-check-circle-fill me-1"></i> Resep Konsisten
              </span>
            @endif
          </div>

          <!-- BREAKDOWN PER CABANG -->
          <div class="row g-2">
            @foreach($bRecipe['outlet_breakdown'] as $otId => $otStat)
              <div class="col-lg-4 col-md-6 col-12">
                <div class="p-2 rounded" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="cell-primary small fw-semibold text-truncate" style="max-width: 140px;">{{ $otStat['outlet_name'] }}</span>
                    <span class="pill {{ $otStat['cogs_percent'] > 40 ? 'pill-danger' : ($otStat['cogs_percent'] > 0 ? 'pill-success' : 'pill-neutral') }} text-mono" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">
                      HPP {{ number_format($otStat['cogs_percent'], 1) }}%
                    </span>
                  </div>
                  <div class="d-flex justify-content-between text-muted-c" style="font-size: 0.76rem;">
                    <span>Terjual: <strong class="text-mono" style="color: var(--text-primary);">{{ number_format($otStat['qty_sold']) }} porsi</strong></span>
                    <span>Omzet: <strong class="text-mono" style="color: var(--text-primary);">Rp {{ number_format($otStat['total_revenue'], 0, ',', '.') }}</strong></span>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    @empty
      <div class="text-center py-4 text-muted-c">
        Belum ada data resep terdaftar untuk di-benchmark.
      </div>
    @endforelse
  </div>
</div>
@endsection
