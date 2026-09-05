@extends('admin.layouts.app')

@section('title', 'Laporan HPP & Proyeksi Laba Rugi')

@php $activeMenu = 'hpp-report' @endphp

@section('content')
<!-- Header Page with Inline Filters & Action Buttons -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 no-print">
  <div>
    <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary);">📊 Laporan HPP & Proyeksi Laba Rugi</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.reports.dashboard') }}">Dashboard Laporan</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Laporan HPP (P&L)</span>
    </div>
  </div>

  <div class="d-flex flex-wrap align-items-center gap-2">
    <!-- Inline Filter Form -->
    <form action="{{ route('admin.keuangan.hpp-report.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
      <button type="button" class="btn btn-outline-soft btn-sm px-3" data-bs-toggle="modal" data-bs-target="#operationalModal">
        <i class="bi bi-pencil-square me-1"></i>Input Gaji & OPEX
      </button>
      <select name="month" class="form-select-modern" style="width:auto; min-width:110px;">
        @for($m = 1; $m <= 12; $m++)
          <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
          </option>
        @endfor
      </select>
      <select name="year" class="form-select-modern" style="width:auto; min-width:90px;">
        @for($y = date('Y'); $y >= 2024; $y--)
          <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endfor
      </select>
      <button type="submit" class="btn btn-primary-grad btn-sm px-3">
        <i class="bi bi-filter me-1"></i>Filter
      </button>
    </form>

    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm px-3">
      <i class="bi bi-printer me-1"></i>Cetak
    </button>
    <a href="{{ route('admin.keuangan.hpp-report.export', ['year' => $year, 'month' => $month]) }}" class="btn btn-success btn-sm px-3">
      <i class="bi bi-file-earmark-excel me-1"></i>Export Excel / CSV
    </a>
  </div>
</div>

<!-- 4 Summary KPI Cards with Glowing Borders -->
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card h-100 p-3" style="background: var(--bg-surface); border: 1.5px solid #3b82f6; border-radius: 12px; box-shadow: 0 0 10px rgba(59, 130, 246, 0.15);">
      <div class="text-muted-c text-uppercase fw-semibold mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">TOTAL OMZET KASIR</div>
      <h3 class="fw-bold mb-0" style="color: #3b82f6;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card h-100 p-3" style="background: var(--bg-surface); border: 1.5px solid #06b6d4; border-radius: 12px; box-shadow: 0 0 10px rgba(6, 182, 212, 0.15);">
      <div class="text-muted-c text-uppercase fw-semibold mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">ESTIMASI COGS (MODAL MENU)</div>
      <h3 class="fw-bold mb-0" style="color: #06b6d4;">Rp {{ number_format($totalCogsEstimated, 0, ',', '.') }}</h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card h-100 p-3" style="background: var(--bg-surface); border: 1.5px solid #ef4444; border-radius: 12px; box-shadow: 0 0 10px rgba(239, 68, 68, 0.15);">
      <div class="text-muted-c text-uppercase fw-semibold mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">KERUGIAN BAHAN BUSUK (WASTE)</div>
      <h3 class="fw-bold mb-0" style="color: #ef4444;">Rp {{ number_format($totalWasteCost, 0, ',', '.') }}</h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card h-100 p-3" style="background: var(--bg-surface); border: 1.5px solid #10b981; border-radius: 12px; box-shadow: 0 0 10px rgba(16, 185, 129, 0.15);">
      <div class="text-muted-c text-uppercase fw-semibold mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">ESTIMASI LABA BERSIH</div>
      <h3 class="fw-bold mb-0" style="color: #10b981;">Rp {{ number_format($netProfit, 0, ',', '.') }}</h3>
    </div>
  </div>
</div>

