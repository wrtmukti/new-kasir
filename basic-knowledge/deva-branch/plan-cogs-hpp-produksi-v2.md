# PRD v2.0 — COGS + HPP + Produksi & Stock Management

> Status: **PRD V2.0 (TERMASUK FITUR ENHANCEMENT)** — tidak menimpa PRD v1.0, menggabungkan fitur dasar COGS + HPP dengan 5 ide penguatan operasional.
> Tanggal: 2026-08-09
> Referensi Database & Migrasi: Evaluasi migrasi existing (`stocks`, `products`, `product_stock`, `stock_logs`)

---

## 1. EVALUASI DAN KOMPATIBILITAS SKEMA EXISTING

Berdasarkan pemeriksaan seluruh migrasi existing:
1. **`stocks` (Existing):** Memiliki `stock_id`, `company_id`, `stock_name`, `stock_unit`, `stock_amount`, `stock_price`. 
   - *Status di V2:* Diperlakukan sebagai **Bahan Olahan / Semi (Level 2)**. Data existing buatan user tetap aman. Kolom baru yang ditambahkan bersifat *nullable* (`stock_loss_percent`, `yield_percent`, `price_per_unit`, `min_amount`).
2. **`product_stock` (Existing):** Menghubungkan `product_id` ke `stock_id` dengan `quantity`.
   - *Status di V2:* Tetap dipertahankan sebagai resep menu (**BOM Menu**). Tambahan kolom `raw_material_id` (nullable) disediakan jika ada menu sederhana yang langsung memakai bahan mentah tanpa melalui bahan semi (seperti Es Botol).
3. **`stock_logs` (Existing):** Memiliki `stock_id`, `reference_type`, `reference_code`, `type`, `qty`, `price`, `total`, `stock_before`, `stock_after`, `notes`.
   - *Status di V2:* Ditambahkan kolom `raw_material_id` (nullable) agar log bisa mencatat pergerakan **Bahan Mentah** maupun **Bahan Semi** dalam satu ledger terpusat. `reference_type` diperluas dengan nilai: `'production'`, `'waste'`, `'expired'`, `'ordering'`, `'sale'`.
4. **`products` & `transaction_items` (Existing):**
   - Ditambahkan `product_cost` di `products` dan `cost` + `packaging_cost` di `transaction_items` (frozen cost saat transaksi).

---

## 2. ARSITEKTUR 4 TINGKAT & HASIL ENHANCEMENT

```
LEVEL 4 (Paket/Bundle)   : bundles & bundle_items
LEVEL 3 (Menu/Products)  : products & product_stock (BOM)
LEVEL 2 (Bahan Semi)     : stocks & produced_recipes (Hasil olahan dapur)
LEVEL 1 (Bahan Mentah)   : raw_materials (Hasil PO/terima barang manual)
```

### Fitur Penguatan V2.0 yang Ditambahkan:
1. **Pencatatan Waste & Expired (`stock_logs` reference_type = waste/expired):**
   Mencatat bahan terbuang/tumpah/basi agar tidak hilang misterius saat opname, dan otomatis dibebankan ke laporan kerugian/biaya operasional.
2. **Konversi Satuan Pembelian (`purchase_unit` & `conversion_ratio`):**
   Memungkinkan pembelian dalam unit grosir (misal: 1 Dus = 24.000 mL) dan dikonversi otomatis saat staf menginput stok masuk.
3. **Peringatan Stok Minimum (`min_amount` Alert):**
   Sistem memberikan indikator visual (kuning/merah) di admin dashboard jika stok `raw_materials` atau `stocks` berada di bawah batas minimal.
4. **Kalkulasi Ulang HPP Batch (*Recalculate Cost*):**
   Tombol admin untuk memperbarui `product_cost` jika ada koreksi salah ketik harga beli bahan mentah.

---

## 3. SKEMA DRAFT TABEL DATABASE (V2.0 FINAL)

### A. `raw_materials` — BAHAN MENTAH (Tabel Baru)
```sql
id                  unsignedBigInteger PK
company_id          string
name                string
code                string (nullable)
slug                string (nullable)
description         text (nullable)
unit                string (20) -- gram, mL, pcs, butir
purchase_unit       string (20) (nullable) -- Dus, Karton, Kaleng
conversion_ratio    decimal (15,4) default 1.0000 -- 1 purchase_unit = X unit
amount              decimal (15,4) default 0 -- stok fisik saat ini
min_amount          decimal (15,4) default 0 -- threshold alert stok tipis
price               decimal (15,2) -- harga per unit
loss_percent        decimal (5,2) default 0.00 -- % susut saat diolah
yield_percent       decimal (5,2) default 100.00 -- 100 - loss_percent
price_per_unit      decimal (15,4) -- harga efektif per unit (price / (yield/100))
tracking_mode       enum('exact', 'coarse', 'bulk') default 'exact'
reference_code      string (nullable)
delete_status       tinyInteger default 0
created_by          string (50)
updated_by          string (50)
timestamps
```

