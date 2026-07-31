# Prompt — Implementasi Pivot Diskon Product ↔ Discount

> Copy-paste prompt ini ke Claude kalo mau ngerjain pivot diskon.
> Udah include semua step, file, kode, dan seeder.
> Aman — gak ganggu fitur lain kalo jalanin step by step.

## ✅ STATUS EKSEKUSI

| Step | Status |
|------|--------|
| **STEP 1** — Migration `discount_product` | ✅ **SELESAI** — file `2026_07_29_000001_create_discount_product_table.php` udah dibikin |
| **STEP 9** — Seeder `DiscountProductSeeder` | ✅ **SELESAI** — file `database/seeders/DiscountProductSeeder.php` udah dibikin |
| **STEP 2** — Update Model Product & Discount | ⏳ **SELANJUTNYA** |
| STEP 3-8, 10 | ❌ Belum |

> Jalankan dulu: `php artisan migrate` lalu `php artisan db:seed --class=DiscountProductSeeder`
> Kalo udah, lanjut ke STEP 2.

---

## 🎯 Tujuan

Normalisasi relasi product↔discount pake pivot `discount_product` + timeline (`start_date`/`end_date`). Biar:
- Diskon yg dipake pas order **LIVE** dari master (bukan snapshot basi di produk)
- Ada history assignment — "produk X pas tgl Y pake diskon mana?"
- Produk cuma punya 1 diskon aktif, tapi bisa gonta-ganti sepanjang waktu

---

## ⚠️ ATURAN MAIN — BACA DULU

1. **Jangan jalanin migration hapus kolom dari `products` di awal** — itu step terakhir setelah data di-backfill
2. **OrderController** harus pake **fallback** — baca dari pivot dulu, kalo null fallback ke `product_discount_*` (kolom lama)
3. **Seeder baru** — isi pivot berdasarkan data `products.product_discount_*` yg udah ada
4. **Catat setiap perubahan di `log_code.md`** — format: `YYYY-MM-DD | [TIPE] | Deskripsi | File terkait`
5. **Jangan ubah table `product_histories`, `discount_histories`, `transaction_items`** — itu urusan lain
6. **Jangan ubah `ProductRequest`** — kalo perlu tambah field `discount_id` optional doang
7. **Baca referensi `resources/views/docs/`** buat class CSS, jangan extends file docs
8. **Form pake input-skeleton, btn-loading, min 400ms, Form Request, error B.Indonesia merah di bawah field**
9. **Notifikasi pake `NexoraToast()`**, gak pake session flash alert
10. **Min 400ms loading skeleton** di table AJAX

---

## 🔴 STEP 1: Migration — Bikin Tabel `discount_product`

Bikin file migration baru: `2026_07_29_000001_create_discount_product_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_product', function (Blueprint $table) {
            $table->id();
            $table->string('company_id')->nullable();

            $table->string('product_id')->nullable();   // FK ke products.product_id (string — ULID/auto)
            $table->string('discount_id')->nullable();   // FK ke discounts.discount_id (auto integer, tp dikirim string via pivot)

            // Timeline — ini yg bikin pivot jadi history
            $table->datetime('start_date')->nullable();   // kapan attach
            $table->datetime('end_date')->nullable();     // null = masih aktif, terisi = diganti/dilepas

            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_product');
    }
};
```

Jalanin: `php artisan migrate`

---

## 🟡 STEP 2: Model — Update `Product.php`

**File:** `app/Models/Admin/Product.php`

Tambahkan:

```php
// HAPUS relasi lama ini:
// public function discount()
// {
//     return $this->belongsTo(Discount::class, 'product_discount_id', 'discount_id');
// }

// TAMBAH relasi baru:
public function discounts()
{
    return $this->belongsToMany(Discount::class, 'discount_product', 'product_id', 'discount_id')
        ->withPivot('start_date', 'end_date')
        ->wherePivot('delete_status', 0)
        ->withTimestamps();
}

public function activeDiscount()
{
    return $this->belongsToMany(Discount::class, 'discount_product', 'product_id', 'discount_id')
        ->wherePivot('delete_status', 0)
        ->wherePivot('end_date', null)
        ->withTimestamps();
}
```

