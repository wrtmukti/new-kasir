# Dokumen Rencana & Arsitektur Finansial: Theoretical Food Cost & Dedicated Cash Flow System (Plan A-v3)

> **Dokumen**: `plan-a-v3.md`  
> **Tanggal**: 2026-08-27 (27 Agustus 2026)  
> **Branch**: `deva-branch`  
> **Status**: FINAL PROPOSED BLUEPRINT V3 (RECOMMENDED STANDARD)  
> **Konsep Inti**: Model Laba Resep Murni (*Theoretical Food Cost*) + Waste Log sebagai Alert Memo Pengawasan + Dedicated Arus Kas Terpisah (Modal Awal, PO, Gaji, OPEX)

---

## 1. Perbandingan Komprehensif: V1 vs V2 vs V3

```
+--------------------------+-----------------------+-----------------------+---------------------------------------+
| Parameter                | Plan A (V1)           | Plan A-v2 (V2)        | Plan A-v3 (V3 - Final Proposed)       |
+--------------------------+-----------------------+-----------------------+---------------------------------------+
| 1. Laba Kotor            | Omzet - (COGS+Waste)  | Omzet - (COGS+Waste)  | Omzet - COGS Resep Murni              |
|    (Gross Profit)        | (Waste motong laba)   | (Waste motong laba)   | (Mencerminkan performa resep murni)   |
+--------------------------+-----------------------+-----------------------+---------------------------------------+
| 2. Posisi Waste Log      | Pengurang laba kotor  | Pengurang laba kotor  | Kartu Alert / Memo Pengawasan Khusus  |
|    (Bahan Rusak/Basi)    |                       |                       | (TIDAK memotong rumus laba rugi)      |
+--------------------------+-----------------------+-----------------------+---------------------------------------+
| 3. Laba Bersih           | GP - Gaji - OPEX      | GP - Gaji - OPEX      | Laba Kotor - Gaji - OPEX              |
|    (Net Profit)          |                       |                       | (Sederhana, presisi, mudah dipahami)  |
+--------------------------+-----------------------+-----------------------+---------------------------------------+
| 4. Halaman Arus Kas      | Tercampur sebagian    | Terpisah total        | Terpisah 100% Dedicated               |
|    (Cash Flow)           |                       |                       | (Route: /admin/reports/cashflow)      |
+--------------------------+-----------------------+-----------------------+---------------------------------------+
| 5. Modal Awal Kasir      | Belum masuk           | Masuk Cash Flow       | Terintegrasi Penuh (Starting Cash     |
|    (Starting Float Pagi) |                       |                       | + Kas Kecil Paid-Out + Selisih Kasir) |
+--------------------------+-----------------------+-----------------------+---------------------------------------+
| 6. Pembelian PO          | Info di P&L           | Memotong Cash Flow    | HARAM memotong P&L, MURNI memotong    |
|    (Belanja Supplier)    |                       | (Bukan P&L)           | Arus Kas Keluar saat barang diterima  |
+--------------------------+-----------------------+-----------------------+---------------------------------------+
| 7. Indikator Prime Cost  | Ada threshold rumit   | Dihilangkan badge     | Disederhanakan (Fokus pada margin %)  |
+--------------------------+-----------------------+-----------------------+---------------------------------------+
```

---

## 2. Arsitektur Dua Halaman Keuangan Mandiri

```
                                  SISTEM KEUANGAN F&B ERP
                                             |
            +--------------------------------+--------------------------------+
            |                                                                 |
     [ HALAMAN 1: P&L ]                                                [ HALAMAN 2: CASH FLOW ]
Laporan HPP & Laba Rugi                                           Dedicated Laporan Arus Kas
Route: /admin/keuangan/hpp-report                                 Route: /admin/reports/cashflow
            |                                                                 |
- Mengukur: PROFITABILITAS USAHA                                  - Mengukur: KETERSEDIAAN UANG KAS RIIL
- Prinsip Akrual (Penjualan vs Beban Pokok)                       - Prinsip Kas Riil (Uang Masuk vs Uang Keluar)
- Struktur:                                                       - Struktur:
  (+) Net Sales (Omzet Bersih POS)                                  (+) Uang Masuk POS (Tunai + Non-Tunai)
  (-) Estimasi COGS Resep Terjual                                   (+) Modal Awal Kasir Pagi (Starting Cash)
  (=) LABA KOTOR (GROSS PROFIT)                                     (+) Kas Masuk Tambahan (Paid-In)
  (-) Biaya Gaji Karyawan (Labor Cost)                              (-) Realisasi Belanja PO Supplier
  (-) Biaya Listrik, Sewa, WiFi (OPEX)                              (-) Biaya Gaji Dibayar
  (=) LABA BERSIH (NET PROFIT)                                      (-) Biaya Listrik, Sewa, WiFi Dibayar
                                                                    (-) Pengeluaran Kas Kecil Laci (Paid-Out)
  [📌 ALERT CARD KHUSUS]:                                           (+/-) Selisih Kasir Tutup Shift (Over/Short)
  ⚠️ Total Kerugian Waste: Rp X                                     (=) NET CASH FLOW RIIL (SURPLUS / DEFISIT)
  📦 Total Nilai Aset Belanja PO: Rp Y
```

