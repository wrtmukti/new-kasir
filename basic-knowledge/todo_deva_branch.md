# Todo — BRANCH `deva-branch` : Eksekusi Decoupled COGS + HPP + Waste Log + History Audit Trail

> Branch: `deva-branch` | Dibuat: 2026-08-09 (Updated V3.1 Audit Trail)
> Basis: PRD (`plan-cogs-hpp-decoupled.md`) + Rencana Migration (`plan-migration-cogs-hpp-decoupled.md`)
> Status: **SELESAI DIBUAT (KODE & SEEDER SIAP, MIGRATION MENUNGGU EKSEKUSI USER)**

---

## 0. Persiapan (SEBELUM eksekusi)

- [x] Pastikan ada di branch `deva-branch` (`git branch --show-current`)
- [x] Baca `basic-knowledge/plan-cogs-hpp-decoupled.md` & `plan-migration-cogs-hpp-decoupled.md`
- [x] Buat struktur folder & namespace `Keuangan`
- [ ] Jalankan `php artisan migrate` (Manual oleh User)

---

## M0 — Fondasi: `stock_logs` fix di Order Kasir

- [ ] **0.1** (PENDING / SKIPPED BY USER) Fix `OrderController@store`: disimpan untuk pembahasan internal tim user
- [ ] **0.2** (PENDING / SKIPPED BY USER) Fix `OrderController@accept`

---

## M1 — Migration Modul COGS, HPP & History Logging (8 Tabel)

- [x] **1.1** Buat Migration Utama (5): `cogs_raw_materials`, `cogs_recipes`, `cogs_recipe_items`, `cogs_waste_logs`, `hpp_financial_reports`
- [x] **1.2** Buat Migration History (3): `cogs_raw_material_histories`, `cogs_recipe_histories`, `cogs_waste_histories`
- [ ] **1.3** Jalankan `php artisan migrate` (Manual oleh User)
- [x] **1.4** Catat di `log_code.md`

---

## M2 — Master Bahan Mentah Estimasi + History (`CogsRawMaterial`)

- [x] **2.1** Model `CogsRawMaterial` + `CogsRawMaterialHistory` (Namespace `App\Models\Admin\Keuangan`)
- [x] **2.2** FormRequest `CogsRawMaterialRequest` (validasi B.Indonesia)
- [x] **2.3** Controller `CogsRawMaterialController` (CRUD + simpan history `create/update/delete`)
- [x] **2.4** Views `admin/keuangan/cogs-raw-material/*` (index/_data/create/edit/show history)
- [x] **2.5** Routes + sidebar menu "Bahan Mentah COGS" under Analitik
- [x] **2.6** Catat di `log_code.md`

---

## M3 — Resep Standar & History (`CogsRecipe`)

- [x] **3.1** Model `CogsRecipe`, `CogsRecipeItem`, & `CogsRecipeHistory` (Namespace `App\Models\Admin\Keuangan`)
- [x] **3.2** Controller `CogsRecipeController` (CRUD + hitung otomatis `estimated_cogs` + simpan history snapshot)
- [x] **3.3** Views `admin/keuangan/cogs-recipe/*` (index/_data/create/edit/show)
- [x] **3.4** Routes + sidebar menu "Resep & COGS Menu" under Analitik
- [x] **3.5** Catat di `log_code.md`

---

## M4 — Modul Waste Log + History (`CogsWasteLog`)

- [x] **4.1** Model `CogsWasteLog` & `CogsWasteHistory` (Namespace `App\Models\Admin\Keuangan`)
- [x] **4.2** Controller `CogsWasteLogController` (CRUD + simpan history kerugian waste)
- [x] **4.3** Views `admin/keuangan/cogs-waste/*` (form catat waste + tabel riwayat kerugian)
- [x] **4.4** Routes + sidebar menu "Bahan Terbuang (Waste Log)" under Analitik
- [x] **4.5** Catat di `log_code.md`

---

## M5 — Laporan Keuangan Bulanan & Dashboard HPP (`HppReport`)

- [x] **5.1** Model `HppFinancialReport` (Namespace `App\Models\Admin\Keuangan`)
- [x] **5.2** Controller `HppReportController` (generate report bulanan)
- [x] **5.3** Hitung: Omzet Kasir − (Estimasi COGS x Qty Terjual) − Total Waste Cost − Gaji − Overhead = Laba Bersih
- [x] **5.4** Views `admin/keuangan/hpp-report/*` (dashboard ringkasan)
- [x] **5.5** Routes + sidebar menu "Laporan HPP & Laba Rugi" under Analitik
- [x] **5.6** Catat di `log_code.md`

---

## ⚠️ Aturan kerja

- Jangan commit di `main` — pakai `deva-branch`.
- Tiap perubahan → catat ke `basic-knowledge/log_code.md`.
- Tiap milestone selesai → update checklist ini.