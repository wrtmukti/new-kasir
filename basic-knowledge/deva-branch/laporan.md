# Blueprint & Daftar Spesifikasi Laporan POS SaaS F&B (`laporan.md`)

> **Dokumen Referensi**: Daftar Lengkap Laporan POS, Arsitektur Dashboard Laporan, Spesifikasi Parameter/Filter, Detail Kolom Data, Fitur Export Excel (`.xlsx`), Pembagian Tier SaaS, dan Status Implementasi.

---

## 1. Arsitektur 2 Level Laporan (Dashboard Hub & Detail Pages)

Sistem Laporan dirancang menggunakan **2 Level UI**:

```text
 ┌────────────────────────────────────────────────────────────────────────────────┐
 │                       LEVEL 1: DASHBOARD PUSAT LAPORAN                         │
 │                                (/admin/reports)                                │
 │  - Summary KPI Cards (Omzet Net, Pemasukan, Pengeluaran, Net Profit, Top Menu) │
 │  - Quick Charts & Quick Date Filter (Hari Ini / Minggu Ini / Bulan Ini)        │
 └───────────────────────────────────────┬────────────────────────────────────────┘
                                         │
       ┌─────────────────────────────────┼─────────────────────────────────┐
       ▼                                 ▼                                 ▼
┌─────────────────────────┐   ┌─────────────────────────┐   ┌─────────────────────────┐
│  LEVEL 2: DETAIL LAPORAN│   │  LEVEL 2: DETAIL LAPORAN│   │  LEVEL 2: DETAIL LAPORAN│
│    PENJUALAN & PEMBAYARAN │   │  PERFORMA MENU & PMIX   │   │  PEMASUKAN & PENGELUARAN│
│   (/admin/reports/sales)│   │ (/admin/reports/products)│   │ (/admin/reports/cashflow)│
│  [📥 Export Excel/PDF]  │   │  [📥 Export Excel/PDF]  │   │  [📥 Export Excel/PDF]  │
└─────────────────────────┘   └─────────────────────────┘   └─────────────────────────┘
```

---

## 2. Rincian Laporan Per Kategori, Spesifikasi Data & Export Excel

### 💳 A. Kategori 1: Laporan Penjualan & Pembayaran (`/admin/reports/sales`)

#### A.1 Laporan Rekap Penjualan Periode & Payment Breakdown
- **Fungsi**: Rekapitulasi omzet bersih, pajak, service charge, diskon, dan breakdown metode pembayaran (Cash, QRIS, EDC, Transfer).
- **Filter**: Tanggal Awal - Tanggal Akhir, Payment Method, Shift.
- **Data Ditampilkan**: Tanggal, Jumlah Transaksi, Gross Sales, Total Diskon, Net Sales, Service Charge, Tax (PB1), Grand Total.
- **📥 Format Export Excel (`.xlsx`)**:
  - Sheet 1: Ringkasan Penjualan & Breakdown Pembayaran (Cash, QRIS, EDC BCA, Transfer Bank).
  - Sheet 2: Rekapitulasi Penjualan Harian.
- **Tier SaaS**: `BASIC`, `PRO`, `PREMIUM`.
- **Status `new-kasir`**: 🕒 *Perlu Dibuat Controller & View Dedicated*.

#### A.2 Laporan Detail Transaksi Struk
- **Fungsi**: Rincian transaksi per order ID secara komprehensif.
- **Filter**: No. Order, Nama Kasir, Status Payment.
- **Data Ditampilkan**: Order Number, Waktu, Kasir, Customer/Meja, Detail Item Terjual, Total Diskon, Total Bayar.
- **📥 Format Export Excel (`.xlsx`)**: Rincian transaksi per baris struk.
- **Tier SaaS**: `BASIC`, `PRO`, `PREMIUM`.
- **Status `new-kasir`**: ✅ *Sudah Ada (`TransactionController`)*.

---

### 🍔 B. Kategori 2: Laporan Performa Menu Terlaris & Slow-Moving (`/admin/reports/products`)

