# Dokumen Rencana & Arsitektur Finansial: Universal Enterprise-Ready F&B Suite (Plan B)

> **Dokumen**: `plan-b.md`  
> **Tanggal**: 2026-08-27 (27 Agustus 2026)  
> **Branch**: `deva-branch`  
> **Status**: MASTER ARCHITECTURE BLUEPRINT (ENTERPRISE-READY & ALL SCALES)  
> **Target Pengguna**: Dari Cafe Kecil / Single-Cashier hingga Restoran Skala Besar (*Multi-Station, Multi-Shift, Multi-Branch*)

---

## 1. Visi Arsitektur: "Progressive Scale"

Plan B dirancang dengan prinsip **Progressive Complexity** (seperti standar *Toast POS*, *Shopify*, dan *Oracle MICROS*):
* **Bagi Cafe / Outlet Kecil:** Tampilan tetap super simpel, cepat, dan tidak membebani kasir.
* **Bagi Resto Besar / Chain:** Sistem otomatis menyediakan pengawasan ketat (*Audit Trail*, *Blind Count*, *Handover Float*, *3-Way PO Payment Matching*, dan *Detailed Cash Movements*).

---

## 2. Empat Pilar Utama Arsitektur Plan B

```
                                      UNIVERSAL F&B FINANCIAL SUITE
                                                    |
            +-------------------+-------------------+-------------------+-------------------+
            |                   |                   |                   |                   |
       [ PILAR 1 ]         [ PILAR 2 ]         [ PILAR 3 ]         [ PILAR 4 ]         [ PILAR 5 ]
     Smart Cash Drawer    PO Pembelian       Laba Rugi Resep     Dedicated Arus      Audit Trail &
      & Shift Handover     & Hutang AP        Murni (P&L)        Kas (Cash Flow)     Drill-Down Matrix
```

---

### 🏛️ PILAR 1: Smart Cash Drawer & Shift Handover (Kasir & Laci Kas)

Mengakomodasi kasus pergantian shift kasir, sisa kas kembalian di laci (*Float*), dan suntikan modal dari owner.

#### A. Alur Operasional Laci Kasir:
1. **Clock-In Pagi / Buka Shift:**
   - Sistem mendeteksi sisa uang kas dari penutupan shift kemarin malam (misal: `Rp 100.000`).
   - Jika owner menambah uang kembalian (misal: `Rp 200.000`), kasir mencatat sebagai **Suntikan Kas Owner (*Owner Paid-In*)**.
   - Kasir memulai shift dengan total Modal Awal: `Rp 300.000` (`100k sisa kemarin + 200k suntikan owner`).
2. **Operasional Berjalan (Kas Kecil & Paid-Out):**
   - Kasir memiliki 2 tombol cepat di layar POS:
     - 🟢 **`[+ Catat Kas Masuk / Paid-In]`** &rarr; Tambah uang kas (misal: top-up kembalian).
     - 🔴 **`[- Catat Kas Keluar / Paid-Out]`** &rarr; Ambil uang laci (misal: beli es batu Rp 20rb, beli galon Rp 25rb, gas elpiji).
   - Setiap transaksi tercatat otomatis ke tabel `cash_drawer_logs` lengkap dengan jam, kasir, nominal, dan alasan.
3. **Clock-Out / Tutup Shift (*Blind Count & Handover*):**
   - Kasir menghitung fisik seluruh uang di laci tanpa melihat angka tebakan sistem (*Blind Cash Count*).
   - Kasir membagi uang fisik menjadi 2:
     - **Disetor ke Brankas / Owner (*Cash Drop*):** Misal `Rp 1.300.000`.
     - **Disisakan di Laci untuk Shift Depan (*Retained Float*):** Misal `Rp 200.000`.
   - Sistem menghitung selisih kas (*Over/Short*) dan mencetak struk **Z-Report (Thermal 80mm)**.
4. **Shift Berikutnya Buka:**
   - Kasir Shift 2 otomatis menerima `starting_cash` sebesar **Rp 200.000** dari sisa kasir Shift 1.

---

