# Projekt Resuer — Kasir POS

> Ringkasan penting arsitektur & state proyek. Dibuat dari percakapan.

---

## Database

- **Driver:** SQLite (default via `.env`). File: `database/database.sqlite`
- **PHP:** `C:/xampp812/php/php.exe` (v8.2.12) — bukan system PHP (v7.2)
- Semua migration 100% sukses (30 file)

## Multi-Tenant (Company)

- `company_id` sebagai tenant scope — string, nullable, tanpa FK constraint
- PK company pake ULID (`HasUlids` trait)
- Hampir semua tabel punya `company_id` → logical scoping aja, belum ada middleware/global scope
- Users belum punya `company_id`

## Stock = Bahan Baku, Bukan Produk Jadi

Stocks itu bahan mentah (beras, minyak, dll). Beda sama products (menu jadi).

| Tabel | Fungsi |
|-------|--------|
| `stocks` | Master bahan baku |
| `stock_histories` | SCD Type 2 history |
| `ingredients` | Pivot product ↔ stock |
| `stock_logs` | Ledger transaksional (in/out/adjustment) |

## SCD Type 2 + Snapshot

Tiap master data punya history table. Transaksi di-snapshot biar laporan akurat.

| Master | History |
|--------|---------|
| products | product_histories |
| stocks | stock_histories |
| vouchers | voucher_histories |
| discounts | discount_histories |
| bundles | bundle_histories |

## Fix Migration (udah dikerjain)

1. `interger` → `integer` (products & product_histories)
2. `id('category_id')` → `unsignedBigInteger` (products — double PK)
3. `product_hostorys` → `product_histories`
4. `stock_historys` → `stock_histories`
5. `cuqtomers` → `customers`
6. Duplikat kolom `customer_name` (hapus 1)
7. Duplikat kolom `payment_id` (hapus 1)
8. `ulid('table_id')` → `ulid('table_id')->primary()`
9. `after('product_discount')` → `after('product_discount_id')`
10. Rename file: `cutomers` → `customers`

## State Aplikasi

- **Migrations:** ✅ 100% (semua udah ada dari awal, gue cuma fix typo)
- **Models:** ⚠️ Awal: cuma Company & User. Sekarang: + Stock, Product, Category (dibikin 2026-07-11)
- **Controllers:** ⚠️ Awal: nihil. Sekarang: CompanyController & StockController (dibikin 2026-07-11)
- **Routes:** ⚠️ Awal: cuma docs. Sekarang: + `/admin/basic_layout/company`, `/admin/basic_layout/stock`
- **Views:** ⚠️ Awal: nihil. Sekarang: 8 view (4 company + 4 stock) di `admin/basic_layout/`
- **Seeders:** ⚠️ Awal: nihil. Sekarang: CompanySeeder + StockSeeder (3 company + 7 stock dummy)
- **Form Requests:** ❌
- **Middleware/Service:** ❌

> **Catatan:** Migration company & stock sudah ada sebelum gue kerja. Yang gue tambahin cuma layer aplikasi (Model, Controller, View, Route, Seeder).

## Alur Bisnis

1. **Purchasing:** Supplier → PO → Receiving → StockLog(in) → stock_amount++
2. **Sales:** Transaksi → lookup ingredients → StockLog(out) → stock_amount--
3. **Adjustment:** StockLog + ubah stock_amount langsung

## Catatan

- `category_image` di fillable Company — kemungkinan typo (should be `company_image`)
- File migration `2026_06_13_175529_create_product_stock_table.php` isinya create table `ingredients` — misleading
