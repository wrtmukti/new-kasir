# Dokumen Rencana & Arsitektur Finansial: Standar USAR & Dedicated Cash Flow System (Plan A-v2)

> **Dokumen**: `plan-a-v2.md`  
> **Tanggal**: 2026-08-27 (27 Agustus 2026)  
> **Branch**: `deva-branch`  
> **Status**: PROPOSED BLUEPRINT V2 (EVOLVED FROM PLAN A)  
> **Fokus Utama**: Pemisahan Total Laba Rugi (P&L) vs Dedicated Arus Kas (Cash Flow) + Manajemen Modal Awal Kasir (*Cash Float*) & Belanja PO

---

## 1. Perbandingan Utama: Plan A vs Plan A-v2

| Parameter | Plan A (Versi Awal) | Plan A-v2 (Versi Yang Disempurnakan) |
| :--- | :--- | :--- |
| **Indikator Prime Cost** | Menyertakan persentase threshold kompleks (<60%, 60-65%, >65%). | **Disederhanakan**: Dihilangkan threshold/badge rumit, fokus pada angka nominal & margin yang presisi dan bersih. |
| **Pemisahan Halaman** | Cash Flow masih bercampur logika di beberapa modul laporan. | **Pemisahan Total**: Halaman **Laporan HPP & Laba Rugi** terpisah 100% dari Halaman **Dedicated Arus Kas (Cash Flow)**. |
| **Modal Awal Kasir (*Cash Float*)** | Belum diakomodasi dalam aliran kas. | **Diakomodasi Penuh**: Mengintegrasikan `starting_cash` (modal kembalian laci buka shift), kas kecil laci (*paid-out*), dan selisih kasir (*over/short*). |
| **Perlakuan Belanja PO** | Dicatat sebagai info di P&L. | **Strict Accounting**: PO adalah Aset Persediaan. PO **hanya** memotong Arus Kas saat dibayar/diterima, dan **haram** memotong Laba Rugi. |
| **Perlakuan Waste Log** | Masuk ke Actual COGS (P&L). | **Strict Accounting**: Waste masuk ke Actual COGS di P&L, dan **dikeluarkan total** dari Cash Flow (karena kerugian non-kas). |

---

## 2. Pemisahan Dua Pilar Keuangan Restoran

```
                                  SISTEM KEUANGAN F&B ERP
                                             |
            +--------------------------------+--------------------------------+
            |                                                                 |
     [ PILAR 1: P&L ]                                                  [ PILAR 2: CASH FLOW ]
Laporan HPP & Laba Rugi                                           Laporan Dedicated Arus Kas
Route: /admin/keuangan/hpp-report                                 Route: /admin/reports/cashflow
            |                                                                 |
- Basis Akrual (Kinerja Bisnis)                                   - Basis Kas Riil (Likuiditas & Kas Fisik)
- Mengukur: Apakah resto PROFIT atau RUGI?                        - Mengukur: Apakah uang kas resto AMAN atau DEFISIT?
- Komponen:                                                       - Komponen:
  (+) Net Sales (Omzet Murni Kasir)                                 (+) Uang Masuk POS (Cash + QRIS/EDC)
  (-) Actual COGS (Resep Terjual + Waste Log)                       (+) Modal Awal Buka Shift (Starting Cash)
  (=) Gross Profit (Laba Kotor)                                     (-) Belanja Bahan Mentah PO Supplier
  (-) Gaji Karyawan (Labor Cost)                                    (-) Biaya Gaji Karyawan Dibayar
  (-) Listrik, Sewa, WiFi (Overhead/OPEX)                           (-) Biaya Listrik, Sewa, WiFi Dibayar
  (=) Net Operating Profit                                          (-) Pengeluaran Kas Kecil Laci (Paid-Out)
                                                                    (+/-) Selisih Kasir (Over/Short Closing)
                                                                    (=) Net Cash Flow & Posisi Kas Riil
```

---

## 3. Detail Arsitektur: Pilar 1 — Laporan HPP & Laba Rugi (P&L)

