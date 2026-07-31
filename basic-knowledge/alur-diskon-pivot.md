# Alur Data Diskon — Pivot `discount_product`

> Visual flow arsitektur diskon setelah normalisasi pivot. Buat lo biar paham alur database → controller → view.

---

## 📦 1. STRUKTUR TABEL

### SEBELUM (❌ — Udah dihapus)

```
┌───────────────────────────────────────────────┐
│                  products                      │
├───────────────────────────────────────────────┤
│ product_id           │ string (PK)             │
│ product_price        │ 25000                  │
│ product_discount_id  │ 1          ← FK ke discounts │
│ product_discount_type│ "percentage" ← SNAPSHOT BASI │
│ product_discount_value│ 10          ← SNAPSHOT BASI │
└───────────────────────────────────────────────┘
```

> ⚠️ Waktu attach diskon, value di-copy ke kolom produk. Pas master diskon diupdate 10% → 15%, produk masih pegang 10% — **basi**.

### SESUDAH (✅ — Sekarang)

```
┌──────────────────────┐      ┌──────────────────────────────┐
│      products        │      │      discount_product        │     ┌──────────────┐
├──────────────────────┤      ├──────────────────────────────┤     │  discounts   │
│ product_id (PK)      │      │ id              (PK)         │     ├──────────────┤
│ product_price 25000  │      │ company_id                   │     │ discount_id  │
│ product_name         │──1:N─│ product_id (FK→discounts)    │──N:1─│ discount_name│
│ (Gak ada kolom       │      │ discount_id (FK→discounts)   │     │ discount_type│
│  diskon lagi)        │      │ start_date  datetime         │     │ discount_val │
└──────────────────────┘      │ end_date    datetime (null)  │     └──────────────┘
                              │ created_by                   │
                              │ delete_status tinyint(0)     │
                              │ created_at                   │
                              │ updated_at                   │
                              └──────────────────────────────┘
```

---

## 🎯 2. KONSEP PIVOT + TIMELINE

```
┌───────────────────────────────────────────────────────────────┐
│                       discount_product                        │
├───────┬────────────┬─────────────┬─────────────────┬──────────┤
│  id   │ product_id │ discount_id │   start_date    │ end_date │
├───────┼────────────┼─────────────┼─────────────────┼──────────┤
│  1    │ PROD-001   │ 1           │ 2026-07-27 10:00│ 2026-07-30│ ← LAMPAU
│  2    │ PROD-001   │ 2           │ 2026-07-30 14:00│ null     │ ← AKTIF ✅
│  3    │ PROD-002   │ 1           │ 2026-07-27 10:00│ null     │ ← AKTIF ✅
│  4    │ PROD-003   │ 3           │ 2026-07-30 14:00│ null     │ ← AKTIF ✅
└───────┴────────────┴─────────────┴─────────────────┴──────────┘
```

| Value `end_date` | Artinya |
|-----------------|---------|
| `null` | Masih aktif — produk pake diskon ini SEKARANG |
| `2026-07-30` | Udah diganti/dilepas — history |

### Query aktif:
```sql
SELECT * FROM discount_product
WHERE product_id = 'PROD-001'
  AND end_date IS NULL
  AND delete_status = 0
```

---

## 🔄 3. ALUR ADMIN ATTACH / GANTI / LEPAS DISKON

### 3a. Attach Diskon ke Produk

```
┌───────────────────────────────────────────────────────────┐
│  Admin di halaman Diskon → klik "Hubungkan ke Produk"     │
└───────────────────────┬───────────────────────────────────┘
                        │
                        ▼
           DiscountController@attachProduct()
                        │
                        ├─ 1. Cek pivot aktif:
                        │      SELECT FROM discount_product
                        │      WHERE product_id = X AND end_date IS NULL
                        │
                        ├─ [KETEMU?] → UPDATE end_date = now()
                        │      (matikan yg lama)
                        │
                        ├─ 2. INSERT discount_product baru:
                        │      product_id  = X
                        │      discount_id = Y
                        │      start_date  = now()
                        │      end_date    = null ← aktif
                        │
                        └─ 3. INSERT product_histories:
                               action_type = 'attach_discount'
```

### 3b. Ganti Diskon (dari A ke B)

