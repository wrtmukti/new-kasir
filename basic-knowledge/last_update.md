# Last Update — State Aplikasi (2026-07-17)

> File ini berisi snapshot terbaru kode & state proyek. Dibaca tiap mulai sesi biar konteks nyambung.

---

## Ringkasan

Proyek masih di **Prioritas 1 — Core (Master Data Tahap 1)**. Dari 7 fitur master data, baru 2 yang selesai CRUD: Company & Stock. Product & Category model doang (Controller & View belum).

## Selesai (Sudah Jalan)

### ✅ Company (SysAdmin)
| Komponen | File |
|----------|------|
| **Controller** | `SysAdmin/CompanyController.php` — full CRUD + `data()` utk AJAX |
| **Model** | `SysAdmin/Company.php` — ULID PK, relasi ke Stock/Product/Category |
| **Routes** | prefix `sys_admin/`, name `sys_admin.company.*`, route resource + `/data` |
| **Views** | `index`, `_data` (AJAX partial), `create`, `edit`, `show` |
| **Seeder** | `CompanySeeder.php` — 3 dummy |
| **Form Request** | ❌ — masih pake validate() di controller (Aturan 7 blom diterapkan) |
| **Nav active** | `@php $activeMenu = 'company' @endphp` |
| **Sidebar menu** | ✅ — via `sys_admin/layouts/app.blade.php` |

### ✅ Stock (Admin)
| Komponen | File |
|----------|------|
| **Controller** | `Admin/StockController.php` — full CRUD + `data()` utk AJAX |
| **Model** | `Admin/Stock.php` — relasi belongsTo Company |
| **Routes** | prefix `admin/`, name `admin.stock.*`, route resource + `/data` |
| **Views** | `index`, `_data` (AJAX partial), `create`, `edit`, `show` |
| **Seeder** | `StockSeeder.php` — 7 dummy |
| **Form Request** | ❌ |
| **Nav active** | `@php $activeMenu = 'stock' @endphp` |

### ✅ AJAX Table Pattern (Company & Stock)
- **Per-page filter** (10/50/100)
- **Skeleton shimmer** min 400ms loading — skeleton per baris dlm `<tbody>`, header tetap muncul
- **Delete via AJAX** — `btn-delete` data-url, confirm, fetch DELETE
- **Pagination** — custom `vendor/pagination/modern.blade.php` via `data-page`
- **Toast** — `NexoraToast()` sukses/error
- **Flash session fallback** — toast lewat `@if(session('success'))`

### ✅ Models (Tanpa Controller/View)
| Model | File | Notes |
|-------|------|-------|
| `Product` | `Admin/Product.php` | field `product_discount_id` (bukan discount_id) |
| `Category` | `Admin/Category.php` | standard CRUD fields |

### ✅ Layouts & Template
| Layout | Route prefix | Sidebar |
|--------|-------------|---------|
| `sys_admin/layouts/app.blade.php` | `/sys_admin/` | Stok Bahan, Kategori, Produk, Pelanggan, Transaksi, Analitik |
| `admin/layouts/app.blade.php` | `/admin/` | Stok Bahan, Kategori, Produk, Pelanggan, Transaksi, Analitik |

> **Catatan:** Kedua layout identik! SysAdmin layout sidebar isinya menu Admin (Stock). Ini perlu dirapikan nanti — SysAdmin harusnya menu Company/User/Tenant management.

### ✅ Routes (`routes/web.php`)
```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('stock/data', [StockController::class, 'data'])->name('stock.data');
    Route::resource('stock', StockController::class);
});

Route::prefix('sys_admin')->name('sys_admin.')->group(function () {
    Route::get('company/data', [CompanyController::class, 'data'])->name('company.data');
    Route::resource('company', CompanyController::class);
});
```

### ✅ Migrations
- **Total:** 33 migration files (30 sukses dijalankan)
- **Semua tabel PRD Core** sudah ada migrationnya: companies, categories, products, product_histories, stocks, stock_histories, ingredients, stock_logs, tables, orders, order_product, transactions, transaction_items, payments, discounts, discount_histories, vouchers, voucher_histories, transaction_voucher, customers, suppliers, purchase_orders, purchase_order_items, purchase_receivings, purchase_receiving_items, bundles, bundle_items, bundle_histories + users, cache, jobs
- **Files .md di folder migrations:** `arsitektur-diskon-voucher-snapshot.md` & `diskon.md` — bukan migration, perlu dipindah

## Belum Dikerjakan

### ❌ Form Request
Aturan 7 mewajibkan Form Request untuk validasi, tapi Company & Stock masih pakai `validate()` langsung di controller.

### ❌ CRUD Belum Dibuat
Dari PRD Core Tahap 1, berikut yg belum dibuat MVC-nya:
1. **Supplier** — model? migration✅
2. **Category** — model✅ (Controller & View❌)
3. **Product** — model✅ (Controller & View❌)
4. **Table** — migration✅
5. **Customer** — migration✅

### ❌ Tahap 2 (Transaksi)
Semua fitur transaksi belum disentuh: POS, Diskon, Payment, Voucher, Bundle, BOM/Resep, PO+Receiving, Auto-decrement, Struk.

### ❌ Tahap 3 (QR Ordering)
Belum.

### ❌ Prioritas 1.5 & 2
Auth, RBAC, SaaS, Laporan, KDS — semuanya belum.

## Arsitektur yang Dipakai

1. **Multi-tenant:** `company_id` string nullable, logical scoping (no middleware/global scope)
2. **Soft delete:** `delete_status` tinyInteger (0/1)
3. **Folder structure:** View/Controller/Model dipisah per layer: `SysAdmin/`, `Admin/`, `Guest/`
4. **SCD Type 2:** Setiap master punya history table (product_histories, stock_histories, dll)
5. **Database:** SQLite (file `database/database.sqlite`)
6. **Template admin:** `resources/views/docs/` sebagai referensi class CSS, layout dibuat sendiri
7. **Dark mode:** default, ada toggle di topbar

## Aturan Yg Wajib Diikuti (dari rule_ai.md)

| Aturan | Detail |
|--------|--------|
| 3 | Template referensi dari `docs/`. Layout & view bikin sendiri. Jangan extends file docs. |
| 5 | Auth belakangan. Semua route open. |
| 6 | Folder layer: Admin/Controller, SysAdmin/Controller, Guest/Controller |
| 7 | Form wajib input-skeleton, btn-loading, min 400ms. Table AJAX + skeleton shimmer. Validasi via Form Request + error B.Indonesia. |
| 8 | Notifikasi pakai NexoraToast(). Gak pakai session flash alert. |

## Catatan Penting

- `product_discount_id` di products migration (bukan `discount_id`) — perhatikan saat buat controller Product
- File `.md` ada di folder `database/migrations/` — perlu dipindah ke `basic-knowledge/`
- Kedua layout (`sys_admin/layouts/app.blade.php` & `admin/layouts/app.blade.php`) isinya **identik** — duplikasi
- Sidebar `sys_admin` masih nunjuk route `admin.stock.*` — perlu dibenerin
