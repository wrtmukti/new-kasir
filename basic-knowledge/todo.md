# Todo — Pekerjaan Belum Selesai

> Daftar pekerjaan yang belum dikerjakan atau perlu dilanjutkan. Update setelah selesai.

---

## ✅ PIVOT DISKON — Relasi Product ↔ Discount (SELESAI 2026-07-30)

> **Tujuan Tercapai:** Normalisasi relasi product↔discount via pivot `discount_product` + timeline. OrderController baca diskon LIVE dari master discounts, ada history assignment, produk bisa gonta-ganti diskon sepanjang waktu.

### 🔴 Masalah Skrg

1. **3 kolom snapshot di `products`** (`product_discount_id/type/value`) — isinya snapshot pas attach, basi kalo master diskon diupdate
2. **Gak ada history assignment** — "produk X pas tgl Y pake diskon mana?" gak terjawab, soalnya `product_histories.history_discount` cuma angka (gak tau dari master ID diskon mana)
3. **Produk cuma bisa pake 1 diskon aktif** — betul secara logika (gak mungkin 2 diskon jalan bareng), tapi sepanjang umur produk bisa gonta-ganti diskon, dan itu gak ke record
4. **OrderController** — pas store/complete, baca `product_discount_*` langsung dari produk. Kalo value di master diskon udah berubah, produk masih pegang value lama → order kena diskon basi

### ✅ Solusi — Pivot `discount_product` + Timeline

Tabel baru:

```
discount_product
├── id              (PK)
├── company_id      (tenant scope)
├── product_id      (FK ke products)
├── discount_id     (FK ke discounts)
├── start_date      (datetime — kapan attach)
├── end_date        (datetime — null = masih aktif, terisi = kapan diganti/dilepas)
├── created_by
├── updated_by
├── delete_status
└── timestamps
```

**Konsep:** Pivot ini **sekaligus jadi history assignment** — `start_date` = attach, `end_date` = detach. Gak perlu tabel `discount_product_history` terpisah soalnya pivot udah cukup buat reconstruct timeline:

- `end_date IS NULL` = diskon yg lagi aktif skrg
- `end_date IS NOT NULL` = diskon yg udah diganti/dilepas
- Query: `WHERE start_date <= T AND (end_date IS NULL OR end_date >= T)` → tau produk pake diskon apa pas tanggal T

### 📦 Yang Berubah

#### 🔴 Migration — 2 file
1. **BARU:** `create_discount_product_table.php` — bikin pivot
2. **BARU:** `remove_discount_columns_from_products.php` — hapus `product_discount_id`, `product_discount_type`, `product_discount_value` dari `products` (STEP TERAKHIR — jalanin setelah semua data di-backfill & kode udah pake pivot)

#### 🟡 Model — 2 file
1. **`Product.php`:**
   - Hapus relasi `discount()` (belongsTo)
   - Tambah relasi `discounts()` (belongsToMany via pivot)
   - Tambah relasi `activeDiscount()` (belongsToMany + wherePivot null end_date)
2. **`Discount.php`:**
   - Tambah relasi `activeProducts()` (belongsToMany + wherePivot null end_date)

#### 🔴 Controller — 3 file
1. **`DiscountController.php`** — `attachProduct` & `detachProduct`:
   - Gak update `products.product_discount_*` lagi
   - Attach: cek pivot aktif → matikan end_date → insert pivot baru
   - Detach: set end_date + delete_status = 1 di pivot row
   - **Gak perlu nulis `product_histories`** — karna ini assignment diskon, bukan perubahan data produk
2. **`OrderController.php`** — `store` & `complete`:
   - Ganti dari `$product->product_discount_type/value` → `$product->activeDiscount()->first()` → ambil type/value dari master discounts
3. **`ProductController.php`** — `create` & `edit`:
   - Load semua diskon aktif buat dropdown di form
   - Saat store/update, simpan ke pivot (kalo ada discount_id dipilih)

#### 🟡 View — 2-3 file
1. **`product/create.blade.php`** — tambah select diskon (input-skeleton, error handling)
2. **`product/edit.blade.php`** — tambah select diskon, tampilin diskon yg lagi aktif
3. **`discount/show.blade.php`** — tabel produk (ubah query, tampilan sama)

### ✅ Step-by-step — Selesai Semua

| Step | Status |
|---|---|
| **1** — Migration `discount_product` | ✅ |
| **2** — Update `DiscountController` nulis ke pivot | ✅ |
| **3** — Update `Product.php` & `Discount.php` relasi pivot | ✅ |
| **4** — Update `OrderController` baca dari pivot aktif | ✅ |
| **5** — Backfill data (DiscountProductSeeder) | ✅ — 9 pivot |
| **6** — Migration hapus kolom dari `products` | ✅ |
| **7** — Hapus fallback + bersihin kode | ✅ |

### 🧠 Cara Query & Logika

#### "Produk sekarang pake diskon apa?"
```php
$active = $product->activeDiscount()->first();
// JOIN discount_product WHERE end_date IS NULL + delete_status = 0
// Balikin object Discount atau null
```

#### "Produk pas tanggal T pake diskon apa?" (history)
```php
$pivot = DB::table('discount_product')
    ->where('product_id', $productId)
    ->where('start_date', '<=', $tanggal)
    ->where(function($q) use ($tanggal) {
        $q->whereNull('end_date')
          ->orWhere('end_date', '>=', $tanggal);
    })
    ->where('delete_status', 0)
    ->first();
```
→ Kalo ada, tinggal `Discount::find($pivot->discount_id)`

#### "Siapa yg attach diskon A ke produk X kapan?"
```php
DB::table('discount_product')
    ->where('product_id', $productId)
    ->where('discount_id', $discountId)
    ->where('delete_status', 0)
    ->first();
// Balikin: created_by, created_at (start_date)
```