---

## 3. Spesifikasi Detail: Halaman 1 — Laporan HPP & Laba Rugi (P&L)

### A. Formula Matematis:
1. **Omzet Penjualan Bersih (*Net Sales*):**
   $$\text{Net Sales} = \text{Total Subtotal Pesanan Sukses} - \text{Diskon}$$
2. **Laba Kotor Resep Murni (*Gross Profit*):**
   $$\text{Gross Profit} = \text{Net Sales} - \text{Total Estimasi COGS Resep Terjual}$$
   $$\text{Gross Margin (\%)} = \left(\frac{\text{Gross Profit}}{\text{Net Sales}}\right) \times 100\%$$
3. **Laba Bersih Toko (*Net Operating Profit*):**
   $$\text{Net Profit} = \text{Gross Profit} - \text{Biaya Gaji (Labor)} - \text{Biaya Operasional (Overhead/OPEX)}$$
   $$\text{Net Margin (\%)} = \left(\frac{\text{Net Profit}}{\text{Net Sales}}\right) \times 100\%$$

### B. Peran Waste Log & PO di Halaman Ini:
* **Waste Log (Kerugian Bahan Terbuang):**
  - **TIDAK MEMOTONG** angka Laba Kotor maupun Laba Bersih di tabel kalkulasi.
  - Ditampilkan dalam **Alert Bento Card / Warning Box**:
    *Menampilkan total nominal bahan rusak/kadaluarsa bulan itu beserta rincian bahannya, sebagai bahan evaluasi kedisiplinan koki/dapur.*
* **Pembelian PO Bahan Mentah:**
  - **TIDAK MEMOTONG** Laba Rugi (karena merupakan Aset Persediaan Gudang).
  - Ditampilkan dalam **Info Memo Box**:
    *Menampilkan total belanja bahan mentah yang masuk (*Receiving*) pada bulan tersebut.*

---

## 4. Spesifikasi Detail: Halaman 2 — Dedicated Laporan Arus Kas (Cash Flow)

