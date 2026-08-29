@extends('admin.layouts.app')

@section('title', 'Audit Selisih Kasir & Waste — Portal Owner')

@php $activeMenu = 'owner-audit' @endphp

@section('content')
<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>🚨 Pusat Audit &amp; Deteksi Kebocoran Kas / Bahan</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Portal Owner</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Audit Selisih &amp; Waste</span>
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
    <form method="GET" action="{{ route('admin.owner.audit') }}" class="row g-2 align-items-end">
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
        <a href="{{ route('admin.owner.audit') }}" class="btn btn-outline-soft btn-sm">
          <i class="bi bi-arrow-counterclockwise"></i> Reset
        </a>
      </div>
    </form>
  </div>
</div>

<!-- 3 RINGKASAN AUDIT STATS -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="stat-card card-glow" style="--card-glow-color: rgba(239, 68, 68, 0.4);">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-secondary-c small fw-bold text-uppercase">Total Selisih Minus (Shortage)</span>
        <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
      </div>
      <h3 class="fw-bold mb-1 text-danger">Rp {{ number_format(abs($auditData['total_shortage']), 0, ',', '.') }}</h3>
      <div class="text-muted-c small">Uang fisik laci hilang/kurang saat tutup shift</div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="stat-card card-glow" style="--card-glow-color: rgba(59, 130, 246, 0.4);">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-secondary-c small fw-bold text-uppercase">Total Selisih Lebih (Overage)</span>
        <i class="bi bi-plus-circle-fill fs-5 text-primary"></i>
      </div>
      <h3 class="fw-bold mb-1 text-primary">Rp {{ number_format($auditData['total_overage'], 0, ',', '.') }}</h3>
      <div class="text-muted-c small">Uang fisik laci berlebih dari sistem</div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="stat-card card-glow" style="--card-glow-color: rgba(245, 158, 11, 0.4);">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-secondary-c small fw-bold text-uppercase">Kerugian Bahan Terbuang (Waste)</span>
        <i class="bi bi-trash3-fill fs-5 text-warning"></i>
      </div>
      <h3 class="fw-bold mb-1 text-warning">Rp {{ number_format($auditData['total_waste_loss'], 0, ',', '.') }}</h3>
      <div class="text-muted-c small">Kerugian bahan baku rusak/basi di dapur</div>
    </div>
  </div>
</div>

<!-- 2 TABEL AUDIT DETAIL: AUDIT KASIR (KIRI) & AUDIT WASTE DAPUR (KANAN) -->
<div class="row g-4 mb-4">
  <!-- TABEL 1: AUDIT SHIFT KASIR -->
  <div class="col-lg-7">
    <div class="card h-100" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 16px;">
      <div class="card-header bg-transparent py-3" style="border-bottom: 1px solid var(--border-subtle);">
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);"><i class="bi bi-wallet2 text-primary me-2"></i>Audit Selisih Tutup Shift Kasir</h5>
        <span class="text-muted-c small">Mendeteksi kasir yang mengalami selisih fisik saat serah terima</span>
      </div>

      <div class="table-responsive">
        <table class="table table-custom mb-0">
          <thead>
            <tr>
              <th>Tanggal / Cabang</th>
              <th>Kasir &amp; Shift</th>
              <th class="text-end">Fisik Laci</th>
              <th class="text-end">Selisih (Variance)</th>
              <th class="text-center">Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($auditData['cashier_closings'] as $closing)
              <tr>
                <td>
                  <strong style="color: var(--text-primary);">{{ \Carbon\Carbon::parse($closing->business_date)->format('d M Y') }}</strong>
                  <div class="text-muted-c small">{{ $closing->outlet->outlet_name ?? 'Cabang' }}</div>
                </td>
                <td>
                  <strong style="color: var(--text-primary);">{{ $closing->cashier_name ?? 'Kasir' }}</strong>
                  <div class="text-muted-c small">{{ $closing->shift_name ?? 'Shift' }}</div>
                </td>
                <td class="text-end fw-bold" style="color: var(--text-primary);">
                  Rp {{ number_format($closing->actual_cash_counted, 0, ',', '.') }}
                </td>
                <td class="text-end fw-bold {{ $closing->cash_difference < 0 ? 'text-danger' : ($closing->cash_difference > 0 ? 'text-primary' : 'text-success') }}">
                  {{ $closing->cash_difference < 0 ? '-' : ($closing->cash_difference > 0 ? '+' : '') }}Rp {{ number_format(abs($closing->cash_difference), 0, ',', '.') }}
                </td>
                <td class="text-center">
                  @if($closing->cash_difference < 0)
                    <span class="badge badge-danger">Minus (Shortage)</span>
                  @elseif($closing->cash_difference > 0)
                    <span class="badge badge-primary">Lebih (Over)</span>
                  @else
                    <span class="badge badge-success">Cocok Pas (0)</span>
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

  <!-- TABEL 2: AUDIT WASTE LOG DAPUR -->
  <div class="col-lg-5">
    <div class="card h-100" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 16px;">
      <div class="card-header bg-transparent py-3" style="border-bottom: 1px solid var(--border-subtle);">
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);"><i class="bi bi-trash3 text-danger me-2"></i>Audit Bahan Terbuang (Waste)</h5>
        <span class="text-muted-c small">Daftar bahan mentah busuk/terbuang di dapur outlet</span>
      </div>

      <div class="table-responsive">
        <table class="table table-custom mb-0">
          <thead>
            <tr>
              <th>Cabang &amp; Bahan</th>
              <th class="text-center">Qty</th>
              <th class="text-end">Biaya Rugi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($auditData['waste_logs'] as $waste)
              <tr>
                <td>
                  <strong style="color: var(--text-primary);">{{ $waste->rawMaterial->material_name ?? $waste->waste_name ?? 'Bahan' }}</strong>
                  <div class="text-muted-c small">{{ $waste->outlet->outlet_name ?? 'Cabang' }} &bull; {{ $waste->reason }}</div>
                </td>
                <td class="text-center">
                  <span class="badge badge-soft-danger">{{ number_format($waste->qty_lost, 1) }} {{ $waste->unit }}</span>
                </td>
                <td class="text-end fw-bold text-danger">
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
@endsection
