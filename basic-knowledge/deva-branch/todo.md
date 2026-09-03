> **Branch Context**: `deva-branch`
> **Project Scope**: POS SaaS F&B MVP (Decoupled COGS + HPP Report + Master Tax & Service Charge + Shift Closing Kasir + Dedicated Cash Flow Plan B + Multi-Outlet Owner Executive Hub).
> **Dokumen Arsitektur Alur Kas & Shift Closing (JSON)**: [`cash_flow_and_clock_in_out_architecture.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/cash_flow_and_clock_in_out_architecture.md)
> **Dokumen Strategi Tiering SaaS & Benchmark Pasar**: [`../saas_tiering_strategy_and_market_benchmark.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/saas_tiering_strategy_and_market_benchmark.md)
> **Dokumen Milestone Phase 3 (Owner Portal)**: [`2026-08-29/milestone.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-08-29/milestone.md)
> **Dokumen Task Tracker Phase 3**: [`2026-08-29/todo.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-08-29/todo.md)
> **Dokumen Milestone Plan B**: [`2026-08-28/milestone.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-08-28/milestone.md)
> **Status**: **Phase 1 & Phase 2 COMPLETED 100% | Phase 3 (Owner Multi-Branch Consolidated Hub) APPROVED & READY**

---

## 📋 Checklist Task & Progress `deva-branch`:

### ✅ PHASE 1 — Decoupled COGS, HPP Report & Seeder (COMPLETED 100%)
- [x] **1.1** Verification active branch `deva-branch` & structure namespace
- [x] **1.2** Database Migrations (Tabel `cogs_raw_materials`, `cogs_recipes`, `cogs_waste_logs`, `hpp_financial_reports`)
- [x] **1.3** Decouple Purchase Order dari stocks & hubungkan ke `cogs_raw_materials`
- [x] **1.4** Modul Bahan Mentah, PO Receiving, COGS Resep, Waste Log, & Stock Opname
- [x] **1.5** Laporan HPP, Laba Rugi, & Modal Resep per Menu (`HppReportController`)
- [x] **1.6** Grafik Analitik Penjualan & Jam Sibuk (`MenuAnalyticsController`)
- [x] **1.7** Seeder 1 Bulan (26 Hari, 294 Pesanan) & Running `php artisan migrate:fresh --seed` (SUKSES 100%)

---

### 🚀 PHASE 2 — Master Pajak (PB1), Service Charge, Shift Closing, & Report Dashboard + Excel Export (COMPLETED 100%)

#### 📌 M1 — Master Pajak (PB1) & Service Charge (Tahap 1) (COMPLETED 100%)
- [x] **2.1.1** Migration tabel `taxes` & `service_charges` (master setting per company/outlet)
- [x] **2.1.2** Snapshot kolom di `orders`: `tax_percent`, `tax_amount`, `tax_type`, `service_charge_percent`, `service_charge_amount`
- [x] **2.1.3** Model `Tax` & `ServiceCharge` + Seeder `TaxSeeder.php` & `ServiceChargeSeeder.php` (PBJT 10% & Service 5%)
- [x] **2.1.4** FormRequest `TaxRequest` & `ServiceChargeRequest` dengan validasi Bahasa Indonesia
- [x] **2.1.5** Controller `App\Http\Controllers\Admin\Keuangan\TaxController` (CRUD Master Setting & AJAX Update)
- [x] **2.1.6** UI View Setting Pajak & Service Charge (`resources/views/admin/keuangan/setting-tax/index.blade.php`)
- [x] **2.1.7** Update kalkulasi Checkout/Order (Subtotal -> Diskon -> Service Charge -> Tax PB1 -> Grand Total) & Testing Matriks Success (100% Presisi)


#### 🔐 M2 — Modul Shift Closing, Cut-Off & Cash Balancing Kasir (Tahap 2) (COMPLETED 100%)
- [x] **2.2.1** Migration tabel `daily_closings` (Buka/tutup shift kasir, cash expected vs actual, variance over/short)
- [x] **2.2.2** Foreign Key Binding `daily_closing_id` (`nullable`) pada tabel `transactions` & `orders`
- [x] **2.2.3** Model `DailyClosing` + Relasi Eloquent di `Order` & `Transaction`
- [x] **2.2.4 (M2.1)** Migration `create_shift_settings_table.php` & `create_shifts_table.php` (Pengaturan Cut-Off & Master Shift)
- [x] **2.2.5 (M2.1)** Model `ShiftSetting` & `Shift` + Seeder `ShiftSeeder.php` (Cut-Off 03:00 AM & Shift 1/2)
- [x] **2.2.6 (M2.1)** Controller `App\Http\Controllers\Admin\Keuangan\ShiftSettingController.php` (CRUD Cutoff & Master Shift)
- [x] **2.2.7 (M2.1)** UI View Setting Shift & Cut-off (`resources/views/admin/keuangan/setting-shift/index.blade.php`) + Menu Sidebar `Master Shift & Cut-off`
- [x] **2.2.8** Controller `App\Http\Controllers\Admin\Keuangan\ShiftOperationalController.php` (Clock-In, Clock-Out, Z-Report, Audit trail)
- [x] **2.2.9 (M2.2)** Dedicated UI View Buka / Tutup Shift Kasir (`resources/views/admin/keuangan/shift-operational/index.blade.php`) + Menu Sidebar `Buka / Tutup Shift (Clock-In)`
- [x] **2.2.10 (M2.2)** Struk Rekap Z-Report Thermal 80mm Print View (`resources/views/admin/keuangan/shift-operational/z-report.blade.php`)
- [x] **2.2.11** Perbaikan Alur Simpan & Redirect Order: Mengarahkan redirect `store()` ke `/admin/order/list` + Memperbaiki aturan validasi ID (`product_id`, `table_id`, `customer_id`, `bundle_id`) untuk menerima integer
- [x] **2.2.12** Perbaikan Presisi Pengurutan Order & Transaksi: Mengubah pengurutan dari `latest()` (`created_at DESC`) menjadi `orderBy('order_id', 'desc')` & `orderBy('transaction_id', 'desc')` agar pesanan & transaksi terbaru SELALU berada paling atas No. 1
- [x] **2.2.13** Penambahan Pop-Up Modal Error & Banner Alert Notifikasi Transparan saat simpan order gagal
- [x] **2.2.14** Seeder `DailyClosingSeeder.php` & Update `OrderSeeder.php` & `TransactionSeeder.php` (Seed 26 hari histori shift & bind 273 order/trx)


#### 💳 M3 — Report Dashboard Hub & 6 Detail Dedicated Laporan + Export Excel (Tahap 3) (COMPLETED 100%)
- [x] **2.3.1** `ReportDashboardController` & View Pusat Dashboard Laporan (`resources/views/admin/keuangan/reports/dashboard.blade.php`) dengan 6 Kartu Navigasi Clickable
- [x] **2.3.2** `SalesReportController` & View Detail Laporan Penjualan + Payment Breakdown + Fitur Export Excel (`.csv`) (`/admin/reports/sales`)
- [x] **2.3.3** `ProductReportController` & View Detail Laporan Menu Terlaris (PMIX & Qty Terjual) + Fitur Export Excel (`.csv`) (`/admin/reports/products`)
- [x] **2.3.4** `CashFlowReportController` & View Detail Laporan Pemasukan vs Pengeluaran (Arus Kas) + Fitur Export Excel (`.csv`) (`/admin/reports/cashflow`)
- [x] **2.3.5** `TaxServiceReportController` & View Detail Laporan Rekap Pajak PB1 & Service Charge + Fitur Export Excel (`.csv`) (`/admin/reports/tax-service`)
- [x] **2.3.6** `InventoryReportController` & View Detail Laporan Stok Bahan Mentah, PO & Waste Log + Fitur Export Excel (`.csv`) (`/admin/reports/inventory`)
- [x] **2.3.7** `ShiftClosingReportController` & View Detail Audit Shift Closing Kasir + Fitur Export Excel (`.csv`) (`/admin/reports/shifts`)
- [x] **2.3.8** Pendaftaran 13 Route Laporan pada `routes/web.php` & Verifikasi Caching Blade Template (100% Success)


---

## ⚠️ Aturan Kerja Khusus Branch `deva-branch`:
1. **Branch Lock**: Jangan pernah commit atau ubah langsung di branch `main`/`master` — seluruh pengerjaan wajib di `deva-branch`.
2. **Log Code Mandatory**: Setiap selesai buat/update file, langsung catat di `basic-knowledge/log_code.md`.
3. **Konfirmasi TODO**: Sebelum mulai pengerjaan sesi, selalu cek file ini (`basic-knowledge/deva-branch/todo.md`) dan berikan kabar branch aktif ke user.