<!-- Middle Section: Rincian Kalkulasi Laba Rugi & Informasi Operasional -->
<div class="row g-3 mb-4">
  <!-- Left Side: Rincian Kalkulasi Laba Rugi Bulanan -->
  <div class="col-lg-7">
    <div class="card h-100 p-3" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 12px;">
      <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: var(--border-subtle) !important;">
        <h6 class="fw-bold mb-0" style="color: var(--text-primary);">
          Rincian Kalkulasi Laba Rugi Bulanan ({{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }})
        </h6>
        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-muted-c" data-bs-toggle="modal" data-bs-target="#operationalModal" style="font-size:0.82rem;">
          <i class="bi bi-pencil me-1"></i>Edit Biaya Operasional
        </button>
      </div>

      <div class="d-flex flex-column gap-2 mb-2">
        <div class="d-flex justify-content-between align-items-center py-1">
          <span class="fw-bold" style="color: var(--text-primary);">Total Omzet Penjualan (Kasir)</span>
          <span class="fw-bold" style="color: #3b82f6;">Rp {{ number_format($totalRevenue, 2, ',', '.') }}</span>
        </div>

        <div class="d-flex justify-content-between align-items-center py-1">
          <span class="text-muted-c">(-) Estimasi COGS (Modal Porsi Terjual)</span>
          <span class="text-danger">- Rp {{ number_format($totalCogsEstimated, 2, ',', '.') }}</span>
        </div>

        <div class="d-flex justify-content-between align-items-center py-1 ps-3 border-start border-3" style="border-color: #8b5cf6 !important; background: rgba(139,92,246,0.04);">
          <small class="text-muted-c"><i class="bi bi-cart-check me-1"></i>Pembelian Bahan Mentah PO (Receiving Diterima)</small>
          <small class="fw-bold" style="color: #8b5cf6;">Rp {{ number_format($totalPoReceivedCost, 2, ',', '.') }}</small>
        </div>

        <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background: rgba(16, 185, 129, 0.08);">
          <div class="d-flex align-items-center gap-2">
            <span class="fw-bold" style="color: #10b981;">LABA KOTOR (GROSS PROFIT)</span>
            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 fs-xs">Margin: {{ number_format($grossMarginPercent, 1) }}%</span>
          </div>
          <span class="fw-bold" style="color: #10b981;">Rp {{ number_format($grossProfit, 2, ',', '.') }}</span>
        </div>

        <div class="d-flex justify-content-between align-items-center py-1">
          <span class="text-muted-c">(-) Total Kerugian Bahan Terbuang (Waste Log)</span>
          <span class="text-danger">- Rp {{ number_format($totalWasteCost, 2, ',', '.') }}</span>
        </div>

        <div class="d-flex justify-content-between align-items-center py-1">
          <span class="text-muted-c">(-) Biaya Gaji Karyawan (Labor Cost)</span>
          <span class="text-danger">- Rp {{ number_format($totalLaborCost, 2, ',', '.') }}</span>
        </div>

        <div class="d-flex justify-content-between align-items-center py-1">
          <span class="text-muted-c">(-) Biaya Operasional / Overhead (Listrik, Sewa, Air, WiFi, dll)</span>
          <span class="text-danger">- Rp {{ number_format($totalOverheadCost, 2, ',', '.') }}</span>
        </div>

        <div class="d-flex justify-content-between align-items-center p-3 rounded mt-2" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3);">
          <div class="d-flex align-items-center gap-2">
            <span class="fw-bold" style="color: #10b981; font-size:0.95rem;">ESTIMASI LABA BERSIH (NET PROFIT)</span>
            <span class="badge {{ $netMarginPercent >= 0 ? 'bg-success' : 'bg-danger' }} fs-xs">Net Margin: {{ number_format($netMarginPercent, 1) }}%</span>
          </div>
          <span class="fw-bold fs-5" style="color: #10b981;">Rp {{ number_format($netProfit, 2, ',', '.') }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Informasi & Catatan Operasional -->
  <div class="col-lg-5">
    <div class="card h-100 p-3 d-flex flex-column justify-content-between" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 12px;">
      <div>
        <h6 class="fw-bold mb-3 pb-2 border-bottom" style="color: var(--text-primary); border-color: var(--border-subtle) !important;">
          Informasi & Catatan Operasional
        </h6>

        <div class="mb-3">
          <small class="text-muted-c d-block mb-1">Gaji Karyawan Bulan Ini:</small>
          <strong class="fs-6" style="color: var(--text-primary);">Rp {{ number_format($totalLaborCost, 0, ',', '.') }}</strong>
        </div>

        <div class="mb-3">
          <small class="text-muted-c d-block mb-1">Operasional Listrik/Sewa/WiFi:</small>
          <strong class="fs-6" style="color: var(--text-primary);">Rp {{ number_format($totalOverheadCost, 0, ',', '.') }}</strong>
        </div>

        <div class="mb-3">
          <small class="text-muted-c d-block mb-1">Catatan Rincian:</small>
          <div class="p-3 rounded" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle); font-size:0.85rem; color: var(--text-secondary); min-height:80px;">
            {{ $notes ? $notes : 'Belum ada catatan operasional untuk bulan ini.' }}
          </div>
        </div>
      </div>

      <button type="button" class="btn btn-outline-soft w-100 py-2 mt-3" data-bs-toggle="modal" data-bs-target="#operationalModal">
        <i class="bi bi-pencil-square me-1"></i>Update Gaji & Listrik Bulan Ini
      </button>
    </div>
  </div>
