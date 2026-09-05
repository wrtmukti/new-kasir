@extends('admin.layouts.app')

@section('title', 'Manual Book & Panduan Operasional Super Lengkap — Nexora POS')

@php $activeMenu = 'manual-book' @endphp

@push('styles')
<style>
  .manual-header-card {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.16) 0%, rgba(124, 58, 237, 0.12) 100%);
    border: 1.5px solid rgba(37, 99, 235, 0.3);
    border-radius: 18px;
  }
  .manual-nav-pill {
    background: var(--bg-surface);
    border: 1px solid var(--border-subtle);
    color: var(--text-secondary);
    border-radius: 99px;
    padding: 0.5rem 1.1rem;
    font-size: 0.84rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    white-space: nowrap;
  }
  .manual-nav-pill:hover, .manual-nav-pill.active {
    background: var(--accent-1);
    color: #ffffff;
    border-color: var(--accent-1);
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
  }
  .chapter-card {
    background: var(--bg-surface);
    border: 1.5px solid var(--border-subtle);
    border-radius: 18px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.04);
    transition: all 0.25s ease;
    overflow: hidden;
  }
  .chapter-card:hover {
    border-color: var(--accent-1);
    box-shadow: 0 8px 28px rgba(37, 99, 235, 0.16);
  }
  .chapter-header {
    background: var(--bg-elevated);
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-subtle);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
  }
  .chapter-badge {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    padding: 0.35rem 0.85rem;
    border-radius: 99px;
    text-transform: uppercase;
  }
  .sub-module-box {
    background: var(--bg-elevated);
    border: 1px solid var(--border-subtle);
    border-radius: 12px;
    padding: 1.25rem;
    height: 100%;
  }
  .guide-step-number {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--accent-1);
    color: #ffffff;
    font-weight: 700;
    font-size: 0.82rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .guide-code-box {
    background: #0f172a;
    color: #38bdf8;
    border: 1px solid #1e293b;
    border-radius: 10px;
    padding: 0.85rem 1.1rem;
    font-family: 'Fira Code', monospace, sans-serif;
    font-size: 0.84rem;
    line-height: 1.6;
  }
  .guide-tip-callout {
    background: rgba(16, 185, 129, 0.1);
    border-left: 4px solid #10b981;
    border-radius: 8px;
    padding: 0.85rem 1.1rem;
    font-size: 0.85rem;
    color: var(--text-primary);
  }
  .guide-warn-callout {
    background: rgba(239, 68, 68, 0.1);
    border-left: 4px solid #ef4444;
    border-radius: 8px;
    padding: 0.85rem 1.1rem;
    font-size: 0.85rem;
    color: var(--text-primary);
  }
  .print-only { display: none !important; }
  @media print {
    .no-print, .sidebar, .topbar { display: none !important; }
    .print-only { display: block !important; }
    .main-col, body { margin: 0 !important; background: #ffffff !important; color: #000000 !important; }
    .chapter-card { border: 1px solid #ccc !important; page-break-inside: avoid; margin-bottom: 20px !important; }
  }
</style>
@endpush

@section('content')
<!-- Header Page -->
<div class="page-header no-print">
  <div>
    <h1 class="h3 fw-bold mb-1" style="color: var(--text-primary);">📖 Manual Book & Buku Panduan Operasional Lengkap</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.reports.dashboard') }}">Dashboard</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Manual Book & Tutorial System</span>
    </div>
  </div>

  <div class="d-flex align-items-center gap-2">
    <button onclick="window.print()" class="btn btn-outline-soft">
      <i class="bi bi-printer me-1"></i>Cetak Panduan PDF
    </button>
    <a href="{{ route('admin.order.create') }}" class="btn btn-primary-grad">
      <i class="bi bi-cart-check me-1"></i>Buka Kasir POS
    </a>
  </div>
</div>

<!-- Header Card Banner -->
<div class="card manual-header-card p-4 mb-4 no-print">
  <div class="row align-items-center g-3">
    <div class="col-lg-7">
      <h4 class="fw-bold mb-2" style="color: var(--text-primary);">
        <i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>Panduan Lengkap Operasional Restoran & Kasir POS
      </h4>
      <p class="text-muted-c mb-0" style="font-size:0.88rem; line-height:1.65;">
        Buku petunjuk resmi ini berisi panduan langkah demi langkah (*step-by-step*), penjelasan fungsi tombol, syarat penggunaan, formula perhitungan rumus, hingga solusi kendala lapangan untuk seluruh 9 modul utama sistem Nexora POS.
      </p>
    </div>
    <div class="col-lg-5">
      <div class="position-relative">
        <input type="text" id="manualSearchInput" class="form-control-modern" placeholder="🔍 Cari fitur / langkah (misal: Pajak, Shift, PO, Struk, Resep)..." style="padding-left: 2.4rem; height: 48px; font-size: 0.9rem;">
        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted-c"></i>
      </div>
    </div>
  </div>

  <!-- Quick Nav Pills -->
  <div class="d-flex flex-wrap gap-2 mt-4 pt-3" style="border-top: 1px solid var(--border-subtle); overflow-x: auto;">
    <a href="#bab1" class="manual-nav-pill"><i class="bi bi-speedometer2"></i> Bab 1: Dashboard</a>
    <a href="#bab2" class="manual-nav-pill"><i class="bi bi-box-seam"></i> Bab 2: Master Data</a>
    <a href="#bab3" class="manual-nav-pill"><i class="bi bi-cart3"></i> Bab 3: Operasional Kasir</a>
    <a href="#bab4" class="manual-nav-pill"><i class="bi bi-lock"></i> Bab 4: Shift Closing</a>
    <a href="#bab5" class="manual-nav-pill"><i class="bi bi-truck"></i> Bab 5: PO & Supplier</a>
    <a href="#bab6" class="manual-nav-pill"><i class="bi bi-journal-text"></i> Bab 6: Resep & Waste Log</a>
    <a href="#bab7" class="manual-nav-pill"><i class="bi bi-receipt"></i> Bab 7: Pajak & Service</a>
    <a href="#bab8" class="manual-nav-pill"><i class="bi bi-bar-chart-line"></i> Bab 8: Hub Laporan</a>
    <a href="#bab9" class="manual-nav-pill"><i class="bi bi-question-circle"></i> Bab 9: FAQ & Solutions</a>
  </div>
</div>

<!-- Header Print Only -->
<div class="print-only mb-4 text-center">
  <h1 class="fw-bold">MANUAL BOOK OPERASIONAL NEXORA POS SYSTEM</h1>
  <p class="text-muted">Panduan Resmi Penggunaan & Alur Kerja Lengkap Sistem Restoran & POS Kasir</p>
  <hr>
</div>

<!-- CONTAINER CHAPTERS -->
<div class="d-flex flex-column gap-4 mb-5" id="manualChaptersContainer">

  <!-- ========================================================================= -->
  <!-- BAB 1: QUICK START & DASHBOARD OVERVIEW -->
  <!-- ========================================================================= -->
  <div class="chapter-card" id="bab1">
    <div class="chapter-header">
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
          <i class="bi bi-speedometer2 text-primary me-2"></i>BAB 1: Pengenalan & Quick Start Dashboard
        </h5>
        <small class="text-muted-c">Navigasi Utama, Ringkasan Performa & Tombol Pintas Akses Cepat</small>
      </div>
      <span class="chapter-badge bg-primary text-white">Dasar Sistem</span>
    </div>
    <div class="p-4">
      <div class="row g-4">
        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-primary mb-2"><i class="bi bi-card-heading me-1"></i>1.1 Fungsi Kartu Ringkasan KPI Dashboard</h6>
            <p class="text-muted-c mb-3" style="font-size:0.86rem;">
              Setelah pengguna (Admin/Kasir) melakukan login ke sistem, halaman utama menampilkan 3 indikator performa outlet secara real-time:
            </p>
            <ul class="text-muted-c ps-3 mb-0" style="font-size:0.85rem; line-height:1.7;">
              <li class="mb-2"><strong>Penjualan Hari Ini (Omzet Clean):</strong> Menampilkan akumulasi total rupiah dari seluruh transaksi kasir yang sukses pada hari berjalan (00:00 - 23:59 WIB). Formula: <code>SUM(transaction_grand_total)</code>.</li>
              <li class="mb-2"><strong>Pesanan Selesai (Completed Orders):</strong> Menampilkan jumlah lembar pesanan yang berhasil di-checkout baik Dine In, Takeaway, maupun Delivery.</li>
              <li class="mb-2"><strong>Alert Stok Bahan Baku Rendah:</strong> Memberikan peringatan jumlah item bahan mentah gudang yang sisa kuantitasnya telah berada di bawah batas minimum (<code>amount &lt; min_amount</code>). Klik kartu ini untuk langsung membuka daftar stok.</li>
            </ul>
          </div>
        </div>

        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-success mb-2"><i class="bi bi-layout-sidebar me-1"></i>1.2 Navigasi Responsive & Mode Tampilan</h6>
            <p class="text-muted-c mb-3" style="font-size:0.86rem;">
              Sistem Nexora POS mendukung kenyamanan penggunaan di berbagai perangkat (Tablet, Laptop, Layar Touchscreen Kasir):
            </p>
            <ul class="text-muted-c ps-3 mb-0" style="font-size:0.85rem; line-height:1.7;">
              <li class="mb-2"><strong>Theme Switcher (Dark/Light):</strong> Klik ikon Mode Malam/Siang di bagian kanan atas topbar. Pilihan tema tersimpan otomatis pada browser.</li>
              <li class="mb-2"><strong>Lipat Sidebar (Collapse Sidebar):</strong> Klik tombol ikon di pojok kiri atas untuk menyembunyikan sidebar agar layar kasir POS menjadi lebih luas saat memproses pesanan di jam sibuk.</li>
              <li class="mb-2"><strong>Pusat Notifikasi:</strong> Menampilkan alert pesanan masuk dari meja pelanggan (*Guest QR Order*) dan pemberitahuan stok mentah yang perlu dibeli kembali.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- BAB 2: MANAJEMEN MASTER DATA -->
  <!-- ========================================================================= -->
  <div class="chapter-card" id="bab2">
    <div class="chapter-header">
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
          <i class="bi bi-box-seam text-info me-2"></i>BAB 2: Manajemen Master Data Restoran
        </h5>
        <small class="text-muted-c">Konfigurasi Produk, Kategori, Diskon, Voucher, Meja, Supplier & Staf</small>
      </div>
      <span class="chapter-badge bg-info text-white">Kelola Produk & Master</span>
    </div>
    <div class="p-4">
      <div class="row g-4">
        <!-- 2.1 Kategori & Produk -->
        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-primary mb-2"><i class="bi bi-tags me-1"></i>2.1 Pengaturan Kategori & Produk Menu</h6>
            <div class="d-flex align-items-start gap-2 mb-2">
              <span class="guide-step-number">1</span>
              <div>
                <strong class="d-block" style="font-size:0.86rem;">Tambah Kategori Produk:</strong>
                <small class="text-muted-c">Akses menu <code>Master Data &rarr; Kategori</code> &rarr; Klik <em>"Tambah Kategori"</em>. Masukkan nama (misal: <em>Makanan Utama, Minuman Cold, Dessert</em>). Kategori ini menjadi tab penyaring di kasir POS.</small>
              </div>
            </div>
            <div class="d-flex align-items-start gap-2">
              <span class="guide-step-number">2</span>
              <div>
                <strong class="d-block" style="font-size:0.86rem;">Tambah Produk / Menu Baru:</strong>
                <small class="text-muted-c">Akses <code>Master Data &rarr; Produk</code> &rarr; Klik <em>"Tambah Produk"</em>. Masukkan Nama Produk, Kategori, Harga Jual, Foto Thumbnail, Varian (Ukuran/Pedas), dan Status Ketersediaan (Aktif/Habis).</small>
              </div>
            </div>
          </div>
        </div>

        <!-- 2.2 Diskon & Voucher -->
        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-success mb-2"><i class="bi bi-percent me-1"></i>2.2 Pengaturan Promo Diskon & Voucher Belanja</h6>
            <div class="d-flex align-items-start gap-2 mb-2">
              <span class="guide-step-number">1</span>
              <div>
                <strong class="d-block" style="font-size:0.86rem;">Promo Diskon Produk:</strong>
                <small class="text-muted-c">Akses <code>Master Data &rarr; Diskon</code>. Tentukan tipe persentase (%) atau nominal rupiah (Rp) pada menu tertentu beserta rentang tanggal aktif.</small>
              </div>
            </div>
            <div class="d-flex align-items-start gap-2">
              <span class="guide-step-number">2</span>
              <div>
                <strong class="d-block" style="font-size:0.86rem;">Voucher Promo Kasir & Guest QR:</strong>
                <small class="text-muted-c">Akses <code>Master Data &rarr; Voucher</code>. Buat Kode Kupon (misal: <code>NEXORAPROMO</code>), tentukan batas minimal transaksi belanja, potongan diskon, dan kuota penggunaan.</small>
              </div>
            </div>
          </div>
        </div>

        <!-- 2.3 Meja & QR Ordering -->
        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-purple mb-2" style="color:#7c3aed;"><i class="bi bi-qr-code-scan me-1"></i>2.3 Master Meja & Guest QR Ordering</h6>
            <p class="text-muted-c mb-2" style="font-size:0.85rem;">
              Fitur <em>Guest QR Ordering</em> memungkinkan pelanggan memesan secara mandiri langsung dari smartphone mereka tanpa perlu mengantre ke kasir:
            </p>
            <ol class="text-muted-c ps-3 mb-0" style="font-size:0.84rem; line-height:1.65;">
              <li>Akses menu <code>Master Data &rarr; Meja</code> &rarr; Klik <em>"Tambah Meja"</em>. Masukkan Nomor Meja & Area (Indoor/Outdoor).</li>
              <li>Klik tombol <strong>"Cetak QR Meja"</strong> untuk mengunduh/mencetak stiker QR unik per meja.</li>
              <li>Pelanggan melakukan scan QR di meja &rarr; Pilih menu &rarr; Kirim Pesanan. Pesanan akan otomatis masuk ke antrean kasir POS dan printer dapur.</li>
            </ol>
          </div>
        </div>

        <!-- 2.4 Supplier & Staf Akses -->
        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-warning mb-2"><i class="bi bi-people me-1"></i>2.4 Master Supplier & User Staf Kasir</h6>
            <ul class="text-muted-c ps-3 mb-0" style="font-size:0.85rem; line-height:1.65;">
              <li class="mb-2"><strong>Master Supplier:</strong> Mendaftarkan identitas vendor pemasok bahan mentah (Nama PT/Toko, No HP Sales, Alamat Kantor). Digunakan saat membuat Purchase Order (PO).</li>
              <li class="mb-2"><strong>Manajemen User Akses:</strong> Mendaftarkan akun login staf kasir, supervisor, dan admin. Membatasi hak akses agar staf kasir tidak bisa mengubah data master produk atau laporan keuangan tanpa otorisasi.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- BAB 3: OPERASIONAL KASIR POS -->
  <!-- ========================================================================= -->
  <div class="chapter-card" id="bab3">
    <div class="chapter-header">
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
          <i class="bi bi-cart3 text-success me-2"></i>BAB 3: Operasional Kasir POS & Checkout Transaksi
        </h5>
        <small class="text-muted-c">Alur Pemrosesan Pesanan, Pembayaran, Breakdown Pajak & Cetak Struk</small>
      </div>
      <span class="chapter-badge bg-success text-white">Point of Sale</span>
    </div>
    <div class="p-4">
      <!-- Steps Grid -->
      <div class="row g-4 mb-4">
        <div class="col-md-4">
          <div class="guide-step-box" style="border-left-color: #3b82f6;">
            <h6 class="fw-bold text-primary mb-1">Langkah 1: Penetapan Tipe Pesanan</h6>
            <p class="text-muted-c mb-0" style="font-size:0.84rem;">
              Buka menu <code>Pesan / Kasir POS</code>. Pilih tipe order:
              <br>&bull; <strong>Dine In:</strong> Pilih No Meja pelanggan.
              <br>&bull; <strong>Take Away:</strong> Pesanan dibungkus.
              <br>&bull; <strong>Delivery:</strong> Pesanan dikirim via kurir.
            </p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="guide-step-box" style="border-left-color: #10b981;">
            <h6 class="fw-bold text-success mb-1">Langkah 2: Keranjang & Notes</h6>
            <p class="text-muted-c mb-0" style="font-size:0.84rem;">
              Klik porsi menu yang dipilih. Klik ikon pensil pada item di keranjang untuk menginput <strong>Catatan Kustom</strong> (misal: <em>"Pedas banget, es sedikit"</em>) atau memilih varian rasa/ukuran.
            </p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="guide-step-box" style="border-left-color: #8b5cf6;">
            <h6 class="fw-bold text-purple mb-1" style="color:#7c3aed;">Langkah 3: Promo & Voucher</h6>
            <p class="text-muted-c mb-0" style="font-size:0.84rem;">
              Jika pelanggan memiliki voucher promo, klik tombol <em>"Terapkan Voucher"</em> dan ketik kode promo. Potongan diskon langsung memotong subtotal transaksi.
            </p>
          </div>
        </div>
      </div>

      <!-- Breakdown Formula Box -->
      <div class="sub-module-box mb-4">
        <h6 class="fw-bold text-primary mb-2"><i class="bi bi-calculator me-1"></i>3.1 Rincian Formula Perhitungan Struk Kasir (Autokalkulasi Sistem)</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <div class="guide-code-box">
              1. Subtotal = SUM(Harga Menu x Qty Porsi)<br>
              2. Diskon = Potongan Promo Produk / Voucher<br>
              3. Service Charge (5%) = (Subtotal - Diskon) x 5%<br>
              4. DPP (Dasar Pajak) = (Subtotal - Diskon) + Service Charge<br>
              5. Pajak PB1 (10%) = DPP x 10%<br>
              6. Grand Total Struk = DPP + Pajak PB1
            </div>
          </div>
          <div class="col-md-6">
            <div class="guide-tip-callout h-100">
              <strong class="d-block text-success mb-1"><i class="bi bi-check-circle me-1"></i>Metode Pembayaran & Kembalian:</strong>
              &bull; <strong>Tunai (Cash):</strong> Kasir memasukkan nominal uang tunai yang diterima (misal Rp 150.000). Sistem otomatis menampilkan jumlah kembalian secara tepat di layar.<br>
              &bull; <strong>Non-Tunai (QRIS / EDC):</strong> Pilih QRIS/EDC, sistem mencatat transaksi tanpa kembalian.<br>
              &bull; <strong>Cetak Struk:</strong> Klik <em>"Cetak Struk"</em> untuk mencetak struk belanja ke printer thermal 80mm/58mm.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- BAB 4: SHIFT CLOSING & CASH BALANCING -->
  <!-- ========================================================================= -->
  <div class="chapter-card" id="bab4">
    <div class="chapter-header">
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
          <i class="bi bi-lock text-warning me-2"></i>BAB 4: Modul Shift Closing Kasir & Cash Balancing (Z-Report)
        </h5>
        <small class="text-muted-c">Mekanisme Buka/Tutup Shift, Hitungan Uang Laci Fisik & Rekapitulasi Z-Report</small>
      </div>
      <span class="chapter-badge bg-warning text-dark">Keamanan Kas</span>
    </div>
    <div class="p-4">
      <div class="guide-warn-callout mb-4">
        <i class="bi bi-shield-lock-fill me-1 text-danger"></i><strong>Aturan Keamanan Shift Closing:</strong>
        Untuk mencegah kebocoran kas, kasir <strong>TIDAK DAPAT</strong> membuat order transaksi baru jika sesi shift belum dibuka. Kasir juga <strong>TIDAK DAPAT</strong> menutup shift jika masih ada transaksi meja yang menggantung (*unpaid active orders*).
      </div>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="sub-module-box">
            <h6 class="fw-bold text-primary mb-2">1. Membuka Shift (Open Shift)</h6>
            <ol class="text-muted-c ps-3 mb-0" style="font-size:0.84rem; line-height:1.7;">
              <li>Kasir yang bertugas membuka layar POS Kasir.</li>
              <li>Pop-up modal <em>Open Shift</em> akan otomatis muncul.</li>
              <li>Masukkan jumlah <strong>Saldo Modal Kas Awal</strong> (misal: Rp 500.000) yang ada di laci uang kasir.</li>
              <li>Klik <em>"Mulai Shift Kasir"</em>. Status shift menjadi <code>open</code>.</li>
            </ol>
          </div>
        </div>

        <div class="col-md-4">
          <div class="sub-module-box">
            <h6 class="fw-bold text-success mb-2">2. Penutupan Shift (Close Shift)</h6>
            <ol class="text-muted-c ps-3 mb-0" style="font-size:0.84rem; line-height:1.7;">
              <li>Di akhir jam tugas kasir, klik tombol <strong>"Close Shift Kasir"</strong> di POS.</li>
              <li>Hitung fisik seluruh lembaran uang kertas (100rb, 50rb, 20rb, 10rb, dll) dan koin di laci kasir.</li>
              <li>Masukkan total hitungan fisik ke kolom <strong>"Hitungan Fisik Uang Tunai (Actual Cash Counted)"</strong>.</li>
              <li>Klik <em>"Proses Closing Shift & Cetak Z-Report"</em>.</li>
            </ol>
          </div>
        </div>

        <div class="col-md-4">
          <div class="sub-module-box">
            <h6 class="fw-bold text-danger mb-2">3. Rekap Z-Report & Selisih Laci</h6>
            <div class="guide-code-box mb-2" style="font-size:0.78rem;">
              Ekspektasi Uang Tunai = Modal Awal + Tunai System<br>
              Selisih Laci (Variance) = Hitungan Fisik - Ekspektasi Uang
            </div>
            <p class="text-muted-c mb-0" style="font-size:0.83rem;">
              Printer thermal akan mencetak fisik struk **Z-Report**. Jika hasil selisih bernilai minus (*shortage*), kasir wajib memberikan penjelasan pertanggungjawaban kepada supervisor.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- BAB 5: PURCHASING (PO) & RECEIVING STOK -->
  <!-- ========================================================================= -->
  <div class="chapter-card" id="bab5">
    <div class="chapter-header">
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
          <i class="bi bi-truck text-purple me-2" style="color:#7c3aed;"></i>BAB 5: Purchase Order (PO) & Receiving Bahan Baku Mentah
        </h5>
        <small class="text-muted-c">Alur Belanja Bahan Baku Mentah ke Supplier, Penerimaan Stok & Harga Efektif</small>
      </div>
      <span class="chapter-badge bg-purple text-white" style="background:#7c3aed;">Procurement & Stok</span>
    </div>
    <div class="p-4">
      <div class="row g-4">
        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-primary mb-2">5.1 Pendaftaran Master Bahan Mentah COGS</h6>
            <p class="text-muted-c mb-2" style="font-size:0.85rem;">
              Buka menu <code>Keuangan & COGS &rarr; Bahan Mentah COGS</code>. Daftarkan seluruh item bahan mentah baku (Ayam Utuh, Daging Sirloin, Beras Premium, Minyak Goreng, Dus Box):
            </p>
            <ul class="text-muted-c ps-3 mb-0" style="font-size:0.84rem; line-height:1.7;">
              <li><strong>Kode Bahan:</strong> Kode unik (misal: <code>RAW-AYAM01</code>).</li>
              <li><strong>Satuan Unit:</strong> Satuan pengukuran baku (<code>kg</code>, <code>liter</code>, <code>pcs</code>, <code>gram</code>).</li>
              <li><strong>Loss Percent (Penyusutan):</strong> Persentase bagian bahan mentah yang terbuang/hilang saat pembersihan (misal: Ayam utuh hilang 10% lemak/tulang saat difilet).</li>
            </ul>
          </div>
        </div>

        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-success mb-2">5.2 Alur Pembuatan Document PO & Receiving</h6>
            <ol class="text-muted-c ps-3 mb-0" style="font-size:0.84rem; line-height:1.7;">
              <li>Akses menu <code>Keuangan & COGS &rarr; Purchase Order (PO)</code> &rarr; Klik <em>"Buat PO Baru"</em>.</li>
              <li>Pilih Supplier, tentukan item bahan mentah yang dibeli, kuantitas, dan harga per unit. Simpan PO (status: <code>ordered</code>).</li>
              <li>Saat fisik barang tiba di toko, buka dokumen PO & klik tombol <strong>"Receiving (Penerimaan Barang)"</strong>.</li>
              <li>Masukkan jumlah kuantitas fisik yang diterima secara aktual. Klik <em>"Konfirmasi Penerimaan"</em> (status PO menjadi <code>completed</code>).</li>
              <li><strong>Otomatisasi Sistem:</strong> Stok <code>cogs_raw_materials.amount</code> bertambah, dan harga efektif unit dihitung ulang secara real-time.</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- BAB 6: RESEP COGS & WASTE LOG -->
  <!-- ========================================================================= -->
  <div class="chapter-card" id="bab6">
    <div class="chapter-header">
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
          <i class="bi bi-journal-text text-danger me-2"></i>BAB 6: Manajemen Resep COGS & Waste Log (Bahan Terbuang)
        </h5>
        <small class="text-muted-c">Formula Pemorsian HPP Menu, Target Food Cost, & Pencatatan Kerugian Bahan Busuk</small>
      </div>
      <span class="chapter-badge bg-danger text-white">HPP & Waste Control</span>
    </div>
    <div class="p-4">
      <div class="row g-4 mb-4">
        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-primary mb-2">🍳 6.1 Pembuatan Resep & Autokalkulasi HPP Menu</h6>
            <p class="text-muted-c mb-2" style="font-size:0.85rem;">
              Resep menghubungkan 1 porsi menu produk yang dijual di kasir dengan takaran konsumsi bahan baku mentah dari gudang:
            </p>
            <ol class="text-muted-c ps-3 mb-2" style="font-size:0.84rem; line-height:1.65;">
              <li>Akses menu <code>Keuangan & COGS &rarr; Resep & COGS Menu</code> &rarr; Pilih menu (misal: <em>Ayam Geprek Spesial</em>).</li>
              <li>Masukkan komposisi bahan baku per 1 porsi: <em>Daging Ayam (0.20 kg), Minyak (0.05 L), Cabe Rawit (0.02 kg), Dus Box (1 pcs)</em>.</li>
              <li>Sistem menghitung Modal COGS Porsi berdasarkan <strong>Harga Efektif Terkini</strong> bahan mentah.</li>
            </ol>
            <div class="guide-code-box" style="font-size:0.8rem;">
              Modal COGS Porsi = SUM(Qty Bahan x Harga Efektif Unit)<br>
              Rekomendasi Harga Jual = Modal COGS Porsi / (Target Food Cost % / 100)
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-danger mb-2">🗑️ 6.2 Waste Log (Pencatatan Bahan Terbuang / Rusak)</h6>
            <p class="text-muted-c mb-2" style="font-size:0.85rem;">
              Jika terdapat bahan baku mentah yang busuk di kulkas, expired, atau tumpah, wajib dicatat di Waste Log agar nilai kerugian terakumulasi ke laporan keuangan:
            </p>
            <ol class="text-muted-c ps-3 mb-2" style="font-size:0.84rem; line-height:1.65;">
              <li>Akses menu <code>Keuangan & COGS &rarr; Bahan Terbuang (Waste Log)</code> &rarr; Klik <em>"Catat Waste Log Baru"</em>.</li>
              <li>Pilih Bahan Baku Mentah, masukkan jumlah kuantitas yang terbuang, dan pilih alasan (<em>Rotten, Expired, Broken, Spilled</em>).</li>
              <li>Kuantitas stok bahan mentah gudang otomatis terpotong.</li>
            </ol>
            <div class="guide-code-box" style="font-size:0.8rem; color:#f87171; border-color:#7f1d1d;">
              Nilai Kerugian Waste = Qty Terbuang x Harga Efektif Unit<br>
              *Nilai kerugian ini memotong langsung Laba Bersih di Laporan HPP Bulanan.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- BAB 7: SETTING PAJAK PB1 & SERVICE CHARGE -->
  <!-- ========================================================================= -->
  <div class="chapter-card" id="bab7">
    <div class="chapter-header">
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
          <i class="bi bi-receipt text-info me-2"></i>BAB 7: Pengaturan Pajak Restoran (PB1) & Service Charge
        </h5>
        <small class="text-muted-c">Konfigurasi Tarif Pajak Daerah, Service Charge, Simulasi Struk & Jam Buka Outlet</small>
      </div>
      <span class="chapter-badge bg-info text-white">Setting Tax & Service</span>
    </div>
    <div class="p-4">
      <div class="row g-4 mb-4">
        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-primary mb-2">🏛️ 7.1 Setting Pajak PB1 (10%) & Service Charge (5%)</h6>
            <p class="text-muted-c mb-2" style="font-size:0.85rem;">
              Akses menu <code>Keuangan & Setting &rarr; Master Pajak & Service Charge</code>:
            </p>
            <ul class="text-muted-c ps-3 mb-0" style="font-size:0.84rem; line-height:1.7;">
              <li><strong>Aktifkan Pajak PB1 (10%):</strong> Centang switch untuk memungut pajak restoran. Tipe pajak <code>Exclusive</code> menambah pajak di atas subtotal pesanan.</li>
              <li><strong>Aktifkan Service Charge (5%):</strong> Centang switch biaya layanan. Service charge dikumpulkan ke *Pool Service* untuk insentif bulanan staf.</li>
            </ul>
          </div>
        </div>

        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-success mb-2">🏬 7.2 Setting Profil Restoran & Jam Operasional</h6>
            <p class="text-muted-c mb-2" style="font-size:0.85rem;">
              Kelola informasi outlet yang akan tercetak di bagian atas struk kasir:
            </p>
            <ul class="text-muted-c ps-3 mb-0" style="font-size:0.84rem; line-height:1.7;">
              <li><strong>Header Struk:</strong> Nama Resto, Alamat Cabang, Nomor Telepon, & Catatan Kaki (*"Terima Kasih Atas Kunjungan Anda"*).</li>
              <li><strong>Jam Buka / Cut-off Harian:</strong> Mengatur batas waktu operasional toko untuk penguncian sesi transaksi otomatis.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- BAB 8: HUB LAPORAN FINANCIAL & ANALYTICS -->
  <!-- ========================================================================= -->
  <div class="chapter-card" id="bab8">
    <div class="chapter-header">
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
          <i class="bi bi-bar-chart-line text-success me-2"></i>BAB 8: Hub Laporan Finansial, Filter & Export Excel
        </h5>
        <small class="text-muted-c">Pusat Analisis Laporan Keuangan, Filter Interaktif, Cetak Print & Export CSV</small>
      </div>
      <span class="chapter-badge bg-success text-white">Laporan & Analytics</span>
    </div>
    <div class="p-4">
      <p class="text-muted-c mb-3" style="font-size:0.88rem;">
        Menu <code>Laporan & Analytics &rarr; Dashboard Laporan</code> (`/admin/reports/dashboard`) menyediakan 6 modul laporan terpisah yang telah mendukung **Theme Auto-Adaptation**, **Filter Per-Halaman (10, 20, 50, 100, All)**, **Realtime Search**, dan **Export Excel**:
      </p>

      <div class="table-responsive">
        <table class="table-modern">
          <thead>
            <tr>
              <th>NAMA MODUL LAPORAN</th>
              <th>FUNGSI UTAMA & INFORMASI YANG DISAJIKAN</th>
              <th>FITUR FILTER & EXPORT</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="fw-bold text-primary">💳 Laporan Penjualan (Sales)</td>
              <td>Rekapitulasi seluruh transaksi penjualan sukses, breakdown Subtotal, Service Charge, Pajak PB1, dan Grand Total.</td>
              <td>Rentang Tanggal, Search Kode Trx, Per-Page (10-All), Export Excel, Cetak Print</td>
            </tr>
            <tr>
              <td class="fw-bold text-success">🍔 Laporan Performa Menu (PMIX)</td>
              <td>Ranking menu terlaris berdasarkan kuantitas porsi terjual dan total omzet per item menu (*Menu Engineering*).</td>
              <td>Rentang Tanggal, Search Nama Menu, Per-Page (10-All), Export Excel, Cetak Print</td>
            </tr>
            <tr>
              <td class="fw-bold text-info">💵 Laporan Cash Flow (Arus Kas)</td>
              <td>Memantau arus kas masuk (pembayaran kasir) dan arus kas keluar (belanja PO supplier) secara teratur.</td>
              <td>Rentang Tanggal Filter, Ringkasan Net Cash Flow, Cetak Print PDF</td>
            </tr>
            <tr>
              <td class="fw-bold text-purple" style="color:#7c3aed;">🏛️ Laporan Pajak & Service</td>
              <td>Rincian akumulasi setoran pajak PB1 (10%) untuk Bapenda dan total pool service charge (5%) untuk insentif staf.</td>
              <td>Rentang Tanggal, Search Order ID, Per-Page (10-All), Export Excel, Cetak Print</td>
            </tr>
            <tr>
              <td class="fw-bold text-warning">📦 Laporan Stok & Inventory</td>
              <td>Nilai total aset bahan baku mentah gudang, PO receiving, dan akumulasi kerugian bahan busuk (waste log).</td>
              <td>Search Kode/Nama Bahan, Per-Page (10-All), Export Excel, Cetak Print</td>
            </tr>
            <tr>
              <td class="fw-bold text-danger">🔐 Audit Shift Closing Kasir</td>
              <td>Audit sesi shift kasir, saldo modal awal, penjualan tunai vs non-tunai, dan histori selisih laci kasir (*variance*).</td>
              <td>Rentang Tanggal, Search Shift Name, Per-Page (10-All), Export Excel, Cetak Print</td>
            </tr>
            <tr>
              <td class="fw-bold text-primary">📈 Laporan HPP & Laba Rugi</td>
              <td>Input biaya operasional (Gaji Karyawan, Listrik/PLN), kalkulasi Laba Kotor dan Laba Bersih akhir outlet.</td>
              <td>Fitur Input Gaji/Listrik, Detail Resep Per-Menu, Export Excel, Cetak Print</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- BAB 9: FAQ & TROUBLESHOOTING -->
  <!-- ========================================================================= -->
  <div class="chapter-card" id="bab9">
    <div class="chapter-header">
      <div>
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
          <i class="bi bi-question-circle text-warning me-2"></i>BAB 9: FAQ, Troubleshooting & Dukungan Sistem
        </h5>
        <small class="text-muted-c">Panduan Penanganan Kendala Lapangan & Kontak Bantuan IT</small>
      </div>
      <span class="chapter-badge bg-warning text-dark">Solusi Kendala</span>
    </div>
    <div class="p-4">
      <div class="row g-4">
        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-primary mb-2">Q: Printer Thermal Tidak Mencetak Struk Belanja?</h6>
            <p class="text-muted-c mb-0" style="font-size:0.85rem; line-height:1.65;">
              <strong>Solusi:</strong><br>
              1. Pastikan printer thermal (USB/Bluetooth/LAN 80mm atau 58mm) dalam keadaan ON dan kertas struk terpasang dengan benar.<br>
              2. Pada dialog print browser Chrome, pilih printer thermal sebagai <em>Default Printer</em>.<br>
              3. Hilangkan centang pada opsi <em>"Headers and footers"</em> agar URL browser tidak ikut tercetak di kertas struk.
            </p>
          </div>
        </div>

        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-danger mb-2">Q: Terjadi Selisih Minus di Laci Kasir Saat Closing Shift?</h6>
            <p class="text-muted-c mb-0" style="font-size:0.85rem; line-height:1.65;">
              <strong>Solusi:</strong><br>
              1. Cek kembali riwayat transaksi non-tunai (QRIS/EDC) yang mungkin salah terinput oleh kasir sebagai transaksi tunai (cash).<br>
              2. Jika memang terjadi kehilangan uang fisik, selisih minus (*shortage*) wajib dicatat di struk Z-Report dan kasir memberikan uang pengganti sesuai kebijakan supervisor.
            </p>
          </div>
        </div>

        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-purple mb-2" style="color:#7c3aed;">Q: Mengapa HPP / Modal Menu Tidak Muncul di Laporan?</h6>
            <p class="text-muted-c mb-0" style="font-size:0.85rem; line-height:1.65;">
              <strong>Solusi:</strong><br>
              1. Pastikan produk menu tersebut telah dibuatkan resep komposisinya di menu <code>Resep & COGS Menu</code>.<br>
              2. Pastikan bahan baku mentah yang terhubung telah memiliki harga beli awal dari transaksi Purchase Order (PO) Receiving.
            </p>
          </div>
        </div>

        <div class="col-md-6">
          <div class="sub-module-box">
            <h6 class="fw-bold text-success mb-2">📞 Layanan Dukungan IT Administrator Restoran</h6>
            <p class="text-muted-c mb-0" style="font-size:0.85rem; line-height:1.65;">
              Jika Anda mengalami kendala teknis sistem yang tidak dapat diselesaikan melalui buku petunjuk ini, silakan hubungi tim Support Administrator IT Resto melalui pesan terdaftar atau email administrator outlet.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('manualSearchInput');
  const chapterCards = document.querySelectorAll('.chapter-card');

  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const query = this.value.toLowerCase().trim();

      chapterCards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (query === '' || text.includes(query)) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    });
  }

  // Smooth scroll for nav pills
  document.querySelectorAll('.manual-nav-pill').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href.startsWith('#')) {
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    });
  });
});
</script>
@endpush
