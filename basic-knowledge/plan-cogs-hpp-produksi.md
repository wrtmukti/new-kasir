# PRD — COGS + HPP + Produksi (v1.0 FINAL)

> Status: **DESAIN TERKUNCI** — semua keputusan disepakati. Belum ada kode.
> Tanggal: 2026-08-09
> Referensi: `C:\Users\user\Desktop\dasar_cogs dan hpp\Seasonality-System-main\Seasonality-System-main` (rumus & struktur pustaka implementasi)

---

## TUJUAN

Menambah layer perhitungan **modal / pemasukan / pengeluaran / untung** di atas kasir yang sudah 70-90% jalan — TANPA membongkar inti yang berfungsi.

Alur produksi restoran 4 tingkat:

```
LEVEL 4 (paket)  : bundle            — existing
LEVEL 3 (menu)   : products          — existing
LEVEL 2 (semi)   : stocks EXISTING   — bahan siap pakai menu (di-famili "semi")
LEVEL 1 (mentah) : raw_materials     — BARU (beli PO, masuk manual)
```

> ⚠️ **Perubahan konsep penting:** Tabel `stocks` yang SUDAH ADA sekarang dianggap **SEMI** (bahan yang dipakai menu langsung). Lapisan bawahnya (BAHAN MENTAH) adalah tabel **baru** `raw_materials`. Ini keputusan final.

---

## PRINSIP KUNCI (terkunci)

1. **PO = tracker saja** — putus total dari stok. PO cuma "pesan apa / dateng belum". TIDAK menambah stock.
2. **Stok mentah masuk MANUAL** — user setelah PO dateng cek fisik, baru isi.
3. **Satu bahan = 1 `tracking_mode`** (exact/coarse/bulk) — user pilih. Anti dobel-hitungan.
   - `exact` → masuk resep/semi, auto hitung COGS per menu
   - `coarse` → perkiraan gram di resep, masuk COGS + selisih terlihat
   - `bulk` → dihitung per periode (mis. minyak 50L/minggu), masahan laporan pengeluaran
4. **Packaging (cup/box) = stok mentah biasa**, decrement saat order **take_away/delivery**; dine_in tidak.
   (order_type sudah ada di sistem)
5. **Loss/waste dinom 2 titik** (mentah→semi, semi→menu) — tidak pernah 100%.
6. **Cost semi & menu DIALAH OTOMATIS** dari resep (never manual).
7. **Snapshot**: cost menu di-frozen ke `transaction_items.cost` saat jual (SCD).
8. **Multi-tenant**: semua tabel baru bawa `company_id`.
9. **Laporan laba**: omzet − COGS − pengeluaran bulk − gaji − overhead = laba bersih.

---

## REFERENSI RUMUS DARI SEASONALITY (dipakai/copy)

```
Dari app Seasonality (folrum):

INGREDIENT (bahan menerima "bahan"):
  yield          = 100 − loss%
  price_after_loss = base_price ÷ (yield ÷ 100)
  price_per_gram = price_after_loss ÷ 1000  (aktif hanya untuk kg/liter; else ÷1)

RECIPE_ITEM
  cost = qty_grams × price_per_gram

RECIPE (menu)
  total_cost  = Σ item costs
  selling_price = total_cost ÷ (target_fc ÷ 100)   ← food cost target
  food_cost%  = total_cost ÷ selling_price × 100
  margin%     = 100 − food_cost%

HPP REPORT (bulanan)
  raw  = Σ recipe.total_cost
  labor = Σ hpp_employees.salary
  overhead = Σ hpp_transactions (overhead) per bulan
  total_hpp = raw + labor + overhead
  hpp_per_menu = total_hpp ÷ total_menus_sold
```

> Table refleksi di Seasonality: `ingredients`, `recipes`, `recipe_items`, `hpp_employees` / `hpp_categories` / `hpp_transactions` / `hpp_reports`. Kita ADAPTASIKAN ke skema new-kasir (company_id bukan user_id, reset-per-new keduduk).

---

## SKEMA TABEL (FINAL)

### A. `raw_materials` — BAHAN MENTAH (BARU)

```
id, company_id
name, code, slug, description
unit            // kg, liter, pcs, butir, ml, gr, ...
amount          // jumlah stok saat ini
price           // harga per unit (masuk manual / ref PO)
loss_percent    // loss saat JADI semi (trim/potong)
yield_percent   // 100 - loss
price_per_unit  // harga ÷ yield ÷ divider  (harga efektif)
tracking_mode   // exact | coarse | bulk    (user pilih)
reference_code  // referensi default saat masuk stok (opsional, bisa PO-xxx)
delete_status, created_by, updated_by, timestamps
```