> **Catatan:** Method `discount()` dihapus. Method `discounts()` (plural) — semua diskon pernah dipake. Method `activeDiscount()` — diskon yg lagi aktif skrg (singular).

---

## 🟡 STEP 3: Model — Update `Discount.php`

**File:** `app/Models/Admin/Discount.php`

Tambahkan:

```php
// Relasi ke product via pivot (produk yg lagi aktif pake diskon ini)
public function activeProducts()
{
    return $this->belongsToMany(Product::class, 'discount_product', 'discount_id', 'product_id')
        ->wherePivot('delete_status', 0)
        ->wherePivot('end_date', null)
        ->withTimestamps();
}
```

---

## 🔴 STEP 4: Controller — Update `DiscountController@attachProduct` & `detachProduct`

**File:** `app/Http/Controllers/Admin/DiscountController.php`

### 4a. Ganti `attachProduct`

PROMPT:
```
Di file app/Http/Controllers/Admin/DiscountController.php, ubah method attachProduct.
Sekarang method ini: (1) validasi product_id, (2) update products.product_discount_* columns, (3) insert product_histories.

Ganti jadi:
1. Validasi product_id (sama)
2. Cek apakah produk punya pivot aktif (discount_product WHERE product_id = X AND end_date IS NULL AND delete_status = 0)
3. Kalo ada pivot aktif: set end_date = now() + updated_by di row lama
4. Insert row baru di discount_product: product_id, discount_id, company_id, start_date = now(), created_by
5. TETAP simpan snapshot ke product_histories — tapi kali ini panggil method terpisah yg isinya copy data produk + discount_id referencenya

Gunakan DB::transaction(). Gak perlu update products.product_discount_* lagi.
```

DETAIL KODE:

```php
public function attachProduct(Request $request, Discount $discount)
{
    $request->validate([
        'product_id' => 'required|exists:products,product_id',
    ]);

    $product = Product::findOrFail($request->product_id);

    DB::transaction(function () use ($product, $discount, $request) {
        $companyId = $product->company_id;
        $userId = $request->input('created_by', 'admin');

        // 1. Matikan pivot aktif kalo ada
        DB::table('discount_product')
            ->where('product_id', $product->product_id)
            ->whereNull('end_date')
            ->where('delete_status', 0)
            ->update([
                'end_date' => now(),
                'updated_by' => $userId,
                'updated_at' => now(),
            ]);

        // 2. Insert pivot baru
        DB::table('discount_product')->insert([
            'company_id' => $companyId,
            'product_id' => $product->product_id,
            'discount_id' => $discount->discount_id,
            'start_date' => now(),
            'created_by' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Log ke product_histories — rekam perubahan
        $oldValue = $product->product_discount_value;
        DB::table('product_histories')->insert([
            'product_id' => $product->product_id,
            'company_id' => $companyId,
            'history_code' => $product->product_code,
            'history_name' => $product->product_name,
            'history_slug' => $product->product_slug,
            'history_description' => $product->product_description,
            'history_price' => $product->product_price,
            'history_discount' => $discount->discount_value,
            'history_status' => $product->product_status,
            'history_image' => $product->product_image,
            'effective_date' => now(),
            'action_type' => $oldValue ? 'update' : 'create',
            'changed_by' => $userId,
            'created_by' => $userId,
            'delete_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    });

    if (request()->ajax()) {
        return response()->json(['success' => 'Diskon berhasil dihubungkan ke produk.']);
    }

    return redirect()->back()->with('success', 'Diskon berhasil dihubungkan ke produk.');
}
```

### 4b. Ganti `detachProduct`

PROMPT:
```
Di file yang sama, ubah method detachProduct.
Sekarang method ini: (1) validasi, (2) update products jadi null, (3) insert product_histories.

Ganti jadi:
1. Validasi (sama)
2. Cari pivot aktif (discount_product WHERE product_id AND discount_id AND end_date IS NULL AND delete_status = 0)
3. Set end_date = now(), delete_status = 1, updated_by
4. Simpen product_histories dgn action_type = 'delete'
5. HAPUS — update products jadi null (karena kolom product_discount_* nanti dihapus)
```

DETAIL KODE:

```php
public function detachProduct(Request $request, Discount $discount)
{
    $request->validate([
        'product_id' => 'required|exists:products,product_id',
    ]);

    $product = Product::findOrFail($request->product_id);

    DB::transaction(function () use ($product, $discount, $request) {
        $userId = $request->input('created_by', 'admin');
        $oldDiscountValue = $discount->discount_value;

        // Matikan pivot
        DB::table('discount_product')
            ->where('product_id', $product->product_id)
            ->where('discount_id', $discount->discount_id)
            ->whereNull('end_date')
            ->where('delete_status', 0)
            ->update([
                'end_date' => now(),
                'delete_status' => 1,
                'updated_by' => $userId,
                'updated_at' => now(),
            ]);

        // Log ke product_histories
        DB::table('product_histories')->insert([
            'product_id' => $product->product_id,
            'company_id' => $product->company_id,
            'history_code' => $product->product_code,
            'history_name' => $product->product_name,
            'history_slug' => $product->product_slug,
            'history_description' => $product->product_description,
            'history_price' => $product->product_price,
            'history_discount' => $oldDiscountValue,
            'history_status' => $product->product_status,
            'history_image' => $product->product_image,
            'effective_date' => now(),
            'action_type' => 'delete',
            'changed_by' => $userId,
            'created_by' => $userId,
            'delete_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    });

    if (request()->ajax()) {
        return response()->json(['success' => 'Diskon berhasil dilepas dari produk.']);
    }

    return redirect()->back()->with('success', 'Diskon berhasil dilepas dari produk.');
}
```

### 4c. Update method `show` — ganti query produk

Di `DiscountController@show`, ganti panggil `$discount->products` (relasi hasMany via `product_discount_id`) jadi `$discount->activeProducts` (relasi many-to-many via pivot).

PROMPT:
```
Di DiscountController@show, ubah $discount->load('company', 'products') jadi $discount->load('company', 'activeProducts').
```

---

## 🔴 STEP 5: Controller — Update `OrderController@store` & `complete`

**File:** `app/Http/Controllers/Admin/OrderController.php`

Cari bagian yg baca `product_discount_type` dan `product_discount_value` dari `$product`. Ganti jadi baca dari pivot aktif, dengan **fallback** ke kolom lama kalo pivot kosong.

PROMPT:
```
Di OrderController, cari 2 lokasi yg baca product_discount_type dan product_discount_value:

LOKASI 1 — method store (foreach items):
Ganti:
  $discountType = $product ? $product->product_discount_type : null;
  $discountValue = $product ? (float) ($product->product_discount_value ?? 0) : 0;

Jadi:
  $activeDisc = $product ? $product->activeDiscount()->first() : null;
  $discountType = $activeDisc?->discount_type ?? $product?->product_discount_type;
  $discountValue = $activeDisc ? (float) ($activeDisc->discount_value ?? 0) : (float) ($product?->product_discount_value ?? 0);

LOKASI 2 — method complete (foreach $order->products):
Ganti:
  $discountType = $product->product_discount_type;
  $discountValue = (float) ($product->product_discount_value ?? 0);

Jadi:
  $activeDisc = $product->activeDiscount()->first();
  $discountType = $activeDisc?->discount_type ?? $product->product_discount_type;
  $discountValue = $activeDisc ? (float) ($activeDisc->discount_value ?? 0) : (float) ($product->product_discount_value ?? 0);
```

> **Logika fallback:** Baca dari pivot (`$product->activeDiscount()`). Kalo null (produk belum pindah ke pivot), fallback ke kolom lama (`product_discount_type/value`). Biar gak broken selama transisi.

---

## 🟡 STEP 6: Controller — Update `ProductController` — tambah dropdown diskon

**File:** `app/Http/Controllers/Admin/ProductController.php`

PROMPT:
```
Di ProductController:
1. Method create: load $discounts = Discount::where('delete_status', 0)->where('discount_status', 1)->get() → kirim ke view
2. Method edit: load $discounts juga + data diskon yg lagi aktif buat old value
3. Method store: kalo ada request('discount_id'), attach ke pivot via DB::table('discount_product')->insert([...])
4. Method update: kalo discount_id berubah, matikan pivot lama + insert baru
```