```
┌───────────────────────────────────────────────────────────┐
│  Admin edit produk → ganti dropdown Diskon A → Diskon B   │
└───────────────────────┬───────────────────────────────────┘
                        │
                        ▼
              ProductController@update()
                        │
                        ├─ Apakah $new != $old?
                        │    └─ [SAMA] → skip (gak ngapa-ngapain)
                        │
                        ├─ 1. UPDATE discount_product
                        │      SET end_date = now()
                        │      WHERE product_id = X AND end_date IS NULL
                        │      (matikan diskon A)
                        │
                        ├─ 2. INSERT discount_product baru
                        │      discount_id = B, start_date = now()
                        │      (aktifkan diskon B)
                        │
                        └─ Selesai — produk skrg pake diskon B
```

### 3c. Lepas Diskon (tanpa ganti)

```
┌───────────────────────────────────────────────────────────┐
│  Admin di Diskon → klik "Lepaskan"                        │
└───────────────────────┬───────────────────────────────────┘
                        │
                        ▼
            DiscountController@detachProduct()
                        │
                        ├─ UPDATE discount_product
                        │    SET end_date = now(),
                        │        delete_status = 1
                        │    WHERE product_id = X
                        │      AND end_date IS NULL
                        │
                        └─ INSERT product_histories
                               action_type = 'detach_discount'
```

---

## 🛒 4. ALUR ORDER → TRANSACTION (PALING PENTING)

### 4a. Halaman Order — Display Produk

```
┌───────────────────────────────────────────────────────────────────┐
│  Browser: /admin/order                                             │
│                                                                   │
│  ┌──────────┐  ┌──────────┐                                       │
│  │ Nasi Goreng│  │ Ayam Bakar│                                     │
│  │ Rp 25.000  │  │ Rp 35.000 │                                     │
│  │ [ -10% ]   │  │ [ -5% ]   │    ← diskon dari pivot (LIVE)      │
│  │ [PESAN]    │  │ [PESAN]   │                                     │
│  └──────────┘  └──────────┘                                       │
└───────────────────────────┬───────────────────────────────────────┘
                            │
                            ▼
               ProductController@data()
                            │
                            ├─ products  ← SELECT * FROM products
                            ├─ activeDiscount ← eager load pivot + discounts
                            │
                            ▼
                    View mengirim data-attribute:
                    data-discount-type="percentage"
                    data-discount-value="10"
```

### 4b. Pilih Produk → Cart (JS)

```
┌───────────────────────────────────────────────────────────┐
│  User klik [PESAN]                                        │
└───────────────────────────┬───────────────────────────────┘
                            │
                            ▼
                    JavaScript membaca:
                    btn.dataset.discountType   // "percentage"
                    btn.dataset.discountValue  // 10
                            │
                            ├─ Hitung subtotal di cart
                            │    discAmt = 25000 × 10/100 = 2500
                            │    subtotal = (25000 - 2500) × 2 = 45000
                            │
                            └─ Cart modal tampil:
                               ┌──────────────────────┐
                               │ Nasi Goreng × 2      │
                               │ Harga: 25000         │
                               │ Diskon: -10% (2500)  │ ← dari data-attribute
                               │ Sub: Rp 45.000       │
                               └──────────────────────┘
```

### 4c. Simpan Order — `OrderController@store()`

```
┌───────────────────────────────────────────────────────────┐
│  User klik "Buat Pesanan"                                 │
└───────────────────────────┬───────────────────────────────┘
                            │
                            ▼
                    Foreach item di cart:
                            │
                            ├─ Product::find(product_id)
                            ├─ $activeDisc = Product->activeDiscount()->first()
                            │       ↓
                            │   SELECT * FROM discount_product
                            │   WHERE product_id = X AND end_date IS NULL
                            │       ↓
                            │   JOIN discounts → ambil discount_type, discount_value
                            │
                            ├─ Hitung diskon (LIVE — kalo admin ganti diskon
                            │   antara waktu pilih & checkout, value terbaru!)
                            │
                            └─ Simpan ke order_product (pivot order↔product)
                                   ⚠️ BELUM ADA SNAPSHOT — ini next step
```

### 4d. Selesaikan Order — `OrderController@complete()`

```
┌───────────────────────────────────────────────────────────┐
│  Admin klik "Selesaikan Pesanan"                          │
└───────────────────────────┬───────────────────────────────┘
                            │
                            ▼
                    DB::transaction():
                            │
                            ├─ 1. Foreach produk di order:
                            │       $activeDisc = activeDiscount() → LIVE
                            │       hitung ulang diskon
                            │       subtotal = (price - discAmount) × qty
                            │
                            ├─ 2. INSERT transaction:
                            │       transaction_subtotal    = total
                            │       transaction_grand_total = total - voucher
                            │       transaction_status      = 'success'
                            │
                            ├─ 3. INSERT ke transaction_items (FROZEN!):
                            │       product_name    = snapshot nama produk
                            │       price           = snapshot harga
                            │       discount_type   = snapshot tipe diskon
                            │       discount_value  = snapshot value diskon
                            │       discount_amount = snapshot hasil hitung
                            │       subtotal        = snapshot subtotal
                            │       qty             = jumlah
                            │
                            ├─ 4. UPDATE order → completed
                            │       order_status = 'completed'
                            │       order_transaction_id = transaction.id
                            │
                            └─ 5. UPDATE meja → tersedia (free table)
```