### B. `stocks` — BAHAN SEMI (Tabel Existing, Tambah Kolom)
```sql
-- Existing: stock_id, company_id, stock_code, stock_name, stock_slug, stock_type, stock_unit, stock_amount, stock_price, stock_status, etc.
+ ADD COLUMN stock_loss_percent  decimal (5,2) default 0.00
+ ADD COLUMN yield_percent       decimal (5,2) default 100.00
+ ADD COLUMN price_per_unit      decimal (15,4) default 0.00
+ ADD COLUMN min_amount          integer default 0
```

### C. `produced_recipes` — RESEP PRODUKSI SEMI (Tabel Baru)
```sql
id                  unsignedBigInteger PK
company_id          string
produced_stock_id   unsignedBigInteger -- FK ke stocks (parent semi)
source_material_id  unsignedBigInteger (nullable) -- FK ke raw_materials
source_semi_id      unsignedBigInteger (nullable) -- FK ke stocks (child semi)
quantity            decimal (15,4) -- jumlah bahan yang dibutuhkan per 1 unit parent
created_by          string (50)
updated_by          string (50)
timestamps
```

### D. `stock_logs` — LEDGER STOK TERPUSAT (Tabel Existing, Tambah Kolom)
```sql
-- Existing: log_id, company_id, stock_id, reference_type, reference_code, type, qty, price, total, stock_before, stock_after, notes
+ ADD COLUMN raw_material_id    unsignedBigInteger (nullable) -- FK ke raw_materials
-- Valid Enum/String for reference_type: 'purchase_receiving', 'ordering', 'production', 'sale', 'waste', 'expired', 'adjustment', 'cooking_usage', 'bulk_period'
```

### E. `products` & `transaction_items` (Tabel Existing, Tambah Kolom)
```sql
+ ADD COLUMN product_cost        decimal (15,2) default 0.00 ke products
+ ADD COLUMN cost                decimal (15,2) default 0.00 ke transaction_items
+ ADD COLUMN packaging_cost      decimal (15,2) default 0.00 ke transaction_items
```

### F. TABEL HPP & FINANCIAL REPORTING (Tabel Baru)
1. `hpp_employees`: `id`, `company_id`, `name`, `position`, `salary`, `timestamps`
2. `hpp_categories`: `id`, `company_id`, `name`, `type` (overhead/license), `timestamps`
3. `hpp_transactions`: `id`, `company_id`, `category_type` (raw_material, bulk, labor, overhead, waste), `description`, `amount`, `year`, `month`, `timestamps`
4. `hpp_reports`: `id`, `company_id`, `year`, `month`, `total_omzet`, `total_cogs`, `total_bulk`, `total_labor`, `total_overhead`, `total_waste`, `net_profit`, `hpp_per_menu`, `timestamps`

---

## 4. TAHAPAN EKSEKUSI REVISI (MILESTONE V2.0)

| Step | Milestone | Output |
|---|---|---|
| **M0** | Fix Ledger & DB | Stock logs mencatat pergerakan keluar `sale` di `OrderController@store` & `@accept`. DB siap. |
| **M1** | Master `raw_materials` + Conversion Ratio & Alert | CRUD Bahan Mentah + input manual + konversi satuan + threshold alert. |
| **M2** | Modul Produksi & Waste Log | Form Produksi (Mentah $\rightarrow$ Semi) + Form Waste/Basi (`reference_type = waste`). |
| **M3** | Costing Menu & Batch Recalculate | Hitung otomatis `product_cost` dari BOM + tombol batch recalculate HPP. |
| **M4** | Snapshot Transaksi & Packaging | Frozen cost di `transaction_items.cost` + auto-decrement packaging by `order_type`. |
| **M5** | Modul HPP & Dashboard Laba Rugi | Input gaji/overhead + laporan bulanan (Omzet − COGS − Bulk − Waste − Gaji − Overhead). |
| **M6** | Opname & Report Discrepancy | Laporan perbandingan stok teoritis vs fisik aktual. |
