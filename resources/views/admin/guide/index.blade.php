@extends('admin.layouts.app')

@section('title', 'Panduan & Alur Penggunaan Lengkap Sistem')

@php $activeMenu = 'guide' @endphp

@push('styles')
<style>
.guide-card {
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.03);
  transition: all 0.2s ease-in-out;
}
.guide-card:hover {
  border-color: var(--primary);
  transform: translateY(-2px);
}
.step-number-badge {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: var(--primary);
  color: #ffffff;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
}
.code-box {
  background: var(--bg-elevated);
  border: 1px solid var(--border-subtle);
  border-radius: 8px;
  padding: 10px 14px;
  font-family: monospace;
  font-size: 0.85rem;
  color: var(--text-secondary);
}
</style>
@endpush

@section('content')
<div class="main-content-container">
  <!-- Page Header -->
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold mb-1" style="color: var(--text-primary);">
        <i class="bi bi-book-half text-primary me-2"></i>Panduan & Alur Penggunaan Lengkap Sistem
      </h4>
      <p class="text-muted-c mb-0" style="font-size: 0.88rem;">
        Petunjuk operasional lengkap dari Pembelian Bahan (PO), Manajemen Resep COGS, POS Kasir, Pencatatan Waste, hingga Laporan HPP & Laba Rugi.
      </p>
    </div>
    <div>
      <a href="{{ route('admin.keuangan.hpp-report.index') }}" class="btn btn-primary-grad">
        <i class="bi bi-graph-up-arrow me-1"></i>Buka Laporan HPP
      </a>
    </div>
  </div>

  <!-- Flow Timeline Header Card -->
  <div class="card guide-card p-4 mb-4" style="background: linear-gradient(135deg, rgba(37,99,235,0.08) 0%, rgba(124,58,237,0.05) 100%);">
    <h5 class="fw-bold mb-3" style="color: var(--text-primary);">
      <i class="bi bi-diagram-3-fill text-primary me-2"></i>Alur Terintegrasi Otomatis (5 Tahap Utama)
    </h5>
    <div class="row g-3">
      <div class="col-md">
        <div class="p-3 bg-surface rounded border h-100">
          <span class="badge bg-primary mb-2">Langkah 1</span>
          <h6 class="fw-bold mb-1" style="font-size:0.9rem;">1. Raw Stock (PO)</h6>
          <small class="text-muted-c d-block">Beli bahan mentah via PO. Stok & harga efektif otomatis ter-update saat Receiving.</small>
        </div>
      </div>
      <div class="col-md">
        <div class="p-3 bg-surface rounded border h-100">
          <span class="badge bg-purple mb-2" style="background:#7c3aed;">Langkah 2</span>
          <h6 class="fw-bold mb-1" style="font-size:0.9rem;">2. Resep COGS</h6>
          <small class="text-muted-c d-block">Daftarkan takaran bahan per porsi. Sistem menghitung otomatis Modal COGS & Rekomendasi Harga Jual.</small>
        </div>
      </div>
      <div class="col-md">
        <div class="p-3 bg-surface rounded border h-100">
          <span class="badge bg-info mb-2" style="background:#0891b2;">Langkah 3</span>
          <h6 class="fw-bold mb-1" style="font-size:0.9rem;">3. Kasir & POS</h6>
          <small class="text-muted-c d-block">Proses pesanan di Kasir. Omzet & estimasi modal COGS terjual otomatis terakumulasi real-time.</small>
        </div>
      </div>
      <div class="col-md">
        <div class="p-3 bg-surface rounded border h-100">
          <span class="badge bg-danger mb-2">Langkah 4</span>
          <h6 class="fw-bold mb-1" style="font-size:0.9rem;">4. Waste Log</h6>
          <small class="text-muted-c d-block">Catat bahan busuk/rusak. Stok terpotong & nilai kerugian tercatat di laporan laba rugi.</small>
        </div>
      </div>
      <div class="col-md">
        <div class="p-3 bg-surface rounded border h-100">
          <span class="badge bg-success mb-2">Langkah 5</span>
          <h6 class="fw-bold mb-1" style="font-size:0.9rem;">5. Laporan HPP</h6>
          <small class="text-muted-c d-block">Input Gaji & Listrik. Sistem menghitung Laba Kotor, Laba Bersih, & Margin Persentase per menu.</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Detailed Module Sections -->
  <div class="row g-4 mb-4">

    <!-- Section 1: Modul Bahan Mentah & PO -->
    <div class="col-lg-6">
      <div class="card guide-card h-100 p-4">
        <div class="d-flex align-items-center mb-3">
          <span class="step-number-badge me-3">1</span>
          <div>
            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">Purchase Order (PO) & Stok Bahan Mentah</h5>
            <small class="text-muted-c">Menu: Keuangan & COGS &rarr; Purchase Order (PO)</small>
          </div>
        </div>
        <ul class="text-muted-c ps-3 mb-3" style="font-size:0.88rem;">
          <li class="mb-2"><strong>Daftar Bahan Mentah:</strong> Akses <code>Bahan Mentah COGS</code> untuk mendaftarkan bahan baku (misal: Ayam, Beras, Minyak, Gelas) beserta satuan, harga beli, dan <code>loss_percent</code> (penyusutan).</li>
          <li class="mb-2"><strong>Buat Pesanan Pembelian (PO):</strong> Pilih supplier, pilih bahan mentah yang ingin dibeli, lalu tentukan kuantitas & harga beli per unit.</li>
          <li class="mb-2"><strong>Konfirmasi & Penerimaan (Receiving):</strong> Saat barang dikirim supplier, buka PO & klik <em>Penerimaan Barang (Receiving)</em>. Masukkan jumlah barang yang diterima secara fisik.</li>
          <li><strong>Otomatisasi Sistem:</strong> Stok bahan mentah <code>cogs_raw_materials.amount</code> otomatis bertambah, harga efektif terhitung ulang, dan tercatat riwayatnya di <code>cogs_raw_material_histories</code>.</li>
        </ul>
        <div class="code-box">
          <i class="bi bi-info-circle me-1 text-primary"></i>Formula Harga Efektif: <code>price_per_unit / (1 - (loss_percent / 100))</code>
        </div>
      </div>
    </div>

    <!-- Section 2: Modul Resep & COGS Menu -->
    <div class="col-lg-6">
      <div class="card guide-card h-100 p-4">
        <div class="d-flex align-items-center mb-3">
          <span class="step-number-badge me-3" style="background:#7c3aed;">2</span>
          <div>
            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">Manajemen Resep & HPP Menu</h5>
            <small class="text-muted-c">Menu: Keuangan & COGS &rarr; Resep & COGS Menu</small>
          </div>
        </div>
        <ul class="text-muted-c ps-3 mb-3" style="font-size:0.88rem;">
          <li class="mb-2"><strong>Buat Resep Baru:</strong> Hubungkan resep dengan produk menu yang ada di daftar menu (misal: Resep Nasi Goreng Spesial).</li>
          <li class="mb-2"><strong>Input Komposisi Takaran:</strong> Masukkan bahan mentah beserta takarannya per 1 porsi (contoh: Beras 0.20 kg, Minyak 0.05 L, Telur 0.06 kg, Dus Box 1 pcs).</li>
          <li class="mb-2"><strong>Tentukan Target Food Cost:</strong> Set standar target persentase modal (contoh: 30%). Sistem akan merekomendasikan harga jual ideal.</li>
          <li><strong>Otomatis Rekalkulasi:</strong> Setiap kali harga beli bahan mentah di PO berubah, modal resep <code>estimated_cogs</code> otomatis dihitung ulang secara real-time.</li>
        </ul>
        <div class="code-box">
          <i class="bi bi-info-circle me-1 text-purple"></i>Formula Modal Menu (COGS): <code>SUM(ingredient_qty * effective_price_bahan)</code>
        </div>
      </div>
    </div>

    <!-- Section 3: Modul Kasir POS & Transaksi -->
    <div class="col-lg-6">
      <div class="card guide-card h-100 p-4">
        <div class="d-flex align-items-center mb-3">
          <span class="step-number-badge me-3" style="background:#0891b2;">3</span>
          <div>
            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">Penjualan Kasir POS & Transaksi</h5>
            <small class="text-muted-c">Menu: Transaksi &rarr; Pesan / Kasir POS</small>
          </div>
        </div>
        <ul class="text-muted-c ps-3 mb-3" style="font-size:0.88rem;">
          <li class="mb-2"><strong>Proses Pesanan:</strong> Kasir memilih menu pesanan pelanggan, memilih nomor meja (jika Dine In) atau tipe Take Away / Delivery.</li>
          <li class="mb-2"><strong>Penerapan Diskon & Voucher:</strong> Kasir dapat mengaplikasikan diskon produk atau kode voucher promosi.</li>
          <li class="mb-2"><strong>Selesai & Struk:</strong> Setelah pembayaran selesai, status pesanan menjadi <code>completed</code> dan transaksi menjadi <code>success</code>.</li>
          <li><strong>Akumulasi Keuangan:</strong> Omzet penjualan dan estimasi modal COGS dari porsi yang terjual otomatis masuk ke Laporan Keuangan Bulanan.</li>
        </ul>
        <div class="code-box">
          <i class="bi bi-info-circle me-1 text-info"></i>Total Omzet Penjualan = <code>SUM(transaction_grand_total)</code> transaksi sukses
        </div>
      </div>
    </div>

    <!-- Section 4: Modul Waste Log (Bahan Terbuang) -->
    <div class="col-lg-6">
      <div class="card guide-card h-100 p-4">
        <div class="d-flex align-items-center mb-3">
          <span class="step-number-badge me-3" style="background:#ef4444;">4</span>
          <div>
            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">Pencatatan Bahan Terbuang (Waste Log)</h5>
            <small class="text-muted-c">Menu: Keuangan & COGS &rarr; Bahan Terbuang (Waste Log)</small>
          </div>
        </div>
        <ul class="text-muted-c ps-3 mb-3" style="font-size:0.88rem;">
          <li class="mb-2"><strong>Pencatatan Kerugian:</strong> Jika terdapat bahan mentah yang busuk di kulkas, expired, atau tumpah, langsung catat di menu ini.</li>
          <li class="mb-2"><strong>Input Kuantitas & Alasan:</strong> Masukkan jumlah bahan mentah yang hilang dan pilih alasannya (Rotten, Expired, Broken, Spilled).</li>
          <li class="mb-2"><strong>Pemotongan Stok:</strong> Kuantitas stok <code>cogs_raw_materials.amount</code> otomatis terpotong sesuai jumlah yang rusak.</li>
          <li><strong>Pengurang Laba Bersih:</strong> Nilai kerugian <code>waste_cost</code> langsung masuk sebagai pengurang laba bersih di Laporan HPP bulanan.</li>
        </ul>
        <div class="code-box text-danger">
          <i class="bi bi-exclamation-triangle me-1"></i>Nilai Kerugian Waste = <code>qty_lost * effective_price_bahan</code>
        </div>
      </div>
    </div>

    <!-- Section 5: Modul Laporan HPP & Laba Rugi -->
    <div class="col-lg-12">
      <div class="card guide-card p-4">
        <div class="d-flex align-items-center mb-3">
          <span class="step-number-badge me-3" style="background:#10b981;">5</span>
          <div>
            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">Laporan HPP, Laba Rugi, & Analisis Per-Menu</h5>
            <small class="text-muted-c">Menu: Keuangan & COGS &rarr; Laporan HPP & Laba Rugi</small>
          </div>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <h6 class="fw-bold text-primary mb-2">📌 Fitur Utama Laporan HPP:</h6>
            <ul class="text-muted-c ps-3 mb-0" style="font-size:0.88rem;">
              <li class="mb-2"><strong>4 Ringkasan KPI Card:</strong> Menampilkan Total Omzet Kasir, Estimasi COGS (Modal Menu), Kerugian Waste, dan Estimasi Laba Bersih.</li>
              <li class="mb-2"><strong>Fitur Input Biaya Operasional:</strong> Klik tombol <em>"Update Gaji & Listrik Bulan Ini"</em> untuk menginput total Gaji Karyawan dan Listrik/Sewa PLN.</li>
              <li class="mb-2"><strong>Ringkasan Pembelian PO:</strong> Menampilkan total nominal pembelian bahan mentah dari PO yang diterima (Receiving).</li>
              <li><strong>Export & Evaluasi:</strong> Digunakan pemilik toko untuk mengawasi kesehatan finansial outlet setiap bulan.</li>
            </ul>
          </div>
          <div class="col-md-6">
            <h6 class="fw-bold text-success mb-2">📊 Tabel Rincian Menu & Pagination Interaktif:</h6>
            <ul class="text-muted-c ps-3 mb-0" style="font-size:0.88rem;">
              <li class="mb-2"><strong>Rincian Per-Menu:</strong> Menampilkan QTY Terjual, Harga Jual, Total Omzet, Modal COGS Porsi, Total COGS, Laba Kotor Menu, & Margin Persentase (%).</li>
              <li class="mb-2"><strong>Dropdown Pagination:</strong> Pilih jumlah baris per halaman (10, 20, 50, 100, atau Semua/Custom) di pojok kanan atas tabel.</li>
              <li class="mb-2"><strong>Modal Detail Resep:</strong> Klik tombol <em>"Rincian"</em> pada menu apa saja untuk melihat breakdown takaran & pemakaian bahan mentah bulan ini.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