Detail store:
```php
// Setelah $product tersimpan:
if ($request->filled('discount_id')) {
    DB::table('discount_product')->insert([
        'company_id' => $companyId,
        'product_id' => $product->product_id,
        'discount_id' => $request->discount_id,
        'start_date' => now(),
        'created_by' => 'admin',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
```

Detail update:
```php
// Kalo discount_id diubah:
$oldDiscountId = $product->product_discount_id; // dari kolom lama
$newDiscountId = $request->discount_id;

if ($newDiscountId != $oldDiscountId) {
    DB::transaction(function () use ($product, $oldDiscountId, $newDiscountId, $request) {
        // Matikan yg lama
        if ($oldDiscountId) {
            DB::table('discount_product')
                ->where('product_id', $product->product_id)
                ->whereNull('end_date')
                ->update(['end_date' => now(), 'updated_at' => now()]);
        }
        // Aktifkan yg baru
        if ($newDiscountId) {
            DB::table('discount_product')->insert([
                'company_id' => $product->company_id,
                'product_id' => $product->product_id,
                'discount_id' => $newDiscountId,
                'start_date' => now(),
                'created_by' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    });
}
```

---

## 🟡 STEP 7: View — Tambah Select Diskon di Form Produk

**File:** `resources/views/admin/product/create.blade.php` & `edit.blade.php`

PROMPT:
```
Di form create dan edit produk, tambah select dropdown buat milih diskon setelah field product_price.

Gunakan pattern input-skeleton + error handling B.Indonesia (aturan 7).

Data $discounts dikirim dari ProductController.
Old value: di create pake old('discount_id'), di edit pake data diskon aktif dari pivot.

Contoh:
<div class="input-skeleton">
    <label for="discount_id" class="form-label-modern">Diskon</label>
    <select name="discount_id" id="discount_id"
            class="form-control-modern @error('discount_id') is-invalid @enderror">
        <option value="">-- Tidak Ada Diskon --</option>
        @foreach($discounts as $d)
            <option value="{{ $d->discount_id }}" 
                {{ old('discount_id', $activeDiscountId ?? '') == $d->discount_id ? 'selected' : '' }}>
                {{ $d->discount_name }} 
                ({{ $d->discount_type == 'percentage' ? $d->discount_value.'%' : 'Rp '.number_format($d->discount_value, 0) }})
            </option>
        @endforeach
    </select>
    @error('discount_id')
        <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
    @enderror
</div>
```

**Untuk edit:** kirim `$activeDiscountId` dari controller:
```php
$activeDiscount = $product->activeDiscount()->first();
$activeDiscountId = $activeDiscount?->id;
return view('admin.product.edit', compact('product', 'categories', 'discounts', 'activeDiscountId'));
```

---

## 🟡 STEP 8: View — Update `discount/show.blade.php` — Tabel Produk

**File:** `resources/views/admin/discount/show.blade.php`

PROMPT:
```
Di view show diskon, tabel "Produk Terkait" yg skrg pake $discount->products (hasMany via product_discount_id), ganti jadi pake $discount->activeProducts (many-to-many via pivot).

Ganti:
  @foreach($discount->products as $product)
Jadi:
  @foreach($discount->activeProducts as $product)

Cek juga query di controller method show — pastiin load('activeProducts') bukan load('products').
```

---

## 🟢 STEP 9: Seeder — Bikin `DiscountProductSeeder`

**File baru:** `database/seeders/DiscountProductSeeder.php`

Seeder ini baca data yg udah ada di `products.product_discount_*` dan pindahin ke `discount_product` pivot. Biar data lama gak ilang.

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua produk yg punya discount_id
        $products = DB::table('products')
            ->whereNotNull('product_discount_id')
            ->where('delete_status', 0)
            ->get();

        $now = now();
        $count = 0;

        foreach ($products as $product) {
            // Cek apakah udah ada pivot buat produk ini
            $existing = DB::table('discount_product')
                ->where('product_id', $product->product_id)
                ->where('delete_status', 0)
                ->exists();

            if ($existing) {
                continue; // skip kalo udah ada (biar idempotent)
            }

            DB::table('discount_product')->insert([
                'company_id' => $product->company_id,
                'product_id' => $product->product_id,
                'discount_id' => $product->product_discount_id,
                'start_date' => $product->created_at ?? $now,
                'end_date' => null,
                'created_by' => 'seeder',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $count++;
        }

        echo "DiscountProductSeeder: {$count} pivot records created.\n";
    }
}
```

### Update `DatabaseSeeder.php`:

PROMPT:
```
Di database/seeders/DatabaseSeeder.php, tambah:
  $this->call(DiscountProductSeeder::class);