</div>

<!-- Breakdown Sales & COGS per Product Table -->
<div class="card mb-4" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 12px;">
  <div class="card-header-flex d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">Rincian Penjualan & Modal COGS Per-Menu ({{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }})</h6>
      <span class="chip-tag mt-1">{{ count($productBreakdown) }} menu terjual</span>
    </div>
    <div class="d-flex align-items-center gap-2">
      <label for="hppPerPageSelect" class="form-label mb-0 text-muted-c" style="font-size:0.82rem;"><i class="bi bi-list-ol me-1"></i>Tampilkan:</label>
      <select id="hppPerPageSelect" class="form-select form-select-sm" style="width: auto; min-width: 90px; background: var(--bg-elevated); border-color: var(--border-subtle); color: var(--text-primary); cursor: pointer;">
        <option value="10" selected>10</option>
        <option value="20">20</option>
        <option value="50">50</option>
        <option value="100">100</option>
        <option value="all">Semua (Custom)</option>
      </select>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern" id="hppBreakdownTable">
        <thead>
          <tr>
            <th class="ps-3">Nama Menu Terjual</th>
            <th class="text-center">QTY Terjual</th>
            <th class="text-end">Harga Jual / Porsi</th>
            <th class="text-end">Total Omzet</th>
            <th class="text-end">Modal COGS / Porsi</th>
            <th class="text-end">Total Modal COGS</th>
            <th class="text-end">Laba Kotor Menu</th>
            <th class="text-center">Margin (%)</th>
            <th class="text-center pe-3">Detail Resep</th>
          </tr>
        </thead>
        <tbody>
          @forelse($productBreakdown as $row)
          <tr>
            <td class="ps-3">
              <div class="fw-bold" style="color: var(--text-primary);">{{ $row['product_name'] }}</div>
              @if($row['has_recipe'])
                <small class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Resep: {{ $row['recipe_name'] }}</small>
              @else
                <small class="text-warning"><i class="bi bi-exclamation-triangle-fill me-1"></i>Belum Terhubung Resep</small>
              @endif
            </td>
            <td class="text-center fw-bold">
              <span class="chip-tag px-2 py-1" style="background: rgba(99, 102, 241, 0.15); color: var(--primary); font-size:0.85rem;">
                <i class="bi bi-bag-check me-1"></i>{{ number_format($row['qty_sold']) }} Pcs
              </span>
            </td>
            <td class="text-end" style="color: var(--text-secondary);">Rp {{ number_format($row['unit_price'], 0, ',', '.') }}</td>
            <td class="text-end fw-bold text-primary">Rp {{ number_format($row['total_revenue'], 0, ',', '.') }}</td>
            <td class="text-end text-muted-c">Rp {{ number_format($row['unit_cogs'], 2, ',', '.') }}</td>
            <td class="text-end fw-semibold text-danger">Rp {{ number_format($row['total_cogs'], 2, ',', '.') }}</td>
            <td class="text-end fw-bold text-success">Rp {{ number_format($row['gross_profit'], 0, ',', '.') }}</td>
            <td class="text-center">
              @if($row['margin_percent'] >= 60)
                <span class="badge" style="background: rgba(52, 211, 153, 0.15); color: var(--success);">{{ number_format($row['margin_percent'], 1) }}%</span>
              @elseif($row['margin_percent'] >= 30)
                <span class="badge" style="background: rgba(34, 211, 238, 0.15); color: var(--info);">{{ number_format($row['margin_percent'], 1) }}%</span>
              @else
                <span class="badge" style="background: rgba(251, 191, 36, 0.15); color: var(--warning);">{{ number_format($row['margin_percent'], 1) }}%</span>
              @endif
            </td>
            <td class="text-center pe-3">
              <button type="button" class="btn btn-ghost btn-sm text-primary btn-view-recipe-detail" data-row="{{ json_encode($row) }}">
                <i class="bi bi-receipt me-1"></i>Rincian
              </button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center py-4 text-muted-c">
              <i class="bi bi-cart-x fs-2 d-block mb-2"></i>Belum ada data transaksi menu terjual pada bulan terpilih.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer d-flex flex-wrap align-items-center justify-content-between py-2 px-3 border-top" style="border-color: var(--border-subtle) !important; background: var(--bg-surface);">
    <div class="text-muted-c" style="font-size: 0.83rem;">
      Menampilkan <span id="hppPageStart" class="fw-bold text-primary">1</span> - <span id="hppPageEnd" class="fw-bold text-primary">10</span> dari <span id="hppTotalItems" class="fw-bold">{{ count($productBreakdown) }}</span> menu terjual
    </div>
    <nav aria-label="Pagination Rincian HPP">
      <ul class="pagination pagination-sm mb-0 gap-1" id="hppPaginationControls">
        <!-- Generasi Tombol Halaman via JS -->
      </ul>
    </nav>
  </div>
