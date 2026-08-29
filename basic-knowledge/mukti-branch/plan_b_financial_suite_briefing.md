# 📘 Architectural Briefing: Universal Enterprise F&B Suite (Plan B)

> **Dokumen**: `plan_b_financial_suite_briefing.md`  
> **Folder**: `basic-knowledge/mukti-branch/`  
> **Ditujukan Untuk**: Tim Developer & Reviewer `mukti-branch`  
> **Dari**: Tim Arsitektur & Keuangan `deva-branch`  
> **Tanggal**: 2026-08-28 (28 Agustus 2026)  
> **Status**: MASTER ARCHITECTURE SPECIFICATION (CROSS-BRANCH HANDOVER)  
> **Tujuan**: Penjelasan komprehensif implementasi **Plan B** yang memadukan Laba Rugi Resep Murni, Dedicated Cash Flow, Laci Kasir Pintar, dan Refactor Bahan Mentah untuk skala cafe kecil hingga restoran besar.

---

## 🧭 1. Executive Summary

Plan B dirancang untuk menyelesaikan ketidaksesuaian akuntansi dan batasan operasional di sistem lama:
1. **Pemisahan 100% P&L vs Cash Flow:** Kerugian bahan basi (*Waste Log*) tidak lagi memotong laba atau arus kas, melainkan menjadi *Radar Alert Pengawasan Dapur*.
2. **Belanja PO Supplier:** Ditegaskan sebagai *Aset Persediaan Gudang* yang tidak memotong Laba Rugi, dan mendukung opsi Beli Tunai vs Beli Tempo 14–30 hari (*Hutang AP*).
3. **Laci Kasir Pintar (`cash_drawer_logs`):** Mencatat setiap rupiah uang modal awal, suntikan kas owner (200k), dan pengeluaran kas kecil (es batu 20k, gas) secara real-time.
4. **Estafet Kasir (Handover Float):** Sisa uang di laci (200k/100k) otomatis terdeteksi saat pembukaan shift berikutnya.
5. **Renaming Bahan Mentah:** Tabel di-rename menjadi `raw_stock_materials` & `raw_stock_material_histories`.

---

## 🏛️ 2. Lima Pilar Utama Arsitektur Plan B

```
                                    ARSITEKTUR UNIVERSAL PLAN B
                                                 │
     ┌───────────────────┬───────────────────────┼───────────────────────┬───────────────────┐
     │                   │                       │                       │                   │
     ▼                   ▼                       ▼                       ▼                   ▼
 [ PILAR 1 ]         [ PILAR 2 ]             [ PILAR 3 ]             [ PILAR 4 ]         [ PILAR 5 ]
Smart Cash Drawer   PO Pembelian:           Laba Rugi Resep         Dedicated Arus      Refactor Nama:
& Shift Handover    Tunai vs Tempo AP       Murni (P&L Engine)      Kas & Drill-Down    raw_stock_materials
```

---

### 💵 PILAR 1: Smart Cash Drawer & Shift Handover Kasir
1. **Clock-In Cerdas:**  
   Kasir membuka shift dengan input **Modal Awal Laci (*Starting Cash Float*)** yang fleksibel. Sistem memberikan info sisa uang fisik dari shift penutupan kemarin.
2. **Buku Kas Laci Kasir (`cash_drawer_logs`):**  
   Mencatat setiap rupiah kas masuk (*Paid-In: Suntikan modal owner*) dan kas keluar (*Paid-Out: Beli es batu, galon, gas*).
3. **Clock-Out Cerdas (*Blind Count & Handover*):**  
   Kasir menghitung fisik uang di laci tanpa melihat angka tebakan sistem. Kasir membagi uang fisik:
   - **Disetor ke Brankas / Owner (*Cash Deposit to Safe*)**
   - **Disisakan di Laci untuk Shift Depan (*Retained Cash Float*)**
4. **Struk Z-Report Thermal 80mm:**  
   Mencetak rekapitulasi modal awal, penjualan cash vs non-cash, kas kecil, uang setoran, dan selisih kasir (*Over/Short*).

---

### 🥩 PILAR 2: Fleksibilitas Pembelian PO Supplier (Tunai vs Tempo Hutang AP)
* Pada tabel `purchase_orders`, ditambahkan: `payment_status` (`unpaid`, `partial`, `paid`), `payment_date`, dan `payment_method`.
* **Beli Tunai:** Status `PAID` &rarr; Arus Kas Keluar langsung tercatat hari itu di Cash Flow.
* **Beli Tempo (AP):** Status `UNPAID` &rarr; Kas toko **belum keluar**. Saat pelunasan di kemudian hari &rarr; barulah memotong Arus Kas.
* **Hukum Laba Rugi:** Keduanya **HARAM MEMOTONG LABA RUGI**, karena barang masih berupa Aset Persediaan.

---