#### Ganti diskon dari A ke B:
```php
DB::transaction(function () {
    // 1. Matikan diskon A
    DB::table('discount_product')
        ->where('product_id', $productId)
        ->whereNull('end_date')
        ->update(['end_date' => now(), 'updated_by' => $user]);

    // 2. Aktifkan diskon B
    DB::table('discount_product')->insert([
        'product_id' => $productId,
        'discount_id' => $discountB->discount_id,
        'company_id' => $companyId,
        'start_date' => now(),
        'created_by' => $user,
    ]);
});
```

#### "Laporan penjualan 15 Maret — harga pas itu brp?"
```php
// LAPORAN KEUANGAN: langsung transaction_items, frozen
// Gak perlu lihat produk / pivot / history
TransactionItem::whereHas('transaction', function($q) {
    $q->where('transaction_date', 'like', '2026-03-15%');
})->get();
// Udah ada price + discount + subtotal di sini
```

### 🟢 Yang GAK Berubah

| File | Alasan |
|---|---|
| `product_histories` | Tetep rekam perubahan data produk (nama, harga, image) — **bukan** urusan assignment diskon |
| `discount_histories` | Tetep rekam perubahan value master diskon |
| `transaction_items` | Snapshot final transaksi — frozen, gak perlu diubah |
| `products` lainnya | Kolom nama, harga, image, category_id — gak disentuh |
| `ProductRequest` | Validasi produk tetep sama (tinggal tambah field discount_id optional) |

### ⚠️ Catatan Penting

1. **Fallback strategy:** Di Step 4, OrderController baca pivot dulu. Kalo gak ada (`null`), fallback ke `product_discount_*` (kolom lama). Ini biar gak broken pas migration 50% jalan. Hapus fallback di Step 7.
2. **Old value:** Kebutuhan "old value pas transaksi" udah dijawab `transaction_items`. Kebutuhan "old value pas attach diskon" — pivot sendiri udah nyimpen start_date, kalo perlu value diskon pas attach, cross ke `discount_histories`.
3. **1 diskon aktif:** Pivot gak nge-enforce ini di DB level (gak ada constraint). Enforce di `DiscountController@attachProduct` — kalo attach baru, otomatis matikan yg lama. Ini sengaja — biar gak ada constraint ribet yg bisa nge-block flow.
4. **Urutan step penting:** Jangan loncat ke Step 6 (hapus kolom) sebelum Step 5 (backfill). Data ilang kalo salah urutan.

## ✅ Diskon Produk → Transaction (Opsi B — Selesai 2026-07-27)

| Item | Status | Keterangan |
|------|--------|------------|
| `OrderController@complete` — hitung diskon per item | ✅ | percentage/nominal, simpan ke transaction_items.discount_* |
| `OrderController@complete` — link order→transaction | ✅ | `order_transaction_id` otomatis terisi |
| `OrderController@show` — snapshot utk completed | ✅ | Load transaction_items instead of live products |
| `OrderController@receipt` — snapshot | ✅ | Pake transaction_items, tampil harga+diskon+subtotal |
| Order show view — kolom diskon | ✅ | Tampil harga, diskon, subtotal |
| Transaction show view — kolom diskon | ✅ | Sama |
| Receipt view — diskon per item | ✅ | Harga, diskon, sub |
| TransactionSeeder — include diskon | ✅ | Hitung diskon + link order→transaction |
| Pembahasan | ✅ | `basic-knowledge/pembahasan-diskon-transaksi-payment.md` |

---

## ⏳ Perlu Dibahas / Dilanjutin

| # | Item | Keterangan |
|---|------|------------|
| 1 | **Dropdown pilih diskon di form produk** | Di create/edit produk, tambah select buat milih diskon dari master |
| 2 | **Diskon transaksi (voucher)** | Nanti — diskon manual pas checkout lewat voucher |
| 3 | **Payment** | Nanti — terima uang, metode bayar |
| 4 | **Auto-decrement stock pas transaksi** | BOM/Resep — lookup pivot → StockLog(out) |
| 5 | **Seeder konsistensi** | CategorySeeder pisah, loop semua company, fix company_code duplikat |

---

## ⏳ Tahap Lanjutan (Nanti)

- Voucher CRUD
- Bundle (enhancement)
- Payment
- PO Receiving flow (lanjutan)
- Auth & RBAC
- QR Ordering
- Laporan & Analitik
- KDS

---

## ✅ QR Ordering (Guest) — Selesai 2026-08-02 (Core)

| Item | Status | Keterangan |
|------|--------|------------|
| Guest/OrderController | ✅ | menu, checkout, submit (pending), status, checkVoucher |
| Guest layout + index (menu) | ✅ | Bootstrap 5 mobile, hero, kategori, search, cart |
| Guest review (note + voucher) | ✅ | flow voucher bagaskara (AJAX cek → potongan) |
| Guest status (track per meja) | ✅ | progress stepper per status |
| Submit → order pending | ✅ | gak decrement stock, nunggu kasir terima |
| Admin accept (pending→in_progress) | ✅ | + decrement stock + meja terisi |
| Route guest + admin.accept | ✅ | `guest.*`, `admin.order.accept` |

### ⏳ Belum (lanjutan QR Ordering)

| # | Item |
|---|------|
| 1 | QR code per meja (scan → /guest/menu/{table_id}) |
| 2 | Bundle di halaman guest (sekarang cuma produk) |
| 3 | Halaman landing `/` (redirect ke menu atau pilih meja) |
| 4 | Auto-refresh status page (polling AJAX) |
| 5 | Detail harga sebelum/sesudah diskon di review (sekarang cuma di menu) |
