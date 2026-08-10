# PRD — COGS & HPP Decoupled Architecture (Model Estimator & PO Raw Material)

> Status: **ARSITEKTUR DECOUPLED (PO MASUK KE RAW MATERIALS, BERKURANG VIA WASTE & ADJUSTMENT)**
> Tanggal: 2026-08-09
> Keputusan Final User: **PO Receiving menambah `cogs_raw_materials`. Stok mentah berkurang via Waste Log & Adjustment Opname. Stok kasir (`stocks`) di-update manual oleh user/staf saat stok siap dijual.**

---

## 1. PRINSIP ALUR PO & BERKURANGNYA RAW MATERIALS (TERKUNCI)

1. **PO Receiving $\rightarrow$ Menambah `cogs_raw_materials` (Bahan Mentah):**
   Saat penerimaan barang dari supplier (PO Receiving), jumlah stok & harga beli **otomatis menambah `cogs_raw_materials`** dan memperbarui harga beli efektif.
2. **Berkurangnya `cogs_raw_materials` $\rightarrow$ Lewat 2 Kejadian Utuh:**
   - **Kejadian A (Bahan Busuk/Terbuang):** Di-input via Form Waste Log (`cogs_waste_logs`), mengurangi `amount` di `cogs_raw_materials` & menghitung nilai kerugian Rupiah (`waste_cost`).
   - **Kejadian B (Pemakaian / Opname Periodic Adjustment):** Di-update manual saat opname/pemakaian bahan mentah di gudang, mencatat `cogs_raw_material_histories` (`action_type = usage / adjustment`).
3. **Stok Kasir (`stocks`) $\rightarrow$ Input / Update Manual User:**
   Pengisian stok siap pakai di kasir (`stocks`) di-update **secara manual oleh user/staf** saat barang siap dijual di kasir.
4. **Penjualan Kasir $\rightarrow$ Mengurangi `stocks` (Stok siap pakai):**
   Penjualan di kasir langsung mengurangi `stocks` via `product_stock` dan mencatat `stock_logs` (`type=out`, `reference_type=sale`). Kasir transaksi instan tanpa lag.

---

## 2. KONTRAK STRUKTUR SISTER SYSTEM (2 LAYER INDEPENDEN)

```
┌────────────────────────────────────────────────────────────────────────┐
│  LAYER 1: PO & MASTER BAHAN MENTAH COGS                                │
│  - PO Receiving: Menambah stok & update harga di `cogs_raw_materials`  │
│  - Waste Log (`cogs_waste_logs`): Mengurangi stok mentah (Rotten)      │
│  - Opname / Usage Adjustment: Mengurangi stok mentah periodic          │
│  - Audit Log History: `cogs_raw_material_histories`                    │
└────────────────────────────────────────────────────────────────────────┘
                                   │ (Manajemen Manual & Analisis)
                                   ▼
┌────────────────────────────────────────────────────────────────────────┐
│  LAYER 2: KASIR & STOK SIAP PAKAI (`stocks`)                           │
│  - User Update Manual `stocks`: Saat stok siap dijual di kasir        │
│  - Penjualan Kasir: Mengurangi `stocks` via `product_stock` (type=out) │
│  - `stock_logs`: Log riwayat keluar/masuk fisik kasir                  │
│  - Laporan Proyeksi Laba Rugi Bulanan: `hpp_financial_reports`         │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 3. RANCANGAN SKEMA DATABASE (8 TABEL MIGRATION BARU)

### A. Tabel Primary COGS & HPP (5 Tabel Baru)
1. `cogs_raw_materials`: Master Bahan Mentah penerima PO (`name`, `unit`, `amount`, `price_per_unit`, `loss_percent`, `yield_percent`, `effective_price`).
2. `cogs_recipes`: Master Resep Standar Perkiraan per Menu (`recipe_name`, `target_food_cost`, `estimated_cogs`).
3. `cogs_recipe_items`: Detail takaran bahan per resep standar.
4. `cogs_waste_logs`: Pencatatan kerugian bahan busuk/terbuang (`qty_lost`, `waste_cost`, `reason`, `loss_date`).
5. `hpp_financial_reports`: Laporan proyeksi laba rugi bulanan.

### B. Tabel Audit Trail / History Logging (3 Tabel Baru)
6. `cogs_raw_material_histories`: Riwayat perubahan stok & harga bahan mentah dari PO/waste/adjustment.
7. `cogs_recipe_histories`: Riwayat perubahan resep standar HPP.
8. `cogs_waste_histories`: Riwayat pencatatan bahan terbuang.