#### B.1 Laporan Product Mix (PMIX) & Ranking Menu
- **Fungsi**: Mengetahui menu mana yang paling laris (*Best Seller*) vs sepi (*Slow-Moving*), beserta total angka penjualan dan margin profitnya.
- **Filter**: Kategori Menu (Makanan, Minuman, Snack), Periode Tanggal.
- **Data Ditampilkan**: Ranking (1-N), Kode/SKU, Nama Menu, Kategori, Qty Terjual, Gross Sales (Rp), Unit COGS (Rp), Total COGS (Rp), Gross Profit (Rp), Margin %, Status Badge (*Best Seller / Popular / Normal / Slow-Moving*).
- **📥 Format Export Excel (`.xlsx`)**:
  - Sheet 1: Ranking Menu Terlaris & Total Omzet Per Menu.
  - Sheet 2: Analisis Menu Slow-Moving (Penjualan Rendah).
- **Tier SaaS**: `BASIC`, `PRO`, `PREMIUM`.
- **Status `new-kasir`**: ✅ *Sudah Ada di (`MenuAnalyticsController`), Perlu Tombol Export Excel*.

---

### 💵 C. Kategori 3: Laporan Pemasukan, Pengeluaran & Arus Kas (`/admin/reports/cashflow`)

#### C.1 Laporan Pemasukan vs Pengeluaran (*Cash Flow & Simple P&L*)
- **Fungsi**: Menampilkan total arus uang masuk (Omzet Penjualan Kasir) dibanding arus uang keluar (Belanja Bahan PO Supplier, Waste Kerusakan Bahan, Gaji Staf, Listrik/Sewa, & Kas Kecil Kasir).
- **Filter**: Bulan, Tahun, Cabang/Outlet.
- **Data Ditampilkan**:
  - **Pemasukan**: Omzet Net Sales Penjualan Kasir.
  - **Pengeluaran**: Belanja Bahan (PO Supplier), Waste Log (Bahan Rusak/Busuk), Biaya Gaji, Biaya Listrik & Sewa, Pengeluaran Kas Kecil.
  - **Net Cash Flow (Laba Bersih Sederhana)**: $\text{Pemasukan} - \text{Total Pengeluaran}$.
- **📥 Format Export Excel (`.xlsx`)**: File `laporan_pemasukan_dan_pengeluaran_YYYYMM.xlsx` (Detail Rincian Pemasukan & Pengeluaran).
- **Tier SaaS**: `PRO`, `PREMIUM`, `FINANCE ADD-ON`.
- **Status `new-kasir`**: ✅ *Terintegrasi di (`HppReportController`), Perlu View Dedicated Cash Flow*.

---

### 📦 D. Kategori 4: Laporan Stok & Inventory (`/admin/reports/inventory`)

#### D.1 Laporan Stok Bahan Mentah, PO Belanja & Waste Log
- **Fungsi**: Rekapitulasi sisa stok fisik bahan mentah, nilai aset rupiah gudang, riwayat belanja PO, dan kerugian bahan mentah busuk/rusak.
- **Filter**: Kategori Bahan, Supplier, Tanggal.
- **Data Ditampilkan**: Nama Bahan, Stok Sisa, Harga Belanja, Total Nilai Aset, Total PO Belanja, Nilai Kerugian Waste (Rp).
- **📥 Format Export Excel (`.xlsx`)**:
  - Sheet 1: Stok Sisa & Nilai Aset Gudang.
  - Sheet 2: Rekapitulasi Belanja PO Supplier.
  - Sheet 3: Log Kerusakan & Busuk Bahan (*Waste Log*).
- **Tier SaaS**: `PRO`, `PREMIUM`.
- **Status `new-kasir`**: ✅ *Sudah Ada di Modul Keuangan COGS*.

---

### 🔐 E. Kategori 5: Laporan Audit Shift Closing & Selisih Kas (`/admin/reports/shifts`)

