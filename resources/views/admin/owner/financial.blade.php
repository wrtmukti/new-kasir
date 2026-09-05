@extends('admin.layouts.app')

@section('title', 'Laba Rugi & Cash Flow — Portal Owner')

@php $activeMenu = 'owner-financial' @endphp

@section('content')
<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>Laba Rugi &amp; Cash Flow</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Home</a>
      <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Owner</span>
      <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Laba Rugi &amp; Cash Flow</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-soft">
      <i class="bi bi-grid me-1"></i> Dashboard
    </a>
    <a href="{{ route('owner.financial.export', ['start_date' => $startDate, 'end_date' => $endDate, 'outlet_ids' => $selectedOutletIds]) }}" class="btn btn-primary-grad">
      <i class="bi bi-download me-1"></i> Ekspor Laporan
    </a>
  </div>
</div>

<!-- FILTER BAR -->
<div class="card mb-3">
  <div class="card-body py-2 px-3">
    <form method="GET" action="{{ route('owner.financial') }}" class="row g-2 align-items-center">
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
        <a href="{{ route('owner.financial') }}" class="btn btn-outline-soft btn-sm" title="Reset Filter" style="padding: 0.45rem 0.65rem;">
          <i class="bi bi-arrow-counterclockwise"></i>
        </a>
      </div>
    </form>
  </div>
</div>

