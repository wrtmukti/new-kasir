# TODO List — Khusus Branch `deva-branch`

> **Branch Context**: `deva-branch`
> **Project Scope**: Decoupled COGS + HPP Report + Purchase Order Raw Stock Integration + Menu Analytics + 1-Month Sales Seeding + Pagination & Documentation.
> **Status**: **ALL COMPLETED & SEEDED 100% (MIGRATE FRESH & SEED VERIFIED SUCCESSFUL)**

---

## 📋 Checklist Task & Progress `deva-branch`:

### M0 — Setup & Pre-requisites
- [x] **0.1** Verifikasi active branch di `deva-branch` (`git branch --show-current`)
- [x] **0.2** Setup struktur folder & namespace `App\Http\Controllers\Admin\Keuangan`

### M1 — Database Migrations (Tabel COGS, HPP, & History Audit Trail)
- [x] **1.1** Tabel `cogs_raw_materials` & `cogs_raw_material_histories`
- [x] **1.2** Tabel `cogs_recipes`, `cogs_recipe_items`, & `cogs_recipe_histories`
- [x] **1.3** Tabel `cogs_waste_logs` & `cogs_waste_histories`
- [x] **1.4** Tabel `hpp_financial_reports`
- [x] **1.5** Migration `purchase_order_items` & `purchase_receiving_items` (Relasi ke `cogs_raw_materials`)

### M2 — Modul Bahan Mentah COGS (`CogsRawMaterial`)
- [x] **2.1** Model `CogsRawMaterial` + `CogsRawMaterialHistory`
- [x] **2.2** FormRequest `CogsRawMaterialRequest` dengan validasi Bahasa Indonesia
- [x] **2.3** Controller `CogsRawMaterialController` (CRUD + history audit log)
- [x] **2.4** Views `admin/keuangan/cogs-raw-material/*` (Datatables, Create, Edit, Show History, Opname)

### M3 — Modul Purchase Order & Raw Stock (`PurchaseOrderController`)
- [x] **3.1** Decouple Purchase Order dari `stocks` produk & hubungkan ke `cogs_raw_materials`
- [x] **3.2** Controller `App\Http\Controllers\Admin\Keuangan\PurchaseOrderController`
- [x] **3.3** Receiving otomatis menambah stok `cogs_raw_materials.amount` & update `effective_price`
- [x] **3.4** Logging otomatis ke `cogs_raw_material_histories` (`action_type = 'purchase_receiving' / 'purchase_return'`)
- [x] **3.5** Form PO & Receiving pilih item `cogs_raw_materials` dengan detail satuan & harga beli

### M4 — Modul Resep & COGS Menu (`CogsRecipe`)
- [x] **4.1** Model `CogsRecipe`, `CogsRecipeItem`, & `CogsRecipeHistory`
- [x] **4.2** Controller `CogsRecipeController` (Hitung otomatis `estimated_cogs` + suggested price)
- [x] **4.3** Views `admin/keuangan/cogs-recipe/*` (Form resep komplit + modal rincian komposisi)

### M5 — Modul Waste Log (`CogsWasteLog`)
- [x] **5.1** Model `CogsWasteLog` & `CogsWasteHistory`
- [x] **5.2** Controller `CogsWasteLogController` (Pencatatan bahan rusak/busuk + pemotongan stok otomatis)
- [x] **5.3** Views `admin/keuangan/cogs-waste/*`

### M6 — Laporan HPP, Laba Rugi, & Pagination Interaktif (`HppReport`)
- [x] **5.1** Model `HppFinancialReport`
- [x] **5.2** Controller `HppReportController` (Integrasi Omzet Kasir - COGS - Waste - Gaji - Listrik - PO Purchases)
- [x] **5.3** Views `admin/keuangan/hpp-report/index.blade.php`
- [x] **5.4** Fitur Pagination Client-side Interaktif (Select per-page: 10, 20, 50, 100, Custom/Semua tanpa reload)
- [x] **5.5** Rincian Modal Resep per Menu & Panduan Lengkap Alur Penggunaan Sistem

### M7 — Grafik Analitik Penjualan (`MenuAnalytics`)
- [x] **7.1** Redesign tampilan grafik `menu-analytics` presisi ala template `laravel-admin`
- [x] **7.2** Konfigurasi Chart.js Light Mode (`#FFFFFF`) & Dark Mode (`#1E293B`) tanpa border hitam kaku
- [x] **7.3** Palette spesifikasi user (Makanan `#2563EB`/`#3B82F6`, Minuman `#7C3AED`/`#A855F7`, Snack `#0891B2`/`#06B6D4`)

### M8 — Seeder & Data Penjualan 1 Bulan (26 Hari)
- [x] **8.1** `CogsRawMaterialSeeder` & `PurchaseOrderSeeder` (10 PO Bahan Mentah)
- [x] **8.2** `OrderSeeder` (294 pesanan terdistribusi 26 hari dalam sebulan)
- [x] **8.3** `TransactionSeeder` (292 transaksi sukses + `transaction_items`)
- [x] **8.4** Fix Diskon Seeder (`DiscountSeeder.php`) agar 0% minus pada laba kotor & margin
- [x] **8.5** Running `php artisan migrate:fresh --seed` (100% SUKSES & Terverifikasi)

### M9 — Dokumentasi & Sidebar Guide
- [x] **9.1** Menu Sidebar Baru `Cara Penggunaan & Alur` (`/admin/guide`)
- [x] **9.2** View Halaman Panduan Lengkap `resources/views/admin/guide/index.blade.php`

---

## ⚠️ Aturan Kerja Khusus Branch `deva-branch`:
1. **Branch Lock**: Jangan pernah commit atau ubah langsung di branch `main`/`master` — seluruh pengerjaan wajib di `deva-branch`.
2. **Log Code Mandatory**: Setiap selesai buat/update file, langsung catat di `basic-knowledge/log_code.md`.
3. **Konfirmasi TODO**: Sebelum mulai pengerjaan sesi, selalu cek file ini (`basic-knowledge/deva-branch/todo.md`) dan berikan kabar branch aktif ke user.
