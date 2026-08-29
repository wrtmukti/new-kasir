# 🎯 MASTER MILESTONE EKSEKUSI TEKNIS: UNIVERSAL ENTERPRISE F&B SUITE (PLAN B)

> **Dokumen**: `milestone.md`  
> **Tanggal**: 2026-08-28 (28 Agustus 2026)  
> **Branch**: `deva-branch`  
> **Status**: APPROVED & READY FOR STEP-BY-STEP EXECUTION  
> **Fokus Utama**: Implementasi Lengkap 5 Pilar Finansial & Operasional (Database Migrations, Refactor Model & Controller, Smart Drawer Kasir, Laba Rugi Murni, Dedicated Cash Flow & Drill-Down, Seeder Multi-Tenant).

---

## 🗺️ Peta Jalan 5 Fase Eksekusi

```
+-------------------------------------------------------------------------------------------------------------------+
| MASTER ROADMAP PLAN B                                                                                             |
+-------------------------------------------------------------------------------------------------------------------+
| 📌 FASE 1: Database Schema & Migrations (5 Migration Files Baru)                                                  |
|    ├── 1.1 Migration `cash_drawer_logs` (Riwayat Kas Masuk & Kas Keluar Laci Kasir)                               |
|    ├── 1.2 Migration Update `daily_closings` (Handover Float & Cash Deposit to Safe)                              |
|    ├── 1.3 Migration Update `purchase_orders` (Payment Status: Paid vs Unpaid Tempo)                              |
|    ├── 1.4 Migration Update `hpp_financial_reports` (Gross Margin % & Net Margin %)                               |
|    └── 1.5 Migration Rename `cogs_raw_materials` -> `raw_stock_materials` & Foreign Keys                          |
|                                                                                                                   |
| 📌 FASE 2: Eloquent Models & Backend Logic Refactoring                                                            |
|    ├── 2.1 Model `RawStockMaterial.php` & `RawStockMaterialHistory.php` (Kartu Stok Gudang)                       |
|    ├── 2.2 Model `CashDrawerLog.php` (Pencatatan Kas Laci)                                                        |
|    ├── 2.3 Refactor Model Terkait (`DailyClosing`, `PurchaseOrder`, `CogsRecipeItem`, `CogsWasteLog`)             |
|    └── 2.4 Refactor Controller Bahan Mentah: `RawStockMaterialController.php`                                    |
|                                                                                                                   |
| 📌 FASE 3: Smart Cash Drawer & Shift Handover Kasir (POS Interface)                                               |
|    ├── 3.1 Form Clock-In Cerdas (Input Modal Bebas + Deteksi Sisa Kas Semalam)                                    |
|    ├── 3.2 Modal Cepat POS: [+ Catat Kas Masuk / Top-Up] & [- Catat Kas Keluar / Petty Cash]                     |
|    ├── 3.3 Form Clock-Out Cerdas (Blind Cash Count, Setor Brankas vs Sisa Laci Shift Depan)                       |
|    ├── 3.4 Struk Z-Report Thermal 80mm (Rincian Modal, Cash/Digital, Kas Kecil, Selisih Kasir)                    |
|    └── 3.5 Refactor `ShiftOperationalController.php` & View `shift-operational/index.blade.php`                  |
|                                                                                                                   |
| 📌 FASE 4: Laba Rugi Resep Murni (P&L) & Dedicated Arus Kas (Cash Flow)                                           |
|    ├── 4.1 Refactor `HppReportController.php` (Net Sales - COGS Resep - Gaji - OPEX)                             |
|    ├── 4.2 Pasang 2 Radar Box di `hpp-report/index.blade.php` (Radar Kerugian Dapur & Memo Belanja PO)           |
|    ├── 4.3 Refactor `CashFlowReportController.php` (Inflow, Outflow PO Paid, Gaji, Kas Kecil, No Waste)           |
|    ├── 4.4 Fitur "Click to Trace" Drill-Down Modal Popup (Rincian PO, Kas Kecil, Audit Shift)                     |
|    └── 4.5 Update View `reports/cashflow.blade.php` & Ekspor CSV Presisi                                          |
|                                                                                                                   |
| 📌 FASE 5: Seeder Enriched Data & Multi-Tenant Verification                                                       |
|    ├── 5.1 Buat Seeder `RawStockMaterialSeeder.php` (Master Bahan Mentah & Kartu Stok)                            |
|    ├── 5.2 Update `KopiSenjaSeeder.php` & `GeprekGambosSeeder.php` (PO Tunai/Tempo, Shift, Laci Kasir)            |
|    ├── 5.3 Eksekusi `php artisan migrate` & Test Database Multi-Tenant                                           |
|    └── 5.4 Uji Coba End-to-End Simulation Kasir -> Dapur -> Gudang -> Laba Rugi -> Cash Flow                     |
+-------------------------------------------------------------------------------------------------------------------+
```

