@extends('admin.layouts.app')

@section('title', 'Laba Rugi & Cash Flow Konsolidasi — Portal Owner')

@php $activeMenu = 'owner-financial' @endphp

@push('styles')
<style>
  .financial-card-box {
    background: var(--bg-surface, #1A1E27);
    border: 1px solid var(--border-subtle, #2F3748);
    border-radius: 16px;
    padding: 1.5rem;
    height: 100%;
  }
  .table-pnl tr td {
    padding: 0.75rem 1rem;
    border-color: var(--border-subtle, #2F3748);
  }
</style>
@endpush

@section('content')
<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>📈 Laba Rugi &amp; Arus Kas Konsolidasi Holding</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Portal Owner</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Laba Rugi &amp; Cash Flow</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('admin.owner.dashboard') }}" class="btn btn-outline-soft">
      <i class="bi bi-grid me-1"></i> Dashboard Konsolidasi
    </a>
    <a href="{{ route('admin.owner.financial.export', ['start_date' => $startDate, 'end_date' => $endDate, 'outlet_ids' => $selectedOutletIds]) }}" class="btn btn-primary">
      <i class="bi bi-file-earmark-excel me-1"></i> Download CSV / Excel
    </a>
  </div>
</div>

<!-- FILTER PANEL -->
<div class="card mb-4" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 14px;">
  <div class="card-body py-3">
    <form method="GET" action="{{ route('admin.owner.financial') }}" class="row g-2 align-items-end">
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
          <i class="bi bi-funnel-fill me-1"></i> Filter Laporan
        </button>
        <a href="{{ route('admin.owner.financial') }}" class="btn btn-outline-soft btn-sm">
          <i class="bi bi-arrow-counterclockwise"></i> Reset
        </a>
      </div>
    </form>
  </div>
</div>

<!-- 2 KOLOM BESAR: LABA RUGI KONSOLIDASI (KIRI) & ARUS KAS NYATA (KANAN) -->
<div class="row g-4 mb-4">
  <!-- KOLOM KIRI: LABA RUGI RESEP (P&L) -->
  <div class="col-lg-6">
    <div class="financial-card-box">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-pie-chart-fill"></i>
          </div>
          <div>
            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">Laba Rugi Konsolidasi (P&amp;L)</h5>
            <span class="text-muted-c small">Prinsip Akrual &bull; Efisiensi Resep Seluruh Cabang</span>
          </div>
        </div>
        <span class="badge badge-primary">P&amp;L HOLDING</span>
      </div>

      <table class="table table-pnl mb-0">
        <tbody>
          <tr>
            <td><strong style="color: var(--text-primary);">Total Omzet Penjualan (Revenue)</strong></td>
            <td class="text-end fw-bold text-primary">Rp {{ number_format($kpis['total_revenue'], 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="ps-4 text-secondary-c">&minus; Total Modal Resep (Theoretical COGS)</td>
            <td class="text-end text-danger">&minus; Rp {{ number_format($kpis['total_cogs'], 0, ',', '.') }}</td>
          </tr>
          <tr style="background: rgba(59, 130, 246, 0.05);">
            <td><strong>LABA KOTOR KONSOLIDASI (GROSS PROFIT)</strong></td>
            <td class="text-end fw-bold text-success">
              Rp {{ number_format($kpis['gross_profit'], 0, ',', '.') }}
              <div class="small text-muted-c">Margin: {{ number_format($kpis['gross_margin_percent'], 1) }}%</div>
            </td>
          </tr>
          <tr>
            <td class="ps-4 text-secondary-c">&minus; Kerugian Bahan Busuk (Waste Cost)</td>
            <td class="text-end text-danger">&minus; Rp {{ number_format($kpis['total_waste_cost'], 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="ps-4 text-secondary-c">&minus; Biaya Gaji Staf Seluruh Cabang</td>
            <td class="text-end text-danger">&minus; Rp {{ number_format($kpis['total_labor_cost'], 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="ps-4 text-secondary-c">&minus; Biaya Listrik / Air / Sewa (Overhead)</td>
            <td class="text-end text-danger">&minus; Rp {{ number_format($kpis['total_overhead_cost'], 0, ',', '.') }}</td>
          </tr>
          <tr style="background: rgba(16, 185, 129, 0.08); border-top: 2px solid var(--border-subtle);">
            <td><h6 class="fw-bold mb-0 text-success">LABA BERSIH KONSOLIDASI (NET PROFIT)</h6></td>
            <td class="text-end">
              <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($kpis['net_profit'], 0, ',', '.') }}</h5>
              <div class="small fw-bold text-success">Net Margin: {{ number_format($kpis['net_margin_percent'], 1) }}%</div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- KOLOM KANAN: ARUS KAS NYATA (CASH FLOW) -->
  <div class="col-lg-6">
    <div class="financial-card-box">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-cash-stack"></i>
          </div>
          <div>
            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">Arus Kas Nyata (Cash Flow)</h5>
            <span class="text-muted-c small">Prinsip Cash Basis &bull; Likuiditas Kas Riil</span>
          </div>
        </div>
        <span class="badge badge-success">CASH BASIS</span>
      </div>

      <table class="table table-pnl mb-0">
        <tbody>
          <tr style="background: rgba(16, 185, 129, 0.05);">
            <td><strong class="text-success">TOTAL CASH INFLOW (KAS MASUK)</strong></td>
            <td class="text-end fw-bold text-success">Rp {{ number_format($kpis['total_cash_inflow'], 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="ps-4 text-secondary-c">&bull; Penjualan Kasir Tunai</td>
            <td class="text-end text-muted-c">Rp {{ number_format($kpis['total_sales_cash'], 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="ps-4 text-secondary-c">&bull; Penjualan Non-Tunai (QRIS / Bank)</td>
            <td class="text-end text-muted-c">Rp {{ number_format($kpis['total_sales_non_cash'], 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="ps-4 text-secondary-c">&bull; Top-up Modal Laci Kasir</td>
            <td class="text-end text-muted-c">Rp {{ number_format($kpis['total_drawer_cash_in'], 0, ',', '.') }}</td>
          </tr>

          <tr style="background: rgba(239, 68, 68, 0.05); border-top: 1.5px solid var(--border-subtle);">
            <td><strong class="text-danger">TOTAL OPERATING OUTFLOW (KAS KELUAR)</strong></td>
            <td class="text-end fw-bold text-danger">&minus; Rp {{ number_format($kpis['total_operating_outflow'], 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="ps-4 text-secondary-c">&bull; Pembayaran PO Supplier Lunas</td>
            <td class="text-end text-muted-c">Rp {{ number_format($kpis['total_po_paid'], 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="ps-4 text-secondary-c">&bull; Petty Cash Laci (Beli Es/Gas)</td>
            <td class="text-end text-muted-c">Rp {{ number_format($kpis['total_drawer_cash_out'], 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="ps-4 text-secondary-c">&bull; Gaji &amp; Overhead Operasional</td>
            <td class="text-end text-muted-c">Rp {{ number_format($kpis['total_labor_cost'] + $kpis['total_overhead_cost'], 0, ',', '.') }}</td>
          </tr>

          <tr style="background: rgba(245, 158, 11, 0.08); border-top: 2px solid var(--border-subtle);">
            <td><h6 class="fw-bold mb-0 text-warning">NET CASH FLOW (SURPLUS / DEFISIT)</h6></td>
            <td class="text-end">
              <h5 class="fw-bold mb-0 {{ $kpis['net_cash_flow'] >= 0 ? 'text-success' : 'text-danger' }}">
                Rp {{ number_format($kpis['net_cash_flow'], 0, ',', '.') }}
              </h5>
              <span class="badge {{ $kpis['net_cash_flow'] >= 0 ? 'badge-success' : 'badge-danger' }} mt-1">
                {{ $kpis['net_cash_flow'] >= 0 ? 'SURPLUS KAS' : 'DEFISIT KAS' }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- TABEL RINCIAN LABA RUGI PER CABANG -->
<div class="card" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 16px;">
  <div class="card-header bg-transparent py-3" style="border-bottom: 1px solid var(--border-subtle);">
    <h5 class="fw-bold mb-0" style="color: var(--text-primary);"><i class="bi bi-table me-2 text-primary"></i>Breakdown Finansial Per Cabang</h5>
    <span class="text-muted-c small">Perbandingan omzet, HPP, laba kotor, dan laba bersih di setiap outlet</span>
  </div>
  <div class="table-responsive">
    <table class="table table-custom mb-0">
      <thead>
        <tr>
          <th>Nama Cabang</th>
          <th class="text-end">Omzet</th>
          <th class="text-end">Modal HPP</th>
          <th class="text-end">Laba Kotor</th>
          <th class="text-center">Gross Margin</th>
          <th class="text-end">Kerugian Waste</th>
          <th class="text-end">Laba Bersih</th>
          <th class="text-center">Net Margin</th>
          <th class="text-end">Setoran Brankas</th>
        </tr>
      </thead>
      <tbody>
        @foreach($leaderboard as $row)
          <tr>
            <td>
              <strong style="color: var(--text-primary);">{{ $row['outlet_name'] }}</strong>
              <div class="text-muted-c small">{{ $row['outlet_code'] }}</div>
            </td>
            <td class="text-end fw-bold" style="color: var(--text-primary);">
              Rp {{ number_format($row['revenue'], 0, ',', '.') }}
            </td>
            <td class="text-end text-danger">
              Rp {{ number_format($row['cogs'], 0, ',', '.') }}
            </td>
            <td class="text-end text-success fw-bold">
              Rp {{ number_format($row['gross_profit'], 0, ',', '.') }}
            </td>
            <td class="text-center">
              <span class="badge badge-soft-primary">{{ number_format($row['gross_margin_percent'], 1) }}%</span>
            </td>
            <td class="text-end text-danger">
              Rp {{ number_format($row['waste_loss'], 0, ',', '.') }}
            </td>
            <td class="text-end fw-bold {{ $row['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
              Rp {{ number_format($row['net_profit'], 0, ',', '.') }}
            </td>
            <td class="text-center">
              <span class="badge {{ $row['net_margin_percent'] >= 20 ? 'badge-success' : ($row['net_margin_percent'] >= 0 ? 'badge-warning' : 'badge-danger') }}">
                {{ number_format($row['net_margin_percent'], 1) }}%
              </span>
            </td>
            <td class="text-end text-warning fw-bold">
              Rp {{ number_format($row['safe_deposit'], 0, ',', '.') }}
            </td>
          </tr>
        @endforeach
      </tbody>
      <tfoot style="background: var(--bg-elevated); font-weight: bold;">
        <tr>
          <td>TOTAL KONSOLIDASI</td>
          <td class="text-end text-primary">Rp {{ number_format($kpis['total_revenue'], 0, ',', '.') }}</td>
          <td class="text-end text-danger">Rp {{ number_format($kpis['total_cogs'], 0, ',', '.') }}</td>
          <td class="text-end text-success">Rp {{ number_format($kpis['gross_profit'], 0, ',', '.') }}</td>
          <td class="text-center">{{ number_format($kpis['gross_margin_percent'], 1) }}%</td>
          <td class="text-end text-danger">Rp {{ number_format($kpis['total_waste_cost'], 0, ',', '.') }}</td>
          <td class="text-end text-success">Rp {{ number_format($kpis['net_profit'], 0, ',', '.') }}</td>
          <td class="text-center">{{ number_format($kpis['net_margin_percent'], 1) }}%</td>
          <td class="text-end text-warning">Rp {{ number_format($kpis['total_safe_deposit'], 0, ',', '.') }}</td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
@endsection