**KENAPA FROZEN?**
```
┌────────────────────────────────────────────────────────┐
│ LAPORAN KEUANGAN TIDAK BOLEH BERUBAH                   │
│                                                        │
│ Transaksi tgl 1 Juli: diskon 10% = Rp 2.500            │
│  ↓                                                      │
│ Master diskon skrg udah 15%                            │
│  ↓                                                      │
│ Laporan harus tetap: Rp 2.500 — bukan Rp 3.750         │
│  ↓                                                      │
│ Makanya transaction_items FROZEN — snapshot permanent  │
└────────────────────────────────────────────────────────┘
```

---

## 👁️ 5. ALUR VIEW (HALAMAN)

### Halaman Order — `/admin/order`

```
┌───────────────────────────────────────────────────────────────┐
│  ProductController@data()                                     │
│    ↓                                                          │
│  products: SELECT * FROM products                            │
│    + activeDiscount (eager loaded via pivot)                  │
│    ↓                                                          │
│  View: _data.blade.php / _card.blade.php                     │
│    ↓                                                          │
│  Loop $products:                                             │
│    @php                                                      │
│      $activeDisc = $product->activeDiscount()->first()       │
│      $discType = $activeDisc?->discount_type                 │
│      $discVal = $activeDisc?->discount_value ?? 0            │
│    @endphp                                                   │
│    ↓                                                         │
│  Display:                                                    │
│    - Harga coret (kalo ada diskon)                           │
│    - Harga setelah diskon                                    │
│    - Badge "-10%"                                            │
│    - Tombol data-discount-type/value ← dikirim ke cart       │
└───────────────────────────────────────────────────────────────┘
```

### Detail Pesanan — `/admin/order/{id}`

```
┌───────────────────────────────────────────────────────────────┐
│  OrderController@show()                                       │
│    ↓                                                          │
│  CEK: order_status                                            │
│    │                                                          │
│    ├─ [in_progress] → Baca pivot (LIVE)                      │
│    │    $activeDisc = $product->activeDiscount()->first()     │
│    │    Hitung subtotal di view (sementara)                   │
│    │    ↓                                                     │
│    │  Tampil: harga, diskon -, subtotal (bisa berubah)        │
│    │                                                          │
│    └─ [completed] → Baca transaction_items (FROZEN)          │
│         $item->price                                          │
│         $item->discount_amount                                │
│         $item->subtotal                                       │
│         ↓                                                     │
│       Tampil: snapshot yang frozen (AKURAT)                   │
└───────────────────────────────────────────────────────────────┘
```

### Detail Diskon — `/admin/discount/{id}`

```
┌───────────────────────────────────────────────────────────────┐
│  DiscountController@show()                                    │
│    ↓                                                          │
│  $discount->load('activeProducts')                            │
│    ↓                                                          │
│  View:                                                        │
│    - Info diskon (type, value, periode)                       │
│    - Daftar produk yang SEDANG pake diskon ini                │
│      (activeProducts — end_date IS NULL)                      │
│    ↓                                                          │
│  Modal attach:                                                │
│    - Dropdown produk yang TERSEDIA                            │
│    - Produk yg udah punya diskon aktif → disabled             │
└───────────────────────────────────────────────────────────────┘
```

### Form Produk — `/admin/product/create` & `/edit`

```
┌───────────────────────────────────────────────────────────────┐
│  ProductController@create() / @edit()                         │
│    ↓                                                          │
│  $discounts = semua diskon aktif                             │
│    ↓                                                          │
│  View: dropdown <select name="discount_id">                   │
│    ↓                                                          │
│  Store:                                                       │
│    if ($request->filled('discount_id'))                       │
│      DB::table('discount_product')->insert([...])             │
│    ↓                                                          │
│  Update:                                                      │
│    if ($newDiscountId != $oldDiscountId)                      │
│      − Matikan pivot lama (end_date = now)                    │
│      + Insert pivot baru                                      │
└───────────────────────────────────────────────────────────────┘
```

---

## 📋 6. DATABASE SEEDERS