---

## 📋 RINCIAN DETAIL PER FASE

---

### 📌 FASE 1: Database Schema & Migrations (5 Files)

#### 1.1 Migration `cash_drawer_logs`
* **File Target**: `database/migrations/client/2026_08_27_000001_create_cash_drawer_logs_table.php`
* **Struktur Kolom**:
  - `id` (bigIncrements)
  - `outlet_id` (string nullable)
  - `daily_closing_id` (unsignedBigInteger indexed nullable)
  - `cashier_id` (unsignedBigInteger nullable)
  - `type` (`enum: in, out`)
  - `category` (`string 50`: `owner_topup`, `petty_cash`, `cash_drop`, `cash_correction`, `other`)
  - `amount` (`decimal 15,2`)
  - `reason` (`string 255`)
  - `created_by` (`string 50 nullable`)
  - `timestamps()`

#### 1.2 Migration Update `daily_closings`
* **File Target**: `database/migrations/client/2026_08_27_000002_add_handover_fields_to_daily_closings_table.php`
* **Kolom Baru**:
  - `retained_cash_float` (`decimal 15,2` default 0) &rarr; Uang kas yang disisakan di laci untuk shift berikutnya.
  - `cash_deposit_to_safe` (`decimal 15,2` default 0) &rarr; Uang kas yang disetor ke brankas/owner.
  - `cashier_note` (`text nullable`) &rarr; Catatan serah terima kasir.

#### 1.3 Migration Update `purchase_orders`
* **File Target**: `database/migrations/client/2026_08_27_000003_add_payment_status_to_purchase_orders_table.php`
* **Kolom Baru**:
  - `payment_status` (`enum: unpaid, partial, paid` default 'paid')
  - `payment_date` (`timestamp nullable`)
  - `payment_method` (`string 50 nullable`: `cash`, `bank_transfer`, `tempo`)
  - `due_date` (`date nullable`)

#### 1.4 Migration Update `hpp_financial_reports`
* **File Target**: `database/migrations/client/2026_08_27_000004_add_margins_to_hpp_financial_reports_table.php`
* **Kolom Baru**:
  - `gross_margin_percent` (`decimal 5,2` default 0)
  - `net_margin_percent` (`decimal 5,2` default 0)

#### 1.5 Migration Rename `cogs_raw_materials` -> `raw_stock_materials`
* **File Target**: `database/migrations/client/2026_08_27_000005_rename_cogs_raw_materials_to_raw_stock_materials_table.php`
* **Aksi**:
  - `Schema::rename('cogs_raw_materials', 'raw_stock_materials');`
  - `Schema::rename('cogs_raw_material_histories', 'raw_stock_material_histories');`
  - Rename kolom FK `cogs_raw_material_id` &rarr; `raw_stock_material_id` di:
    * `purchase_order_items`
    * `purchase_receiving_items`
    * `cogs_recipe_items`
    * `cogs_waste_logs`
    * `cogs_waste_histories`

---

### 📌 FASE 2: Eloquent Models & Backend Logic Refactoring

1. **Model `RawStockMaterial.php`:**
   - Class name: `App\Models\Admin\Keuangan\RawStockMaterial`
   - Table: `raw_stock_materials`, Primary Key: `raw_stock_material_id`
   - Relasi: `hasMany(RawStockMaterialHistory::class)`, `hasMany(CogsRecipeItem::class)`, `hasMany(CogsWasteLog::class)`
2. **Model `RawStockMaterialHistory.php`:**
   - Table: `raw_stock_material_histories`
   - Handle mutasi kartu stok: `purchase_receiving`, `recipe_consumption`, `waste_spoilage`, `adjustment`.
3. **Model `CashDrawerLog.php`:**
   - Table: `cash_drawer_logs`
   - Relasi: `belongsTo(DailyClosing::class)`, `belongsTo(Outlet::class)`
4. **Penyelarasan Model:**
   - Update `DailyClosing.php` (relasi `cashDrawerLogs()`, accessor `totalPaidIn`, `totalPaidOut`).
   - Update `PurchaseOrder.php` (scope `paid()`, scope `unpaid()`).
5. **Controller `RawStockMaterialController.php`:**
   - Rename dari `CogsRawMaterialController.php`.
   - Update request validation & response message.

---

### 📌 FASE 3: Smart Cash Drawer & Shift Kasir (POS Interface)

1. **Endpoint & Logika Clock-In (`ShiftOperationalController::openShift`):**
   - Mendeteksi sisa fisik kas laci penutupan shift sebelumnya.
   - Input modal awal fleksibel dari kasir.
2. **Endpoint Catat Kas Masuk / Keluar Laci (`ShiftOperationalController::logCashMovement`):**
   - Route: `POST /admin/shift-operational/cash-drawer-log`
   - Validasi: `daily_closing_id`, `type` (`in`/`out`), `amount`, `category`, `reason`.
   - Insert ke `cash_drawer_logs`.
