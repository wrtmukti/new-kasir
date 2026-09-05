@extends('admin.layouts.app')

@section('title', 'Setoran & Hutang PO — Portal Owner')

@php $activeMenu = 'owner-cash-debt' @endphp

@section('content')
<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>Setoran &amp; Hutang PO</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Home</a>
      <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Owner</span>
      <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Setoran &amp; Hutang PO</span>
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
    <form method="GET" action="{{ route('owner.cash-debt') }}" class="row g-2 align-items-center">
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
        <a href="{{ route('owner.cash-debt') }}" class="btn btn-outline-soft btn-sm" title="Reset Filter" style="padding: 0.45rem 0.65rem;">
          <i class="bi bi-arrow-counterclockwise"></i>
        </a>
      </div>
    </form>
  </div>
</div>

<!-- 2 STAT CARDS UTAMA -->
<div class="row g-3 mb-3">
  <!-- Setoran Kas Brankas -->
  <div class="col-md-6 col-12">
    <div class="card card-glow h-100">
      <div class="card-inner card-body stat-card">
        <div class="stat-icon" style="background: var(--warning-bg); color: var(--warning);">
          <i class="bi bi-safe"></i>
        </div>
        <div class="stat-value text-mono" style="color: var(--warning);">
          Rp {{ number_format($cashDebtData['total_safe_deposit'], 0, ',', '.') }}
        </div>
        <div class="stat-label">Total Setoran Kas Brankas</div>
        <span class="stat-trend mt-2" style="background: var(--warning-bg); color: var(--warning);">
          <i class="bi bi-wallet2"></i> Kas Fisik Masuk
        </span>
      </div>
    </div>
  </div>

  <!-- Hutang Supplier Tempo -->
  <div class="col-md-6 col-12">
    <div class="card card-glow h-100">
      <div class="card-inner card-body stat-card">
        <div class="stat-icon" style="background: var(--danger-bg); color: var(--danger);">
          <i class="bi bi-clock-history"></i>
        </div>
        <div class="stat-value text-mono" style="color: var(--danger);">
          Rp {{ number_format($cashDebtData['total_unpaid_po'], 0, ',', '.') }}
        </div>
        <div class="stat-label">Total Hutang Supplier Tempo</div>
        <span class="stat-trend down mt-2">
          <i class="bi bi-exclamation-circle"></i> Menunggu Pelunasan
        </span>
      </div>
    </div>
  </div>
</div>

<!-- 2 TABEL: LIVE SETORAN BRANKAS (KIRI) & KALENDER JATUH TEMPO SUPPLIER (KANAN) -->
<div class="row g-3 align-items-start">
  <!-- TABEL 1: SETORAN BRANKAS -->
  <div class="col-lg-6 col-12">
    <div class="card">
      <div class="card-header-flex">
        <div>
          <h6>Setoran Kas Brankas</h6>
          <span class="text-muted-c" style="font-size: 0.78rem;">Rekap serah terima fisik kas saat tutup shift</span>
        </div>
        <span class="pill pill-neutral text-mono">{{ count($cashDebtData['safe_deposits']) }} setoran</span>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table-modern striped mb-0">
            <thead>
              <tr>
                <th>Cabang &amp; Tanggal</th>
                <th>Kasir &amp; Shift</th>
                <th class="text-end">Sisa Kas Laci</th>
                <th class="text-end">Setor Brankas</th>
              </tr>
            </thead>
            <tbody>
              @forelse($cashDebtData['safe_deposits'] as $dep)
                <tr>
                  <td>
                    <div class="cell-primary">{{ $dep->outlet->outlet_name ?? 'Cabang' }}</div>
                    <div class="text-muted-c" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($dep->business_date)->format('d M Y') }}</div>
                  </td>
                  <td>
                    <div class="cell-primary">{{ $dep->cashier_name ?? 'Kasir' }}</div>
                    <div class="text-muted-c" style="font-size: 0.75rem;">{{ $dep->shift_name ?? 'Shift' }}</div>
                  </td>
                  <td class="text-end text-mono text-muted-c">
                    Rp {{ number_format($dep->retained_cash_float, 0, ',', '.') }}
                  </td>
                  <td class="text-end text-mono fw-bold text-warning">
                    Rp {{ number_format($dep->cash_deposit_to_safe, 0, ',', '.') }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-4 text-muted-c">
                    Belum ada catatan setoran brankas pada periode ini.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- TABEL 2: KALENDER HUTANG SUPPLIER TEMPO -->
  <div class="col-lg-6 col-12">
    <div class="card">
      <div class="card-header-flex">
        <div>
          <h6>Jatuh Tempo Tagihan Supplier</h6>
          <span class="text-muted-c" style="font-size: 0.78rem;">Daftar tagihan PO tempo yang menunggu pelunasan</span>
        </div>
        <span class="pill pill-neutral text-mono">{{ count($cashDebtData['unpaid_purchase_orders']) }} tagihan</span>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table-modern striped mb-0">
            <thead>
              <tr>
                <th>No. PO &amp; Supplier</th>
                <th>Cabang</th>
                <th class="text-center">Jatuh Tempo</th>
                <th class="text-end">Total Tagihan</th>
              </tr>
            </thead>
            <tbody>
              @forelse($cashDebtData['unpaid_purchase_orders'] as $po)
                <tr>
                  <td>
                    <div class="cell-primary">{{ $po->po_code ?? $po->po_number }}</div>
                    <div class="text-muted-c" style="font-size: 0.75rem;">{{ $po->supplier->supplier_name ?? 'Supplier' }}</div>
                  </td>
                  <td>
                    <span class="pill pill-neutral">{{ $po->outlet->outlet_name ?? 'Cabang' }}</span>
                  </td>
                  <td class="text-center">
                    @if($po->urgency_level === 'overdue')
                      <span class="pill pill-danger text-mono">Lewat Tempo!</span>
                    @elseif($po->urgency_level === 'critical')
                      <span class="pill pill-danger text-mono">Besok Tempo</span>
                    @elseif($po->urgency_level === 'warning')
                      <span class="pill pill-warning text-mono">{{ $po->days_remaining }} Hari Lagi</span>
                    @else
                      <span class="pill pill-success text-mono">{{ \Carbon\Carbon::parse($po->due_date)->format('d M Y') }}</span>
                    @endif
                  </td>
                  <td class="text-end text-mono fw-bold text-danger">
                    Rp {{ number_format($po->po_total_amount, 0, ',', '.') }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-4 text-muted-c">
                    Tidak ada tagihan supplier tempo yang belum lunas.
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