### 🏛️ PILAR 2: Pembelian PO Fleksibel (Bayar Langsung Tunai vs Bayar Tempo Hutang AP)

Mengakomodasi cafe kecil yang beli bahan langsung bayar lunas, DAN resto besar yang beli bahan dengan sistem tempo (TOP 14/30 hari).

#### A. Skema Kolom Pembayaran PO:
Pada tabel `purchase_orders`, ditambahkan kolom:
* `payment_status` (`unpaid`, `partial`, `paid`)
* `payment_date` (`timestamp nullable`)
* `payment_method` (`cash`, `bank_transfer`, `tempo`)
* `due_date` (`date nullable`)

#### B. Logika Pembayaran:
* **Skenario Bayar Tunai Langsung (Cafe Kecil):**
  - Saat barang PO diterima (*Receiving*), status pembayaran otomatis `PAID` &rarr; Arus Kas Keluar langsung tercatat hari itu di Cash Flow.
* **Skenario Beli Tempo 14/30 Hari (Resto Besar):**
  - Saat barang PO diterima, status `UNPAID` (Hutang Dagang). Kas toko **TIDAK KELUAR**.
  - Saat tim Finance melunasi faktur supplier 2 minggu kemudian &rarr; Barulah Arus Kas Keluar dipotong di Cash Flow.
* **Hukum Akuntansi Laba Rugi:**
  - Keduanya **HARAM MEMOTONG LABA RUGI**, karena bahan mentah yang masuk masih menjadi Aset Persediaan Gudang.

---

### 🏛️ PILAR 3: Laporan HPP & Laba Rugi Resep Murni (P&L)

Halaman ini berdiri di route [`/admin/keuangan/hpp-report`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/keuangan/hpp-report/index.blade.php) dan fokus pada **Kinerja Profitabilitas Usaha**.

#### A. Formula Laba Rugi:
1. **Net Revenue (Omzet Murni):**
   $$\text{Net Sales} = \text{Total Subtotal Pesanan Berhasil} - \text{Diskon}$$
   *(Pajak PB1 10% dan Service Charge 5% dipisahkan karena titipan pemda & pool karyawan).*
2. **Laba Kotor (Gross Profit):**
   $$\text{Gross Profit} = \text{Net Sales} - \text{Estimasi Modal COGS Resep Terjual}$$
   $$\text{Gross Margin (\%)} = \left(\frac{\text{Gross Profit}}{\text{Net Sales}}\right) \times 100\%$$
3. **Laba Bersih (Net Profit):**
   $$\text{Net Profit} = \text{Gross Profit} - \text{Biaya Gaji (Labor)} - \text{Biaya Operasional (Overhead/OPEX)}$$
   $$\text{Net Margin (\%)} = \left(\frac{\text{Net Profit}}{\text{Net Sales}}\right) \times 100\%$$

#### B. Dua Radar Box Pengawasan:
* 🚨 **Radar Kerugian Dapur (Waste Log Alert):**
  Menampilkan total kerugian bahan basi/tumpah bulan itu beserta rincian bahannya, **tanpa memotong rumus laba rugi**.
* 📦 **Radar Belanja Gudang (Memo PO):**
  Menampilkan total bahan mentah baru yang masuk dari supplier bulan itu sebagai status aset persediaan.

---

### 🏛️ PILAR 4: Dedicated Laporan Arus Kas (Cash Flow Statement)

Halaman ini berdiri sendiri di route [`/admin/reports/cashflow`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/keuangan/reports/cashflow.blade.php) dan fokus pada **Ketersediaan Uang Tunai & Saldo Rekening Riil**.

#### A. Struktur Aliran Kas:
1. **Arus Kas Masuk (*Inflow*):**
   - Penjualan Kasir Tunai (*Cash Sales*)
   - Penjualan Kasir Non-Tunai (*QRIS, EDC, Transfer Bank*)
   - Modal Awal Buka Shift (*Opening Cash Float*)
   - Suntikan Modal / Kas Masuk Tambahan dari Owner (*Owner Paid-In*)
