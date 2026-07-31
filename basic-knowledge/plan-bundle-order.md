# Plan — Bundle Order (Bundle Tetap Utuh + Campur Cart)

> Dibuat: 2026-07-31
> Status: **IMPLEMENTASI SELESAI** — 1 tabel `order_bundle` (order + transaksi digabung)

---

## 🎯 Tujuan

1. **Bundle tetep 1 entitas** dari cart → order → transaksi → struk (gak dipecah sampe akhir)
2. **Bundle ikut ngurangin stok** via produk isinya (BOM) — stok akurat
3. **Cart bisa campur** bundle + produk biasa, checkout bareng

---

## 🧩 Konsep Cart (2 tipe item)

```js
cart = [
  { type: 'product', id: 'P1', name: 'Nasi Goreng', price: 25000, qty: 2 },
  { type: 'bundle',  id: 'B1', name: 'Paket Nasi Goreng Komplit', price: 40500, qty: 1,
    items: [
      { product_id: 'P1',  name: 'Nasi Goreng', qty: 1 },
      { product_id: 'P13', name: 'Es Teh',      qty: 1 },
      { product_id: 'P31', name: 'Keju Parut',  qty: 1 },
    ]
  },
]
```

---

## 📦 Database — 1 Tabel Rapi Khusus Bundle

> **Keputusan:** Gabung `order_bundle` + `transaction_bundle` jadi **satu** tabel `order_bundle`.
> Gak expand bundle ke `order_product` (gak nyentuh pivot produk).

### `order_bundle` (identitas bundle utuh — order & transaksi)

```
id, company_id
order_id        (FK ke orders)
transaction_id  (FK ke transactions — NULL pas pesan, TERISI pas complete)
bundle_id       (FK ke bundles)
bundle_name     (snapshot)
bundle_price    (snapshot)
quantity
subtotal        = bundle_price × quantity
created_by, updated_by, delete_status, timestamps
```

> Konsep: **1 baris = 1 bundle di 1 order.** Pas complete, cuma `transaction_id` yg diisi (update, gak copy ke tabel lain). Data bundle frozen di snapshot ini.

---

## 🔄 Alur

### Store (Pesan)
```
Cart (campur)
  │
  ▼
DB::transaction
  ├─ [PRODUK] Nasi Goreng ×2
  │    ├─ sync → order_product
  │    └─ BOM → ngurangin stok
  │
  └─ [BUNDEL] Paket ×1
       ├─ INSERT → order_bundle (1 baris, subtotal dihitung)
       └─ BOM → ngurangin stok semua produk isinya (lookup bundle->items)
```

> **Stok akurat:** isi bundle di-decrement lewat BOM per produk isinya (`bundle->items`), qty = item.quantity × bundle.qty. Gak ada double-count (isi bundle gak masuk order_product).

### Complete (Transaksi)
```
complete()
  ├─ transaction_items → per produk (dari order_product — produk reguler doang)
  ├─ transaction_subtotal = Σ item + Σ order_bundle.subtotal
  └─ UPDATE order_bundle SET transaction_id = <trx>
  └─ Struk:
       Paket Nasi Goreng Komplit (Paket) × 1   Rp 40.500
         ├ Nasi Goreng
         ├ Es Teh
         └ Keju Parut
```

---

## ✅ Selesai (Sudah Dibuat)

| # | Item | File |
|---|------|------|
| 1 | Migration `order_bundle` — 1 tabel (order_id + transaction_id + subtotal) | `database/migrations/2026_07_31_000001_create_order_bundle_table.php` |
| 2 | ~~Migration `transaction_bundle`~~ — DIHAPUS (konsep masuk 1 tabel) | — |
| 3 | Seeder `OrderBundleSeeder` — backfill + isi transaction_id utk yg completed | `database/seeders/OrderBundleSeeder.php` |
| 4 | ~~Seeder `TransactionBundleSeeder`~~ — DIHAPUS | — |
| 5 | Register seeder | `database/seeders/DatabaseSeeder.php` |
| 6 | Model `OrderBundle` + relasi `Order::bundles()` & `Transaction::bundles()` | `app/Models/Admin/OrderBundle.php`, `Order.php`, `Transaction.php` |
| 7 | Cart JS — bundle jadi 1 entitas (`type: bundle`) + render isi | `views/admin/order/index.blade.php` |
| 8 | Tombol bundle — data-bundle-id / data-bundle-price | `views/admin/order/_bundle_card.blade.php`, `_bundle_data.blade.php` |
| 9 | `OrderController@store` — validasi bundles + insert order_bundle + decrement stok isi | `app/Http/Controllers/Admin/OrderController.php` |
| 10 | `OrderController@complete` — subtotal bundle + isi transaction_id | `app/Http/Controllers/Admin/OrderController.php` |
| 11 | `order/create.blade.php` — render item bundle + hidden inputs | `resources/views/admin/order/create.blade.php` |
| 12 | `order/show.blade.php` — baris bundle + isi (in_progress & completed) | `resources/views/admin/order/show.blade.php` |
| 13 | `receipt.blade.php` — baris bundle + isi di struk | `resources/views/admin/order/receipt.blade.php` |
| 14 | `transaction/show.blade.php` — baris bundle + isi | `resources/views/admin/transaction/show.blade.php` |
| 15 | Order list — total include bundle | `views/admin/order/_list_data.blade.php` |

---

## 🔧 Cara Migrate Manual

> ⚠️ Migration `order_bundle` LAMA + `transaction_bundle` udah pernah jalan → **rollback dulu**, baru migrate ulang:

```bash
# 1. Rollback 2 migration bundle (yang lama)
C:/xampp812/php/php.exe artisan migrate:rollback --step=2

# 2. Migrate ulang (jalankan migration order_bundle versi baru — 1 tabel)
C:/xampp812/php/php.exe artisan migrate

# 3. Backfill data bundle existing (idempotent)
C:/xampp812/php/php.exe artisan db:seed --class=OrderBundleSeeder
```

---

## ⚠️ Catatan

- `order_bundle` = identitas bundle utuh (struk, show, laporan). Snapshot nama+harga → aman kalo master bundle diubah/dihapus
- Isi bundle (komponen) diambil dari relasi live `bundle->items` pas render — kalo mau frozen juga, tinggal tambah kolom snapshot
- Stok isi bundle di-decrement pas store (BOM), qty = item.quantity × bundle.qty
- Gak ada double-count stok & uang: bundle uangnya cuma di `order_bundle.subtotal`, gak masuk `order_product`
