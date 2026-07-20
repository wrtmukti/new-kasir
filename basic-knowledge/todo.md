# Todo — Pekerjaan Belum Selesai

> Daftar pekerjaan yang belum dikerjakan atau perlu dilanjutkan. Update setelah selesai.

---

## Prioritas 1: Ingredients (BOM/Resep)

**Tabel `ingredients` udah ada di migration, tapi layer aplikasi belum disentuh sama sekali.**

| Item | Status | Keterangan |
|------|--------|------------|
| Model `Ingredient` | ❌ | `app/Models/Admin/Ingredient.php` — belum dibuat |
| Relasi `Product → ingredients()` | ❌ | `belongsToMany(Ingredient::class)` atau `hasMany` |
| Relasi `Stock → ingredients()` | ❌ | kebalikannya |
| Relasi `Product → stocks()` via ingredients | ❌ | `belongsToMany` biar bisa `$product->stocks` langsung |
| Seeder ingredients | ❌ | bikin resep/BOM buat 36 produk dari stock yang ada |
| CRUD ingredients | ❌ | form buat atur bahan baku per produk |
| Auto-decrement stock pas transaksi | ❌ | lookup ingredients → StockLog(out) → stock_amount-- |

**Catatan:** Tanpa ingredients, fitur transaksi gak bisa otomatis ngurangin stok.

---

## Prioritas 2: Seeder yang Kurang

| Seeder | Status |
|--------|--------|
| CategorySeeder | ❌ — masih inline di ProductSeeder tanpa `company_id` |
| SupplierSeeder | ❌ — perlu dummy supplier buat testing PO |
| TableSeeder | ❌ — perlu dummy meja buat testing transaksi |

---

## Prioritas 3: Konsistensi Seeder

- **StockSeeder & ProductSeeder** — cuma buat company[0], perlu loop ke semua company
- **Category di ProductSeeder** — `firstOrCreate` gak pake `company_id`, jadinya `null`
- **company_code duplikat** — Company 1 & 2 sama-sama `'GGB'`, riskan kalau nambah unique

---

## Prioritas 4: Tahap 2 (Transaksi)

- POS / Transaksi
- Diskon, Voucher, Bundle
- Payment
- Struk
- Auto-decrement stock via ingredients
- PO Receiving flow

---

## Prioritas 5: Tahap 3+

- Auth & RBAC
- QR Ordering
- Laporan & Analitik
- KDS (Kitchen Display System)