#### E.1 Laporan Pertanggungjawaban Shift Kasir
- **Fungsi**: Audit pertanggungjawaban kasir saat buka/tutup shift (mencegah kecurangan uang kasir).
- **Filter**: Nama Kasir, Tanggal Shift.
- **Data Ditampilkan**: Tanggal/Jam Shift, Nama Kasir, Modal Kas Awal, Total Penjualan Tunai, Penjualan Non-Tunai, Uang Kas Diinginkan Sistem, Uang Kas Fisik Dihitung, Selisih Kas (*Over/Short*).
- **📥 Format Export Excel (`.xlsx`)**: Audit Log Tutup Shift Kasir.
- **Tier SaaS**: `BASIC`, `PRO`, `PREMIUM`.
- **Status `new-kasir`**: 🕒 *Perlu Dibuat Controller & View Dedicated*.

---

### 🏛️ F. Kategori 6: Laporan Rekap Pajak (PB1) & Service Charge (`/admin/reports/tax-service`)

#### F.1 Laporan Rekap Setoran Pajak Restoran & Service Charge
- **Fungsi**: Rekapitulasi Dasar Pengenaan Pajak (DPP), nilai pajak PB1 yang wajib disetor ke Pemda, dan akumulasi service charge karyawan.
- **Filter**: Bulan, Tahun.
- **Data Ditampilkan**: Periode, Total DPP (Dasar Pajak), Total Pajak PB1 (10%), Total Service Charge (5%), Total Grand Total Penjualan.
- **📥 Format Export Excel (`.xlsx`)**: File Rekap Pajak Resmi F&B.
- **Tier SaaS**: `PRO`, `PREMIUM`, `FINANCE ADD-ON`.
- **Status `new-kasir`**: 🕒 *Perlu Dibuat bersama Modul Tax*.

---

## 3. Matriks Fitur Export Excel & Tier SaaS

| Nama Laporan | Fitur Export Excel (`.xlsx`) | Basic Tier | Pro Tier | Premium / Finance |
| :--- | :---: | :---: | :---: | :---: |
| **Laporan Penjualan & Pembayaran** | ✅ Available | ✅ | ✅ | ✅ |
| **Laporan Detail Struk Transaksi** | ✅ Available | ✅ | ✅ | ✅ |
| **Laporan Performa Menu Terlaris & PMIX**| ✅ Available | ✅ | ✅ | ✅ |
| **Laporan Shift Closing Kasir** | ✅ Available | ✅ | ✅ | ✅ |
| **Laporan Pemasukan & Pengeluaran** | ✅ Available | ❌ | ✅ | ✅ |
| **Laporan Stok Bahan Mentah & PO** | ✅ Available | ❌ | ✅ | ✅ |
| **Laporan Waste Log (Barang Rusak)**| ✅ Available | ❌ | ✅ | ✅ |
| **Laporan Rekap Pajak PB1 & Service** | ✅ Available | ❌ | ✅ | ✅ |

---

## 4. Rekomendasi Struktur Menu Sidebar Laporan

```text
📁 KEUANGAN & LAPORAN
 ├── 📊 Pusat Dashboard Laporan      (/admin/reports)             -> Executive Summary Dashboard
 ├── 💳 Laporan Penjualan & Bayar    (/admin/reports/sales)       -> Rekap Omzet & Payment (Excel Export)
 ├── 🍔 Laporan Performa Menu        (/admin/reports/products)    -> Top Seller & Slow Moving (Excel Export)
 ├── 💵 Laporan Pemasukan & Out      (/admin/reports/cashflow)    -> Arus Kas Pemasukan & Pengeluaran (Excel)
 ├── 🏛️ Laporan Pajak & Service      (/admin/reports/tax-service) -> Setoran PB1 Pemda & Service Pool (Excel)
 ├── 📦 Laporan Stok & Waste Log     (/admin/reports/inventory)   -> Rekap Bahan, PO & Waste (Excel Export)
 ├── 🔐 Audit Shift Closing Kasir    (/admin/reports/shifts)      -> Rekap Tutup Shift & Selisih Uang Kas
 └── 💰 Laporan HPP & Laba Rugi      (/admin/keuangan/hpp-report) -> (Sudah Ada) Detail Financial Report
```

