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
|
| 2026-07-19 | CREATE | Product CRUD — Model (relasi category), FormRequest, Controller, 5 views, routes, sidebar | `Product.php`, `ProductRequest.php`, `ProductController.php`, `views/admin/product/*`, `routes/web.php`, `layouts/app.blade.php` |
| 2026-07-19 | SEED | Categories + dummy products | database |
|
| 2026-07-19 | CREATE | Category CRUD — FormRequest, Controller, 5 views, routes, sidebar | `CategoryRequest.php`, `CategoryController.php`, `views/admin/category/*`, `routes/web.php`, `layouts/app.blade.php` |
|
| 2026-07-19 | CREATE | Table CRUD — Model (ULID PK), FormRequest, Controller, 5 views, routes, sidebar | `Table.php`, `TableRequest.php`, `TableController.php`, `views/admin/table/*`, `routes/web.php`, `layouts/app.blade.php` |
| 2026-07-19 | SEED | 5 meja dummy | database |
|
| 2026-07-19 | SEED | 36 produk dummy (4 kategori) | `ProductSeeder.php` |
|
| 2026-07-19 | UPDATE | Category CRUD — Aturan 7: semua field pake input-skeleton, btn-loading, min 400ms submit delay | `views/admin/category/create.blade.php`, `views/admin/category/edit.blade.php` |
|
| 2026-07-19 | UPDATE | Product CRUD — Aturan 7: semua field pake input-skeleton, btn-loading, min 400ms submit delay | `views/admin/product/create.blade.php`, `views/admin/product/edit.blade.php` |
|
| 2026-07-19 | MOVE | Menu Meja pindah dari Master Data ke Sample Menu di sidebar | `views/admin/layouts/app.blade.php` |
| 2026-07-19 | UPDATE | Table CRUD — Aturan 7: semua field pake input-skeleton, btn-loading, min 400ms submit delay | `views/admin/table/create.blade.php`, `views/admin/table/edit.blade.php` |
|
| 2026-07-19 | CREATE | Category image upload — preview di create/edit, thumbnail di table/show, storage symlink, validasi file | `CategoryRequest.php`, `CategoryController.php`, `views/admin/category/*.blade.php` |
|
| 2026-07-19 | UPDATE | Product image upload — preview di create/edit, thumbnail di table/show, row clickable → edit | `ProductRequest.php`, `ProductController.php`, `views/admin/product/*.blade.php` |
|
| 2026-07-19 | UPDATE | Product & Table — ganti confirm() browser jadi Bootstrap modal konfirmasi hapus | `views/admin/product/index.blade.php`, `views/admin/table/index.blade.php` |
|
| 2026-07-19 | UPDATE | Product index — tabs kategori filter AJAX + toggle List/Card view | `ProductController@data`, `views/admin/product/index.blade.php`, `views/admin/product/_card.blade.php` |
|
| 2026-07-20 | UPDATE | Supplier — input-skeleton + btn-loading + 400ms delay di create/edit, modal konfirmasi hapus di index | `views/admin/supplier/create.blade.php`, `edit.blade.php`, `index.blade.php` |
| 2026-07-20 | UPDATE | Stock — input-skeleton + btn-loading + 400ms delay di create/edit, modal konfirmasi hapus di index | `views/admin/stock/create.blade.php`, `edit.blade.php`, `index.blade.php` |
| 2026-07-20 | UPDATE | Purchase Order — input-skeleton + btn-loading + 400ms delay di create/edit, modal konfirmasi hapus di index | `views/admin/purchase-order/create.blade.php`, `edit.blade.php`, `index.blade.php` |
| 2026-07-20 | UPDATE | Supplier/Stock/PO — row clickable ke halaman show (ganti link nama doang) | `_data.blade.php`, `index.blade.php` (supplier, stock, purchase-order) |
| 2026-07-20 | CREATE | Todo list — Ingredients (BOM/Resep), seeder yg kurang, konsistensi, tahap 2-5 | `basic-knowledge/todo.md` |
|
| 2026-07-21 | PULL | Commit `e8decac` — "combine product > stock" (product↔stock pivot di create/edit via stepper) | ProductController, StockController, ProductRequest, views product/* |
| 2026-07-21 | FIX | Relasi model `'ingredients'` → `'product_stock'` (ikuti rename migration) | `Product.php`, `Stock.php` |
| 2026-07-21 | FIX | Table model — tambah `HasUlids` trait biar auto-generate PK | `Table.php` |
| 2026-07-21 | UPDATE | ProductSeeder — 36 produk + pivot `product_stock` langsung (68 relasi) | `ProductSeeder.php` |
| 2026-07-21 | CREATE | SupplierSeeder — 5 supplier (PT Sumber Bahan Pangan, CV Berkah Minyak, UD Ayam Segar, Toko Bumbu Makmur, CV Kemasan Plastik) | `SupplierSeeder.php` |
| 2026-07-21 | CREATE | TableSeeder — 15 meja (kapasitas 2-10 kursi) | `TableSeeder.php` |
| 2026-07-21 | CREATE | PurchaseOrderSeeder — 5 PO (3 received + receiving + stock_logs, 2 pending) | `PurchaseOrderSeeder.php` |
| 2026-07-21 | UPDATE | DatabaseSeeder — urutan: Company→Supplier→Table→Stock→PO→Product→Bundle | `DatabaseSeeder.php` |
| 2026-07-21 | FIX | Detail pages — Supplier/Stock/PO show: ganti `table table-borderless` → `detail-table` + CSS | `views admin/*/show.blade.php`, `main.css` |
| 2026-07-21 | CREATE | Bundle CRUD — Model, BundleItem, FormRequest, Controller (full CRUD + AJAX), 5 views (index/_data/create/show/edit), routes, sidebar | `Bundle.php`, `BundleItem.php`, `BundleRequest.php`, `BundleController.php`, `views/admin/bundle/*`, `routes/web.php`, `layouts/app.blade.php` |
| 2026-07-21 | CREATE | BundleSeeder — 8 bundle (Paket Nasi Goreng, Ayam Geprek, Bakso, Snack, Minuman, dll) | `BundleSeeder.php` |
|
| 2026-07-22 | UPDATE | PO Enhancement — confirm(), cancel(), return() + 3 route + 3 modal + validasi receiving + hapus Langsung Pesan + NexoraToast | `PurchaseOrderController.php`, `web.php`, `PurchaseReceivingRequest.php`, `show.blade.php`, `create.blade.php`, `index.blade.php` |
| 2026-07-22 | UPDATE | PurchaseOrderSeeder — 10 PO dengan 5 status (2 draft, 2 ordered, 2 partial, 2 completed, 2 cancelled + 1 return) | `PurchaseOrderSeeder.php` |
| 2026-07-22 | FIX | Bundle table — row clickable ke show (ganti link nama doang) | `views/admin/bundle/_data.blade.php`, `index.blade.php` |
| 2026-07-22 | FIX | Bundle create/edit — .product-tag warna ilang di dark mode, tambah `color:var(--text-primary)` | `views/admin/bundle/create.blade.php`, `edit.blade.php` |
| 2026-07-22 | FIX | Receiving create — teks referensi PO & tombol Konfirmasi Terima ilang di dark mode, tambah `color` + `btn-success-grad` style | `views/admin/purchase-receiving/create.blade.php` |
| 2026-07-22 | RULES | Aturan 10 — setiap perubahan wajib catat di `log_code.md` langsung | `rule_ai.md` |