### Rumus Matematis:
1. **Omzet Penjualan Bersih (*Net Sales*):**
   $$\text{Net Sales} = \text{Total Subtotal Pesanan Berhasil} - \text{Diskon}$$
   *(Pajak PB1 10% dan Service Charge 5% dipisahkan karena merupakan titipan pemda & pool karyawan).*
2. **Total Harga Pokok Penjualan (*Actual Cost of Sales*):**
   $$\text{Total Cost of Sales} = \text{Estimasi Modal COGS Porsi Terjual} + \text{Kerugian Waste Log}$$
3. **Laba Kotor (*Gross Profit*):**
   $$\text{Gross Profit} = \text{Net Sales} - \text{Total Cost of Sales}$$
   $$\text{Gross Margin (\%)} = \left(\frac{\text{Gross Profit}}{\text{Net Sales}}\right) \times 100\%$$
4. **Laba Bersih (*Net Profit*):**
   $$\text{Net Profit} = \text{Gross Profit} - \text{Biaya Gaji (Labor)} - \text{Biaya Operasional (Overhead/OPEX)}$$
   $$\text{Net Margin (\%)} = \left(\frac{\text{Net Profit}}{\text{Net Sales}}\right) \times 100\%$$

*Catatan: Pembelian PO di halaman ini hanya tampil sebagai **Info Box / Memo** status aset gudang, tidak mengurangi laba.*

---

## 4. Detail Arsitektur: Pilar 2 — Laporan Dedicated Arus Kas (Cash Flow)