2. **Arus Kas Keluar (*Outflow*):**
   - Realisasi Pembayaran Belanja PO Supplier yang sudah `PAID`
   - Pembayaran Biaya Gaji Karyawan
   - Pembayaran Biaya Listrik, Sewa, WiFi, & OPEX
   - Pengeluaran Kas Kecil Laci Kasir (*Petty Cash Paid-Out*)
3. **Rekonsiliasi Kas Bersih (*Net Cash Flow*):**
   $$\text{Net Cash Flow} = (\text{Total Inflow} - \text{Total Outflow}) + \text{Selisih Kasir Tutup Shift (Over/Short)}$$

*(Waste Log dan Estimasi COGS **100% dikeluarkan** dari Cash Flow karena bukan transaksi uang keluar).*

---

### 🏛️ PILAR 5: "Click to Trace" (Audit Trail & Drill-Down Matrix)

Setiap angka di Laporan Arus Kas dan Laba Rugi memiliki tombol **`[🔍 Drill-Down / Rincian]`**:
* Klik `Belanja PO` &rarr; Muncul daftar No. PO, Supplier, Tanggal Bayar, dan Nominal.
* Klik `Suntikan Modal Owner` &rarr; Muncul daftar jam transfer, kasir yang menerima, dan shift-nya.
* Klik `COGS Menu Terjual` &rarr; Muncul daftar menu apa saja yang laku dan modal per porsinya.
* Klik `Kas Kecil Laci` &rarr; Muncul tiket struk pengeluaran (beli es batu, galon, gas).
* Klik `Selisih Kasir` &rarr; Muncul riwayat audit shift closing (siapa kasir yang lebih/tekor).

---

## 3. Rencana Perubahan Database & Migrations

### A. Tabel Baru: `cash_drawer_logs`
File: `2026_08_27_000001_create_cash_drawer_logs_table.php`
```php
Schema::create('cash_drawer_logs', function (Blueprint $table) {
    $table->id();
    $table->string('outlet_id')->nullable();
    $table->unsignedBigInteger('daily_closing_id')->nullable();
    $table->unsignedBigInteger('cashier_id')->nullable();
    $table->enum('type', ['in', 'out']); // 'in' = Kas Masuk, 'out' = Kas Keluar
    $table->string('category', 50)->default('general'); // 'owner_topup', 'petty_cash', 'cash_drop', 'other'
    $table->decimal('amount', 15, 2);
    $table->string('reason', 255);
    $table->string('created_by', 50)->nullable();
    $table->timestamps();
});
```

### B. Update Tabel `daily_closings`
File: `2026_08_27_000002_add_handover_fields_to_daily_closings_table.php`
- `retained_cash_float` (`decimal(15,2)` default 0): Uang disisakan di laci untuk shift berikutnya.
- `cash_deposit_to_safe` (`decimal(15,2)` default 0): Uang disetor ke brankas/owner.

### C. Update Tabel `purchase_orders`
File: `2026_08_27_000003_add_payment_status_to_purchase_orders_table.php`
- `payment_status` (`enum: unpaid, partial, paid` default 'paid')
- `payment_date` (`timestamp nullable`)
- `payment_method` (`string nullable`)

### D. Update Tabel `hpp_financial_reports`
File: `2026_08_27_000004_add_margins_to_hpp_financial_reports_table.php`
- `gross_margin_percent` (`decimal(5,2)`)
- `net_margin_percent` (`decimal(5,2)`)

### E. Migration Renaming & Refactor: `cogs_raw_materials` -> `raw_stock_materials`
File: `2026_08_27_000005_rename_cogs_raw_materials_to_raw_stock_materials_table.php`
- Rename table `cogs_raw_materials` -> `raw_stock_materials`
- Rename table `cogs_raw_material_histories` -> `raw_stock_material_histories`
- Rename foreign keys:
  - `purchase_order_items.cogs_raw_material_id` -> `raw_stock_material_id`
  - `purchase_receiving_items.cogs_raw_material_id` -> `raw_stock_material_id`
  - `cogs_recipe_items.cogs_raw_material_id` -> `raw_stock_material_id`
  - `cogs_waste_logs.cogs_raw_material_id` -> `raw_stock_material_id`
  - `cogs_waste_histories.cogs_raw_material_id` -> `raw_stock_material_id`