Letakkan setelah DiscountSeeder.
```

---

## 🔴 STEP 10: Migration — Hapus Kolom dari `products` (STEP TERAKHIR!)

**Hanya jalanin kalo SEMUA step 1-9 udah selesai, data udah backfill, aplikasi jalan pake pivot.**

File baru: `2026_07_29_000002_remove_discount_columns_from_products.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_discount_id');
            $table->dropColumn('product_discount_type');
            $table->dropColumn('product_discount_value');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('product_discount_id')->nullable()->after('product_price');
            $table->string('product_discount_type')->nullable()->after('product_discount_id');
            $table->decimal('product_discount_value', 15, 2)->nullable()->after('product_discount_type');
        });
    }
};
```

### Setelah ini — Hapus fallback code di OrderController

PROMPT:
```
Setelah migration hapus kolom dijalankan, hapus fallback di OrderController.
Ganti:
  $discountType = $activeDisc?->discount_type ?? $product->product_discount_type;
  $discountValue = $activeDisc ? (float) ($activeDisc->discount_value ?? 0) : (float) ($product->product_discount_value ?? 0);

Jadi (tanpa fallback):
  $discountType = $activeDisc?->discount_type;
  $discountValue = $activeDisc ? (float) ($activeDisc->discount_value ?? 0) : 0;
```

---

## 📋 URUTAN EKSEKUSI (WAJIB DIKUTI)

| Urut | Step | Status setelah |
|---|---|---|
| 1 | STEP 1 — Migration `discount_product` ✅ | Tabel pivot ada, kolom lama masih utuh |
| 2 | STEP 2 — Update Product.php | Relasi baru aktif, relasi lama dihapus |
| 3 | STEP 3 — Update Discount.php | Relasi activeProducts() siap |
| 4 | STEP 4 — Update DiscountController | Attach/detach nulis ke pivot, gak ke kolom lama |
| 5 | STEP 5 — Update OrderController (FALLBACK) | Baca pivot dulu, fallback ke kolom lama |
| 6 | STEP 6 — Update ProductController | Form produk bisa simpan ke pivot |
| 7 | STEP 7 — Update View produk | Select diskon muncul |
| 8 | STEP 8 — Update View diskon show | Tabel produk pake activeProducts |
| 9 | STEP 9 — Seeder DiscountProductSeeder | Data lama pindah ke pivot |
| — | **TEST** — cek order, cek attach/detach, cek laporan | ✅ |
| 10 | STEP 10 — Hapus kolom dari products | ✅ Final. Hapus fallback code. |

---

## 🧪 Test Checklist (setelah semua step)

- [ ] Bikin produk baru → pilih diskon → cek pivot terisi
- [ ] Edit produk → ganti diskon → cek pivot lama end_date terisi + pivot baru
- [ ] Hapus diskon dari produk → detach → cek end_date + delete_status
- [ ] Order → store → cek discount_type/value dari pivot (bukan kolom produk)
- [ ] Order → complete → cek transaction_items discount_amount bener
- [ ] Laporan → cek transaction_items subtotal frozen
- [ ] Menu Diskon → show → cek daftar produk related bener
- [ ] Menu Diskon → attach product → cek pivot terisi + notif sukses

---

## 🔄 Rollback Plan (kalo error)

| Error | Rollback |
|---|---|
| Step 4 error — DiscountController | Balikin kode ke commit sebelumnya. Pivot udah terisi tapi gak dipake (OrderController masih fallback) |
| Step 5 error — OrderController | Balikin kode, produk masih jalan pake kolom lama |
| Step 10 error — hapus kolom | `php artisan migrate:rollback` → kolom balik. Tapi kalo data di kolom udah dikosongin (karena dihapus), perlu restore dari backup |