### 📊 PILAR 3: Laporan HPP & Laba Rugi Resep Murni (P&L Engine)
Berdiri di route [`/admin/keuangan/hpp-report`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/keuangan/hpp-report/index.blade.php):
1. **Net Revenue (Omzet Murni):**  
   $$\text{Net Sales} = \text{Total Subtotal Transaksi Selesai} - \text{Diskon}$$
2. **Laba Kotor (Gross Profit):**  
   $$\text{Gross Profit} = \text{Net Sales} - \text{Estimasi Modal COGS Resep Terjual}$$
3. **Laba Bersih Toko (Net Profit):**  
   $$\text{Net Profit} = \text{Gross Profit} - \text{Gaji Karyawan} - \text{Biaya Listrik, Sewa, WiFi (OPEX)}$$
4. **Dua Radar Box Pengawasan:**
   - 🚨 **Radar Kerugian Dapur (Waste Log Alert):** Menampilkan rincian bahan basi/tumpah bulan itu, **tanpa memotong rumus laba**.
   - 📦 **Radar Belanja Gudang (Memo PO):** Menampilkan total bahan mentah baru yang masuk dari supplier bulan itu sebagai status aset persediaan.

---

### 🌊 PILAR 4: Dedicated Laporan Arus Kas (Cash Flow) & "Click to Trace"
Berdiri mandiri di route [`/admin/reports/cashflow`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/keuangan/reports/cashflow.blade.php):
1. **Arus Kas Masuk (Inflow):**  
   Penjualan POS Tunai + Penjualan POS Digital (QRIS/EDC) + Modal Awal Laci + Suntikan Modal Owner (*Paid-In*).
2. **Arus Kas Keluar (Outflow):**  
   Pembayaran PO Supplier `PAID` + Gaji Karyawan + Biaya Listrik/OPEX + Kas Kecil Laci (*Paid-Out*).
3. **Posisi Kas Bersih (Net Cash Flow):**  
   $$\text{Net Cash Flow} = (\text{Total Inflow} - \text{Total Outflow}) + \text{Selisih Kasir Tutup Shift (Over/Short)}$$
4. **Fitur "Click to Trace":**  
   Setiap angka di tabel Cash Flow dan P&L dapat diklik untuk membuka modal popup bukti rincian transaksi (daftar PO, struk kas kecil, audit shift).

---

### 📦 PILAR 5: Refactor & Renaming `raw_stock_materials`
* Rename tabel `cogs_raw_materials` &rarr; **`raw_stock_materials`** (PK: `raw_stock_material_id`).
* Rename tabel `cogs_raw_material_histories` &rarr; **`raw_stock_material_histories`**.
* Menyelaraskan seluruh Foreign Key relasi di `purchase_order_items`, `purchase_receiving_items`, `cogs_recipe_items`, dan `cogs_waste_logs`.

---

## 🗄️ 3. Perubahan Skema Database (5 Migration Baru)

Semua migration bersifat **additive & non-breaking**:
1. `2026_08_27_000001_create_cash_drawer_logs_table.php` (Tabel `cash_drawer_logs`)
2. `2026_08_27_000002_add_handover_fields_to_daily_closings_table.php` (`retained_cash_float`, `cash_deposit_to_safe`)
3. `2026_08_27_000003_add_payment_status_to_purchase_orders_table.php` (`payment_status`, `payment_date`, `payment_method`)
4. `2026_08_27_000004_add_margins_to_hpp_financial_reports_table.php` (`gross_margin_percent`, `net_margin_percent`)
5. `2026_08_27_000005_rename_cogs_raw_materials_to_raw_stock_materials_table.php` (Rename table & FKs)

---

## 🔄 4. Keterhubungan dengan Tabel `transactions`

Tabel `transactions` **TIDAK PERLU DIUBAH**, karena sudah lengkap:
* `daily_closing_id`: Mengikat transaksi ke sesi kasir aktif.
* `transaction_subtotal`: Menjadi basis omzet murni (*Net Sales*).
* `transaction_items`: Menghitung modal resep terjual (*COGS*).
* `payment_id`: Menentukan kas masuk tunai (*Cash Inflow*) vs digital (*Bank Inflow*).

---

## 🚀 5. Roadmap Eksekusi 5 Fase

1. **Fase 1:** Database Migrations (5 Migration Baru).
2. **Fase 2:** Eloquent Models & Backend Refactor (`RawStockMaterial`, `CashDrawerLog`, `DailyClosing`, `PurchaseOrder`).
3. **Fase 3:** Smart Cash Drawer & Shift Kasir (Clock-In, [+ Kas Masuk], [- Kas Keluar], Z-Report 80mm).
4. **Fase 4:** Laba Rugi Murni (P&L) & Dedicated Cash Flow + Fitur "Click to Trace".
5. **Fase 5:** Seeder Enriched Data & Multi-Tenant Testing (`KopiSenjaSeeder`, `GeprekGambosSeeder`).

---

> 📌 *Dokumen ini disahkan sebagai acuan teknis resmi bersama antara deva-branch dan mukti-branch.*
