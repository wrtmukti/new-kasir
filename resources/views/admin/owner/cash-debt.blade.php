@extends('admin.layouts.app')

@section('title', 'Setoran Brankas & Hutang PO — Portal Owner')

@php $activeMenu = 'owner-cash-debt' @endphp

@section('content')
<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>🏦 Pusat Setoran Kas &amp; Manajemen Hutang Supplier</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Portal Owner</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Setoran &amp; Hutang PO</span>
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
    <form method="GET" action="{{ route('admin.owner.cash-debt') }}" class="row g-2 align-items-end">
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
        <a href="{{ route('admin.owner.cash-debt') }}" class="btn btn-outline-soft btn-sm">
          <i class="bi bi-arrow-counterclockwise"></i> Reset
        </a>
      </div>
    </form>
  </div>
</div>

<!-- 2 STAT CARDS UTAMA -->
<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="stat-card card-glow" style="--card-glow-color: rgba(245, 158, 11, 0.4);">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-secondary-c small fw-bold text-uppercase">Total Uang Fisik Disetor ke Brankas</span>
        <i class="bi bi-safe-fill fs-4 text-warning"></i>
      </div>
      <h3 class="fw-bold mb-1 text-warning">Rp {{ number_format($cashDebtData['total_safe_deposit'], 0, ',', '.') }}</h3>
      <div class="text-muted-c small">Uang kas bersih yang telah diserahkan kasir kepada owner</div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="stat-card card-glow" style="--card-glow-color: rgba(239, 68, 68, 0.4);">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-secondary-c small fw-bold text-uppercase">Total Komitmen Hutang PO Supplier</span>
        <i class="bi bi-clock-history fs-4 text-danger"></i>
      </div>
      <h3 class="fw-bold mb-1 text-danger">Rp {{ number_format($cashDebtData['total_unpaid_po'], 0, ',', '.') }}</h3>
      <div class="text-muted-c small">Tagihan bahan baku tempo dari seluruh cabang</div>
    </div>
  </div>
</div>

<!-- 2 TABEL: LIVE SETORAN BRANKAS (KIRI) & KALENDER JATUH TEMPO SUPPLIER (KANAN) -->
<div class="row g-4 mb-4">
  <!-- TABEL 1: SETORAN BRANKAS -->
  <div class="col-lg-6">
    <div class="card h-100" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 16px;">
      <div class="card-header bg-transparent py-3" style="border-bottom: 1px solid var(--border-subtle);">
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);"><i class="bi bi-wallet2 text-warning me-2"></i>Daftar Setoran Kas Brankas (Safe Deposit)</h5>
        <span class="text-muted-c small">Rekap uang laci kasir yang disetorkan saat tutup shift</span>
      </div>

      <div class="table-responsive">
        <table class="table table-custom mb-0">
          <thead>
            <tr>
              <th>Cabang &amp; Tanggal</th>
              <th>Kasir &amp; Shift</th>
              <th class="text-end">Sisa Modal Laci</th>
              <th class="text-end">Setor Brankas</th>
            </tr>
          </thead>
          <tbody>
            @forelse($cashDebtData['safe_deposits'] as $dep)
              <tr>
                <td>
                  <strong style="color: var(--text-primary);">{{ $dep->outlet->outlet_name ?? 'Cabang' }}</strong>
                  <div class="text-muted-c small">{{ \Carbon\Carbon::parse($dep->business_date)->format('d M Y') }}</div>
                </td>
                <td>
                  <strong style="color: var(--text-primary);">{{ $dep->cashier_name ?? 'Kasir' }}</strong>
                  <div class="text-muted-c small">{{ $dep->shift_name ?? 'Shift' }}</div>
                </td>
                <td class="text-end text-secondary-c">
                  Rp {{ number_format($dep->retained_cash_float, 0, ',', '.') }}
                </td>
                <td class="text-end fw-bold text-warning">
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

  <!-- TABEL 2: KALENDER HUTANG SUPPLIER TEMPO -->
  <div class="col-lg-6">
    <div class="card h-100" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 16px;">
      <div class="card-header bg-transparent py-3" style="border-bottom: 1px solid var(--border-subtle);">
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);"><i class="bi bi-calendar-event text-danger me-2"></i>Kalender Jatuh Tempo Tagihan Supplier</h5>
        <span class="text-muted-c small">Daftar PO tempo bahan mentah yang menunggu pelunasan</span>
      </div>

      <div class="table-responsive">
        <table class="table table-custom mb-0">
          <thead>
            <tr>
              <th>No. PO &amp; Supplier</th>
              <th>Cabang Outlet</th>
              <th class="text-center">Jatuh Tempo</th>
              <th class="text-end">Total Tagihan</th>
            </tr>
          </thead>
          <tbody>
            @forelse($cashDebtData['unpaid_purchase_orders'] as $po)
              <tr>
                <td>
                  <strong style="color: var(--text-primary);">{{ $po->po_number }}</strong>
                  <div class="text-muted-c small">{{ $po->supplier->supplier_name ?? 'Supplier' }}</div>
                </td>
                <td>
                  <span class="badge badge-soft-primary">{{ $po->outlet->outlet_name ?? 'Cabang' }}</span>
                </td>
                <td class="text-center">
                  @if($po->urgency_level === 'overdue')
                    <span class="badge badge-danger">Lewat Jatuh Tempo!</span>
                  @elseif($po->urgency_level === 'critical')
                    <span class="badge badge-danger">Besok Jatuh Tempo</span>
                  @elseif($po->urgency_level === 'warning')
                    <span class="badge badge-warning">{{ $po->days_remaining }} Hari Lagi</span>
                  @else
                    <span class="badge badge-success">{{ \Carbon\Carbon::parse($po->due_date)->format('d M Y') }}</span>
                  @endif
                </td>
                <td class="text-end fw-bold text-danger">
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
@endsection