</div>

<!-- Panduan Lengkap Alur Penggunaan & Formula Kalkulasi Keuangan -->
<div class="card mb-4" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
  <div class="card-header border-bottom py-3 px-3" style="border-color: var(--border-subtle) !important; background: var(--bg-surface);">
    <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
      <i class="bi bi-journal-bookmark-fill text-primary me-2"></i>Panduan & Alur Lengkap Penggunaan Sistem Keuangan HPP & Laba Rugi
    </h6>
  </div>
  <div class="card-body p-4">
    <!-- 5 Step Interactive Flowchart Cards -->
    <div class="row g-3 mb-4">
      <div class="col-md">
        <div class="p-3 rounded h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
          <div class="d-flex align-items-center mb-2">
            <span class="badge rounded-circle me-2" style="background: #2563eb; color: #fff; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">1</span>
            <strong style="font-size:0.85rem; color: var(--text-primary);">Pembelian Bahan (PO)</strong>
          </div>
          <p class="text-muted-c mb-0" style="font-size:0.78rem;">
            Beli bahan mentah via <strong>Purchase Order (PO)</strong> di menu Keuangan. Saat barang diterima (Receiving), stok bahan mentah otomatis bertambah & harga per unit dihitung.
          </p>
        </div>
      </div>

      <div class="col-md">
        <div class="p-3 rounded h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
          <div class="d-flex align-items-center mb-2">
            <span class="badge rounded-circle me-2" style="background: #7c3aed; color: #fff; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">2</span>
            <strong style="font-size:0.85rem; color: var(--text-primary);">Resep & COGS Menu</strong>
          </div>
          <p class="text-muted-c mb-0" style="font-size:0.78rem;">
            Daftarkan komposisi takaran bahan mentah per porsi di menu <strong>Resep COGS</strong>. Sistem menghitung otomatis modal pokok (HPP) & rekomendasi harga jual.
          </p>
        </div>
      </div>

      <div class="col-md">
        <div class="p-3 rounded h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
          <div class="d-flex align-items-center mb-2">
            <span class="badge rounded-circle me-2" style="background: #0891b2; color: #fff; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">3</span>
            <strong style="font-size:0.85rem; color: var(--text-primary);">Penjualan Kasir (POS)</strong>
          </div>
          <p class="text-muted-c mb-0" style="font-size:0.78rem;">
            Kasir memproses transaksi pesanan. Setiap menu terjual otomatis dihitung modal COGS-nya berdasarkan resep yang terhubung dan diakumulasikan ke omzet bulanan.
          </p>
        </div>
      </div>

      <div class="col-md">
        <div class="p-3 rounded h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
          <div class="d-flex align-items-center mb-2">
            <span class="badge rounded-circle me-2" style="background: #ef4444; color: #fff; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">4</span>
            <strong style="font-size:0.85rem; color: var(--text-primary);">Bahan Busuk (Waste)</strong>
          </div>
          <p class="text-muted-c mb-0" style="font-size:0.78rem;">
            Catat bahan mentah busuk/expired di menu <strong>Waste Log</strong>. Kerugian bahan terbuang akan dikurangi langsung sebagai pengurang laba bersih toko.
          </p>
        </div>
      </div>

      <div class="col-md">
        <div class="p-3 rounded h-100" style="background: var(--bg-elevated); border: 1px solid #10b981;">
          <div class="d-flex align-items-center mb-2">
            <span class="badge rounded-circle me-2" style="background: #10b981; color: #fff; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">5</span>
            <strong style="font-size:0.85rem; color: var(--text-primary);">Evaluasi Laba Rugi</strong>
          </div>
          <p class="text-muted-c mb-0" style="font-size:0.78rem;">
            Klik tombol <strong>"Edit Biaya Operasional"</strong> untuk menginput Gaji Karyawan & Listrik. Sistem akan menyajikan Laba Bersih & Margin (%) secara akurat.
          </p>
        </div>
      </div>
    </div>

    <!-- Formula Kalkulasi Keuangan Section -->
    <div class="p-3 rounded" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
      <h6 class="fw-bold mb-2 text-uppercase text-muted-c" style="font-size:0.75rem; letter-spacing:0.5px;">
        <i class="bi bi-calculator me-1 text-primary"></i>Formula & Rumus Perhitungan Keuangan Teraplikasi:
      </h6>
      <div class="row g-2 text-muted-c" style="font-size:0.82rem;">
        <div class="col-md-4">
          <div class="p-2 rounded bg-surface border">
            <span class="fw-bold text-primary">1. LABA KOTOR (GROSS PROFIT)</span><br>
            <code>Total Omzet Kasir - Estimasi COGS Menu Terjual</code>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-2 rounded bg-surface border">
            <span class="fw-bold text-success">2. LABA BERSIH (NET PROFIT)</span><br>
            <code>Laba Kotor - Waste - Gaji Karyawan - Listrik/Overhead</code>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-2 rounded bg-surface border">
            <span class="fw-bold text-info">3. MARGIN LABA MENU (%)</span><br>
            <code>((Harga Jual - Modal COGS Porsi) / Harga Jual) x 100%</code>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detail Resep & Pemakaian Bahan -->
