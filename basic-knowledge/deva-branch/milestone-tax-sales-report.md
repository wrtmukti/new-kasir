# Milestone Master Utuh — Pajak (PB1), Service Charge, Shift Closing (Cut-off), & Report Dashboard (Excel Export)

> **Branch Context**: `deva-branch`
> **Status Overall**: **Phase 1 (COGS & HPP) COMPLETED 100% | Phase 2 M1 (Tax & Service Setting) COMPLETED 100% | M2 (Shift Closing) IN PROGRESS**

---

## 📌 MILESTONE 1 — Master Pajak (PB1) & Service Charge (✅ COMPLETED 100%)

### 1.1 Database Migration & Snapshot Order (✅ COMPLETED)
* **`[DONE]` Migration `create_taxes_table.php`**: `taxes` (`tax_id`, `company_id`, `tax_name`, `rate_percent`, `type`, `is_active`).
* **`[DONE]` Migration `create_service_charges_table.php`**: `service_charges` (`service_charge_id`, `company_id`, `service_name`, `rate_percent`, `is_taxable`, `is_active`).
* **`[DONE]` Snapshot di Migration `create_orders_table.php`**: `orders` (`tax_percent`, `tax_amount`, `tax_type`, `service_charge_percent`, `service_charge_amount`).

### 1.2 Model, Validation & Seeder (✅ COMPLETED)
* **`[DONE]` Model `App\Models\Admin\Tax.php` & `ServiceCharge.php`**.
* **`[DONE]` Seeder `TaxSeeder.php` & `ServiceChargeSeeder.php`**: PBJT 10% & Service 5% seeded.
* **`[DONE]` FormRequest `TaxRequest.php` & `ServiceChargeRequest.php`**: Validasi Bahasa Indonesia.

### 1.3 Controller, Routes & View UI (✅ COMPLETED)
* **`[DONE]` Controller `App\Http\Controllers\Admin\Keuangan\TaxController.php`**: `index()`, `updateTax()`, `updateServiceCharge()`.
* **`[DONE]` View UI `resources/views/admin/keuangan/setting-tax/index.blade.php`**: Form Master Setting Pajak PB1 & Service Charge + Simulasi Kalkulasi Checkout + Toast Alert (`NexoraToast`) & 400ms Feedback Latency.
* **`[DONE]` Routes**: `/admin/keuangan/setting-tax`, `/admin/keuangan/setting-tax/update-tax`, `/admin/keuangan/setting-tax/update-service`.

### 1.4 Integration & Calculation Testing (✅ COMPLETED)
* **`[DONE]` OrderController & Test Script**:
  - Subtotal Rp 100.000 - Diskon Rp 10.000 = Rp 90.000
  - Service Charge (5%) = Rp 4.500
  - Dasar Pajak (DPP) = Rp 94.500
  - Pajak PB1 (10% Exclusive) = Rp 9.450
  - **Grand Total Struk = Rp 103.950 (100% Presisi Matematic Test Verified)**.

---

## 🔐 MILESTONE 2 — Modul Shift Closing & Daily Cut-Off (CURRENT FOCUS)

### 2.1 Database & Binding (✅ COMPLETED)
* **`[DONE]` Migration `create_daily_closings_table.php`**: Master Sesi Shift Kasir.
* **`[DONE]` FK `daily_closing_id` (`nullable`)**: Ditambahkan pada migration `orders` & `transactions`.
* **`[DONE]` Model `DailyClosing.php`**: Relasi Eloquent ke `Company`, `Order`, `Transaction`.
* **`[DONE]` Seeder `DailyClosingSeeder.php`**: Seed 52 sesi shift (26 hari x 2 shift) & bind 273 order + 271 transaksi.

### 2.2 Master Shift & Cut-Off Settings System (SUB-MILESTONE M2.1 ✅ COMPLETED 100%)
* **`[DONE]` Migration 1 `create_shift_settings_table.php`**: `shift_settings` (`id`, `company_id`, `daily_cutoff_time`, `shift_mode`, `auto_lock_unclosed`, `timestamps`).
* **`[DONE]` Migration 2 `create_shifts_table.php`**: `shifts` (`id`, `company_id`, `shift_number`, `shift_name`, `start_time`, `end_time`, `default_starting_cash`, `is_active`, `timestamps`).
* **`[DONE]` Model `App\Models\Admin\ShiftSetting.php` & `App\Models\Admin\Shift.php`**.
* **`[DONE]` Seeder `ShiftSeeder.php`**: Default Cut-Off 03:00 Pagi & Default Master Shift 1 (08:00-16:00) & Shift 2 (16:00-00:00).
* **`[DONE]` Controller `App\Http\Controllers\Admin\Keuangan\ShiftSettingController.php`**: `index()`, `updateCutoff()`, `storeShift()`, `updateShift()`, `destroyShift()`.
* **`[DONE]` View UI `resources/views/admin/keuangan/setting-shift/index.blade.php`**: Form Setting Cut-off Operasional (Otomatis/Manual/Full Day) + Tabel Master Shift + Modal Tambah/Edit Shift (Theme Auto-Adaptation Dark/Light Mode).
* **`[DONE]` Routes & Sidebar Menu**: Route `/admin/keuangan/setting-shift` & Menu Sidebar **Master Shift & Cut-off** di bawah `Keuangan & Setting`.