3. **Endpoint & Logika Clock-Out (`ShiftOperationalController::closeShift`):**
   - Menghitung expected cash: `starting_cash + cash_sales + total_paid_in - total_paid_out`.
   - Validasi input uang fisik kasir (`actual_cash`).
   - Simpan `retained_cash_float` dan `cash_deposit_to_safe`.
   - Hitung selisih: `cash_variance = actual_cash - expected_cash`.
4. **Desain Struk Z-Report Thermal 80mm (`ShiftOperationalController::printZReport`):**
   - Mencetak: Modal Awal, Penjualan Tunai, QRIS/EDC, Kas Masuk (Top-up), Kas Keluar (Petty Cash), Uang Disetor ke Brankas, Sisa di Laci, dan Selisih.
5. **Pembaruan View `shift-operational/index.blade.php`:**
   - Modal Clock-In cerdas.
   - Modal pop-up `[+ Kas Masuk]` dan `[- Kas Keluar]`.
   - Modal Clock-Out dengan pembagian setoran brankas vs sisa laci.

---

### 📌 FASE 4: Laba Rugi Resep Murni (P&L) & Dedicated Arus Kas (Cash Flow)

1. **Refaktor `HppReportController.php` (Laba Rugi):**
   - **Net Sales:** $\sum(\text{Transaction Subtotal})$ pesanan selesai tanpa PB1 dan tanpa Service Charge.
   - **COGS Resep:** $\sum(\text{Porsi Menu Terjual} \times \text{Modal Resep BOM})$.
   - **Laba Kotor:** $\text{Net Sales} - \text{COGS Resep}$.
   - **Laba Bersih:** $\text{Laba Kotor} - \text{Gaji} - \text{Listrik/Sewa/OPEX}$.
   - **Radar Box Alert Waste Log:** Total bahan terbuang ditampilkan sebagai alert evaluasi tanpa memotong laba.
   - **Memo Box Gudang PO:** Total bahan mentah masuk dari PO ditampilkan sebagai aset gudang.
2. **Pembaruan View `hpp-report/index.blade.php`:**
   - 4 Bento KPI Cards: Net Revenue, COGS Resep, Net Profit, Radar Waste Alert.
   - Waterfall Kalkulasi Laba Rugi yang bersih.
   - 2 Radar Box di bagian bawah.
3. **Refaktor `CashFlowReportController.php` (Arus Kas):**
   - **Kas Masuk (Inflow):** Penjualan Tunai + Penjualan Digital + Modal Awal Kasir + Suntikan Owner (`cash_drawer_logs` type `in`).
   - **Kas Keluar (Outflow):** Belanja PO (`payment_status = 'paid'`) + Gaji Dibayar + Listrik Dibayar + Kas Keluar Laci (`cash_drawer_logs` type `out`).
   - **Net Cash Flow:** $(\text{Inflow} - \text{Outflow}) + \text{Selisih Kasir Tutup Shift}$.
   - **Eliminasi Waste Log:** Waste Log 100% dihapus dari pengurang arus kas.
4. **Fitur "Click to Trace" (Audit Trail):**
   - Modal pop-up rincian PO yang dibayar.
   - Modal pop-up rincian kas kecil laci kasir.
   - Modal pop-up rincian audit shift kasir.
5. **Pembaruan View `reports/cashflow.blade.php`:**
   - 3 Bento KPI Cards (Total Inflow, Total Outflow, Net Cash Flow).
   - Tabel Aliran Kas interaktif dengan tombol `[🔍 Lihat Bukti Transaksi]`.
   - Update handler ekspor CSV.

---

### 📌 FASE 5: Seeder Enriched Data & Multi-Tenant Testing

1. **Seeder `RawStockMaterialSeeder.php`:**
   - Mengisi data bahan mentah realistis (Daging Sapi, Ayam, Beras, Cabai, Telur, Susu) lengkap dengan stok dan riwayat kartu stok awal.
2. **Update Seeder Tenant (`KopiSenjaSeeder` & `GeprekGambosSeeder`):**
   - Mengisi resep menu (`cogs_recipes`).
   - Mengisi transaksi PO supplier (Campuran PO Status `PAID` dan `UNPAID` tempo).
   - Mengisi sesi shift kasir dengan transaksi POS, suntikan modal owner 200k, kas kecil es batu 20k, dan Z-Report closing.
3. **Verifikasi Teknis:**
   - Menjalankan migrasi `php artisan migrate`.
   - Menjalankan seeder `php artisan db:seed`.
   - Pengujian end-to-end seluruh alur kasir, laba rugi, dan arus kas tanpa error.

---

> 🚀 **Milestone ini siap dieksekusi langkah demi langkah mulai dari Fase 1.**
