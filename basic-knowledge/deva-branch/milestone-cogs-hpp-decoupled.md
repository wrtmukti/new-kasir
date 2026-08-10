# MILESTONE — COGS + HPP + Waste Log Decoupled Architecture (v3.1)

> Status: **RENCANA MILESTONE V3.1 DECOUPLED**
> Tanggal: 2026-08-09
> Basis: PRD (`plan-cogs-hpp-decoupled.md`) + Rencana Migration (`plan-migration-cogs-hpp-decoupled.md`)
> Pola kerja: Tiap milestone standalone $\rightarrow$ Verifikasi (Gate) $\rightarrow$ Lanjut milestone berikutnya.

---

## PETA MILESTONE (RINGKAS)

```
M0 — Fondasi Ledger (Fix stock_logs type=out di Order Kasir)
M1 — Migration Modul COGS & HPP Decoupled (8 Tabel Migration Baru)
M2 — Master Bahan Mentah Estimasi + History (cogs_raw_materials & cogs_raw_material_histories)
M3 — Resep Standar HPP + History (cogs_recipes, cogs_recipe_items & cogs_recipe_histories)
M4 — Modul Waste Log / Bahan Busuk + History (cogs_waste_logs & cogs_waste_histories)
M5 — Laporan Keuangan Bulanan & Dashboard HPP (hpp_financial_reports)
M6 — Opname & Usage Adjustment (Koreksi Stok Mentah & Penyesuaian Periodic)
```

---

## M0 — FONDASI LEDGER (Fix Log Stock Out di Kasir)

**Tujuan:** Memastikan pergerakan stok keluar saat pesanan kasir accepted/completed 100% tercatat ke `stock_logs`.

### Deliverables:
1. DB menyala & koneksi `.env` stabil.
2. Fix `OrderController@store` & `accept`: setiap kali kasir memotong stok fisik `stocks`, wajib menulis record ke `stock_logs` (`reference_type = sale`, `type = out`).
3. Tes: Accept order kasir $\rightarrow$ Jumlah record `stock_logs` bertambah.

**Gate Verifikasi:** Accept order kasir $\rightarrow$ `stock_logs (type=out)` tercatat presisi.

---

## M1 — MIGRATION MODUL COGS & HPP DECOUPLED (8 Tabel)

**Tujuan:** Membuat 8 tabel migration baru (5 Tabel Utama + 3 Tabel History Audit Trail) tanpa merusak tabel lama.

### Deliverables:
1. Migration 5 Tabel Utama: `cogs_raw_materials`, `cogs_recipes`, `cogs_recipe_items`, `cogs_waste_logs`, `hpp_financial_reports`.
2. Migration 3 Tabel History Audit Trail: `cogs_raw_material_histories`, `cogs_recipe_histories`, `cogs_waste_histories`.
3. Jalankan `php artisan migrate` dan catat di `log_code.md`.

**Gate Verifikasi:** `php artisan migrate:status` menampilkan 8 tabel baru tereksekusi *Batch OK*.

---

## M2 — MASTER BAHAN MENTAH ESTIMASI (`CogsRawMaterial`)

**Tujuan:** Mengelola master bahan mentah COGS, penentuan harga beli dari PO, harga efektif, serta pencatatan audit log perubahan harga.

### Deliverables:
1. Model `CogsRawMaterial` & `CogsRawMaterialHistory` (formula `effective_price = price / (yield/100)`).
2. FormRequest `CogsRawMaterialRequest` (validasi Bahasa Indonesia).
3. Controller `CogsRawMaterialController` (CRUD + simpan history `create/update/delete`).
4. Integration PO Receiving: Saat PO diterima, stok & harga di `cogs_raw_materials` otomatis bertambah/ter-update.
5. Views `admin/cogs-raw-material/*` (index/_data/create/edit/show history).
6. Sidebar menu: "Bahan Mentah COGS" & "Riwayat Bahan Mentah".

**Gate Verifikasi:** CRUD bahan mentah + PO Receiving bertambah di `cogs_raw_materials` + riwayat `cogs_raw_material_histories` tercatat.

---

## M3 — RESEP STANDAR & KALKULATOR HPP (`CogsRecipe`)

**Tujuan:** Mengisi resep perkiraan standar per menu, menghitung modal ideal (`estimated_cogs`), dan persentase *Food Cost %*.

### Deliverables:
1. Model `CogsRecipe`, `CogsRecipeItem`, & `CogsRecipeHistory`.
2. Controller `CogsRecipeController` (CRUD + hitung otomatis `estimated_cogs` & `food_cost_percent`).
3. Views `admin/cogs-recipe/*` (index/_data/create/edit/show).
4. Sidebar menu: "Resep & COGS Menu".

**Gate Verifikasi:** Buat resep menu $\rightarrow$ `estimated_cogs` & *Food Cost %* terhitung presisi + audit log tercatat.

---

## M4 — MODUL WASTE LOG / BAHAN BUSUK (`CogsWasteLog`)

**Tujuan:** Mencatat bahan basi/rusak/terbuang di dapur dan langsung menghitung kerugian nilai Rupiah-nya.

### Deliverables:
1. Model `CogsWasteLog` & `CogsWasteHistory` (formula `waste_cost = qty_lost * effective_price`).
2. Controller `CogsWasteLogController` (Form catat waste + kalkulasi otomatis rupiah).
3. Mengurangi stok mentah di `cogs_raw_materials`.
4. Views `admin/cogs-waste/*` (form catat waste + tabel riwayat kerugian).
5. Sidebar menu: "Bahan Terbuang (Waste Log)".

**Gate Verifikasi:** Input 2kg Ayam Basi $\rightarrow$ Stok mentah berkurang 2kg + Nilai kerugian Rupiah terhitung di Waste Log.

---

## M5 — LAPORAN KEUANGAN BULANAN & DASHBOARD HPP (`HppReport`)

**Tujuan:** Menyajikan laporan proyeksi Laba Rugi Bersih bulanan.

### Deliverables:
1. Model `HppFinancialReport` & `HppReportController`.
2. Hitung: Omzet Kasir − (Estimasi COGS x Qty Terjual) − Total Waste Cost − Gaji − Overhead = Laba Bersih.
3. Views `admin/hpp-report/*` (dashboard ringkasan + export CSV).
4. Sidebar menu: "Laporan HPP & Laba Rugi".

**Gate Verifikasi:** Dashboard menampilkan Omzet, Estimasi COGS, Waste Cost, Gaji, Overhead, dan Laba Bersih secara akurat.

---

## M6 — OPNAME & USAGE ADJUSTMENT

**Tujuan:** Melakukan penyesuaian/koreksi stok mentah di `cogs_raw_materials` secara periodic (mingguan/bulanan).

### Deliverables:
1. Form Opname & Adjustment stok mentah.
2. Catat audit log `cogs_raw_material_histories` (`action_type = adjustment / usage`).

---

## GATES VERIFIKASI SELESAI TIAP MILESTONE

| M | Gate Pass Ketika |
|---|---|
| **0** | Accept order kasir $\rightarrow$ `stock_logs (type=out)` terisi. |
| **1** | 8 migration baru ter-run tanpa error. |
| **2** | PO Receiving bertambah di `cogs_raw_materials` & history tercatat. |
| **3** | `estimated_cogs` & Food Cost % terhitung otomatis di resep. |
| **4** | Input bahan basi $\rightarrow$ stok mentah berkurang & nilai kerugian Rupiah terhitung. |
| **5** | Laporan bulanan Laba Rugi Bersih terhitung akurat. |
| **6** | Opname penyesuaian stok mentah tercatat di audit log. |
