@extends('admin.layouts.app')

@section('title', 'Leaderboard & Benchmark Resep — Portal Owner')

@php $activeMenu = 'owner-benchmark' @endphp

@push('styles')
<style>
  .benchmark-recipe-card {
    background: var(--bg-surface, #1A1E27);
    border: 1px solid var(--border-subtle, #2F3748);
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.25rem;
    transition: border-color 0.2s ease;
  }
  .benchmark-recipe-card.anomaly-card {
    border-color: rgba(245, 158, 11, 0.5);
    background: linear-gradient(135deg, var(--bg-surface) 0%, rgba(245, 158, 11, 0.04) 100%);
  }
</style>
@endpush

@section('content')
<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>🏆 Leaderboard &amp; Benchmark Resep Antar Cabang</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Portal Owner</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Benchmark Cabang</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('admin.owner.dashboard') }}" class="btn btn-outline-soft">
      <i class="bi bi-grid me-1"></i> Dashboard Konsolidasi
    </a>
  </div>
</div>

<!-- FILTER PANEL -->
<div class="card mb-4" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 14px;">
  <div class="card-body py-3">
    <form method="GET" action="{{ route('admin.owner.benchmark') }}" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label small fw-bold text-secondary-c">Filter Cabang:</label>
        <select name="outlet_ids[]" class="form-select form-select-sm" style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
          <option value="">-- Semua Cabang (Konsolidasi) --</option>
          @foreach($activeOutlets as $ot)
            <option value="{{ $ot->outlet_id }}" {{ in_array($ot->outlet_id, $selectedOutletIds) ? 'selected' : '' }}>
              {{ $ot->outlet_name }}
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
        <a href="{{ route('admin.owner.benchmark') }}" class="btn btn-outline-soft btn-sm">
          <i class="bi bi-arrow-counterclockwise"></i> Reset
        </a>
      </div>
    </form>
  </div>
</div>

<!-- LEADERBOARD RANKING PODIUM -->
<div class="row g-3 mb-4">
  @foreach($leaderboard as $index => $row)
    @if($index < 3)
      <div class="col-md-4">
        <div class="stat-card card-glow" style="--card-glow-color: {{ $index === 0 ? 'rgba(245, 158, 11, 0.4)' : ($index === 1 ? 'rgba(148, 163, 184, 0.4)' : 'rgba(205, 127, 50, 0.4)') }};">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge {{ $index === 0 ? 'badge-warning' : ($index === 1 ? 'badge-secondary' : 'badge-danger') }} px-2.5 py-1 fw-bold">
              {{ $index === 0 ? '🥇 JUARA 1 OMZET' : ($index === 1 ? '🥈 RUNNER UP' : '🥉 PERINGKAT 3') }}
            </span>
            <span class="text-muted-c small">{{ $row['outlet_branch'] }}</span>
          </div>
          <h4 class="fw-bold mb-1" style="color: var(--text-primary);">{{ $row['outlet_name'] }}</h4>
          <h3 class="fw-bold text-primary mb-2">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</h3>
          <div class="d-flex justify-content-between small text-secondary-c pt-2 border-top" style="border-color: var(--border-subtle) !important;">
            <span>Gross Margin: <strong class="text-success">{{ number_format($row['gross_margin_percent'], 1) }}%</strong></span>
            <span>Net Profit: <strong class="{{ $row['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($row['net_profit'], 0, ',', '.') }}</strong></span>
          </div>
        </div>
      </div>
    @endif
  @endforeach
</div>

<!-- BENCHMARK HPP RESEP MENU ANTAR CABANG -->
<div class="card mb-4" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 16px;">
  <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3" style="border-bottom: 1px solid var(--border-subtle);">
    <div>
      <h5 class="fw-bold mb-0" style="color: var(--text-primary);"><i class="bi bi-bullseye text-primary me-2"></i>Benchmark HPP Resep Antar Cabang &amp; Deteksi Anomali Porsi</h5>
      <span class="text-muted-c small">Mendeteksi pemborosan gramasi takaran atau penyimpangan harga jual antar outlet</span>
    </div>
    <span class="badge badge-soft-info">Auto Anomaly Detection</span>
  </div>

  <div class="card-body p-3">
    @forelse($benchmarkRecipes as $bRecipe)
      <div class="benchmark-recipe-card {{ $bRecipe['is_anomaly'] ? 'anomaly-card' : '' }}">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
          <div class="d-flex align-items-center gap-2">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6; width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
              <i class="bi bi-book"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-0" style="color: var(--text-primary);">{{ $bRecipe['recipe_name'] }}</h6>
              <span class="text-muted-c small">Standard Modal Resep: <strong>Rp {{ number_format($bRecipe['standard_cogs'], 0, ',', '.') }}</strong></span>
            </div>
          </div>

          @if($bRecipe['is_anomaly'])
            <span class="badge badge-warning px-3 py-1.5 fw-bold">
              <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $bRecipe['anomaly_message'] }}
            </span>
          @else
            <span class="badge badge-success px-3 py-1.5">
              <i class="bi bi-check-circle-fill me-1"></i> Resep Konsisten Antar Cabang
            </span>
          @endif
        </div>

        <!-- OUTLET COMPARISON GRID -->
        <div class="row g-2">
          @foreach($bRecipe['outlet_breakdown'] as $otId => $otStat)
            <div class="col-md-4 col-sm-6">
              <div class="p-2.5 rounded" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <strong class="small text-truncate" style="color: var(--text-primary); max-width: 140px;">{{ $otStat['outlet_name'] }}</strong>
                  <span class="badge {{ $otStat['cogs_percent'] > 40 ? 'badge-danger' : ($otStat['cogs_percent'] > 0 ? 'badge-success' : 'badge-secondary') }}" style="font-size: 0.7rem;">
                    HPP {{ number_format($otStat['cogs_percent'], 1) }}%
                  </span>
                </div>
                <div class="d-flex justify-content-between text-muted-c" style="font-size: 0.78rem;">
                  <span>Terjual: <strong>{{ number_format($otStat['qty_sold']) }} porsi</strong></span>
                  <span>Omzet: <strong>Rp {{ number_format($otStat['total_revenue'], 0, ',', '.') }}</strong></span>
                </div>
              </div>
            </div>
          @endforeach
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