---

## 4. Matriks Lengkap Pengalokasian Transaksi

```
+-----------------------------------+-----------------------+-----------------------+-----------------------+
| Jenis Transaksi                   | Laba Rugi (P&L)       | Arus Kas (Cash Flow)  | Audit Trail / Log     |
+-----------------------------------+-----------------------+-----------------------+-----------------------+
| Omzet Penjualan POS (Kasir)       | YA (Net Revenue)      | YA (Cash Inflow)      | Tabel Transactions    |
| Modal Awal Kembalian Shift (Float)| TIDAK                 | YA (Starting Float)   | Tabel Daily Closings  |
| Suntikan Modal dari Owner (200k)  | TIDAK (Bukan Laba)    | YA (Owner Paid-In)    | Tabel Cash Drawer Logs|
| COGS Resep Menu Terjual           | YA (Pengurang Laba)   | TIDAK (Bukan Uang Out)| BOM Resep & Orders    |
| Waste Log (Bahan Busuk/Tumpah)    | TIDAK (Hanya Alert)   | TIDAK (Bukan Uang Out)| Tabel Cogs Waste Logs |
| Belanja PO Bahan (Status: PAID)   | TIDAK (Aset Gudang)   | YA (Cash Outflow)     | Tabel Purchase Orders |
| Belanja PO Bahan (Status: UNPAID) | TIDAK (Aset Gudang)   | TIDAK (Hutang Belum)  | Tabel Purchase Orders |
| Gaji Karyawan                     | YA (Labor Cost)       | YA (Cash Outflow)     | HPP Report & Cash Flow|
| Listrik, Sewa, WiFi (Overhead)    | YA (Overhead OPEX)    | YA (Cash Outflow)     | HPP Report & Cash Flow|
| Kas Kecil Laci (Beli Es Batu 20k) | YA (Masuk Overhead)   | YA (Cash Outflow)     | Tabel Cash Drawer Logs|
| Selisih Tekor/Lebih Kasir Closing | TIDAK                 | YA (Cash Variance)    | Audit Struk Z-Report  |
+-----------------------------------+-----------------------+-----------------------+-----------------------+
```

---

## 5. Rencana Eksekusi Bertahap

| Tahap | Fokus Pekerjaan | Target File Terkait |
| :--- | :--- | :--- |
| **Tahap 1** | Migrations Database (5 Migration Lengkap: Cash Drawer, Handover, PO Status, Margins, Rename Raw Stock Materials) | `database/migrations/client/2026_08_27_000001_*.php` |
| **Tahap 2** | Update Model Eloquent (`DailyClosing`, `CashDrawerLog`, `PurchaseOrder`, `HppFinancialReport`, `RawStockMaterial`, `RawStockMaterialHistory`) | `app/Models/Admin/**` |
| **Tahap 3** | Update Modul Bahan Mentah & Resep (`RawStockMaterialController`, `CogsRecipeController`, `CogsWasteLogController`) | Controller & Views Bahan Mentah |
| **Tahap 4** | Logika Kasir & Shift (`ShiftOperationalController` + Modal Kas Masuk/Keluar) | `ShiftOperationalController.php`, `shift-operational/index.blade.php` |
| **Tahap 5** | Logika Laba Rugi Murni (`HppReportController` + Radar Box) | `HppReportController.php`, `hpp-report/index.blade.php` |
| **Tahap 6** | Logika Dedicated Cash Flow (`CashFlowReportController` + Drill-Down View) | `CashFlowReportController.php`, `reports/cashflow.blade.php` |
| **Tahap 7** | Verifikasi Matriks & Pencatatan Log Kerja | `basic-knowledge/log_code.md` & `basic-knowledge/deva-branch/todo.md` |

---

> 🏆 **Plan B adalah cetak biru pamungkas: fleksibel untuk cafe kecil, kuat dan anti-bocor untuk resto besar skala enterprise.**