Halaman ini berdiri sendiri di route [`/admin/reports/cashflow`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/keuangan/reports/cashflow.blade.php) dan controller [`CashFlowReportController.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Keuangan/CashFlowReportController.php).

### Struktur Aliran Kas yang Diintegrasikan:

#### A. Arus Kas Masuk (*Cash Inflows*)
1. **Penerimaan Transaksi Kas Tunai (*Cash Sales*):** Uang fisik yang masuk ke laci kasir dari pembayaran tunai.
2. **Penerimaan Transaksi Non-Tunai (*Digital Sales*):** Pembayaran via QRIS, EDC Kartu Debit/Kredit, dan Transfer Bank.
3. **Modal Awal Laci Kasir (*Opening Cash Float*):** Uang kas pecahan kecil yang disiapkan tiap pagi untuk modal kembalian kasir (diambil dari agregasi `starting_cash` pada tabel `daily_closings`).
4. **Kas Masuk Tambahan Manual (*Paid-In*):** Penambahan modal kasir darurat di tengah shift (`cash_in_amount` di `daily_closings`).

#### B. Arus Kas Keluar (*Cash Outflows*)
1. **Realisasi Belanja Bahan Mentah PO Supplier:** Total uang keluar untuk pembayaran barang PO yang berstatus `completed` / diterima pada periode tersebut.
2. **Pengeluaran Biaya Gaji Karyawan:** Gaji yang dibayarkan pada periode tersebut.
3. **Pengeluaran Beban Operasional / Listrik / Sewa / WiFi:** Tagihan operasional yang dibayar tunai/transfer.
4. **Pengeluaran Kas Kecil Laci (*Paid-Out / Petty Cash*):** Pengeluaran mendadak dari laci kasir, seperti beli es batu/gas elpiji/kurir (`cash_out_amount` di `daily_closings`).

#### C. Penyesuaian & Rekonsiliasi Kas (*Cash Variance*)
* **Selisih Kasir (*Cash Difference Over/Short*):** Selisih antara uang fisik yang dihitung kasir saat tutup shift (*Clock-Out*) dengan perhitungan sistem (`cash_difference` di `daily_closings`).

#### D. Rumus Arus Kas Bersih (*Net Cash Flow*):
$$\text{Total Inflow} = \text{Kas Tunai POS} + \text{Non-Tunai POS} + \text{Modal Awal Kasir}$$
$$\text{Total Outflow} = \text{Belanja PO Supplier} + \text{Gaji} + \text{Listrik/OPEX} + \text{Kas Kecil Laci}$$
$$\text{Net Cash Flow} = (\text{Total Inflow} - \text{Total Outflow}) + \text{Total Selisih Kasir (Over/Short)}$$

---

## 5. Rencana Perubahan Database & Migrations

### A. Tabel `hpp_financial_reports`
Migration: `2026_08_27_000001_add_actual_cogs_to_hpp_financial_reports_table.php`
- Menambahkan kolom `total_actual_cogs` (`decimal(15,2)`) = `total_cogs_estimated + total_waste_cost`.
- Menambahkan kolom `gross_margin_percent` (`decimal(5,2)`).
- Menambahkan kolom `net_margin_percent` (`decimal(5,2)`).

### B. Tabel `daily_closings` & `purchase_orders`
- Skema tabel `daily_closings` yang sudah ada (`starting_cash`, `system_cash_sales`, `system_non_cash_sales`, `cash_in_amount`, `cash_out_amount`, `cash_difference`) langsung dihubungkan (*aggregated*) ke query `CashFlowReportController`.

---

## 6. Rencana Desain UI/UX (Bento Modern, Dark/Light Mode, No Bootstrap 5)

### A. Tampilan Dashboard Laba Rugi (`/admin/keuangan/hpp-report`)
* **Header Bento Cards (4 Kartu):**
  1. `NET REVENUE (OMZET MURNI)` (Biru)
  2. `ACTUAL COGS (MODAL + WASTE)` (Cyan/Orange)
  3. `LABA KOTOR (GROSS PROFIT)` (Emerald)
  4. `LABA BERSIH (NET PROFIT)` (Hijau Terang / Merah)
* **Waterfall Laba Rugi:**
  Menampilkan urutan dari Omzet &rarr; Pemakaian Bahan (COGS + Waste) &rarr; Gross Profit &rarr; Biaya Gaji & OPEX &rarr; Net Profit.

### B. Tampilan Dedicated Laporan Arus Kas (`/admin/reports/cashflow`)
* **Header Bento Cards (3 Kartu Utama):**
  1. `TOTAL INFLOW (UANG MASUK)`: Omzet POS + Modal Awal Laci Kasir.
  2. `TOTAL OUTFLOW (PENGELUARAN)`: Belanja PO + Gaji + Listrik + Kas Kecil.
  3. `SURPLUS / DEFISIT KAS BERSIH`: Saldo akhir posisi kas riil.
* **Tabel Rincian Arus Kas (3 Sesi):**
  - **Sesi A: Penerimaan Kas Operasional & Laci Kasir** (Tunai, QRIS, Modal Awal Shift).
  - **Sesi B: Pengeluaran Belanja & Beban** (PO Supplier, Gaji, Listrik/WiFi, Kasbon/Petty Cash).
  - **Sesi C: Rekonsiliasi Audit Shift Kasir** (Total Selisih Kasir Over/Short).
* **Export CSV:** Format ekspor rapi tanpa mencantumkan waste sebagai uang keluar tunai.

---

## 7. Matriks Perbandingan Ringkas

```
+--------------------------+-----------------------+-----------------------+
| Komponen Transaksi       | Masuk ke Laba Rugi?   | Masuk ke Cash Flow?   |
+--------------------------+-----------------------+-----------------------+
| Omzet Penjualan POS      | YA (Net Sales)        | YA (Cash Inflow)      |
| Modal Awal Laci Kasir    | TIDAK                 | YA (Float Inflow)     |
| Resep Menu Terjual       | YA (COGS Pengurang)   | TIDAK                 |
| Waste Log (Bahan Busuk)  | YA (COGS Pengurang)   | TIDAK                 |
| Belanja PO Bahan Mentah  | TIDAK (Aset Gudang)   | YA (Cash Outflow)     |
| Gaji Karyawan            | YA (Labor Cost)       | YA (Cash Outflow)     |
| Listrik, Sewa, WiFi      | YA (Overhead OPEX)    | YA (Cash Outflow)     |
| Kas Kecil Laci Kasir     | YA (Overhead/Misc)    | YA (Cash Outflow)     |
| Selisih Tekor/Lebih Kas  | TIDAK (Kecuali audit) | YA (Cash Variance)    |
+--------------------------+-----------------------+-----------------------+
```

---

> 📝 *Dokumen ini telah disimpan berdampingan dengan `plan-a.md` untuk perbandingan mendalam.*