### 2.3 Dedicated Clock-In & Clock-Out System (SUB-MILESTONE M2.2 ✅ COMPLETED 100%)
* **`[DONE]` Controller `App\Http\Controllers\Admin\Keuangan\ShiftOperationalController.php`**: `index()`, `openShift()`, `closeShift()`, `zReport()`.
* **`[DONE]` View UI `resources/views/admin/keuangan/shift-operational/index.blade.php`**: Dedicated Dashboard Clock-In & Clock-Out Kasir + Live Sales Stats + Quick Preset Modal Cash + Cash Balancing Calculator & Variance Detector.
* **`[DONE]` Struk Z-Report `resources/views/admin/keuangan/shift-operational/z-report.blade.php`**: Struk Rekap Z-Report Printer Thermal 80mm format + Auto Print.
* **`[DONE]` Routes & Sidebar Menu**: Route `/admin/keuangan/shift-operational` & Menu Sidebar **Buka / Tutup Shift (Clock-In)** di bawah `Keuangan & Setting`.

* **`[SECURITY GUARDS]` 3 Aturan Proteksi**:
  1. *POS Order Guard*: Kunci order jika belum ada shift `open`.
  2. *Guest QR Guard*: Kunci order QR Meja jika toko sudah Cut-Off.
  3. *Unpaid Order Block*: Larang Tutup Shift jika masih ada order menggantung di meja.

### 2.3 Views UI Shift Closing
* **`[NEW]` Modal Buka Shift (`resources/views/admin/pos/modal-open-shift.blade.php`)**.
* **`[NEW]` Modal Tutup Shift (`resources/views/admin/pos/modal-close-shift.blade.php`)**.
* **`[NEW]` View Audit Shift (`resources/views/admin/keuangan/reports/shifts.blade.php`)**.

---

## 💳 MILESTONE 3 — Report Dashboard Hub & 6 Detail Dedicated Laporan + Export Excel (✅ COMPLETED 100%)

### 3.1 Executive Summary Report Hub Dashboard (✅ COMPLETED)
* **`[DONE]` Controller `ReportDashboardController.php`**: Executive KPI cards & filter tanggal global.
* **`[DONE]` View UI `resources/views/admin/keuangan/reports/dashboard.blade.php`**:
  - 4 Executive KPI Cards (Gross Omzet, Net Revenue, Tax & Service, Waste Loss).
  - 6 Kartu Navigasi Clickable mengarah langsung ke masing-masing 6 Laporan Dedicated!

### 3.2 6 Detail Laporan Dedicated & Fitur Export Excel/CSV (✅ COMPLETED)
1. **`[DONE]` Laporan Penjualan & Payment Breakdown** (`/admin/reports/sales` & `/export`):
   - Controller: `SalesReportController.php` | View: `sales.blade.php`
2. **`[DONE]` Laporan Performa Menu Terlaris (PMIX)** (`/admin/reports/products` & `/export`):
   - Controller: `ProductReportController.php` | View: `products.blade.php`
3. **`[DONE]` Laporan Arus Kas (Cash Flow)** (`/admin/reports/cashflow` & `/export`):
   - Controller: `CashFlowReportController.php` | View: `cashflow.blade.php`
4. **`[DONE]` Laporan Pajak PB1 & Service Charge** (`/admin/reports/tax-service` & `/export`):
   - Controller: `TaxServiceReportController.php` | View: `tax-service.blade.php`
5. **`[DONE]` Laporan Stok Bahan Mentah & Waste Log** (`/admin/reports/inventory` & `/export`):
   - Controller: `InventoryReportController.php` | View: `inventory.blade.php`
6. **`[DONE]` Audit Shift Closing Kasir** (`/admin/reports/shifts` & `/export`):
   - Controller: `ShiftClosingReportController.php` | View: `shifts.blade.php`