<!-- 2 KOLOM: LABA RUGI & CASH FLOW -->
<div class="row g-3 mb-3">
  <!-- KOLOM 1: LABA RUGI (P&L AKRUAL) -->
  <div class="col-lg-6 col-12">
    <div class="card h-100">
      <div class="card-header-flex">
        <div>
          <h6>Laba Rugi (P&amp;L)</h6>
          <span class="text-muted-c" style="font-size: 0.78rem;">Prinsip akrual efisiensi operasional seluruh cabang</span>
        </div>
        <span class="pill pill-primary text-mono">Akrual</span>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table-modern mb-0">
            <tbody>
              <tr>
                <td class="cell-primary">Total Penjualan (Omzet)</td>
                <td class="text-end text-mono fw-bold" style="color: var(--text-primary);">
                  Rp {{ number_format($kpis['total_revenue'], 0, ',', '.') }}
                </td>
              </tr>
              <tr>
                <td class="ps-4 text-muted-c">&minus; Modal Resep (HPP COGS)</td>
                <td class="text-end text-mono text-danger">
                  &minus; Rp {{ number_format($kpis['total_cogs'], 0, ',', '.') }}
                </td>
              </tr>
              <tr style="background: var(--bg-elevated);">
                <td class="fw-semibold" style="color: var(--text-primary);">Laba Kotor (Gross Profit)</td>
                <td class="text-end text-mono fw-bold text-success">
                  Rp {{ number_format($kpis['gross_profit'], 0, ',', '.') }}
                  <span class="pill pill-success text-mono ms-1" style="font-size: 0.68rem; padding: 0.12rem 0.45rem;">{{ number_format($kpis['gross_margin_percent'], 1) }}%</span>
                </td>
              </tr>
              <tr>
                <td class="ps-4 text-muted-c">&minus; Kerugian Waste Dapur</td>
                <td class="text-end text-mono text-danger">
                  &minus; Rp {{ number_format($kpis['total_waste_cost'], 0, ',', '.') }}
                </td>
              </tr>
              <tr>
                <td class="ps-4 text-muted-c">&minus; Beban Gaji Staf Cabang</td>
                <td class="text-end text-mono text-danger">
                  &minus; Rp {{ number_format($kpis['total_labor_cost'], 0, ',', '.') }}
                </td>
              </tr>
              <tr>
                <td class="ps-4 text-muted-c">&minus; Beban Listrik, Air &amp; Sewa (Overhead)</td>
                <td class="text-end text-mono text-danger">
                  &minus; Rp {{ number_format($kpis['total_overhead_cost'], 0, ',', '.') }}
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td>
                  <div class="fw-bold" style="color: var(--text-primary);">Laba Bersih (Net Profit)</div>
                  <div class="text-muted-c small" style="font-weight: normal;">Margin Bersih: {{ number_format($kpis['net_margin_percent'], 1) }}%</div>
                </td>
                <td class="text-end text-mono fw-bold {{ $kpis['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.15rem;">
                  Rp {{ number_format($kpis['net_profit'], 0, ',', '.') }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- KOLOM 2: ARUS KAS NYATA (CASH FLOW) -->
  <div class="col-lg-6 col-12">
    <div class="card h-100">
      <div class="card-header-flex">
        <div>
          <h6>Arus Kas Nyata (Cash Flow)</h6>
          <span class="text-muted-c" style="font-size: 0.78rem;">Likuiditas pergerakan uang kas fisik &amp; bank riil</span>
        </div>
        <span class="pill pill-success text-mono">Kas Riil</span>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table-modern mb-0">
            <tbody>
              <tr style="background: var(--bg-elevated);">
                <td class="fw-semibold text-success">Total Kas Masuk (Inflow)</td>
                <td class="text-end text-mono fw-bold text-success">
                  Rp {{ number_format($kpis['total_cash_inflow'], 0, ',', '.') }}
                </td>
              </tr>
              <tr>
                <td class="ps-4 text-muted-c">&bull; Penjualan Kasir Tunai</td>
                <td class="text-end text-mono text-muted-c">
                  Rp {{ number_format($kpis['total_sales_cash'], 0, ',', '.') }}
                </td>
              </tr>
              <tr>
                <td class="ps-4 text-muted-c">&bull; Penjualan Non-Tunai (QRIS / Transfer)</td>
                <td class="text-end text-mono text-muted-c">
                  Rp {{ number_format($kpis['total_sales_non_cash'], 0, ',', '.') }}
                </td>
              </tr>
              <tr>
                <td class="ps-4 text-muted-c">&bull; Modal Awal Laci Kasir</td>
                <td class="text-end text-mono text-muted-c">
                  Rp {{ number_format($kpis['total_drawer_cash_in'], 0, ',', '.') }}
                </td>
              </tr>

              <tr style="background: var(--bg-elevated);">
                <td class="fw-semibold text-danger">Total Kas Keluar (Outflow)</td>
                <td class="text-end text-mono fw-bold text-danger">
                  &minus; Rp {{ number_format($kpis['total_operating_outflow'], 0, ',', '.') }}
                </td>
              </tr>
              <tr>
                <td class="ps-4 text-muted-c">&bull; Pembayaran Lunas PO Supplier</td>
                <td class="text-end text-mono text-muted-c">
                  Rp {{ number_format($kpis['total_po_paid'], 0, ',', '.') }}
                </td>
              </tr>
              <tr>
                <td class="ps-4 text-muted-c">&bull; Pengeluaran Kas Kecil Laci (Petty Cash)</td>
                <td class="text-end text-mono text-muted-c">
                  Rp {{ number_format($kpis['total_drawer_cash_out'], 0, ',', '.') }}
                </td>
              </tr>
              <tr>
                <td class="ps-4 text-muted-c">&bull; Pembayaran Gaji &amp; Overhead</td>
                <td class="text-end text-mono text-muted-c">
                  Rp {{ number_format($kpis['total_labor_cost'] + $kpis['total_overhead_cost'], 0, ',', '.') }}
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td>
                  <div class="fw-bold" style="color: var(--text-primary);">Arus Kas Bersih (Net Cash)</div>
                  <span class="pill {{ $kpis['net_cash_flow'] >= 0 ? 'pill-success' : 'pill-danger' }} text-mono mt-1" style="font-size: 0.7rem;">
                    {{ $kpis['net_cash_flow'] >= 0 ? 'Surplus Kas' : 'Defisit Kas' }}
                  </span>
                </td>
                <td class="text-end text-mono fw-bold {{ $kpis['net_cash_flow'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.15rem;">
                  Rp {{ number_format($kpis['net_cash_flow'], 0, ',', '.') }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- TABEL RINCIAN LABA RUGI PER CABANG -->
<div class="card">
  <div class="card-header-flex">
    <div>
      <h6>Rincian Finansial Per Cabang</h6>
      <span class="text-muted-c" style="font-size: 0.78rem;">Perbandingan omzet, HPP, laba kotor, dan laba bersih di setiap outlet</span>
    </div>
    <span class="pill pill-neutral text-mono">{{ count($leaderboard) }} cabang</span>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern striped mb-0">
        <thead>
          <tr>
            <th>Cabang</th>
            <th class="text-end">Omzet</th>
            <th class="text-end">HPP</th>
            <th class="text-end">Laba Kotor</th>
            <th class="text-center">Gross Margin</th>
            <th class="text-end">Waste</th>
            <th class="text-end">Laba Bersih</th>
            <th class="text-center">Net Margin</th>
            <th class="text-end">Setor Brankas</th>
          </tr>
        </thead>
        <tbody>
          @foreach($leaderboard as $row)
            <tr>
              <td>
                <div class="cell-primary">{{ $row['outlet_name'] }}</div>
                <div class="text-muted-c" style="font-size: 0.75rem;">{{ $row['outlet_branch'] ?? $row['outlet_code'] }}</div>
              </td>
              <td class="text-end text-mono fw-bold" style="color: var(--text-primary);">
                Rp {{ number_format($row['revenue'], 0, ',', '.') }}
              </td>
              <td class="text-end text-mono text-danger">
                Rp {{ number_format($row['cogs'], 0, ',', '.') }}
              </td>
              <td class="text-end text-mono text-success fw-bold">
                Rp {{ number_format($row['gross_profit'], 0, ',', '.') }}
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
              </td>
              <td class="text-center">
                <span class="pill {{ $row['net_margin_percent'] >= 20 ? 'pill-success' : ($row['net_margin_percent'] >= 0 ? 'pill-warning' : 'pill-danger') }} text-mono">
                  {{ number_format($row['net_margin_percent'], 1) }}%
                </span>
              </td>
              <td class="text-end text-mono fw-bold text-warning">
                Rp {{ number_format($row['safe_deposit'], 0, ',', '.') }}
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td>TOTAL KESELURUHAN</td>
            <td class="text-end text-mono text-primary fw-bold">Rp {{ number_format($kpis['total_revenue'], 0, ',', '.') }}</td>
            <td class="text-end text-mono text-danger">Rp {{ number_format($kpis['total_cogs'], 0, ',', '.') }}</td>
            <td class="text-end text-mono text-success fw-bold">Rp {{ number_format($kpis['gross_profit'], 0, ',', '.') }}</td>
            <td class="text-center text-mono fw-bold">{{ number_format($kpis['gross_margin_percent'], 1) }}%</td>
            <td class="text-end text-mono text-danger">Rp {{ number_format($kpis['total_waste_cost'], 0, ',', '.') }}</td>
            <td class="text-end text-mono text-success fw-bold">Rp {{ number_format($kpis['net_profit'], 0, ',', '.') }}</td>
            <td class="text-center text-mono fw-bold">{{ number_format($kpis['net_margin_percent'], 1) }}%</td>
            <td class="text-end text-mono text-warning fw-bold">Rp {{ number_format($kpis['total_safe_deposit'], 0, ',', '.') }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
