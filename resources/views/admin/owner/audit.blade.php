@extends('admin.layouts.app')

@section('title', 'Audit Selisih & Waste — Portal Owner')

@php $activeMenu = 'owner-audit' @endphp

@section('content')
<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>Audit Selisih &amp; Waste</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Home</a>
      <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Owner</span>
      <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Audit Selisih &amp; Waste</span>
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
    <form method="GET" action="{{ route('owner.audit') }}" class="row g-2 align-items-center">
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
        <a href="{{ route('owner.audit') }}" class="btn btn-outline-soft btn-sm" title="Reset Filter" style="padding: 0.45rem 0.65rem;">
          <i class="bi bi-arrow-counterclockwise"></i>
        </a>
      </div>
    </form>
  </div>
</div>

<!-- 3 RINGKASAN AUDIT STATS -->
<div class="row g-3 mb-3">
  <!-- Selisih Minus -->
  <div class="col-md-4 col-sm-12">
    <div class="card card-glow h-100">
      <div class="card-inner card-body stat-card">
        <div class="stat-icon" style="background: var(--danger-bg); color: var(--danger);">
          <i class="bi bi-dash-circle"></i>
        </div>
        <div class="stat-value text-mono" style="color: var(--danger);">
          Rp {{ number_format(abs($auditData['total_shortage']), 0, ',', '.') }}
        </div>
        <div class="stat-label">Selisih Minus Kasir</div>
        <span class="stat-trend down mt-2">
          <i class="bi bi-arrow-down-short"></i> Uang Fisik Kurang
        </span>
      </div>
    </div>
  </div>

  <!-- Selisih Lebih -->
  <div class="col-md-4 col-sm-12">
    <div class="card card-glow h-100">
      <div class="card-inner card-body stat-card">
        <div class="stat-icon" style="background: rgba(99,102,241,0.12); color: var(--accent-1);">
          <i class="bi bi-plus-circle"></i>
        </div>
        <div class="stat-value text-mono" style="color: var(--text-primary);">
          Rp {{ number_format($auditData['total_overage'], 0, ',', '.') }}
        </div>
        <div class="stat-label">Selisih Lebih Kasir</div>
        <span class="stat-trend up mt-2">
          <i class="bi bi-arrow-up-short"></i> Uang Fisik Lebih
        </span>
      </div>
    </div>
  </div>

  <!-- Kerugian Waste -->
  <div class="col-md-4 col-sm-12">
    <div class="card card-glow h-100">
      <div class="card-inner card-body stat-card">
        <div class="stat-icon" style="background: var(--warning-bg); color: var(--warning);">
          <i class="bi bi-trash3"></i>
        </div>
        <div class="stat-value text-mono" style="color: var(--warning);">
          Rp {{ number_format($auditData['total_waste_loss'], 0, ',', '.') }}
        </div>
        <div class="stat-label">Kerugian Waste Dapur</div>
        <span class="stat-trend mt-2" style="background: var(--warning-bg); color: var(--warning);">
          <i class="bi bi-exclamation-triangle"></i> Bahan Rusak / Terbuang
        </span>
      </div>
    </div>
  </div>
</div>

<!-- 2 TABEL AUDIT: AUDIT KASIR & AUDIT WASTE DAPUR -->
<div class="row g-3 align-items-start">
  <!-- TABEL 1: AUDIT SHIFT KASIR -->
  <div class="col-lg-7 col-12">
    <div class="card">
      <div class="card-header-flex">
        <div>
          <h6>Audit Selisih Kasir</h6>
          <span class="text-muted-c" style="font-size: 0.78rem;">Rekap selisih kas fisik laci saat tutup shift</span>
        </div>
        <span class="pill pill-neutral text-mono">{{ count($auditData['cashier_closings']) }} shift</span>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table-modern striped mb-0">
            <thead>
              <tr>
                <th>Tanggal &amp; Cabang</th>
                <th>Kasir &amp; Shift</th>
                <th class="text-end">Fisik Laci</th>
                <th class="text-end">Selisih</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($auditData['cashier_closings'] as $closing)
                <tr>
                  <td>
                    <div class="cell-primary">{{ \Carbon\Carbon::parse($closing->business_date)->format('d M Y') }}</div>
                    <div class="text-muted-c" style="font-size: 0.75rem;">{{ $closing->outlet->outlet_name ?? 'Cabang' }}</div>
                  </td>
                  <td>
                    <div class="cell-primary">{{ $closing->cashier_name ?? 'Kasir' }}</div>
                    <div class="text-muted-c" style="font-size: 0.75rem;">{{ $closing->shift_name ?? 'Shift' }}</div>
                  </td>
                  <td class="text-end text-mono fw-bold" style="color: var(--text-primary);">
                    Rp {{ number_format($closing->actual_cash_counted, 0, ',', '.') }}
                  </td>
                  <td class="text-end text-mono fw-bold {{ $closing->cash_difference < 0 ? 'text-danger' : ($closing->cash_difference > 0 ? 'text-primary' : 'text-success') }}">
                    {{ $closing->cash_difference < 0 ? '-' : ($closing->cash_difference > 0 ? '+' : '') }}Rp {{ number_format(abs($closing->cash_difference), 0, ',', '.') }}
                  </td>
                  <td class="text-center">
                    @if($closing->cash_difference < 0)
                      <span class="pill pill-danger text-mono">Minus</span>
                    @elseif($closing->cash_difference > 0)
                      <span class="pill pill-primary text-mono">Lebih</span>
                    @else
                      <span class="pill pill-success text-mono">Sesuai (0)</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted-c">
                    Belum ada data shift tutup kasir pada periode ini.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- TABEL 2: AUDIT WASTE DAPUR -->
  <div class="col-lg-5 col-12">
    <div class="card">
      <div class="card-header-flex">
        <div>
          <h6>Audit Waste Dapur</h6>
          <span class="text-muted-c" style="font-size: 0.78rem;">Catatan bahan mentah rusak atau terbuang di dapur</span>
        </div>
        <span class="pill pill-neutral text-mono">{{ count($auditData['waste_logs']) }} log</span>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table-modern striped mb-0">
            <thead>
              <tr>
                <th>Bahan &amp; Cabang</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Biaya Rugi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($auditData['waste_logs'] as $waste)
                <tr>
                  <td>
                    <div class="cell-primary">{{ $waste->rawMaterial->name ?? $waste->rawMaterial->material_name ?? $waste->waste_name ?? 'Bahan' }}</div>
                    <div class="text-muted-c" style="font-size: 0.75rem;">{{ $waste->outlet->outlet_name ?? 'Cabang' }} &bull; {{ $waste->reason }}</div>
                  </td>
                  <td class="text-center">
                    <span class="pill pill-warning text-mono">{{ number_format($waste->qty_lost, 0) }} {{ $waste->rawMaterial->unit ?? $waste->unit ?? '' }}</span>
                  </td>
                  <td class="text-end text-mono fw-bold text-danger">
                    Rp {{ number_format($waste->waste_cost, 0, ',', '.') }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center py-4 text-muted-c">
                    Tidak ada catatan kerugian bahan pada periode ini.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
