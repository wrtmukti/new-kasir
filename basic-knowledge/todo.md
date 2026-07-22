# Todo — Pekerjaan Belum Selesai

> Daftar pekerjaan yang belum dikerjakan atau perlu dilanjutkan. Update setelah selesai.

---

## 🔴 TODAY (2026-07-22): PO Enhancement ✅

**Flow lengkap PO: Draft → Confirm → Ordered → Receive → Partial/Completed + Cancel + Return**

| Item | Status | Keterangan |
|------|--------|------------|
| Controller — `confirm()` | ✅ | Draft→Ordered |
| Controller — `cancel()` | ✅ | Draft/Ordered→Cancelled + alasan di `po_notes` |
| Controller — `return()` | ✅ | StockLog type 'return' |
| Controller — `store()` + `?confirm=1` | ✅ | Auto modal confirm abis create |
| Validasi received ≤ remaining | ✅ | `PurchaseReceivingRequest.php` |
| Routes — confirm/cancel/return | ✅ | 3 POST route baru |
| Show view — tombol + 3 modal | ✅ | Confirm, Cancel (alasan), Return (qty+alasan) |
| Create view — hapus "Langsung Pesan" | ✅ | Draft aja |
| Flash → NexoraToast | ✅ | index & show |

## Prioritas 1: BOM/Resep (Product↔Stock Pivot)

**Pivot `product_stock` udah jalan.** Relasi `$product->stocks` + `quantity` udah siap. Yang kurang tinggal auto-decrement stok pas transaksi.

| Item | Status | Keterangan |
|------|--------|------------|
| Tabel pivot `product_stock` | ✅ | migration `create_product_stock_table.php` |
| Relasi `Product → stocks()` | ✅ | `belongsToMany` via `product_stock` + `quantity` pivot |
| Relasi `Stock → products()` | ✅ | kebalikannya |
| Seeder pivot | ✅ | 68 relasi produk↔stock udah di-seed |
| CRUD pivot di form produk | ✅ | stepper di create/edit produk |
| **Auto-decrement stock pas transaksi** | ❌ | lookup pivot → StockLog(out) → stock_amount-- |

**Catatan:** Tanpa auto-decrement, stok gak otomatis berkurang pas transaksi.

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