Halaman ini berdiri sendiri di route [`/admin/reports/cashflow`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/keuangan/reports/cashflow.blade.php) dan controller [`CashFlowReportController.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Keuangan/CashFlowReportController.php).

### A. Komponen Arus Kas Masuk (*Cash Inflow*):
1. **Penjualan Kasir Tunai (*Cash Sales*):** Uang fisik yang diterima laci kasir.
2. **Penjualan Non-Tunai (*Digital Sales*):** Transaksi via QRIS, EDC BCA/Mandiri, dan Transfer Bank.
3. **Modal Awal Laci Buka Shift (*Opening Cash Float*):** Uang pecahan kecil yang dimasukkan tiap pagi untuk kembalian kasir (agregasi `starting_cash` dari tabel `daily_closings`).
4. **Kas Masuk Tambahan (*Paid-In*):** Suntikan dana laci darurat di tengah shift (`cash_in_amount` di `daily_closings`).

### B. Komponen Arus Kas Keluar (*Cash Outflow*):
1. **Realisasi Belanja Bahan Mentah PO Supplier:** Total pembayaran untuk barang PO yang diterima / `completed` pada rentang tanggal filter.
2. **Pembayaran Biaya Gaji Karyawan (*Labor Cost Paid*):** Total pengeluaran upah & gaji pada periode tersebut.
3. **Pembayaran Biaya Operasional / Listrik / Sewa / WiFi (*OPEX Paid*):** Total tagihan operasional yang dibayarkan.
4. **Pengeluaran Kas Kecil Laci (*Paid-Out / Petty Cash*):** Pembelian mendadak dari laci kasir (es batu, galon air, gas elpiji, parkir, kurir) dari `cash_out_amount` tabel `daily_closings`.

### C. Rekonsiliasi Kasir & Net Cash Flow:
* **Selisih Kasir (*Cash Difference Over/Short*):** Selisih antara hitungan fisik kasir saat *Clock-Out* dengan kalkulasi sistem (`cash_difference` di `daily_closings`).
* **Formula Arus Kas Bersih (*Net Cash Flow*):**
  $$\text{Total Inflow} = \text{Kas Tunai POS} + \text{Non-Tunai POS} + \text{Modal Awal Kasir} + \text{Kas Masuk Tambahan}$$
  $$\text{Total Outflow} = \text{Belanja PO Supplier} + \text{Gaji} + \text{Listrik/OPEX} + \text{Kas Kecil Laci}$$
  $$\text{Net Cash Flow} = (\text{Total Inflow} - \text{Total Outflow}) + \text{Total Selisih Kasir Over/Short}$$

*(Waste Log dan Estimasi COGS **100% dikeluarkan** dari Cash Flow karena bukan transaksi uang keluar).*

---

## 5. Rencana Perubahan Database & Migrations

### Update Tabel `hpp_financial_reports`
Migration: `2026_08_27_000001_add_margins_to_hpp_financial_reports_table.php`
- Menambahkan kolom `gross_margin_percent` (`decimal(5,2)`).
- Menambahkan kolom `net_margin_percent` (`decimal(5,2)`).
- Mempertahankan `total_revenue`, `total_cogs_estimated`, `total_waste_cost`, `total_labor_cost`, `total_overhead_cost`, `gross_profit`, `net_profit_estimated`, `notes`.

### Model `HppFinancialReport.php`
- Update `$fillable` dan casts `float` untuk ketepatan JSON.

---

## 6. Desain Visual UI/UX Dashboard (Custom Styling, Dark/Light Mode)

### A. Halaman Laba Rugi (`/admin/keuangan/hpp-report`)
* **4 Header Bento KPI Cards:**
  1. `OMZET PENJUALAN (NET REVENUE)` (Border Biru)
  2. `ESTIMASI COGS RESEP TERJUAL` (Border Oranye)
  3. `ESTIMASI LABA BERSIH (NET PROFIT)` (Border Hijau)
  4. `RADAR KERUGIAN WASTE LOG (ALERT)` (Border Merah Lembut)
* **Waterfall Laba Rugi Bulanan:**
  - Net Sales Omzet Kasir
  - (-) Estimasi COGS Resep Terjual
  - (=) **LABA KOTOR (GROSS PROFIT)** *(Margin %)*
  - (-) Biaya Gaji Karyawan (Labor Cost)
  - (-) Biaya Operasional / Listrik / Sewa (Overhead)
  - (=) **LABA BERSIH OPERASIONAL (NET PROFIT)** *(Margin %)*
* **Card Memo Pengawasan Dapur & Gudang:**
  - Menampilkan total Kerugian Bahan Terbuang (Waste Log).
  - Menampilkan total Pembelian Bahan Mentah PO (Receiving).

### B. Halaman Dedicated Arus Kas (`/admin/reports/cashflow`)
* **3 Header Bento KPI Cards:**
  1. `TOTAL ARUS KAS MASUK (INFLOW)` (Hijau)
  2. `TOTAL ARUS KAS KELUAR (OUTFLOW)` (Merah)
  3. `SURPLUS / DEFISIT KAS BERSIH` (Biru / Ungu)
* **3 Panel Rincian Kas:**
  - Panel A: Penerimaan Kasir & Modal Awal Shift
  - Panel B: Pengeluaran Belanja PO Supplier, Gaji & Operasional
  - Panel C: Audit Rekonsiliasi Selisih Kasir (Over/Short)
* **Export CSV:** Format ekspor rapi untuk akuntan/pembukuan.

---

## 7. Rangkuman Matriks Pengalokasian

```
+-----------------------------------+-----------------------+-----------------------+
| Jenis Transaksi                   | Laba Rugi (P&L)       | Arus Kas (Cash Flow)  |
+-----------------------------------+-----------------------+-----------------------+
| Omzet Penjualan POS (Kasir)       | YA (Net Revenue)      | YA (Cash Inflow)      |
| Modal Awal Buka Shift (Float)     | TIDAK                 | YA (Starting Float)   |
| COGS Resep Menu Terjual           | YA (Pengurang Laba)   | TIDAK                 |
| Waste Log (Bahan Terbuang/Basi)   | TIDAK (Hanya Alert)   | TIDAK                 |
| Belanja PO Bahan Mentah           | TIDAK (Hanya Memo)    | YA (Cash Outflow)     |
| Gaji Karyawan                     | YA (Labor Cost)       | YA (Cash Outflow)     |
| Listrik, Sewa, WiFi (Overhead)    | YA (Overhead OPEX)    | YA (Cash Outflow)     |
| Kas Kecil Laci (Paid-Out)         | YA (Masuk Overhead)   | YA (Cash Outflow)     |
| Selisih Kasir (Over/Short)        | TIDAK                 | YA (Cash Variance)    |
+-----------------------------------+-----------------------+-----------------------+
```

---

> 🎯 *Plan A-v3 adalah blueprint paling seimbang: menyajikan performa resep murni di Laba Rugi, kontrol ketat uang tunai di Cash Flow, dan radar pengawasan kebocoran bahan di Waste Log.*