### B. `stocks` — SEMI (EXISTING, tutup dengan kolom baru)

```
(stock_id, company_id, ...nama/unit/amount/price ...)

+ KOLOM BARU:

  stock_loss_percent  // loss pas dijadikan bahan menu
  yield_percent       // 100 - loss
  price_per_unit      // harga per satuan — hasil hitung dari resep produksi
```

### C. `produced_recipes` — resep PRODUKSI (BARU)

1 baris = 1 bahan penyusun semi. Pembuat dari mentah DAN semi lainnya (nested).

```
id, company_id
produced_id     FK → stocks (semi)
source_material FK → raw_materials (nullable)  // mentah
source_semi_id  FK → stocks (nullable)         // semi lain
quantity        // jumlah per SATU unit parent
```
Validasi: minimal satu dari `source_material_id` / `source_semi_id` terisi.

### D. `product_stock` — resep MENU (existing, modifikasi minimal)

```
product_id       FK → products (menu)
raw_id           FK → raw_materials (nullable)   // untuk menu jual-langsung (Es Botol)
```

> `stocks` (semi) tetap sebagai sumber utama BOM menu — TIDAK dirubah. Kolom baru `raw_id` hanya untuk menu yang dan_field langsung dari bahan mentah. Existing data tidak berubah.

### E. `stock_logs` (existing, dipake lebih banyak)

```
reference_type  + 'production'     (semi diproduksi)
                + 'ordering'       (mentah masuk manual)
                + 'sale'           (menu jual → decrement semi)
                + 'cooking_usage'  (mode coarse; pemakaian masak manual)
                + 'bulk_period'    (mode bulk; beli 50L minggu = biaya periode)
```

### F. `transaction_items` + `transaction` — KOYEN Snapshot BARU

```
transaction_items : + cost (COGS eksak+coarse)     — frozen saat jual
                  : + packaging_cost (per item yg pakai pack) — by order_type 0/ harga
transaction       : optional lajur bulk_amount untuk periode (atau hpp_transactions)
```

### G. HPP — 4 tabel BARU (mirip Seasonality, beda: company_id)

```
hpp_employees    : name, position, salary
hpp_categories   : name, type (overhead/license)
hpp_transactions : category_type (raw_material, bulk, labor, overhead), desc, amount, year, month
hpp_reports      : year, month, total_* , hpp_per_menu   (1/company/month)
```

---

## RUMUS INTI (rolling up)

```
MENTAH:
  yield          = 100 − loss%
  price_per_unit = (harga ÷ (yield÷100)) ÷ unit_divider   (div=1000 utk kg/l)

SEMI (sauce, porsi,...):
  produced_cost = Σ( produced_prices.qty × price_per_unit anak )
  price_per_unit = produced_cost ÷ (yield ÷ 100)

MENU:
  product_cost = Σ(product_stock.qty × price_per_unit semi)
                [+ raw langsung bila `raw_id` set]

COGS transaksi = Σ transaction_items.cost     (frozen, exact+coarse)
COGS bulan     = Σ per bulan di trans_page/teoritik
BULK periode   = Σ hpp_transactions(category=bulk) — bukan COGS menu, biaya operasional

FOOD COST % = product_cost ÷ harga_jual × 100
```

---

## ALUR LENGKAP (list)

### 1. PO (existing, dimodifikasi — Cut)
```
PO → receiving → update PO status (tracking : ordered→d)
    → TIDAK menyentuh stocks. TIDAK otomatis.
```

### 2. Stok mentah — manual masuk (baru)
```
user form: bahan, qty, harga, mode(exact/coarse/bulk), refPO(optional0)
  → raw_materials.amount += qty
  → stock_logs(type=in, ref=PO..., type=purchase_manual)
```

### 3. Proding isi — produce semi (baru)
```
user isi "produksi 5L chicken sauce"
  → produced_prices (per liter) → hitung kebutuhan → raw.amount − bahan
  → stock_logs(type=out, ref=production, cost=per harga bahan)
  → `id` (semi).amount += 5L, harga dihitung via produced_recipes
  → stock_logs(type=in, produced, price=cost batch)
```

### 4. Menu — jual (existing)
```
product meng-gunakan semi (default) atau raw (option mode direct)
Order/penjualan → decrement semi stok (`stock_logs out`)
```

### 5. Transaction — snapshot (existing + bar)
```
complete():
  transaction_items.cost      = product_cost saat itu (frozen)
  packaging_cost             = order_type !== dine_in ? qty×packaging : 0
```
> ⚠️ FIX GAP (dari deep-read): decrement stok di `OrderController@store/accept` harus nulis `stock_logs(type=out, ref=sale)`. Sekarang TIDAK.

