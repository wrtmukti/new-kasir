# Log Code — Riwayat Perubahan

> Catat setiap perubahan penting di proyek ini. Format: `YYYY-MM-DD | [Tipe] | Deskripsi | File terkait`

---

## 2026-07-11

| Tipe | Deskripsi | File |
|------|-----------|------|
| FIX | Typo `interger` → `integer` | `create_products_table.php`, `create_product_histories_table.php` |
| FIX | Double primary key `id('category_id')` → `unsignedBigInteger` | `create_products_table.php` |
| FIX | Typo table name `product_hostorys` → `product_histories` | `create_product_histories_table.php` |
| FIX | Typo table name `stock_historys` → `stock_histories` | `create_stock_histories_table.php` |
| FIX | Typo table name `cuqtomers` → `customers` | `create_customers_table.php` |
| FIX | Duplikat kolom `customer_name` (hapus 1) | `create_customers_table.php` |
| FIX | Duplikat kolom `payment_id` (hapus 1) | `create_payments_table.php` |
| FIX | Missing primary key `ulid('table_id')` → `->primary()` | `create_tables_table.php` |
| FIX | Wrong `after('product_discount')` → `after('product_discount_id')` | `add_discount_fields_to_products_table.php` |
| RENAME | File migration typo `cutomers` → `customers` | `2026_06_13_175535_create_cutomers_table.php` |
| CREATE | DB file SQLite + migrate 30 migration sukses | `database/database.sqlite` |
| CREATE | Dokumentasi arsitektur proyek | `basic-knowledge/README.md` |
| CREATE | Aturan AI | `basic-knowledge/rule_ai.md` |
| CREATE | Log perubahan | `basic-knowledge/log_code.md` |
| RULES | Aturan 1-4 dicatat | `rule_ai.md` |
| CREATE | Company + Stock CRUD (MVC, routes, seeder) | CompanyController, StockController, 8 views, 2 seeders, routes |
| CREATE | Model Product & Category | `app/Models/Product.php`, `Category.php` |
| SEED | 3 company dummy + 7 stock dummy | `database/database.sqlite` |
| FIX | Layout sendiri di `admin/layouts/` — gak pake `docs/` langsung | 8 views + layout baru |
| RULES | Aturan 5 — auth & login menyusul, semua route open | `rule_ai.md` |
| MOVE | Semua view admin pindah ke `admin/basic_layout/` + route prefix `/admin/basic_layout/` | Controllers, routes, 8 views |
| RULES | Aturan 6 — folder structure `basic_layout/` | `rule_ai.md` |
| RULES | Aturan 6 diperluas — folder separation Model & Controller ikut layer (Admin/SysAdmin/Guest) | `rule_ai.md` |
| MOVE | CompanyController pindah `Admin/` → `SysAdmin/`, namespace & route redirect ikut berubah | `app/Http/Controllers/SysAdmin/CompanyController.php`, `routes/web.php` |
| DELETE | CompanyController lama dari `Admin/` | `app/Http/Controllers/Admin/CompanyController.php` |
| MOVE | Semua model dipisah ke folder: `SysAdmin/` (Company, User), `Admin/` (Product, Stock, Category) | `app/Models/SysAdmin/*`, `app/Models/Admin/*` |
| FIX | Import model di Controllers, Seeders, Factory, config/auth nunjuk ke namespace baru | semua file terkait |
| RULES | Aturan 7 — form wajib pake input-skeleton & btn-loading, minimal 400ms loading setelah submit | `rule_ai.md` |
| RULES | Aturan 7 diperluas — table wajib pagination (class `pagination-modern`), filter 10/50/100 per page, AJAX, loading shimmer min 400ms | `rule_ai.md` |
| RULES | Aturan 8 — semua alert/notifikasi pakai `NexoraToast()`, gak pake session flash alert atau alert browser | `rule_ai.md` |
| UPDATE | Stock & Company index — AJAX pagination, per-page filter (10/50/100), skeleton shimmer min 400ms, toast, delete via AJAX | view, controller, routes, partial _data, pagination modern |
| FIX | Skeleton loading stock & company — dari div di luar table diganti jadi row skeleton per cell di dalam tbody, header tetap muncul | `admin/stock/index.blade.php`, `sys_admin/company/index.blade.php` |
|
| 2026-07-15 | RULES | Aturan 7 diperluas — validasi backend via Form Request, pesan error Bahasa Indonesia di bawah field warna merah | `rule_ai.md` |
|
| 2026-07-17 | CREATE | Form Request Company (+ messages B.Indonesia) | `app/Http/Requests/SysAdmin/CompanyRequest.php` |
| 2026-07-17 | CREATE | Form Request Stock (+ messages B.Indonesia) | `app/Http/Requests/Admin/StockRequest.php` |
| 2026-07-17 | UPDATE | CompanyController — store/update pake CompanyRequest | `app/Http/Controllers/SysAdmin/CompanyController.php` |
| 2026-07-17 | UPDATE | StockController — store/update pake StockRequest | `app/Http/Controllers/Admin/StockController.php` |
|
| 2026-07-17 | CREATE | Supplier CRUD — Model, FormRequest, Controller, 5 views, routes, sidebar | `app/Models/Admin/Supplier.php`, `SupplierController.php`, `views/admin/supplier/*` |
|
| 2026-07-17 | CREATE | PO + Receiving — 4 Models, 2 FormRequest, Controller, 5 views, routes, sidebar | `PurchaseOrder*`, `PurchaseReceiving*`, `PurchaseOrderController.php`, `views/admin/purchase-order/*` |
|
| 2026-07-19 | CREATE | StockLog model | `app/Models/Admin/StockLog.php` |
| 2026-07-19 | UPDATE | Receiving store — inject stock_logs (type=in, stok_before/after) | `PurchaseOrderController@receivingStore` |
