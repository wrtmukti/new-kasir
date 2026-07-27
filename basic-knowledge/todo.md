# Todo — Pekerjaan Belum Selesai

> Daftar pekerjaan yang belum dikerjakan atau perlu dilanjutkan. Update setelah selesai.

---

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
