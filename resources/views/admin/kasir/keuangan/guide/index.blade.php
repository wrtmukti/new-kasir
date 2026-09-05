@extends('admin.layouts.app')

@section('title', 'Panduan Arsitektur Finansial, HPP Resep & Arus Kas')

@php $activeMenu = 'financial-guide' @endphp

@push('styles')
<style>
  .guide-hero-card {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.14) 0%, rgba(16, 185, 129, 0.10) 100%);
    border: 1px solid rgba(59, 130, 246, 0.3);
    border-radius: 18px;
    padding: 2rem;
    margin-bottom: 2rem;
  }

  .nav-pill-guide {
    background: var(--bg-surface, #1A1E27);
    border: 1px solid var(--border-subtle, #2F3748);
    color: var(--text-secondary, #94a3b8);
    border-radius: 20px;
    padding: 0.45rem 1rem;
    font-size: 0.82rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
  }

  .nav-pill-guide:hover {
    background: rgba(59, 130, 246, 0.15);
    border-color: #3b82f6;
    color: #60a5fa;
  }

  .chapter-box {
    background: var(--bg-surface, #1A1E27);
    border: 1px solid var(--border-subtle, #2F3748);
    border-radius: 16px;
    margin-bottom: 1.75rem;
    overflow: hidden;
    transition: border-color 0.2s ease;
  }

  .chapter-box:hover {
    border-color: rgba(59, 130, 246, 0.4);
  }

  .chapter-header-custom {
    background: var(--bg-elevated, #222834);
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-subtle, #2F3748);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
  }

  .chapter-body-custom {
    padding: 1.5rem;
  }

  .flow-card-box {
    background: rgba(0, 0, 0, 0.15);
    border: 1px solid var(--border-subtle, #2F3748);
    border-radius: 12px;
    padding: 1.25rem;
    height: 100%;
  }

  .formula-pill {
    background: rgba(0, 0, 0, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 10px;
    padding: 0.85rem 1.1rem;
    font-family: var(--font-mono, monospace);
    font-size: 0.92rem;
    color: #67e8f9;
  }

  [data-theme="light"] .guide-hero-card {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(16, 185, 129, 0.06) 100%) !important;
    border-color: #cbd5e1 !important;
  }
  [data-theme="light"] .chapter-box {
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
  }
  [data-theme="light"] .chapter-header-custom {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
  }
  [data-theme="light"] .flow-card-box {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
  }
  [data-theme="light"] .nav-pill-guide {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #475569 !important;
  }
  [data-theme="light"] .formula-pill {
    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
    color: #0369a1 !important;
  }
</style>
@endpush

@section('content')
<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>📖 Panduan Finansial, HPP Resep &amp; Arus Kas (Plan B)</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Keuangan</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Panduan Finansial &amp; Kas</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('admin.keuangan.hpp-report.index') }}" class="btn btn-outline-soft">
      <i class="bi bi-pie-chart me-1"></i> Laporan HPP
    </a>
    <a href="{{ route('admin.keuangan.cashflow-report.index') }}" class="btn btn-outline-soft">
      <i class="bi bi-cash-stack me-1"></i> Laporan Arus Kas
    </a>
  </div>
</div>

<!-- HERO BANNER -->
<div class="guide-hero-card">
  <div class="d-flex align-items-center gap-2 mb-2">
    <span class="badge badge-primary px-3 py-1.5">ARSITEKTUR PLAN B</span>
    <span class="text-muted-c small"><i class="bi bi-shield-check text-success me-1"></i>Enterprise-Ready Multi-Tenant POS</span>
  </div>
  <h3 class="fw-bold mb-2" style="color: var(--text-primary);">Pemisahan Murni: Laba Rugi Resep vs Arus Kas Nyata vs Buku Kas Laci</h3>
  <p class="text-secondary-c mb-3" style="max-width: 850px; font-size: 0.92rem;">
    Dokumen ini adalah pedoman lengkap dan baku yang menjelaskan bagaimana setiap rupiah uang masuk (*Cash In*), uang keluar (*Cash Out*), modal resep (*Theoretical COGS*), dan belanja bahan baku (*Purchase Order*) dicatat secara akurat di Nexora POS tanpa saling mencemari.
  </p>

  <!-- QUICK NAVIGATION PILLS -->
  <div class="d-flex align-items-center gap-2 flex-wrap pt-2">
    <a href="#bab1" class="nav-pill-guide"><i class="bi bi-layers me-1"></i> 1. 3 Pilar Finansial</a>
    <a href="#bab2" class="nav-pill-guide"><i class="bi bi-arrow-down-left-circle text-success me-1"></i> 2. Anatomi Cash In</a>
    <a href="#bab3" class="nav-pill-guide"><i class="bi bi-arrow-up-right-circle text-danger me-1"></i> 3. Anatomi Cash Out</a>
    <a href="#bab4" class="nav-pill-guide"><i class="bi bi-calculator me-1"></i> 4. Rumus &amp; Formula</a>
    <a href="#bab5" class="nav-pill-guide"><i class="bi bi-person-badge me-1"></i> 5. SOP Kasir &amp; Laci</a>
    <a href="#bab6" class="nav-pill-guide"><i class="bi bi-question-circle me-1"></i> 6. FAQ Owner</a>
  </div>
</div>


<!-- =========================================================================
     BAB 1: 3 PILAR UTAMA FINANSIAL RESTORAN
     ========================================================================= -->
<div class="chapter-box" id="bab1">
  <div class="chapter-header-custom">
    <div class="d-flex align-items-center gap-2">
      <div class="stat-icon" style="width:36px; height:36px; background: rgba(59,130,246,0.15); color: #3b82f6; border-radius:8px; display:flex; align-items:center; justify-content:center;">
        <i class="bi bi-layers"></i>
      </div>
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">BAB 1: 3 Pilar Utama Keuangan F&amp;B di Nexora POS</h5>
        <span class="text-muted-c small">Memahami perbedaan fundamental antara Laba Rugi, Arus Kas, dan Buku Kas Shift Kasir</span>
      </div>
    </div>
  </div>

  <div class="chapter-body-custom">
    <p class="text-secondary-c" style="font-size:0.9rem;">
      Banyak pemilik restoran dan sistem kasir konvensional mengalami kebingungan karena mencampuradukkan <strong>"Uang di Laci Kasir"</strong> dengan <strong>"Laba Bersih Toko"</strong>. Di Nexora POS Plan B, ketiga dimensi ini dipisahkan secara tegas:
    </p>

    <div class="row g-3 mt-1">
      <!-- 1. Laba Rugi -->
      <div class="col-lg-4">
        <div class="flow-card-box">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge badge-primary">P&amp;L (LABA RUGI)</span>
            <i class="bi bi-pie-chart fs-5 text-primary"></i>
          </div>
          <h6 class="fw-bold mb-2" style="color: var(--text-primary);">1. Laporan HPP &amp; Laba Rugi</h6>
          <ul class="text-secondary-c small ps-3 mb-2" style="line-height: 1.6;">
            <li><strong>Prinsip:</strong> Akrual Murni (Theoretical Recipe Cost).</li>
            <li><strong>Tujuan:</strong> Mengukur <em>Profitabilitas &amp; Margin Resep</em> dari menu yang terjual.</li>
            <li><strong>Fokus:</strong> Omzet Kasir &minus; Modal Resep HPP &minus; Waste Bahan &minus; Gaji &minus; Listrik.</li>
            <li><strong>Ciri Khas:</strong> Belanja stok PO berton-ton TIDAK memotong laba resep hari ini.</li>
          </ul>
        </div>
      </div>

      <!-- 2. Arus Kas -->
      <div class="col-lg-4">
        <div class="flow-card-box">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge badge-success">CASH FLOW</span>
            <i class="bi bi-cash-stack fs-5 text-success"></i>
          </div>
          <h6 class="fw-bold mb-2" style="color: var(--text-primary);">2. Laporan Arus Kas Nyata</h6>
          <ul class="text-secondary-c small ps-3 mb-2" style="line-height: 1.6;">
            <li><strong>Prinsip:</strong> Cash Basis Murni (Real Physical Money).</li>
            <li><strong>Tujuan:</strong> Mengukur <em>Likuiditas Uang Nyata</em> yang masuk dan keluar dari outlet.</li>
            <li><strong>Fokus:</strong> Total Uang Masuk &minus; PO Bahan Lunas &minus; Petty Cash &minus; Gaji/OPEX Dibayar.</li>
            <li><strong>Ciri Khas:</strong> Hanya mencatat saat uang nyata berpindah tangan. Kerugian bahan busuk (non-kas) dieliminasi dari sini.</li>
          </ul>
        </div>
      </div>

      <!-- 3. Buku Kas Laci -->
      <div class="col-lg-4">
        <div class="flow-card-box">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge badge-warning">CASH DRAWER</span>
            <i class="bi bi-wallet2 fs-5 text-warning"></i>
          </div>
          <h6 class="fw-bold mb-2" style="color: var(--text-primary);">3. Buku Kas Laci Kasir (Shift)</h6>
          <ul class="text-secondary-c small ps-3 mb-2" style="line-height: 1.6;">
            <li><strong>Prinsip:</strong> Shift Physical Audit (Clock-In &rarr; Clock-Out).</li>
            <li><strong>Tujuan:</strong> Mencegah kebocoran kas, mendeteksi selisih kasir, dan mengelola uang kembalian.</li>
            <li><strong>Fokus:</strong> Modal Awal + Sales Tunai + Topup Masuk &minus; Petty Cash Keluar.</li>
            <li><strong>Ciri Khas:</strong> Memisahkan uang setoran brankas (*deposit*) dan modal yang ditinggal (*float*).</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- =========================================================================
     BAB 2: ANATOMI LENGKAP "CASH IN" KASIR
     ========================================================================= -->
<div class="chapter-box" id="bab2">
  <div class="chapter-header-custom">
    <div class="d-flex align-items-center gap-2">
      <div class="stat-icon" style="width:36px; height:36px; background: rgba(34,197,94,0.15); color: #22c55e; border-radius:8px; display:flex; align-items:center; justify-content:center;">
        <i class="bi bi-arrow-down-left-circle"></i>
      </div>
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">BAB 2: Mengapa Cash In Kasir Sama Sekali TIDAK Masuk HPP?</h5>
        <span class="text-muted-c small">Bedah skenario uang masuk ke laci kasir dan aliran sistemnya</span>
      </div>
    </div>
  </div>

  <div class="chapter-body-custom">
    <div class="alert alert-info border-0 mb-4" style="background: rgba(34, 211, 238, 0.1); border-left: 4px solid #22d3ee !important; color: var(--text-primary);">
      <h6 class="fw-bold text-info mb-1"><i class="bi bi-lightbulb me-1"></i> Kaidah Akuntansi Restoran:</h6>
      <p class="mb-0 small">
        <strong>HPP (Harga Pokok Penjualan / COGS)</strong> hanyalah biaya bahan mentah yang melekat pada <em>makanan/minuman yang terjual</em> ke pelanggan. Menambahkan uang ke laci kasir <strong>bukanlah biaya memasak menu</strong>, sehingga tidak boleh menyentuh HPP resep!
      </p>
    </div>

    <h6 class="fw-bold mb-3" style="color: var(--text-primary);">4 Skenario Nyata Cash In di Restoran:</h6>

    <div class="row g-3 mb-4">
      <!-- Skenario A -->
      <div class="col-md-6">
        <div class="flow-card-box">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge badge-success">KASUS A</span>
            <strong style="color: var(--text-primary);">Modal Awal Kasir (Starting Float)</strong>
          </div>
          <p class="text-secondary-c small mb-2">
            Uang modal pecahan kecil yang dimasukkan ke laci saat buka toko (misal: Rp 200.000).
          </p>
          <div class="text-muted-c" style="font-size:0.78rem;">
            &bull; <strong>Aliran:</strong> Masuk ke <code>daily_closings.starting_cash</code> &rarr; Menjadi batas dasar uang laci saat clock-out.
          </div>
        </div>
      </div>

      <!-- Skenario B -->
      <div class="col-md-6">
        <div class="flow-card-box">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge badge-success">KASUS B</span>
            <strong style="color: var(--text-primary);">Top-Up Uang Kembalian Owner</strong>
          </div>
          <p class="text-secondary-c small mb-2">
            Pecahan Rp 2.000 / Rp 5.000 habis di siang hari. Owner menambahkan Rp 150.000 uang receh ke laci kasir.
          </p>
          <div class="text-muted-c" style="font-size:0.78rem;">
            &bull; <strong>Aliran:</strong> Kasir klik tombol <strong>+ Kas Masuk</strong> &rarr; Masuk ke <code>cash_drawer_logs</code> &rarr; Masuk kartu Inflow Cash Flow.
          </div>
        </div>
      </div>

      <!-- Skenario C -->
      <div class="col-md-6">
        <div class="flow-card-box">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge badge-info">KASUS C</span>
            <strong style="color: var(--text-primary);">Pengembalian Sisa Belanja Dapur (Refund)</strong>
          </div>
          <p class="text-secondary-c small mb-2">
            Koki diberi Rp 100.000 untuk beli cabai darurat. Total belanja hanya Rp 70.000. Sisa Rp 30.000 dikembalikan ke laci kasir.
          </p>
          <div class="text-muted-c" style="font-size:0.78rem;">
            &bull; <strong>Aliran:</strong> Catat Kas Masuk (Kategori: <em>Sisa Belanja Cabai</em> Rp 30.000) &rarr; Saldo laci kasir bertambah kembali tanpa mengganggu HPP resep.
          </div>
        </div>
      </div>

      <!-- Skenario D -->
      <div class="col-md-6">
        <div class="flow-card-box">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge badge-info">KASUS D</span>
            <strong style="color: var(--text-primary);">Konsinyasi / Pelunasan Kasbon Karyawan</strong>
          </div>
          <p class="text-secondary-c small mb-2">
            Staf mencicil kasbon tunai Rp 100.000 ke kasir, atau ada uang titipan barang pihak ketiga.
          </p>
          <div class="text-muted-c" style="font-size:0.78rem;">
            &bull; <strong>Aliran:</strong> Uang fisik masuk laci kasir &rarr; Tercatat di Z-Report &rarr; Masuk Cash Flow Inflow.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- =========================================================================
     BAB 3: ANATOMI LENGKAP "CASH OUT" KASIR
     ========================================================================= -->
<div class="chapter-box" id="bab3">
  <div class="chapter-header-custom">
    <div class="d-flex align-items-center gap-2">
      <div class="stat-icon" style="width:36px; height:36px; background: rgba(239,68,68,0.15); color: #ef4444; border-radius:8px; display:flex; align-items:center; justify-content:center;">
        <i class="bi bi-arrow-up-right-circle"></i>
      </div>
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">BAB 3: Anatomi Cash Out (Petty Cash vs Belanja PO vs OPEX)</h5>
        <span class="text-muted-c small">Bagaimana setiap jenis pengeluaran kas dicatat dan dipetakan</span>
      </div>
    </div>
  </div>

  <div class="chapter-body-custom">
    <div class="row g-3">
      <div class="col-md-4">
        <div class="flow-card-box">
          <span class="badge badge-danger mb-2">1. PETTY CASH LACI KASIR</span>
          <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Pengeluaran Darurat Kasir</h6>
          <p class="text-secondary-c small mb-2">
            Beli es batu kristal, gas LPG 3kg, galon air, atau sabun cuci piring saat jam operasional.
          </p>
          <div class="text-muted-c small">
            &bull; <strong>Aksi:</strong> Kasir klik tombol <strong>- Kas Keluar</strong> di menu Clock In.<br>
            &bull; <strong>Dampak:</strong> Memotong ekspektasi kas laci dan masuk Outflow Cash Flow.
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="flow-card-box">
          <span class="badge badge-warning mb-2">2. PO BELANJA BAHAN SUPPLIER</span>
          <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Pembelian Bahan Baku (PO)</h6>
          <p class="text-secondary-c small mb-2">
            Beli ayam potong 50kg atau biji kopi 20kg dari supplier rekanan.
          </p>
          <div class="text-muted-c small">
            &bull; <strong>PO Lunas:</strong> Memotong kas keluar di Laporan Cash Flow.<br>
            &bull; <strong>PO Tempo (Hutang 14 Hari):</strong> Masuk box pemantauan hutang tempo, belum memotong kas sampai tanggal pelunasan.
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="flow-card-box">
          <span class="badge badge-primary mb-2">3. BIAYA OPEX &amp; GAJI</span>
          <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Biaya Operasional Bulanan</h6>
          <p class="text-secondary-c small mb-2">
            Gaji koki/barista, tagihan listrik PLN, air PDAM, WiFi, dan sewa tempat.
          </p>
          <div class="text-muted-c small">
            &bull; <strong>P&amp;L:</strong> Memotong Laba Bersih bulanan (*Net Profit*).<br>
            &bull; <strong>Cash Flow:</strong> Memotong kas keluar riil saat dibayarkan.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- =========================================================================
     BAB 4: RUMUS & FORMULA LENGKAP PLAN B
     ========================================================================= -->
<div class="chapter-box" id="bab4">
  <div class="chapter-header-custom">
    <div class="d-flex align-items-center gap-2">
      <div class="stat-icon" style="width:36px; height:36px; background: rgba(16,185,129,0.15); color: #10b981; border-radius:8px; display:flex; align-items:center; justify-content:center;">
        <i class="bi bi-calculator"></i>
      </div>
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">BAB 4: Rumus Finansial &amp; Formula Kalkulasi</h5>
        <span class="text-muted-c small">Matematika di balik seluruh laporan keuangan Nexora POS</span>
      </div>
    </div>
  </div>

  <div class="chapter-body-custom">
    <div class="row g-3">
      <!-- Formula 1 -->
      <div class="col-md-6">
        <div class="flow-card-box">
          <h6 class="fw-bold mb-2 text-primary"><i class="bi bi-pie-chart me-1"></i> Formula Laba Rugi Resep (P&amp;L)</h6>
          <div class="formula-pill mb-2">
            Gross Profit = Total Omzet Kasir &minus; Total COGS Resep<br>
            Gross Margin % = (Gross Profit / Total Omzet) &times; 100%
          </div>
          <div class="formula-pill">
            Net Profit = Gross Profit &minus; Waste &minus; Gaji &minus; Overhead<br>
            Net Margin % = (Net Profit / Total Omzet) &times; 100%
          </div>
        </div>
      </div>

      <!-- Formula 2 -->
      <div class="col-md-6">
        <div class="flow-card-box">
          <h6 class="fw-bold mb-2 text-success"><i class="bi bi-cash-stack me-1"></i> Formula Arus Kas Nyata (Cash Flow)</h6>
          <div class="formula-pill mb-2">
            Total Inflow = Omzet Tunai + Non-Tunai QRIS + Top-Up Laci
          </div>
          <div class="formula-pill mb-2">
            Total Outflow = PO Lunas + Petty Cash Laci + Gaji + OPEX
          </div>
          <div class="formula-pill text-warning">
            Net Cash Flow = Total Inflow &minus; Total Outflow
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- =========================================================================
     BAB 5: SOP KASIR, SHIFT CLOSING & HANDOVER
     ========================================================================= -->
<div class="chapter-box" id="bab5">
  <div class="chapter-header-custom">
    <div class="d-flex align-items-center gap-2">
      <div class="stat-icon" style="width:36px; height:36px; background: rgba(245,158,11,0.15); color: #f59e0b; border-radius:8px; display:flex; align-items:center; justify-content:center;">
        <i class="bi bi-person-badge"></i>
      </div>
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">BAB 5: SOP Standar Kasir &amp; Serah Terima Laci (Handover)</h5>
        <span class="text-muted-c small">Alur kerja harian kasir dari buka toko hingga tutup shift malam</span>
      </div>
    </div>
  </div>

  <div class="chapter-body-custom">
    <div class="row g-3">
      <!-- Step 1 -->
      <div class="col-md-4">
        <div class="flow-card-box">
          <div class="badge badge-warning mb-2">LANGKAH 1: PAGI HARI</div>
          <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Buka Shift (Clock-In)</h6>
          <p class="text-secondary-c small mb-0">
            Kasir login &rarr; Buka menu <strong>Clock In</strong> &rarr; Pilih nama shift &rarr; Masukkan modal awal laci (misal: Rp 200.000) &rarr; Klik <strong>Buka Shift Kasir</strong>.
          </p>
        </div>
      </div>

      <!-- Step 2 -->
      <div class="col-md-4">
        <div class="flow-card-box">
          <div class="badge badge-info mb-2">LANGKAH 2: SIANG HARI</div>
          <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Mutasi Uang Laci</h6>
          <p class="text-secondary-c small mb-0">
            Jika ada tambah modal kembalian &rarr; Klik <strong>+ Kas Masuk</strong>.<br>
            Jika beli es batu / gas darurat &rarr; Klik <strong>- Kas Keluar</strong>.<br>
            <em>Catat secara real-time setiap kali uang berpindah tangan!</em>
          </p>
        </div>
      </div>

      <!-- Step 3 -->
      <div class="col-md-4">
        <div class="flow-card-box">
          <div class="badge badge-success mb-2">LANGKAH 3: MALAM HARI</div>
          <h6 class="fw-bold mb-1" style="color: var(--text-primary);">Tutup Shift &amp; Setor Brankas</h6>
          <p class="text-secondary-c small mb-0">
            Hitung seluruh fisik uang kertas/koin &rarr; Masukkan ke kolom <em>Fisik Laci</em> &rarr; Isi modal yang ditinggalkan untuk besok &rarr; Sistem menghitung uang setoran brankas &rarr; Klik <strong>Clock-Out &amp; Cetak Z-Report</strong>.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- =========================================================================
     BAB 6: FAQ & TANYA JAWAB UMUM OWNER
     ========================================================================= -->
<div class="chapter-box" id="bab6">
  <div class="chapter-header-custom">
    <div class="d-flex align-items-center gap-2">
      <div class="stat-icon" style="width:36px; height:36px; background: rgba(99,102,241,0.15); color: #6366f1; border-radius:8px; display:flex; align-items:center; justify-content:center;">
        <i class="bi bi-question-circle"></i>
      </div>
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">BAB 6: FAQ &amp; Pertanyaan Kunci Pemilik Restoran</h5>
        <span class="text-muted-c small">Jawaban atas pertanyaan-pertanyaan akuntansi yang sering diajukan</span>
      </div>
    </div>
  </div>

  <div class="chapter-body-custom">
    <div class="d-flex flex-column gap-3">
      <div class="p-3 rounded" style="background: rgba(0,0,0,0.15); border: 1px solid var(--border-subtle);">
        <h6 class="fw-bold text-primary mb-1">❓ Tanya: "Jika saya beli peralatan dapur / mesin espresso Rp 15.000.000, masuk ke mana?"</h6>
        <p class="text-secondary-c small mb-0">
          <strong>Jawaban:</strong> Pembelian mesin espresso adalah <strong>Belanja Modal Aset (CAPEX)</strong>. Uang kas keluar Rp 15 juta masuk ke Laporan Cash Flow Outflow. Namun di Laporan Laba Rugi HPP, ini <strong>TIDAK memotong laba resep kopi hari ini</strong>, melainkan dicatat sebagai aset inventaris outlet.
        </p>
      </div>

      <div class="p-3 rounded" style="background: rgba(0,0,0,0.15); border: 1px solid var(--border-subtle);">
        <h6 class="fw-bold text-success mb-1">❓ Tanya: "Jika ada susu basi / ayam busuk senilai Rp 50.000, apakah uang laci berkurang?"</h6>
        <p class="text-secondary-c small mb-0">
          <strong>Jawaban:</strong> <strong>TIDAK.</strong> Susu basi tidak mengeluarkan uang fisik dari laci kasir. Maka di Laporan Cash Flow, kerugian ini <strong>dieliminasi (Rp 0)</strong>. Tetapi di Laporan Laba Rugi (P&amp;L), kerugian Rp 50.000 dicatat pada <strong>Waste Log</strong> dan memotong Laba Bersih (*Net Profit*).
        </p>
      </div>

      <div class="p-3 rounded" style="background: rgba(0,0,0,0.15); border: 1px solid var(--border-subtle);">
        <h6 class="fw-bold text-warning mb-1">❓ Tanya: "Jika saya beli bahan ke supplier Rp 5.000.000 tempo 14 hari, kapan tercatat di Cash Flow?"</h6>
        <p class="text-secondary-c small mb-0">
          <strong>Jawaban:</strong> Selama belum dibayar, status PO adalah <code>Unpaid (Tempo)</code> dan masuk ke box <strong>Komitmen Hutang PO Tempo</strong> di dashboard Cash Flow. Uang kas Anda baru tercatat keluar pada saat kasir/keuangan melakukan pelunasan (*Pay*) di tanggal jatuh tempo.
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