<div class="modal fade" id="recipeDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); color: var(--text-primary);">
      <div class="modal-header border-0 pb-0">
        <div>
          <h6 class="modal-title fw-bold" id="modalProductName" style="color: var(--text-primary);">Rincian Resep & Modal Menu</h6>
          <small class="text-muted-c" id="modalRecipeName">Resep Standar Terhubung</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body pt-3">
        <!-- Summary Cards in Modal -->
        <div class="row g-2 mb-3">
          <div class="col-md-3">
            <div class="p-2 rounded text-center" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
              <small class="text-muted-c d-block" style="font-size:0.75rem;">QTY Terjual</small>
              <strong class="text-primary fs-6" id="modalQtySold">0 Pcs</strong>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-2 rounded text-center" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
              <small class="text-muted-c d-block" style="font-size:0.75rem;">Total Omzet</small>
              <strong class="text-success fs-6" id="modalTotalRevenue">Rp 0</strong>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-2 rounded text-center" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
              <small class="text-muted-c d-block" style="font-size:0.75rem;">Modal COGS / Porsi</small>
              <strong class="text-warning fs-6" id="modalUnitCogs">Rp 0</strong>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-2 rounded text-center" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle);">
              <small class="text-muted-c d-block" style="font-size:0.75rem;">Total Modal COGS</small>
              <strong class="text-danger fs-6" id="modalTotalCogs">Rp 0</strong>
            </div>
          </div>
        </div>

        <h6 class="fw-bold mb-2" style="font-size:0.9rem; color: var(--text-primary);"><i class="bi bi-list-task me-1 text-primary"></i>Komposisi Resep & Total Pemakaian Bahan Mentah Bulan Ini</h6>
        <div class="table-responsive">
          <table class="table-modern" style="font-size:0.85rem;">
            <thead>
              <tr>
                <th class="ps-2">Bahan Mentah</th>
                <th class="text-center">Takaran / 1 Porsi</th>
                <th class="text-end">Harga Efektif Bahan</th>
                <th class="text-end">Subtotal Modal / Porsi</th>
                <th class="text-end pe-2">Total Pemakaian Bahan Bulan Ini</th>
              </tr>
            </thead>
            <tbody id="modalRecipeItemsBody">
              <!-- Rendered dynamically -->
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-soft px-4" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Input / Edit Biaya Operasional -->
<div class="modal fade" id="operationalModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); color: var(--text-primary);">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-bold" style="color: var(--text-primary);"><i class="bi bi-wallet2 me-2 text-primary"></i>Input Biaya Gaji & Operasional ({{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }})</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.keuangan.hpp-report.store-operational') }}" method="POST">
        @csrf
        <input type="hidden" name="year" value="{{ $year }}">
        <input type="hidden" name="month" value="{{ $month }}">

        <div class="modal-body pt-3">
          <div class="mb-3">
            <label for="total_labor_cost" class="form-label-modern">Total Gaji Karyawan Bulan Ini (Rp) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="total_labor_cost" id="total_labor_cost" class="form-control-modern" value="{{ old('total_labor_cost', $totalLaborCost) }}" placeholder="Contoh: 5000000" required>
            <span class="text-muted-c d-block mt-1" style="font-size:0.78rem;">Total seluruh gaji & komisi staff toko bulan ini.</span>
          </div>

          <div class="mb-3">
            <label for="total_overhead_cost" class="form-label-modern">Total Biaya Operasional / Overhead (Rp) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="total_overhead_cost" id="total_overhead_cost" class="form-control-modern" value="{{ old('total_overhead_cost', $totalOverheadCost) }}" placeholder="Contoh: 2500000" required>
            <span class="text-muted-c d-block mt-1" style="font-size:0.78rem;">Total tagihan listrik PLN, sewa tempat, air, internet WiFi, dll.</span>
          </div>

          <div class="mb-2">
            <label for="notes" class="form-label-modern">Catatan Rincian (Opsional)</label>
            <textarea name="notes" id="notes" rows="3" class="form-control-modern" placeholder="Contoh: Gaji 2 Kasir @2.5jt, Listrik PLN: 1.2jt, WiFi: 300rb, Sewa: 1jt">{{ old('notes', $notes) }}</textarea>
          </div>
        </div>

        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-soft px-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-grad px-4">Simpan Ke Laporan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.btn-view-recipe-detail').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const data = JSON.parse(this.dataset.row);

      document.getElementById('modalProductName').textContent = 'Rincian Resep: ' + data.product_name;
      document.getElementById('modalRecipeName').textContent = data.has_recipe ? 'Resep Standar: ' + data.recipe_name : 'Belum Ada Resep Terhubung';
      document.getElementById('modalQtySold').textContent = data.qty_sold + ' Pcs';
      document.getElementById('modalTotalRevenue').textContent = 'Rp ' + Number(data.total_revenue).toLocaleString('id-ID');
      document.getElementById('modalUnitCogs').textContent = 'Rp ' + Number(data.unit_cogs).toLocaleString('id-ID');
      document.getElementById('modalTotalCogs').textContent = 'Rp ' + Number(data.total_cogs).toLocaleString('id-ID');

      const tbody = document.getElementById('modalRecipeItemsBody');
      tbody.innerHTML = '';

      if (data.recipe_items && data.recipe_items.length > 0) {
        data.recipe_items.forEach(function(item) {
          const totalMaterialUsed = (item.ingredient_qty * data.qty_sold).toFixed(2);
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td class="ps-2 fw-bold" style="color: var(--text-primary);">${item.material_name}</td>
            <td class="text-center">${item.ingredient_qty} ${item.unit}</td>
            <td class="text-end text-muted-c">Rp ${Number(item.effective_price).toLocaleString('id-ID')} / ${item.unit}</td>
            <td class="text-end fw-semibold text-warning">Rp ${Number(item.ingredient_cost).toLocaleString('id-ID')}</td>
            <td class="text-end pe-2 fw-bold text-primary">${totalMaterialUsed} ${item.unit}</td>
          `;
          tbody.appendChild(tr);
        });
      } else {
        tbody.innerHTML = `
          <tr>
            <td colspan="5" class="text-center text-muted-c py-3">
              Belum ada rincian bahan mentah terhubung untuk menu ini.
            </td>
          </tr>
        `;
      }

      const modalEl = document.getElementById('recipeDetailModal');
      const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
      modal.show();
    });
  });

  // =========================================================================
  // CLIENT-SIDE TABLE PAGINATION FOR HPP BREAKDOWN TABLE
  // =========================================================================
  const table = document.getElementById('hppBreakdownTable');
  if (table) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => !r.querySelector('td[colspan]'));
    const perPageSelect = document.getElementById('hppPerPageSelect');
    const pageStartEl = document.getElementById('hppPageStart');
    const pageEndEl = document.getElementById('hppPageEnd');
    const totalItemsEl = document.getElementById('hppTotalItems');
    const paginationControls = document.getElementById('hppPaginationControls');

    let currentPage = 1;
    let perPage = 10;

    function renderPagination() {
      const totalRows = rows.length;
      if (totalRows === 0) return;

      const effectivePerPage = (perPage === 'all') ? totalRows : parseInt(perPage, 10);
      const totalPages = Math.ceil(totalRows / effectivePerPage);

      if (currentPage > totalPages) currentPage = totalPages;
      if (currentPage < 1) currentPage = 1;

      const startIndex = (currentPage - 1) * effectivePerPage;
      const endIndex = Math.min(startIndex + effectivePerPage, totalRows);

      rows.forEach((row, index) => {
        if (index >= startIndex && index < endIndex) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });

      if (pageStartEl) pageStartEl.textContent = (totalRows > 0) ? (startIndex + 1) : 0;
      if (pageEndEl) pageEndEl.textContent = endIndex;
      if (totalItemsEl) totalItemsEl.textContent = totalRows;

      if (!paginationControls) return;
      paginationControls.innerHTML = '';

      if (totalPages <= 1) return;

      // Prev Button
      const prevLi = document.createElement('li');
      prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
      prevLi.innerHTML = `<button class="page-link py-1 px-2" style="border-radius:6px; cursor:pointer;"><i class="bi bi-chevron-left"></i></button>`;
      prevLi.addEventListener('click', () => {
        if (currentPage > 1) {
          currentPage--;
          renderPagination();
        }
      });
      paginationControls.appendChild(prevLi);

      // Page numbers (smart sliding pagination)
      let startPage = Math.max(1, currentPage - 2);
      let endPage = Math.min(totalPages, currentPage + 2);

      if (startPage > 1) {
        const firstLi = document.createElement('li');
        firstLi.className = 'page-item';
        firstLi.innerHTML = `<button class="page-link py-1 px-2" style="border-radius:6px; cursor:pointer;">1</button>`;
        firstLi.addEventListener('click', () => { currentPage = 1; renderPagination(); });
        paginationControls.appendChild(firstLi);

        if (startPage > 2) {
          const dotsLi = document.createElement('li');
          dotsLi.className = 'page-item disabled';
          dotsLi.innerHTML = `<span class="page-link py-1 px-2 border-0">...</span>`;
          paginationControls.appendChild(dotsLi);
        }
      }

      for (let p = startPage; p <= endPage; p++) {
        const li = document.createElement('li');
        li.className = `page-item ${p === currentPage ? 'active' : ''}`;
        li.innerHTML = `<button class="page-link py-1 px-2" style="border-radius:6px; cursor:pointer;">${p}</button>`;
        li.addEventListener('click', () => {
          currentPage = p;
          renderPagination();
        });
        paginationControls.appendChild(li);
      }

      if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
          const dotsLi = document.createElement('li');
          dotsLi.className = 'page-item disabled';
          dotsLi.innerHTML = `<span class="page-link py-1 px-2 border-0">...</span>`;
          paginationControls.appendChild(dotsLi);
        }
        const lastLi = document.createElement('li');
        lastLi.className = 'page-item';
        lastLi.innerHTML = `<button class="page-link py-1 px-2" style="border-radius:6px; cursor:pointer;">${totalPages}</button>`;
        lastLi.addEventListener('click', () => { currentPage = totalPages; renderPagination(); });
        paginationControls.appendChild(lastLi);
      }

      // Next Button
      const nextLi = document.createElement('li');
      nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
      nextLi.innerHTML = `<button class="page-link py-1 px-2" style="border-radius:6px; cursor:pointer;"><i class="bi bi-chevron-right"></i></button>`;
      nextLi.addEventListener('click', () => {
        if (currentPage < totalPages) {
          currentPage++;
          renderPagination();
        }
      });
      paginationControls.appendChild(nextLi);
    }

    if (perPageSelect) {
      perPageSelect.addEventListener('change', function() {
        perPage = this.value;
        currentPage = 1;
        renderPagination();
      });
    }

    renderPagination();
  }
});
</script>
@endpush
