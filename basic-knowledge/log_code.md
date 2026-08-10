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
|
| 2026-07-22 | CREATE | Order menu (Pesan) — controller, view index (copy product index), partials _data & _card, route, sidebar, cart modal | `OrderController.php`, `views/admin/order/*`, `routes/web.php`, `layouts/app.blade.php` |
|
| 2026-07-22 | UPDATE | Order flow — create view (form detail + notes per item), store (status in_progress), auto-decrement stock via BOM, update meja jadi terisi | `OrderController.php`, `views/admin/order/create.blade.php`, `routes/web.php` |
|
| 2026-07-22 | CREATE | Order list (table AJAX) + detail show — controller method, view list & show, sidebar menu Riwayat Pesanan | `OrderController.php`, `views/admin/order/list.blade.php`, `views/admin/order/_list_data.blade.php`, `views/admin/order/show.blade.php`, `routes/web.php`, `layouts/app.blade.php` |
|
| 2026-07-22 | CREATE | Tombol complete order + cetak struk — complete (in_progress→completed, free table), receipt view (thermal 80mm) | `OrderController.php`, `views/admin/order/receipt.blade.php`, `views/admin/order/show.blade.php`, `routes/web.php` |
|
| 2026-07-23 | UPDATE | Complete order — simpan ke transactions + transaction_items | `OrderController.php`, `app/Models/Admin/Transaction.php`, `app/Models/Admin/TransactionItem.php` |
| 2026-07-23 | CREATE | Menu Transaksi — controller, views (index AJAX + show detail), route, sidebar di bawah Riwayat Pesanan | `TransactionController.php`, `views/admin/transaction/*`, `routes/web.php`, `layouts/app.blade.php` |
|
| 2026-07-25 | UPDATE | Migration diskon — tambah `start_date` + `end_date` ke `discounts` (histories udah ada) | `2026_06_27_000001_create_discounts_table.php` |
| 2026-07-25 | UPDATE | Todo — fokus ke diskon, prioritas baru | `basic-knowledge/todo.md` |
|
| 2026-07-25 | CREATE | Model Discount + relasi hasMany Product | `app/Models/Admin/Discount.php` |
| 2026-07-25 | CREATE | FormRequest Discount + validasi B.Indonesia | `app/Http/Requests/Admin/DiscountRequest.php` |
| 2026-07-25 | CREATE | DiscountController — CRUD + AJAX data() + attachProduct/detachProduct + log ke product_histories | `app/Http/Controllers/Admin/DiscountController.php` |
| 2026-07-25 | CREATE | 5 views Diskon — index, _data, create, edit, show (dengan attach/detach produk modal) | `resources/views/admin/discount/*.blade.php` |
| 2026-07-25 | UPDATE | Routes — discount resource + attach/detach routes | `routes/web.php` |
| 2026-07-25 | UPDATE | Sidebar — section Promo > Diskon | `resources/views/admin/layouts/app.blade.php` |
| 2026-07-25 | UPDATE | Product model — relasi belongsTo Discount | `app/Models/Admin/Product.php` |
| 2026-07-25 | CREATE | DiscountSeeder — 5 dummy diskon | `database/seeders/DiscountSeeder.php` |
| 2026-07-25 | UPDATE | DatabaseSeeder — panggil DiscountSeeder | `database/seeders/DatabaseSeeder.php` |
|
| 2026-07-27 | UPDATE | OrderController@complete — hitung diskon produk per item, simpan discount_* ke transaction_items + link order→transaction via order_transaction_id | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-27 | UPDATE | OrderController@show — kalo completed, load transaction_items buat snapshot harga & diskon | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-27 | UPDATE | OrderController@receipt — pake transaction_items instead of live products | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-27 | UPDATE | Order show view — tambah kolom Diskon, pake transaction_items kalo completed | `resources/views/admin/order/show.blade.php` |
| 2026-07-27 | UPDATE | Transaction show view — tambah kolom Diskon | `resources/views/admin/transaction/show.blade.php` |
| 2026-07-27 | UPDATE | Receipt view — tampilkan harga + diskon + subtotal per item dari transaction_items | `resources/views/admin/order/receipt.blade.php` |
| 2026-07-27 | UPDATE | TransactionSeeder — hitung diskon produk, simpan discount_* + link order→transaction | `database/seeders/TransactionSeeder.php` |
| 2026-07-27 | UPDATE | DiscountSeeder — link 9 produk ke 4 diskon + log ke product_histories | `database/seeders/DiscountSeeder.php` |
| 2026-07-27 | UPDATE | Tampilan produk — harga diskon di order & product list/card view (inline hitung di blade, gak ubah backend) | `resources/views/admin/order/_data.blade.php`, `_card.blade.php`, `resources/views/admin/product/_data.blade.php`, `_card.blade.php` |
| 2026-07-27 | UPDATE | Cart modal — tambah kolom Diskon + hitung subtotal pake diskon di JS | `resources/views/admin/order/index.blade.php` |
| 2026-07-27 | UPDATE | Create order — tambah kolom Diskon +hitung subtotal include diskon | `resources/views/admin/order/create.blade.php` |
| 2026-07-27 | UPDATE | Tombol Pesan — tambah data-discount-type & data-discount-value | `resources/views/admin/order/_data.blade.php`, `_card.blade.php` |
| 2026-07-27 | CREATE | Pembahasan diskon-transaksi-payment | `basic-knowledge/pembahasan-diskon-transaksi-payment.md` |
|
| 2026-07-27 | UPDATE | Order list view — total completed ambil dari transaction_items (include diskon) | `views/admin/order/_list_data.blade.php` |
| 2026-07-27 | UPDATE | Order model — relasi `transaction()` | `app/Models/Admin/Order.php` |
| 2026-07-27 | UPDATE | OrderController list + listData — eager load transaction.items | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-27 | UPDATE | Order show view — inline hitung diskon buat in_progress & completed tanpa transaction | `views/admin/order/show.blade.php` |
| 2026-07-27 | UPDATE | Pembahasan — keputusan Opsi B (Single Snapshot di transaction_items) | `basic-knowledge/pembahasan-diskon-transaksi-payment.md` |
|
| 2026-07-27 | CREATE | Migration order_voucher — pivot order↔voucher dengan snapshot | `database/migrations/2026_07_27_000001_create_order_voucher_table.php` |
| 2026-07-27 | CREATE | Model Voucher (belongsTo Company, scope active/byCode) | `app/Models/Admin/Voucher.php` |
| 2026-07-27 | CREATE | Model OrderVoucher (belongsTo Order) | `app/Models/Admin/OrderVoucher.php` |
| 2026-07-27 | UPDATE | Order model — tambah relasi `vouchers()` hasMany OrderVoucher | `app/Models/Admin/Order.php` |
| 2026-07-27 | UPDATE | OrderController — create() load vouchers aktif, store() hitung grand_total include diskon + potongan voucher | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-27 | CREATE | OrderController@checkVoucher — AJAX cek voucher (validasi, hitung potongan) | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-27 | UPDATE | OrderController@complete — transaksi terima bersih order_grand_total (udah include voucher) | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-27 | UPDATE | OrderController@receipt — load vouchers buat tampil di struk | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-27 | UPDATE | Routes — tambah POST order/check-voucher | `routes/web.php` |
| 2026-07-27 | UPDATE | View create order — input voucher + AJAX cek + modal konfirmasi tampil potongan | `resources/views/admin/order/create.blade.php` |
| 2026-07-27 | UPDATE | View show order — tampil baris voucher di tfoot | `resources/views/admin/order/show.blade.php` |
| 2026-07-27 | UPDATE | View receipt — tampil baris voucher di receipt | `resources/views/admin/order/receipt.blade.php` |
|
| 2026-07-27 | CREATE | VoucherRequest — validasi field voucher B.Indonesia | `app/Http/Requests/Admin/VoucherRequest.php` |
| 2026-07-27 | CREATE | VoucherController — CRUD + AJAX data() + destroy | `app/Http/Controllers/Admin/VoucherController.php` |
| 2026-07-27 | CREATE | 5 view Voucher — index, _data, create, edit, show (ikut pattern Diskon) | `resources/views/admin/voucher/*.blade.php` |
| 2026-07-27 | UPDATE | Routes — tambah route resource voucher + prefix admin, name admin.voucher.* | `routes/web.php` |
| 2026-07-27 | UPDATE | Sidebar admin — tambah Voucher di section Promo | `resources/views/admin/layouts/app.blade.php` |
| 2026-07-27 | CREATE | VoucherSeeder — 5 voucher (BARU10, ULTAH15, HEMAT20, NGOPI50, GRATIS01) | `database/seeders/VoucherSeeder.php` |
| 2026-07-28 | UPDATE | Voucher model — tambah $casts (datetime buat start_date/end_date) | `app/Models/Admin/Voucher.php` |
| 2026-07-28 | UPDATE | VoucherController — redirect show ke edit, log history di store/update/destroy | `app/Http/Controllers/Admin/VoucherController.php` |
| 2026-07-28 | UPDATE | Voucher _data — row clickable langsung ke edit (bukan show) | `resources/views/admin/voucher/_data.blade.php` |
| 2026-07-28 | UPDATE | DatabaseSeeder — panggil VoucherSeeder setelah DiscountSeeder | `database/seeders/DatabaseSeeder.php` |
|
| 2026-07-27 | UPDATE | Bundle _data — tambah kolom foto thumbnail (kayak produk) | `views/admin/bundle/_data.blade.php` |
| 2026-07-27 | CREATE | Bundle _card — card view buat toggle List/Card | `views/admin/bundle/_card.blade.php` |
| 2026-07-27 | UPDATE | Bundle index — tambah toggle List/Card, skeleton shimmer, modal hapus | `views/admin/bundle/index.blade.php` |
| 2026-07-27 | UPDATE | BundleController@data — dukung parameter view (list/card) | `app/Http/Controllers/Admin/BundleController.php` |
| 2026-07-27 | FIX | Duplikat @endsection + section di luar content — error 500 | `views/admin/bundle/index.blade.php` |
| 2026-07-28 | UPDATE | Bundle show — placeholder gambar pake icon gift kalo kosong | `views/admin/bundle/show.blade.php` |
| 2026-07-28 | ROUTE | Tambah route bundle.product-data | `routes/web.php` |
| 2026-07-28 | CREATE | BundleController@productData — AJAX paginated product picker | `app/Http/Controllers/Admin/BundleController.php` |
| 2026-07-28 | CREATE | Bundle _product_card — partial card + foto buat product picker | `views/admin/bundle/_product_card.blade.php` |
| 2026-07-28 | UPDATE | Bundle create/edit — Step 2 Pilih Produk jadi card grid + foto + pagination 10/page + search | `views/admin/bundle/create.blade.php`, `edit.blade.php` |
|
| 2026-07-30 | PIVOT | Implementasi pivot discount_product — migration, model, controller, view | `database/migrations/2026_07_29_000001_create_discount_product_table.php` |
| 2026-07-30 | UPDATE | Product.php — hapus relasi discount(), tambah discounts() & activeDiscount() belongsToMany via pivot | `app/Models/Admin/Product.php` |
| 2026-07-30 | UPDATE | Discount.php — ganti products() → activeProducts() belongsToMany via pivot | `app/Models/Admin/Discount.php` |
| 2026-07-30 | UPDATE | DiscountController — attach/detach nulis ke pivot, show pake activeProducts | `app/Http/Controllers/Admin/DiscountController.php` |
| 2026-07-30 | UPDATE | OrderController — baca diskon dari pivot aktif (tanpa fallback), kolom produk udah dihapus | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-30 | UPDATE | ProductController — tambah dropdown diskon di create/edit + simpan ke pivot | `app/Http/Controllers/Admin/ProductController.php` |
| 2026-07-30 | UPDATE | View produk — tambah select diskon di create & edit | `views/admin/product/create.blade.php`, `edit.blade.php` |
| 2026-07-30 | UPDATE | View diskon show — ganti $discount->products → activeProducts | `views/admin/discount/show.blade.php` |
|
| 2026-08-02 | BRANCH | Branch `feature/guest-ordering` — QR ordering halaman guest (Opsi A: pending dulu) | git |
| 2026-08-02 | CREATE | Guest/OrderController — index(menu), checkout, submit(order pending + voucher), status, checkVoucher | `app/Http/Controllers/Guest/OrderController.php` |
| 2026-08-02 | CREATE | Guest layout Bootstrap 5 mobile + 3 view (index, review, status) + partial _product_card | `resources/views/guest/*` |
| 2026-08-02 | CREATE | CSS & JS guest standalone (NexoraGuestToast, cart sessionStorage) | `public/guest/css/guest.css`, `public/guest/js/guest.js` |
| 2026-08-02 | ROUTE | Tambah route guest (menu, checkout, submit, status, check-voucher) | `routes/web.php` |
| 2026-08-02 | UPDATE | Admin/OrderController — method baru `accept()` pending→in_progress + decrement stock + meja terisi | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-08-02 | ROUTE | Tambah route admin.order.accept | `routes/web.php` |
| 2026-08-02 | UPDATE | Admin order show — tombol & modal Terima Pesanan untuk status pending | `views/admin/order/show.blade.php` |
| 2026-08-02 | FIX | Category model — tambah relasi products() (dipakai guest index) | `app/Models/Admin/Category.php` |
| 2026-08-02 | FIX | Guest review — itemsJson dibangun di controller (fix Blade @json + array_map error) | `app/Http/Controllers/Guest/OrderController.php`, `views/guest/review.blade.php` |
| 2026-08-02 | TEST | Tested: menu 200, checkout 200, submit pending + voucher, check-voucher, accept logic, status 200, admin list 200 | curl + tinker |
| 2026-08-02 | FIX | Guest PRG — checkout POST simpan session + redirect ke review GET (refresh-safe, fix "GET method not supported"), submit balikin redirect (browser pindah halaman) | `Guest/OrderController.php`, `routes/web.php` |
| 2026-08-02 | UPDATE | Guest review — total_price hidden field di-set JS ke nilai final (setelah voucher) | `views/guest/review.blade.php` |
| 2026-08-02 | FIX | Guest review & status error `Undefined variable $company` — layout guest butuh $company (navbar/hero), kirim dari controller | `Guest/OrderController.php` |
| 2026-08-04 | MOVE | Guest views pindah ke `resources/views/guest/standard/` — base template (prep multi-template). View path `guest.X` → `guest.standard.X`, route name `guest.*` tetap | `resources/views/guest/standard/*`, `app/Http/Controllers/Guest/OrderController.php` |
| 2026-08-04 | FEAT | Guest template dinamis via `.env GUEST_TEMPLATE` — helper `guestView()` resolve `guest.{template}.{view}`; config key `app.guest_template`. Verified resolve standard + semua view ada | `.env`, `.env.example`, `config/app.php`, `app/Http/Controllers/Guest/OrderController.php` |
| 2026-08-02 | FIX | Guest checkout 302 — root cause ParseError `Unclosed '['` di review.blade.php (sudah fix jam 19:39) + clear view cache | `php artisan view:clear` |
| 2026-08-02 | TEST | Review & status guest render OK (tinker, status 200) setelah fix $company | tinker |
| 2026-07-30 | UPDATE | Views — ganti semua $product->product_discount_* → activeDiscount() (5 file) | `views/admin/product/_card.blade.php`, `_data.blade.php`, `views/admin/order/_card.blade.php`, `_data.blade.php`, `show.blade.php` |
| 2026-07-30 | UPDATE | DiscountSeeder & TransactionSeeder — pake pivot bukan kolom produk | `database/seeders/DiscountSeeder.php`, `TransactionSeeder.php` |
| 2026-07-30 | SEED | DiscountProductSeeder — 9 pivot records backfill | `database/seeders/DiscountProductSeeder.php` |
| 2026-07-30 | CLEAN | Hapus product_discount_* dari fillable Product.php | `app/Models/Admin/Product.php` |
| 2026-07-30 | CLEAN | Hapus DiscountProductSeeder dari DatabaseSeeder (udah jalan sekali) | `database/seeders/DatabaseSeeder.php` |
|
| 2026-07-31 | UPDATE | StockController — nulis stock_histories pas store/update/destroy | `app/Http/Controllers/Admin/StockController.php` |
| 2026-07-31 | UPDATE | DiscountController — nulis discount_histories pas store/update/destroy | `app/Http/Controllers/Admin/DiscountController.php` |
| 2026-07-31 | UPDATE | BundleController — nulis bundle_histories pas store/update/destroy | `app/Http/Controllers/Admin/BundleController.php` |
| 2026-07-31 | CREATE | HistoryController — 6 method (stock/discount/bundle index + data) | `app/Http/Controllers/Admin/HistoryController.php` |
| 2026-07-31 | CREATE | 6 view history — stock, discount, bundle (index + _data) | `resources/views/admin/history/{stock,discount,bundle}/*.blade.php` |
| 2026-07-31 | UPDATE | Routes — tambah prefix /admin/history/ (6 route) | `routes/web.php` |
| 2026-07-31 | UPDATE | Sidebar — tambah section Riwayat (Stok, Diskon, Bundle) | `views/admin/layouts/app.blade.php` |
| 2026-07-31 | CREATE | HistoryStockSeeder — backfill stock_histories dari tabel stocks | `database/seeders/HistoryStockSeeder.php` |
| 2026-07-31 | CREATE | HistoryDiscountSeeder — backfill discount_histories dari tabel discounts | `database/seeders/HistoryDiscountSeeder.php` |
| 2026-07-31 | CREATE | HistoryBundleSeeder — backfill bundle_histories dari tabel bundles | `database/seeders/HistoryBundleSeeder.php` |
| 2026-07-31 | CREATE | Detail history — stock/discount/bundle show (info + comparison table) | `views/admin/history/{stock,discount,bundle}/show.blade.php` |
| 2026-07-31 | UPDATE | HistoryController — tambah stockShow, discountShow, bundleShow | `app/Http/Controllers/Admin/HistoryController.php` |
| 2026-07-31 | UPDATE | Routes — tambah 3 route history/{type}/{id} | `routes/web.php` |
| 2026-07-31 | UPDATE | History _data — row clickable ke detail | `views/admin/history/*/_data.blade.php` |
| 2026-07-31 | CREATE | OrderController@bundleData — AJAX pagination bundle aktif (method baru) | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-31 | ROUTE | Tambah route order/bundle-data | `routes/web.php` |
| 2026-07-31 | CREATE | _bundle_card.blade.php — partial card bundle + data-items buat cart | `resources/views/admin/order/_bundle_card.blade.php` |
| 2026-07-31 | UPDATE | order/index.blade.php — tambah tab Bundel + panel + JS handler (gak ubah existing) | `resources/views/admin/order/index.blade.php` |
| 2026-07-31 | FIX | _bundle_card — Unclosed '[' @json → pindah ke @php block | `views/admin/order/_bundle_card.blade.php` |
| 2026-07-31 | CREATE | _bundle_data.blade.php — list/table view bundle | `resources/views/admin/order/_bundle_data.blade.php` |
| 2026-07-31 | UPDATE | OrderController@bundleData — dukung view (card/list) | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-31 | UPDATE | order/index — toggle view khusus bundle (list/card) tanpa ganggu produk | `resources/views/admin/order/index.blade.php` |
| 2026-07-31 | CREATE | Migration order_bundle — pivot order↔bundle snapshot | `database/migrations/2026_07_31_000001_create_order_bundle_table.php` |
| 2026-07-31 | CREATE | Migration transaction_bundle — snapshot bundle frozen | `database/migrations/2026_07_31_000002_create_transaction_bundle_table.php` |
| 2026-07-31 | CREATE | OrderBundleSeeder — backfill bundle ke order yg cocok | `database/seeders/OrderBundleSeeder.php` |
| 2026-07-31 | CREATE | TransactionBundleSeeder — backfill ke transaksi | `database/seeders/TransactionBundleSeeder.php` |
| 2026-07-31 | CREATE | Plan bundle order | `basic-knowledge/plan-bundle-order.md` |
| 2026-07-31 | IMPL | Eksekusi plan bundle order — 1 tabel `order_bundle` (order+transaksi digabung, transaction_id nullable diisi pas complete) | `database/migrations/2026_07_31_000001_create_order_bundle_table.php` |
| 2026-07-31 | DELETE | Migration `transaction_bundle` + `TransactionBundleSeeder` — konsep masuk 1 tabel | `2026_07_31_000002...`, `TransactionBundleSeeder.php` |
| 2026-07-31 | UPDATE | OrderBundleSeeder — backfill + isi transaction_id utk order completed | `database/seeders/OrderBundleSeeder.php` |
| 2026-07-31 | UPDATE | DatabaseSeeder — hapus TransactionBundleSeeder | `database/seeders/DatabaseSeeder.php` |
| 2026-07-31 | CREATE | Model OrderBundle + relasi Order::bundles() & Transaction::bundles() | `app/Models/Admin/OrderBundle.php`, `Order.php`, `Transaction.php` |
| 2026-07-31 | UPDATE | Cart JS — bundle jadi 1 entitas (type:bundle), render isi di keranjang | `views/admin/order/index.blade.php` |
| 2026-07-31 | UPDATE | Tombol bundle — tambah data-bundle-id & data-bundle-price | `views/admin/order/_bundle_card.blade.php`, `_bundle_data.blade.php` |
| 2026-07-31 | UPDATE | OrderController@store — validasi bundles, insert order_bundle, decrement stok isi bundle via BOM | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-31 | UPDATE | OrderController@complete — subtotal include bundle, isi transaction_id di order_bundle | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-31 | UPDATE | OrderController@show & @receipt — load bundles.bundle.items.product | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-31 | UPDATE | OrderController@list & @listData — eager load bundles | `app/Http/Controllers/Admin/OrderController.php` |
| 2026-07-31 | UPDATE | create.blade.php — render item bundle + hidden inputs bundles.* | `resources/views/admin/order/create.blade.php` |
| 2026-07-31 | UPDATE | show.blade.php — baris bundle + isi (in_progress & completed), item count & grand total include bundle | `resources/views/admin/order/show.blade.php` |
| 2026-07-31 | UPDATE | receipt.blade.php — baris bundle + isi di struk | `resources/views/admin/order/receipt.blade.php` |
| 2026-07-31 | UPDATE | transaction/show.blade.php — baris bundle + isi | `resources/views/admin/transaction/show.blade.php` |
| 2026-07-31 | UPDATE | _list_data.blade.php — total completed include subtotal bundle | `views/admin/order/_list_data.blade.php` |
| 2026-07-31 | FIX | validasi store — items & bundles nullable (cart boleh isi bundle doang), wajib minimal 1 | `app/Http/Controllers/Admin/OrderController.php` |
|
| 2026-08-02 | UPDATE | HistoryController — tambah productIndex/productData/productShow + voucherIndex/voucherData/voucherShow (6 method baru) | `app/Http/Controllers/Admin/HistoryController.php` |
| 2026-08-02 | CREATE | 6 view riwayat product & voucher — index/_data/show (ikut pattern stock/discount/bundle) | `resources/views/admin/history/{product,voucher}/*.blade.php` |
| 2026-08-02 | ROUTE | Tambah 6 route history product & voucher | `routes/web.php` |
| 2026-08-02 | UPDATE | Sidebar — tambah Riwayat Produk & Riwayat Voucher di section Riwayat | `views/admin/layouts/app.blade.php` |
| 2026-08-02 | CREATE | HistoryProductSeeder — backfill product_histories (27 produk) | `database/seeders/HistoryProductSeeder.php` |
| 2026-08-02 | CREATE | HistoryVoucherSeeder — backfill voucher_histories (5 voucher, tabel awalnya kosong) | `database/seeders/HistoryVoucherSeeder.php` |
| 2026-08-02 | UPDATE | DatabaseSeeder — register HistoryProductSeeder & HistoryVoucherSeeder | `database/seeders/DatabaseSeeder.php` |
| 2026-08-02 | TEST | Riwayat product & voucher — index 200, data AJAX OK, show 200 (product_histories 36, voucher_histories 5) | curl + route:list |
| 2026-08-02 | FIX | Guest submit 302 — validasi items.*.product_id `required|string` tolak int (itemsJson @json kirim number), ganti `required|alpha_num` | `app/Http/Controllers/Guest/OrderController.php` |
| 2026-08-02 | UPDATE | Guest cart — FAB + offcanvas diganti cart bar (bottom pill, muncul dari bawah) + bottom sheet (naik dari bawah), ikut konsep bagaskara. Modal tambah item dipertahankan | `views/guest/index.blade.php`, `public/guest/css/guest.css` |
| 2026-08-02 | TEST | Guest cart baru — menu 200, JS syntax OK (node), checkout→review 200 | curl + node |
| 2026-08-02 | UPDATE | Guest menu — tambah bar status toko (Buka 10:00-22:00 WIB, hijau) + link Cek Status Pesanan ke guest.status, ikut bagaskara | `views/guest/index.blade.php`, `public/guest/css/guest.css` |
| 2026-08-02 | UPDATE | Guest menu — tambah toggle view list/card produk (guest-list-view horizontal), persist di sessionStorage, ikut bagaskara | `views/guest/index.blade.php`, `public/guest/css/guest.css` |
| 2026-08-02 | UPDATE | Guest desktop friendly — blok @media (min-width:992px): navbar/container 1200px, grid 4 kolom, hero 220px, review/status dibungkus .guest-narrow (max 720px). Mobile (<992px) gak tersentuh | `public/guest/css/guest.css`, `views/guest/review.blade.php`, `views/guest/status.blade.php` |
| 2026-08-02 | UPDATE | Guest bundle — tab kategori 'Bundle', bundle card (partial _bundle_card), cart dukung type:bundle, checkout/review/submit handle bundle (order_bundle), item chip | `Guest/OrderController.php`, `views/guest/index.blade.php`, `views/guest/partials/_bundle_card.blade.php`, `views/guest/review.blade.php`, `public/guest/css/guest.css` |
| 2026-08-02 | TEST | Guest bundle — menu tampil 8 bundle, submit bundle sukses (order #14 pending + order_bundle terisi), review render bundle | tinker |

| 2026-08-04 | FIX | Guest submit 302 — root cause: form kirim items & bundles sebagai JSON string di hidden input, tapi controller validasi expect PHP array. Tambah `json_decode` sebelum validasi + relax validasi product_id/bundle_id dari `alpha_num`/`string` ke `required` (JSON decode hasilin integer, bukan string) | `app/Http/Controllers/Guest/OrderController.php` |

## 2026-08-06

| Tipe | Deskripsi | File |
|------|-----------|------|
| CREATE | CSS & JS Theme Spicy Bites Guest | `public/guest/spicy_bites/css/spicy_bites.css`, `public/guest/spicy_bites/js/spicy_bites.js` |
| CREATE | View Template Guest Spicy Bites (Layout, Index, Review, Status, Partials) | `resources/views/guest/spicy_bites/{layouts/app, index, review, status, partials/_product_card, partials/_bundle_card}.blade.php` |
| UPDATE | Fitur View Toggle Grid/List mode + responsive CSS di template Spicy Bites (persist di sessionStorage `sb_guest_view`) | `resources/views/guest/spicy_bites/index.blade.php`, `public/guest/spicy_bites/css/spicy_bites.css` |
| FIX | Fix bug bundle 'undefined' di keranjang, perpindahan badge harga & icon api dari overlay gambar ke body card, dan pengecilan tombol tambah di List View | `resources/views/guest/spicy_bites/index.blade.php`, `resources/views/guest/spicy_bites/partials/_product_card.blade.php`, `public/guest/spicy_bites/css/spicy_bites.css` |
| FIX | Pengaktifan trigger modal dari seluruh area kartu produk, bundle items payload parser (`data-bundle-items`), serta penyesuaian alur checkout -> review -> status pesanan | `resources/views/guest/metropolis_brew/{index, partials/_bundle_card}.blade.php` |
| FIX | Perbaikan form submit `checkoutForm` (`pointerEvents = 'none'`) & pencocokan multi-key `sessionStorage` agar tombol 'Lanjut ke Pembayaran' berpindah 100% lancar ke Halaman Review | `resources/views/guest/{metropolis_brew, spicy_bites}/index.blade.php` |
| UPDATE | Set `GUEST_TEMPLATE=metropolis_brew` di file `.env` | `.env` |

## 2026-08-09

| Tipe | Deskripsi | File |
|------|-----------|------|
| CREATE | PRD COGS+HPP+Produksi v1.0 — desain terkunci (PO putus stok, raw_materials baru, stocks=semi, tracking_mode exact/coarse/bulk, packaging by order_type, loss 2 titik, rumus reference Seasonality) | `basic-knowledge/plan-cogs-hpp-produksi.md` |
| CREATE | Milestone eksekusi COGS+HPP (M0 fondasi → M6 opname), tiap fase ada deliverables+file+gate | `basic-knowledge/milestone-cogs-hpp-produksi.md` |
| CREATE | Todo checklist eksekusi persisten per milestone, utk branch deva-branch | `basic-knowledge/todo_deva_branch.md` |
| RULES | Aturan 11 — cek branch + todo file yg nyambung ke branch + konfirmasi user sebelum jalanin todo | `basic-knowledge/rule_ai.md` |
| CREATE | Arsitektur Decoupled COGS + HPP + Waste Log + History Audit Trail (V3.1 PRD & Migration Plan) | `basic-knowledge/plan-cogs-hpp-decoupled.md`, `basic-knowledge/plan-migration-cogs-hpp-decoupled.md` |
| CREATE | 8 Migrations Decoupled COGS, HPP, Waste Log & 3 Audit Trail Tables (Not migrated by AI per user instruction) | `database/migrations/2026_08_10_000001_create_cogs_raw_materials_table.php` s/d `000008_create_cogs_waste_histories_table.php` |
| CREATE | 8 Models namespace `App\Models\Admin\Keuangan` | `app/Models/Admin/Keuangan/{CogsRawMaterial, CogsRawMaterialHistory, CogsRecipe, CogsRecipeItem, CogsRecipeHistory, CogsWasteLog, CogsWasteHistory, HppFinancialReport}.php` |
| CREATE | 3 Form Requests namespace `App\Http\Requests\Admin\Keuangan` | `app/Http/Requests/Admin/Keuangan/{CogsRawMaterialRequest, CogsRecipeRequest, CogsWasteLogRequest}.php` |
| CREATE | 4 Controllers namespace `App\Http\Controllers\Admin\Keuangan` | `app/Http/Controllers/Admin/Keuangan/{CogsRawMaterialController, CogsRecipeController, CogsWasteLogController, HppReportController}.php` |
| CREATE | Views CRUD & Audit Trail namespace `resources/views/admin/keuangan` | `resources/views/admin/keuangan/{cogs-raw-material, cogs-recipe, cogs-waste, hpp-report}/*` |
| CREATE | 3 Seeders namespace `Database\Seeders\Keuangan` + registered in DatabaseSeeder | `database/seeders/Keuangan/{CogsRawMaterialSeeder, CogsRecipeSeeder, CogsWasteLogSeeder}.php`, `database/seeders/DatabaseSeeder.php` |
| UPDATE | Register admin/keuangan routes & update Sidebar sub-menu under Analitik | `routes/web.php`, `resources/views/admin/layouts/app.blade.php` |
| UPDATE | Alignment seluruh tampilan UI Keuangan (14 Blade files) menggunakan komponen native `new-kasir` (`.page-header`, `.breadcrumb-trail`, `.card`, `.card-header-flex`, `.table-modern`, `.chip-tag`, `.btn-ghost`, `.btn-primary-grad`) | `resources/views/admin/keuangan/{cogs-raw-material, cogs-recipe, cogs-waste, hpp-report}/*` |
| CREATE | Fitur Input & Penyimpanan Biaya Operasional (Gaji Karyawan, Listrik/Overhead & Catatan) pada Laporan HPP & Laba Rugi di database | `app/Http/Controllers/Admin/Keuangan/HppReportController.php`, `routes/web.php`, `resources/views/admin/keuangan/hpp-report/index.blade.php` |
| UPDATE | Tabel Rincian Penjualan & Modal COGS Per-Menu di Laporan HPP (Qty terjual, total omzet, unit COGS, total COGS, gross profit & margin %) + CogsRecipeSeeder presisi tersambung ke 36 produk dari ProductSeeder | `app/Http/Controllers/Admin/Keuangan/HppReportController.php`, `resources/views/admin/keuangan/hpp-report/index.blade.php`, `database/seeders/Keuangan/{CogsRawMaterialSeeder, CogsRecipeSeeder}.php` |
| CREATE | Fitur DEDICATED Stock Opname / Penyesuaian Stok Fisik (Modal opname, kalkulasi selisih +/- Qty, pilihan alasan & audit history `action_type = adjustment`) | `app/Http/Controllers/Admin/Keuangan/CogsRawMaterialController.php`, `routes/web.php`, `resources/views/admin/keuangan/cogs-raw-material/{index, _data}.blade.php` |
| UPDATE | Refactoring seluruh 14 tampilan Blade Keuangan untuk support Dark Mode (Nexora Theme: `var(--bg-surface)`, `var(--bg-elevated)`, `var(--text-primary)`, `var(--border-subtle)`, soft translucent badges, hapus `bg-light` / `text-dark` belang) | `resources/views/admin/keuangan/{cogs-raw-material, cogs-recipe, cogs-waste, hpp-report}/*` |
| FIX | Konversi seluruh script JavaScript di tampilan Blade Keuangan ke Vanilla JS murni (native `fetch`, `FormData`, `bootstrap.Modal`, `document.querySelector`) untuk mengeliminasi error `Uncaught ReferenceError: $ is not defined` | `resources/views/admin/keuangan/{cogs-raw-material, cogs-recipe, cogs-waste}/*` |
| CREATE | Fitur OPSIONAL "Potong Stok Bahan Mentah (Raw Stock) Otomatis" saat membuat/update stok fisik menu (Simulasi kalkulasi real-time, potongan otomatis ke `cogs_raw_materials`, audit history `action_type = production`) | `app/Http/Controllers/Admin/StockController.php`, `app/Http/Requests/Admin/StockRequest.php`, `resources/views/admin/stock/{create, edit}.blade.php` |
| UPDATE | Penyempurnaan Seeder Keuangan Presisi Akurat (`CogsRawMaterialSeeder`, `CogsRecipeSeeder` terhubung ke 36 Produk, dan `CogsWasteLogSeeder` dengan pemotongan stok otomatis & audit history) | `database/seeders/Keuangan/{CogsRawMaterialSeeder, CogsRecipeSeeder, CogsWasteLogSeeder}.php` |
| CREATE | Fitur Modal Popup "Rincian Resep & Total Pemakaian Bahan Bulan Ini" pada Laporan HPP Per-Menu (Penampilan QTY Terjual Pcs, Omzet, Takaran 1 Porsi, Harga Efektif & Kalkulasi Total Bahan Mentah Terpakai Bulan Ini) | `app/Http/Controllers/Admin/Keuangan/HppReportController.php`, `resources/views/admin/keuangan/hpp-report/index.blade.php` |
| FIX | Perbaikan duplikasi direktif `@empty` pada `hpp-report/index.blade.php` & verifikasi kompilasi Blade templates 100% lulus tanpa syntax error (`php artisan view:cache`) | `resources/views/admin/keuangan/hpp-report/index.blade.php` |
| FIX | Perbaikan Passing Variabel `$totalPaidTransactions`, `$grossMarginPercent`, `$netMarginPercent` ke View pada `HppReportController.php` (Mengeliminasi Error 500 Undefined Variable) | `app/Http/Controllers/Admin/Keuangan/HppReportController.php` |
| UPDATE | Redesign Tampilan Atas HPP Report Sesuai Contoh Gambar Reference (4 Glowing KPI Cards, Layout 2 Kolom Rincian Kalkulasi Laba Rugi & Informasi Catatan Operasional) | `resources/views/admin/keuangan/hpp-report/index.blade.php` |
| CREATE | Fitur Menu Baru "Performa Penjualan & Grafik Analitik" di bawah Section Analitik (4 KPI Cards, Bar Chart Top 10 Terlaris, Donut Chart Kategori, Line Chart Tren Harian, Tracing Penjualan Paket Bundle/Combo, & Tabel Matriks Peringkat Menu dengan Status Performa Badge) | `app/Http/Controllers/Admin/Keuangan/MenuAnalyticsController.php`, `routes/web.php`, `resources/views/admin/layouts/app.blade.php`, `resources/views/admin/keuangan/menu-analytics/index.blade.php` |
| UPDATE | Integrasi Otomatis Kalkulasi HPP & COGS Paket Bundle (`OrderBundle`) ke dalam Rumus Laporan HPP (`HppReportController.php`) (Secara otomatis menjumlahkan HPP dari resep produk-produk penyusunnya & menampilkan komposisinya pada modal Rincian Resep) | `app/Http/Controllers/Admin/Keuangan/HppReportController.php` |
| FIX | Perbaikan Missing Import Model `use App\Models\Admin\OrderBundle;` pada `HppReportController.php` (Mengeliminasi Error `Class "App\Http\Controllers\Admin\Keuangan\OrderBundle" not found`) | `app/Http/Controllers/Admin/Keuangan/HppReportController.php` |
| UPDATE | Penyempurnaan Seeder Presisi Lengkap Akurat (`CogsRawMaterialSeeder` 17 bahan mentah, `CogsRecipeSeeder` terhubung ke seluruh 36 produk, `CogsWasteLogSeeder`, `HppFinancialReportSeeder` gaji & operasional listrik/overhead, serta didaftarkan ke `DatabaseSeeder.php`) | `database/seeders/Keuangan/*`, `database/seeders/DatabaseSeeder.php` |
| FIX | Perbaikan Carbon Method Call `shortTranslatedFormat` ke `translatedFormat` pada `MenuAnalyticsController.php` (Mengeliminasi Error `Method shortTranslatedFormat does not exist`) | `app/Http/Controllers/Admin/Keuangan/MenuAnalyticsController.php` |
| FIX | Perbaikan Nama Kolom pada `HppFinancialReportSeeder.php` Sesuai Skema Migration `2026_08_10_000005_create_hpp_financial_reports_table.php` (`year`, `month`, `total_labor_cost`, `total_overhead_cost`) (Mengeliminasi Error `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'period_year'`) | `database/seeders/Keuangan/HppFinancialReportSeeder.php` |
| UPDATE | Penyesuaian Warna Grafik Analitik Sesuai Palette Nexora Theme (Warna soft eye-friendly: `--accent-1`, `--accent-2`, `--success`, `--warning`, `--danger`, tidak mencolok/ngejreng, & adaptif otomatis terhadap Dark/Light Mode) | `resources/views/admin/keuangan/menu-analytics/index.blade.php` |
| FIX | Perbaikan Syntax Error Komentar JS `# Soft Mint` ke `// Soft Mint` pada Array Palette `menu-analytics/index.blade.php` (Mengeliminasi Error `Uncaught SyntaxError: Invalid or unexpected token`) | `resources/views/admin/keuangan/menu-analytics/index.blade.php` |
| UPDATE | Redesign Tampilan Grafik Analitik Penjualan Presisi Sesuai Gambar Referensi Dashboard Inspeksi (Batang Grafik Slender Multi-Warna Bebas Tabrakan Teks, Donut Ring Modern 72% Cutout, Badges Periode, & Adaptif Otomatis Terhadap Mode Gelap & Mode Terang) | `resources/views/admin/keuangan/menu-analytics/index.blade.php` |
| UPDATE | Penyesuaian Presisi Warna Donut Ring Chart Sesuai Screenshot Zoomed Referensi (`#2563eb` Electric Royal Blue, `#6366f1` Indigo, `#06b6d4` Cyan, `#a855f7` Violet, `#34d399` Mint Green) | `resources/views/admin/keuangan/menu-analytics/index.blade.php` |
| UPDATE | Refactoring Tampilan Halaman Analitik Penjualan Presisi Menggunakan Struktur Layout & Komponen UI Native `laravel-admin` (`main.page-content`, `card card-glow`, `stat-card`, `stat-icon`, `stat-value`, `stat-label`, `.status-dot.live`) | `resources/views/admin/keuangan/menu-analytics/index.blade.php` |
| UPDATE | Penyempurnaan Styling Chart.js pada `menu-analytics` untuk Light Mode & Dark Mode (Menghilangkan border hitam Donut Chart & mengganti dengan gap warna background kartu `borderWidth: 3`, Mempertegas garis grid sumbu Y Bar Chart `#E2E8F0` / `rgba(255,255,255,0.08)`, & Teks Netral Slate `#475569` / `#94A3B8` dengan MutationObserver Tema Otomatis) | `resources/views/admin/keuangan/menu-analytics/index.blade.php` |
| UPDATE | Penerapan Eksplisit Color Palette Spesifikasi User untuk Light Mode & Dark Mode pada halaman `/admin/keuangan/menu-analytics` (`#FFFFFF` Light BG, `#1E293B` Dark Slate BG, `#2563EB` / `#3B82F6` Makanan, `#7C3AED` / `#A855F7` Minuman, `#0891B2` / `#06B6D4` Snack) | `resources/views/admin/keuangan/menu-analytics/index.blade.php` |
| UPDATE | Migration `purchase_order_items` & `purchase_receiving_items` (Ganti `stock_id` menjadi `cogs_raw_material_id` untuk menghubungkan PO langsung ke Raw Stock `cogs_raw_materials`) | `database/migrations/2026_06_29_000003_create_purchase_order_items_table.php`, `database/migrations/2026_06_29_000005_create_purchase_receiving_items_table.php` |
| UPDATE | Refactoring `PurchaseOrderController` ke Namespace `App\Http\Controllers\Admin\Keuangan\PurchaseOrderController` (Penerimaan PO meng-update `cogs_raw_materials.amount` & mencatat ke `cogs_raw_material_histories`) | `app/Http/Controllers/Admin/Keuangan/PurchaseOrderController.php`, `app/Models/Admin/PurchaseOrderItem.php`, `app/Models/Admin/PurchaseReceivingItem.php`, `app/Models/Admin/Keuangan/CogsRawMaterial.php` |
| UPDATE | Pemindahan UI Views & Routes Purchase Order ke Kelompok Keuangan (`/admin/keuangan/purchase-order`) & Form Pilihan Item Menggunakan Bahan Mentah (`cogs_raw_materials`) | `routes/web.php`, `resources/views/admin/keuangan/purchase-order/*`, `resources/views/admin/keuangan/purchase-receiving/*` |
| UPDATE | Integrasi Ringkasan Pembelian PO Bahan Mentah ke dalam Laporan Keuangan HPP (`HppReportController` & `hpp-report/index.blade.php`) | `app/Http/Controllers/Admin/Keuangan/HppReportController.php`, `resources/views/admin/keuangan/hpp-report/index.blade.php` |
| UPDATE | Pembaruan Seeder `PurchaseOrderSeeder.php` & `DatabaseSeeder.php` (Pengurutan seeder agar `CogsRawMaterialSeeder` berjalan sebelum `PurchaseOrderSeeder` & Seeding 10 PO Bahan Mentah Berhasil 100%) | `database/seeders/PurchaseOrderSeeder.php`, `database/seeders/DatabaseSeeder.php` |
| CREATE | Pembuatan Folder Khusus `basic-knowledge/deva-branch/` dan File TODO `todo.md` khusus pengerjaan `deva-branch` | `basic-knowledge/deva-branch/todo.md` |
| CREATE | Pembuatan Rule AI Workspace Customization Root di `.agents/AGENTS.md` (Mengikat AI wajib mengecek branch active, `basic-knowledge/deva-branch/todo.md`, dan melakukan `log_code.md` instan) | `.agents/AGENTS.md`, `basic-knowledge/rule_ai.md` |
| REFACTOR | Restrukturisasi Folder `basic-knowledge/` (Memindahkan seluruh file plan & todo khusus ke dalam `basic-knowledge/deva-branch/`, sehingga root `basic-knowledge/` hanya berisi `log_code.md`, `rule_ai.md`, dan folder `deva-branch/`) | `basic-knowledge/deva-branch/*` |