#### 6. COGS & HPP & Laba

```
PER BULAN:
  omzet      = Σ transaction.grand_total
  COGS food  = Σ transaction_items.cost         (exact+bumbu resep)
  Biaya bulk = Σ hpp_transactions(bulk)         (per-forecast / periode)
  Gaji      = Σ hpp_employees.salary
  Overhead  = Σ hpp_transactions(overhead)
  ──
  LABA KOTOR       = omzet − COGS food
  LABA BERSIH      = kotor − bulk − gaji − overhead
  HPP/menu         = (COGS + bulk + gaji + overhead) ÷ jumlah menu terjual
```

**Pemisah laporan:** COGS (bahan yang terukur) terpisah dari Bulk & HPP (operasional) — konvergensi di laba.

---

## TRACKING MODE — detail (final)

| Mode         | Sumber | Masuk | Contoh |
|--------------|--------|-------|--------|
| `exact`      | resep semi/menu | COGS menu | ayam, beras, sauce |
| `coarse`     | resep perkiraan gr/menu | COGS menu (+ selisih opname) | garam, lada saus |
| `bulk`       | beli-siran per periode | biaya operasional (BUKAN COGS menu) | minyak 50L/minggu |

Aturan WAJIB:
- 1 bahan = 1 mode (tidak bisa ditebak).
- Mode `bulk` TIDAK boleh masuk daftar resep (anti dobel); tersimpan di pilih resep.
- `coarse` boleh masuk resep tapi qty = perkiraan (bukan presisi).
- Selisih saat opname / tracking fisik dilihat di laporan (teoritik vs aktual) — fitur tahap lanjut.

---

## PACKAGING — order_type

```
products + pack_cost (harga pack per item) + pack_material (raw_id cup/box)
Order:
  order_type dine_in      → decrement pack = NONEmat (kecuali pack dine-in, cup=0)
  take_away/delivery      → decrement packing di stok mentah (cup/box)
cogs = food_cost + Σ pack_cost(qty)

Startup simplification: cup/box cukup di stok manual decrement saat order
callback; tidak membutuhkan tracking mode khusus.
```

---

## ⚠️ DEEP-READ TEMUAN (REQUIRED SEBELUM KERJA)

1. **GAP**: `OrderController@store` & `@accept` decrement `stock_amount` **TANPA** `stock_logs` → ledger timpang (masuk dicatat, keluar tidak). **FIX PERTAMA.**
2. `.env` = mysql, tapi koneksi refused di tinker — **DB harus hidup** sebelum migrate/test.
3. `product_stock` modif: tambah `raw_id` (opsional); identical lakukan balance; existing terjaga.
4. `product_stock` tanpa primary key — deserves non-blocking.

---

## BATTLE ORDER (eksekusi nanti)

| Step | Isi | Output |
|------|-----|--------|
| 0 | Fix `stock_logs` out decrement order | ledger lengkap |
| 1 | Siapkan DB (mysql/sqlite) | dev jalan |
| 2 | Migrasi `raw_materials` + mode + price calc | bahan mentah master |
| 3 | Migrasi `produced_recipes` + produksi modul (raw→semi) | produksi |
| 4 | Kolom cost di `stocks` (semi) + recalc | harga semi |
| 5 | `product_stock` + `raw_id`, form produk | menu bisa langsung |
| 6 | `transaction_items.cost` + packaging | snapshot |
| 7 | 4 tabel HPP + modul HPP + laporan laba | laporan |
| 8 | COGS/laporan + food cost % + bulk | la/rata |

---

## KEPUTUSANNYA TERTUTUP (tersegel)

- [x] PO putus total dari stok (tracker only)
- [x] Stok mentah = baru `raw_materials`, masuk manual
- [x] Existing `stocks` = semi (bahan menu), dipakai via BOM
- [x] 1 bahan 1 `tracking_mode`: exact / coarse / bulk
- [x] `bulk` tidak masuk resep — biaya peri
- [x] Packaging = stok biasa, decrement saat order takeaway/delivery
- [x] Cost semi & menu dihitung otomatis, frozen di transaksi
- [x] Loss 2 titik
- [x] Laporan: omzet − COGS − bulk − gaji − overhead = laba
- [x] Referensi rumus dari Seasonality (yield/loss/food cost/HPP)
- [x] Multi-tenant company_id

## TERBUKA (keumbahan)

- [ ] default % indirect seasoning kasar awal (0 dulu — disanding dorong lewat coarse/bulk)?
- [ ] Opname (teori vs aktual) — ditunda fase 2 bila mulai, status opsional
- [ ] `raw_id` langsung di menu — jikal final choice atau optional