```
DatabaseSeeder
  │
  ├── 1. CompanySeeder         → 3 company
  ├── 2. SupplierSeeder        → 5 supplier
  ├── 3. TableSeeder           → 15 meja
  ├── 4. StockSeeder           → 7 stok
  ├── 5. PurchaseOrderSeeder   → 10 PO
  ├── 6. ProductSeeder         → 36 produk + pivot product_stock
  ├── 7. BundleSeeder          → 8 bundle
  ├── 8. DiscountSeeder        → 5 diskon + INSERT ke pivot discount_product
  ├── 9. VoucherSeeder         → 5 voucher
  ├── 10. OrderSeeder          → beberapa order
  └── 11. TransactionSeeder    → transaction dari order completed
                              (baca pivot activeDiscount)
```

**Yang berubah:**
| Seeder | Dulu | Sekarang |
|--------|------|----------|
| `DiscountSeeder` | `$product->update([product_discount_*])` | `DB::table('discount_product')->insert([...])` |
| `TransactionSeeder` | `$product->product_discount_type` | `$product->activeDiscount()->first()` |

---

## 🧪 7. CARA QUERY

### "Produk skrg pake diskon apa?"
```php
$activeDisc = $product->activeDiscount()->first();
// atau
$activeDisc = Product::with('activeDiscount')->find($id)->activeDiscount->first();
```

Hasil: object `Discount` atau `null`

### "Produk pas tgl 15 Maret 2026 pake diskon apa?"
```php
$pivot = DB::table('discount_product')
    ->where('product_id', $productId)
    ->where('start_date', '<=', '2026-03-15 23:59:59')
    ->where(function($q) {
        $q->whereNull('end_date')
          ->orWhere('end_date', '>=', '2026-03-15 00:00:00');
    })
    ->where('delete_status', 0)
    ->first();

$discount = $pivot ? Discount::find($pivot->discount_id) : null;
```

### "Semua produk yg lagi pake diskon X?"
```php
$discount->activeProducts; // belongsToMany dengan wherePivot('end_date', null)
```

### "History gonta-ganti diskon produk A?"
```php
DB::table('discount_product')
    ->where('product_id', 'PROD-001')
    ->where('delete_status', 0)
    ->orderBy('start_date')
    ->get();
// Balikin: diskon 1 (27 Jul → 30 Jul), diskon 2 (30 Jul → sekarang)
```

---

## 🎯 8. INTISARI — 3 HUKUMAN

| Hukum | Artinya | Implementasi |
|-------|---------|-------------|
| **1. Pivot aktif** | `end_date IS NULL` + `delete_status = 0` | Semua baca diskon pake kondisi ini |
| **2. Order baca LIVE** | Pas store/complete, baca dari master discounts via pivot, bukan snapshot produk | `$product->activeDiscount()->first()` |
| **3. Transaksi FROZEN** | Begitu complete, snapshot ke `transaction_items` — gak berubah | Laporan keuangan akurat selamanya |

```
┌──────────────────────────────────────────────────────────────────┐
│                                                                  │
│   FLOW RINGKAS:                                                  │
│                                                                  │
│   Master Discounts (LIVE)                                        │
│        │                                                         │
│        ▼ via pivot activeDiscount()                              │
│   ┌──────────────┐                                               │
│   │ Halaman Order │── data-attribute → Cart JS ──→ store()       │
│   └──────────────┘                                               │
│        │                                                         │
│        ▼ pivot activeDiscount() (LIVE — nilai terbaru)           │
│   ┌──────────────┐                                               │
│   │ complete()   │── INSERT transaction_items (FROZEN)           │
│   └──────────────┘                                               │
│        │                                                         │
│        ▼ transaction_items (FROZEN — gak berubah)               │
│   ┌──────────────┐                                               │
│   │ Laporan      │                                               │
│   └──────────────┘                                               │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘
```

---

## 🏁 9. YANG UDAH & BELUM

### ✅ Udah selesai
- Pivot `discount_product` + migration
- Model relasi `activeDiscount()` & `activeProducts()`
- `DiscountController` — attach/detach ke pivot
- `OrderController` — baca dari pivot (store + complete)
- `ProductController` — dropdown diskon di form
- Semua view — ganti `$product->product_discount_*` → `activeDiscount()`
- Seeders — backfill + pake pivot
- Kolom `product_discount_*` dihapus dari DB

### ⏳ Next
- **Eager loading** — biar gak N+1 query di `ProductController@data`
- **Checkout flow** — payment setelah transaction
- **`order_product` snapshot** — tambah kolom price/discount/subtotal biar order history akurat
